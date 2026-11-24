<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * Every publicly registered account receives only the normal user role.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class,
            ],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
            ],
        ]);

        $user = DB::transaction(function () use ($request) {

            // Create the new account.
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Assign Default Role
            |--------------------------------------------------------------------------
            |
            | Public registration always creates a normal user.
            | Never accept role information from the registration form.
            | Admin-created accounts are handled separately.
            |
            */

            $user->assignRole('user');

            /*
            |--------------------------------------------------------------------------
            | Link Guest Reservation
            |--------------------------------------------------------------------------
            |
            | Only link a reservation using the server-generated token
            | stored in the current browser session.
            |
            */

            $this->linkGuestBookingFromSession($user, $request);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        // Return to the guest reservation if registration happened
        // during the booking process.
        if (session()->has('guest_booking_return')) {
            $returnUrl = session()->pull('guest_booking_return');

            return redirect($returnUrl);
        }

        return redirect(route('dashboard', absolute: false));
    }

    /**
     * Link the guest reservation stored in this session to the new user.
     */
    private function linkGuestBookingFromSession(
        User $user,
        Request $request
    ): void {
        $token = session()->pull('guest_booking_token');

        if (!$token) {
            return;
        }

        $reservation = Reservation::where('guest_token', $token)
            ->whereNull('user_id')
            ->first();

        if (!$reservation) {
            Log::warning('[Register] Guest token found but reservation missing', [
                'token' => $token,
                'user_id' => $user->id,
            ]);

            return;
        }

        // Attach the reservation to the newly registered account.
        $reservation->update([
            'user_id' => $user->id,
            'guest_token' => null,
        ]);

        Log::info('[Register] Guest reservation linked to new user', [
            'user_id' => $user->id,
            'reservation_id' => $reservation->id,
        ]);
    }
}

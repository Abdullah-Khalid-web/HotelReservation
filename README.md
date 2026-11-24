# Hotel Reservation & AI Voice Assistant

An intelligent hotel reservation management system built with Laravel, featuring an AI-powered conversational assistant that helps guests explore available tables, make reservations, and interact with the booking system through natural language and voice commands.

The project combines traditional hotel reservation functionality with modern AI capabilities to create a smoother, more accessible, and user-friendly booking experience.

## Overview

The Hotel Reservation System is designed to simplify the guest booking journey while providing hotel administrators with tools to manage reservations and hotel operations.

Guests can interact with the system through a web-based booking interface or communicate with the AI assistant to get assistance with reservations.

The AI assistant supports conversational interactions in English and Urdu, including voice input and spoken responses.

## Key Features

### AI-Powered Reservation Assistant
- Conversational AI assistant powered by Google's Gemini API.
- Understands natural-language booking requests.
- Helps guests navigate the reservation process.
- Uses function calling to interact with application tools.
- Maintains conversation context across interactions.
- Supports English and Urdu conversations.

### Voice Interaction
- Voice input using browser speech recognition.
- Text-to-speech responses for conversational interaction.
- Language-aware speech recognition.
- Voice-enabled guest interaction.
- Browser-based speech synthesis without requiring a separate voice application.

### Guest Reservation System
- User-friendly guest booking interface.
- View available tables.
- Select reservation details.
- Submit booking requests.
- Guest booking identification through reservation tokens.
- Reservation information management.

### Reservation Management
- Centralized reservation handling.
- Table availability management.
- Hotel and table relationship management.
- Reservation validation and processing.

### AI Tool Integration
- Structured AI function calling.
- Application tools for accessing reservation functionality.
- Controlled execution of application operations.
- Separation of AI conversation logic and business logic.

## Technology Stack

| Technology | Purpose |
|---|---|
| PHP | Backend programming |
| Laravel | Application framework |
| MySQL | Database management |
| JavaScript | Frontend interactions |
| Blade | Server-rendered UI |
| Alpine.js | Reactive frontend components |
| Google Gemini API | AI conversational assistant |
| Web Speech API | Voice recognition and speech synthesis |
| HTML5 & CSS3 | User interface |

## How It Works

1. A guest opens the hotel reservation interface.
2. The guest can browse available tables or interact with the AI assistant.
3. The guest communicates using text or voice.
4. Gemini processes the request and determines the appropriate application action.
5. The backend executes the relevant tool and retrieves application data.
6. The assistant generates a conversational response.
7. The guest receives the response through the chat interface and, when enabled, voice output.
8. The guest can proceed with the reservation process.

## AI Architecture

The AI assistant uses a tool-based architecture rather than relying exclusively on plain-text AI responses.

The backend handles:
- Conversation processing.
- Gemini API communication.
- Function/tool definitions.
- Tool execution.
- Conversation state management.
- Reservation-related business logic.

This architecture allows the AI assistant to interact with real application functionality while keeping business operations within the Laravel backend.

## Project Goals

- Simplify the hotel reservation experience.
- Make booking interactions more conversational.
- Reduce the effort required to navigate booking interfaces.
- Introduce practical AI integration into a Laravel application.
- Support multilingual and voice-based interactions.
- Demonstrate the integration of AI with real-world business workflows.

## Installation

Clone the repository:

```bash
git clone YOUR_REPOSITORY_URL
```

Navigate to the project directory:

```bash
cd YOUR_PROJECT_DIRECTORY
```

Install PHP dependencies:

```bash
composer install
```

Create the environment file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Configure your database and Gemini API credentials in `.env`.

Run migrations:

```bash
php artisan migrate
```

Start the development server:

```bash
php artisan serve
```

Open the application:

```text
http://127.0.0.1:8000
```

## Environment Configuration

Configure the required application settings in your `.env` file.

```env
APP_NAME="Hotel Reservation System"
APP_ENV=local
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hotel_reservation
DB_USERNAME=root
DB_PASSWORD=

GEMINI_API_KEY=your_gemini_api_key
```

Use the actual environment variable names expected by your application.

## Future Enhancements

- Online payment integration.
- Reservation confirmation notifications.
- Advanced hotel analytics and reporting.
- Improved multilingual voice support.
- Admin dashboard enhancements.
- Reservation history and guest profiles.
- Additional AI-powered hotel management features.

## Developers

### Abdullah Khalid
**Software Engineer | Full Stack Developer**

- [LinkedIn](https://linkedin.com/in/abdullah-khalid-se)
- [GitHub](https://github.com/Abdullah-Khalid-web)

### Muhammad Nassar
**Software Engineer | Full Stack Developer**

---

*An AI-powered hotel reservation experience combining conversational interfaces, voice interaction, and Laravel-based business logic.*git 

<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $hotels = Hotel::all();

        if ($hotels->isEmpty()) {
            $this->command->warn('No hotels found. Skipping ProductSeeder.');
            return;
        }

        // Master menu — applied to every hotel
        $menu = [
            // ───────────── Starters ─────────────
            [
                'name'               => 'Chicken Tikka',
                'category'           => 'Starters',
                'price'              => 450.00,
                'description'        => 'Tender chicken chunks marinated in yogurt and spices, grilled to perfection.',
                'is_vegetarian'      => false,
                'preparation_minutes'=> 20,
                'stock_quantity'     => 50,
            ],
            [
                'name'               => 'Seekh Kebab',
                'category'           => 'Starters',
                'price'              => 500.00,
                'description'        => 'Minced meat skewers with aromatic herbs and spices.',
                'is_vegetarian'      => false,
                'preparation_minutes'=> 25,
                'stock_quantity'     => 40,
            ],
            [
                'name'               => 'Vegetable Samosa (4 pcs)',
                'category'           => 'Starters',
                'price'              => 250.00,
                'description'        => 'Crispy pastry filled with spiced potatoes and peas.',
                'is_vegetarian'      => true,
                'preparation_minutes'=> 15,
                'stock_quantity'     => 100,
            ],
            [
                'name'               => 'Paneer Tikka',
                'category'           => 'Starters',
                'price'              => 550.00,
                'description'        => 'Marinated cottage cheese cubes grilled with peppers and onions.',
                'is_vegetarian'      => true,
                'preparation_minutes'=> 20,
                'stock_quantity'     => 35,
            ],

            // ───────────── Main Course ─────────────
            [
                'name'               => 'Chicken Karahi',
                'category'           => 'Main Course',
                'price'              => 1200.00,
                'description'        => 'Classic Pakistani chicken karahi cooked in tomatoes, ginger, and green chilies.',
                'is_vegetarian'      => false,
                'preparation_minutes'=> 30,
                'stock_quantity'     => 30,
            ],
            [
                'name'               => 'Mutton Karahi',
                'category'           => 'Main Course',
                'price'              => 1800.00,
                'description'        => 'Tender mutton pieces slow-cooked in a rich tomato-based gravy.',
                'is_vegetarian'      => false,
                'preparation_minutes'=> 40,
                'stock_quantity'     => 20,
            ],
            [
                'name'               => 'Beef Nihari',
                'category'           => 'Main Course',
                'price'              => 1500.00,
                'description'        => 'Slow-cooked beef shank in aromatic spice blend, served with naan.',
                'is_vegetarian'      => false,
                'preparation_minutes'=> 45,
                'stock_quantity'     => 15,
            ],
            [
                'name'               => 'Chicken Biryani',
                'category'           => 'Main Course',
                'price'              => 900.00,
                'description'        => 'Fragrant basmati rice layered with spiced chicken and saffron.',
                'is_vegetarian'      => false,
                'preparation_minutes'=> 35,
                'stock_quantity'     => 40,
            ],
            [
                'name'               => 'Daal Makhani',
                'category'           => 'Main Course',
                'price'              => 700.00,
                'description'        => 'Creamy black lentils slow-cooked overnight with butter and cream.',
                'is_vegetarian'      => true,
                'preparation_minutes'=> 25,
                'stock_quantity'     => 50,
            ],
            [
                'name'               => 'Palak Paneer',
                'category'           => 'Main Course',
                'price'              => 800.00,
                'description'        => 'Cottage cheese cubes in a smooth spinach gravy.',
                'is_vegetarian'      => true,
                'preparation_minutes'=> 25,
                'stock_quantity'     => 35,
            ],

            // ───────────── Breads ─────────────
            [
                'name'               => 'Naan (2 pcs)',
                'category'           => 'Breads',
                'price'              => 100.00,
                'description'        => 'Soft tandoori flatbread.',
                'is_vegetarian'      => true,
                'preparation_minutes'=> 8,
                'stock_quantity'     => null,
            ],
            [
                'name'               => 'Garlic Naan',
                'category'           => 'Breads',
                'price'              => 150.00,
                'description'        => 'Tandoori naan topped with fresh garlic and coriander.',
                'is_vegetarian'      => true,
                'preparation_minutes'=> 10,
                'stock_quantity'     => null,
            ],
            [
                'name'               => 'Roti (2 pcs)',
                'category'           => 'Breads',
                'price'              => 80.00,
                'description'        => 'Whole wheat tandoori roti.',
                'is_vegetarian'      => true,
                'preparation_minutes'=> 8,
                'stock_quantity'     => null,
            ],

            // ───────────── Rice ─────────────
            [
                'name'               => 'Plain Rice',
                'category'           => 'Rice',
                'price'              => 300.00,
                'description'        => 'Steamed basmati rice.',
                'is_vegetarian'      => true,
                'preparation_minutes'=> 15,
                'stock_quantity'     => null,
            ],
            [
                'name'               => 'Kashmiri Pulao',
                'category'           => 'Rice',
                'price'              => 600.00,
                'description'        => 'Fragrant rice with dry fruits and saffron.',
                'is_vegetarian'      => true,
                'preparation_minutes'=> 25,
                'stock_quantity'     => 30,
            ],

            // ───────────── Drinks ─────────────
            [
                'name'               => 'Soft Drink (500ml)',
                'category'           => 'Drinks',
                'price'              => 150.00,
                'description'        => 'Coke, Sprite, or Fanta.',
                'is_vegetarian'      => true,
                'preparation_minutes'=> 2,
                'stock_quantity'     => 200,
            ],
            [
                'name'               => 'Fresh Lime',
                'category'           => 'Drinks',
                'price'              => 200.00,
                'description'        => 'Fresh lime juice with mint.',
                'is_vegetarian'      => true,
                'preparation_minutes'=> 5,
                'stock_quantity'     => 100,
            ],
            [
                'name'               => 'Mango Lassi',
                'category'           => 'Drinks',
                'price'              => 350.00,
                'description'        => 'Creamy yogurt drink with fresh mango.',
                'is_vegetarian'      => true,
                'preparation_minutes'=> 5,
                'stock_quantity'     => 60,
            ],
            [
                'name'               => 'Green Tea',
                'category'           => 'Drinks',
                'price'              => 120.00,
                'description'        => 'Hot green tea.',
                'is_vegetarian'      => true,
                'preparation_minutes'=> 3,
                'stock_quantity'     => null,
            ],

            // ───────────── Desserts ─────────────
            [
                'name'               => 'Gulab Jamun (2 pcs)',
                'category'           => 'Desserts',
                'price'              => 250.00,
                'description'        => 'Sweet milk dumplings in rose syrup.',
                'is_vegetarian'      => true,
                'preparation_minutes'=> 5,
                'stock_quantity'     => 50,
            ],
            [
                'name'               => 'Kheer',
                'category'           => 'Desserts',
                'price'              => 300.00,
                'description'        => 'Traditional rice pudding with cardamom and nuts.',
                'is_vegetarian'      => true,
                'preparation_minutes'=> 5,
                'stock_quantity'     => 40,
            ],
            [
                'name'               => 'Kulfi',
                'category'           => 'Desserts',
                'price'              => 200.00,
                'description'        => 'Traditional frozen milk dessert.',
                'is_vegetarian'      => true,
                'preparation_minutes'=> 2,
                'stock_quantity'     => 30,
            ],
        ];

        $totalCreated = 0;

        foreach ($hotels as $hotel) {
            foreach ($menu as $index => $item) {
                Product::updateOrCreate(
                    [
                        'hotel_id' => $hotel->id,
                        'name'     => $item['name'],
                    ],
                    [
                        'description'         => $item['description'],
                        'price'               => $item['price'],
                        'category'            => $item['category'],
                        'is_vegetarian'       => $item['is_vegetarian'],
                        'is_available'        => true,
                        'stock_quantity'      => $item['stock_quantity'],
                        'preparation_minutes' => $item['preparation_minutes'],
                        'sort_order'          => $index,
                    ]
                );

                $totalCreated++;
            }
        }

        $this->command->info("ProductSeeder: {$totalCreated} products seeded across {$hotels->count()} hotel(s).");
    }
}

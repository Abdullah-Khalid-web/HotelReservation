<?php

namespace App\Services\Chatbot;

class Tools
{
    public static function definitions(): array
    {
        return [
            /* ══════════════ LOGGED-IN USER TOOLS ══════════════ */

            [
                'name' => 'book_table',
                'description' => 'Book a table for the CURRENT LOGGED-IN user. Do not use for guests.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'hotel_id' => ['type' => 'INTEGER'],
                        'date' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD'],
                        'time' => ['type' => 'STRING', 'description' => 'HH:MM 24h'],
                        'guests' => ['type' => 'INTEGER'],
                    ],
                    'required' => ['hotel_id', 'date', 'time', 'guests'],
                ],
            ],

            [
                'name' => 'place_pre_order',
                'description' => 'Place a food pre-order for an existing reservation of the current user.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'reservation_id' => ['type' => 'INTEGER'],
                        'items' => [
                            'type' => 'ARRAY',
                            'items' => [
                                'type' => 'OBJECT',
                                'properties' => [
                                    'product_name' => ['type' => 'STRING'],
                                    'quantity' => ['type' => 'INTEGER'],
                                ],
                                'required' => ['product_name', 'quantity'],
                            ],
                        ],
                    ],
                    'required' => ['reservation_id', 'items'],
                ],
            ],

            [
                'name' => 'list_user_reservations',
                'description' => 'List current user reservations, optionally between two dates.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'limit' => ['type' => 'INTEGER'],
                        'from' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD'],
                        'to' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD'],
                    ],
                ],
            ],

            [
                'name' => 'update_reservation',
                'description' => 'Update an existing reservation of the current user.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'reservation_id' => ['type' => 'INTEGER'],
                        'date' => ['type' => 'STRING'],
                        'time' => ['type' => 'STRING'],
                        'guests' => ['type' => 'INTEGER'],
                    ],
                    'required' => ['reservation_id'],
                ],
            ],

            [
                'name' => 'cancel_reservation',
                'description' => 'Cancel an existing reservation of the current user.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'reservation_id' => ['type' => 'INTEGER'],
                    ],
                    'required' => ['reservation_id'],
                ],
            ],

            /* ══════════════ PUBLIC READ-ONLY TOOLS ══════════════ */

            [
                'name' => 'list_hotels',
                'description' => 'List all available hotels.',
                'parameters' => ['type' => 'OBJECT'],
            ],

            [
                'name' => 'list_menu',
                'description' => 'List menu items for a hotel. Filter by category or vegetarian_only.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'hotel_id' => ['type' => 'INTEGER'],
                        'category' => ['type' => 'STRING'],
                        'vegetarian_only' => ['type' => 'BOOLEAN'],
                    ],
                ],
            ],

            [
                'name' => 'list_available_tables',
                'description' => 'List tables currently available for a hotel.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'hotel_id' => ['type' => 'INTEGER'],
                        'min_capacity' => ['type' => 'INTEGER'],
                    ],
                ],
            ],

            [
                'name' => 'hotel_info',
                'description' => 'Get hotel name, address, phone, email.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'hotel_id' => ['type' => 'INTEGER'],
                    ],
                ],
            ],

            /* ══════════════ GUEST BOOKING TOOLS ══════════════ */

            [
                'name' => 'create_guest_reservation',
                'description' => 'Create a reservation for a GUEST (not logged in). Call ONLY when all 5 required fields are present.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'guest_name' => ['type' => 'STRING'],
                        'guest_phone' => ['type' => 'STRING', 'description' => 'Phone number, NOT email'],
                        'guest_email' => ['type' => 'STRING'],
                        'reservation_date' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD'],
                        'reservation_time' => ['type' => 'STRING', 'description' => 'HH:MM 24h'],
                        'number_of_guests' => ['type' => 'INTEGER'],
                        'notes' => ['type' => 'STRING'],
                        'hotel_id' => ['type' => 'INTEGER'],
                    ],
                    'required' => [
                        'guest_name',
                        'guest_phone',
                        'reservation_date',
                        'reservation_time',
                        'number_of_guests',
                    ],
                ],
            ],
            [
                'name' => 'manage_pre_order',
                'description' => 'Manage an EXISTING pre-order for the current reservation. Supports add, remove, update_quantity and replace. Use this for BOTH logged-in users and guests. Never ask the guest to register just to manage food.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'reservation_id' => [
                            'type' => 'INTEGER',
                        ],
                        'action' => [
                            'type' => 'STRING',
                            'enum' => ['add', 'remove', 'update_quantity', 'replace'],
                        ],
                        'product_name' => [
                            'type' => 'STRING',
                            'description' => 'Existing or target food product name.',
                        ],
                        'quantity' => [
                            'type' => 'INTEGER',
                            'description' => 'Quantity to add or set.',
                        ],
                        'replacement_product_name' => [
                            'type' => 'STRING',
                            'description' => 'New food item name when action is replace.',
                        ],
                        'replacement_quantity' => [
                            'type' => 'INTEGER',
                            'description' => 'Quantity of replacement food item.',
                        ],
                    ],
                    'required' => ['reservation_id', 'action'],
                ],
            ],

            [
                'name' => 'create_guest_pre_order',
                'description' => 'Place a food pre-order for a guest reservation created earlier.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'reservation_id' => ['type' => 'INTEGER'],
                        'items' => [
                            'type' => 'ARRAY',
                            'items' => [
                                'type' => 'OBJECT',
                                'properties' => [
                                    'product_name' => ['type' => 'STRING'],
                                    'product_id' => ['type' => 'INTEGER'],
                                    'quantity' => ['type' => 'INTEGER'],
                                ],
                            ],
                        ],
                    ],
                    'required' => ['reservation_id', 'items'],
                ],
            ],
        ];
    }
}

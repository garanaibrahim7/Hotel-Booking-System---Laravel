<?php

return [
    'client' => [
        'navbar' => [
            'Home' => 'client.home',
            'Explore' => '#explore',
            'Hotels' => 'client.hotels.explore',
            'Rooms' => 'client.rooms',
            'Bookings' => 'booking.all',
            'About Us' => 'client.aboutus',
            'Contact' => 'client.contact',
            'Stay Summary' => 'booking.stay.summary',
            'Login' => 'login',
        ],
    ],

    'admin' => [
        'navbar' => [
            'Hotels' => 'admin.hotels.index',
            'Rooms Categories' => 'admin.categories.index',
            'Bookings' => 'admin.bookings.index',
            'Discounts' => 'admin.discounts.index',
            'Room Blocks' => 'admin.rooms.blocks',
            'Refunds' => 'admin.refunds',
            'Transactions' => 'admin.transactions',
        ],
        'sidebar' => [
            [
                'title' => 'Dashboard',
                'icon' => 'bi bi-speedometer2',
                'fill-icon' => 'bi bi-speedometer',
                'link' => 'admin.dashboard',
            ],
            [
                'title' => 'Live Bookings',
                'icon' => 'bi bi-hourglass',
                'fill-icon' => 'bi bi-hourglass-split',
                'link' => 'admin.bookings.live',
            ],
            // [
            //     'title' => 'Bookings',
            //     'icon' => 'bi bi-calendar-check',
            //     'fill-icon' => 'bi bi-calendar-check',
            //     'link' => 'admin.bookings.index',
            // ],
            [
                'title' => 'Discounts',
                'icon' => 'bi bi-cash',
                'fill-icon' => 'bi bi-cash-stack',
                'link' => 'admin.discounts.index',
            ],
            [
                'title' => 'Bookings',
                'icon' => 'bi bi-calendar-check',
                'fill-icon' => 'bi bi-calendar-check-fill',
                'link' => [
                    [
                        'title' => 'Live Bookings',
                        'icon' => 'bi bi-hourglass',
                        'fill-icon' => 'bi bi-hourglass-split',
                        'link' => 'admin.bookings.live',
                    ],
                    [
                        'title' => 'Booking Report',
                        'icon' => 'bi bi-file-earmark-ruled',
                        'fill-icon' => 'bi bi-file-earmark-ruled-fill',
                        'link' => 'admin.bookings.report',
                    ],
                    [
                        'title' => 'Bookings',
                        'icon' => 'bi bi-calendar2-week',
                        'fill-icon' => 'bi bi-calendar2-week-fill',
                        'link' => 'admin.bookings.index',
                    ],
                ],
            ],
            [
                'title' => 'Hotels',
                'icon' => 'bi bi-building',
                'fill-icon' => 'bi bi-building',
                'link' => [
                    [
                        'title' => 'Add New Hotel',
                        'icon' => 'bi bi-building-add',
                        'fill-icon' => 'bi bi-building-fill-add',
                        'link' => 'admin.hotels.create',
                    ],
                    [
                        'title' => 'All Hotels',
                        'icon' => 'bi bi-buildings',
                        'fill-icon' => 'bi bi-buildings-fill',
                        'link' => 'admin.hotels.index',
                    ],
                ],
            ],
            [
                'title' => 'Rooms',
                'icon' => 'bi bi-door-open',
                'fill-icon' => 'bi bi-door-open',
                'link' => [
                    [
                        'title' => 'Add New Room',
                        'icon' => 'bi bi-house-add',
                        'fill-icon' => 'bi bi-house-add-fill',
                        'link' => 'admin.rooms.create',
                    ],
                    [
                        'title' => 'All Categories',
                        'icon' => 'bi bi-houses',
                        'fill-icon' => 'bi bi-houses-fill',
                        'link' => 'admin.categories.index',
                    ],
                    [
                        'title' => 'Add New Category',
                        'icon' => 'bi bi-house-add',
                        'fill-icon' => 'bi bi-house-add-fill',
                        'link' => 'admin.categories.create',
                    ],
                ],
            ],
            [
                'title' => 'Reviews',
                'icon' => 'bi bi-chat-left-quote',
                'fill-icon' => 'bi bi-chat-left-quote-fill',
                'link' => 'admin.reviews.index',
            ],
            [
                'title' => 'Users',
                'icon' => 'bi bi-person',
                'fill-icon' => 'bi bi-person-fill',
                'link' => 'admin.users.index',
            ],
            [
                'title' => 'Transactions',
                'icon' => 'bi bi-list-columns',
                'fill-icon' => 'bi bi-list-columns-reverse',
                'link' => 'admin.transactions',
            ],
            [
                'title' => 'Subscription Plans',
                'icon' => 'bi bi-bell',
                'fill-icon' => 'bi bi-bell-fill',
                'link' => 'admin.subscription.index',
            ],
        ],
    ],
    'manager' => [
        'navbar' => [
            // 'Hotels' => 'admin.hotels.index',
            // 'Rooms Categories' => 'admin.categories.index',
            'Bookings' => 'manager.bookings.index',
            'Live Bookings' => 'manager.bookings.live',
            // 'Discounts' => 'manager.discounts.index',
            // 'Room Blocks' => 'admin.rooms.blocks',
        ],
        'sidebar' => [
            [
                'title' => 'Dashboard',
                'icon' => 'bi bi-speedometer2',
                'fill-icon' => 'bi bi-speedometer',
                'link' => 'manager.dashboard',
            ],
            [
                'title' => 'Booking Report',
                'icon' => 'bi bi-file-earmark-ruled',
                'fill-icon' => 'bi bi-file-earmark-ruled-fill',
                'link' => 'manager.bookings.print',
            ],
            [
                'title' => 'Bookings',
                'icon' => 'bi bi-calendar2-week',
                'fill-icon' => 'bi bi-calendar2-week-fill',
                'link' => 'manager.bookings.index',
            ],
            [
                'title' => 'Live Bookings',
                'icon' => 'bi bi-hourglass',
                'fill-icon' => 'bi bi-hourglass-split',
                'link' => 'admin.bookings.live',
            ],
            // [
            //     'title' => 'Discounts',
            //     'icon' => 'bi bi-cash',
            //     'fill-icon' => 'bi bi-cash-stack',
            //     'link' => 'admin.discounts.index',
            // ],

            // [
            //     'title' => 'Reviews',
            //     'icon' => 'bi bi-chat-left-quote',
            //     'fill-icon' => 'bi bi-chat-left-quote-fill',
            //     'link' => 'manager.reviews.index',
            // ],
        ],
    ],

    'customer' => [],
    'guest' => [],

];

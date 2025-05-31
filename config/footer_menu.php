<?php

return [
    'sections' => [
        'company' => [
            'title' => 'Company',
            'order' => 1,
            'links' => [
                ['label' => 'About Us', 'url' => '/about', 'external' => false],
                ['label' => 'Our Team', 'url' => '/team', 'external' => false],
                ['label' => 'Careers', 'url' => '/careers', 'external' => false],
                ['label' => 'News & Press', 'url' => '/news', 'external' => false],
                ['label' => 'Contact', 'url' => '/contact', 'external' => false],
            ]
        ],
        'products' => [
            'title' => 'Products & Services',
            'order' => 2,
            'links' => [
                ['label' => 'Dashboard', 'url' => '/dashboard', 'external' => false],
                ['label' => 'Analytics', 'url' => '/analytics', 'external' => false],
                ['label' => 'Reports', 'url' => '/reports', 'external' => false],
                ['label' => 'Integrations', 'url' => '/integrations', 'external' => false],
                ['label' => 'API Documentation', 'url' => '/api/docs', 'external' => false],
            ]
        ],
        'support' => [
            'title' => 'Support & Help',
            'order' => 3,
            'links' => [
                ['label' => 'Help Center', 'url' => '/help', 'external' => false],
                ['label' => 'Documentation', 'url' => '/docs', 'external' => false],
                ['label' => 'Community Forum', 'url' => 'https://community.example.com', 'external' => true],
                ['label' => 'Submit Ticket', 'url' => '/support/ticket', 'external' => false],
                ['label' => 'System Status', 'url' => 'https://status.example.com', 'external' => true],
            ]
        ],
        'legal' => [
            'title' => 'Legal & Privacy',
            'order' => 4,
            'links' => [
                ['label' => 'Privacy Policy', 'url' => '/privacy', 'external' => false],
                ['label' => 'Terms of Service', 'url' => '/terms', 'external' => false],
                ['label' => 'Cookie Policy', 'url' => '/cookies', 'external' => false],
                ['label' => 'GDPR Compliance', 'url' => '/gdpr', 'external' => false],
            ]
        ],
        'social' => [
            'title' => 'Follow Us',
            'order' => 5,
            'links' => [
                ['label' => 'Twitter', 'url' => 'https://twitter.com/yourcompany', 'external' => true, 'icon' => 'heroicon-o-x-mark'],
                ['label' => 'LinkedIn', 'url' => 'https://linkedin.com/company/yourcompany', 'external' => true, 'icon' => 'heroicon-o-building-office'],
                ['label' => 'GitHub', 'url' => 'https://github.com/yourcompany', 'external' => true, 'icon' => 'heroicon-o-code-bracket'],
                ['label' => 'YouTube', 'url' => 'https://youtube.com/yourcompany', 'external' => true, 'icon' => 'heroicon-o-play'],
            ]
        ]
    ],

    // Global footer settings
    'settings' => [
        'show_newsletter' => true,
        'show_company_info' => true,
        'copyright_text' => '© 2024 Your Company Name. All rights reserved.',
        'company_description' => 'Building innovative solutions for modern businesses.',
    ],

    // Company contact information
    'contact_info' => [
        'address' => '123 Business Street, City, State 12345',
        'phone' => '+1 (555) 123-4567',
        'email' => 'contact@yourcompany.com',
    ]
];
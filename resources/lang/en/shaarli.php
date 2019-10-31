<?php

return [
    // Settings
    'settings' => [
        'general' => [
            'title' => 'General',
            'install_button' => 'Install',
            'site_name' => 'Site name',
            'lang' => 'Language',
            'private_help' => 'Private content (all content is private and login is required)',
        ],

        'appearance' => [
            'title' => 'Appearance',
            'is_dark' => 'Dark mode',
            'home_show_tags' => 'Show tags on homepage',
            'home_show_chests' => 'Show chests on homepage',
            'compact_cardslist' => 'Compact cards list',
            'columns_count' => 'Number of columns to show',
            'custom_background' => 'Custom background',
            'custom_icon' => 'Custom icon (512x512, .png)',
        ],

        '2fa' => [
            'title' => 'Secure login',
            'secure_login' => '2-FA login (requires a code sent by email)',
            'secure_code_expires' => 'Secure code expiration (in minutes)',
            'secure_code_length' => 'Secure code length',
        ],

        'archiving' => [
            'title' => 'Archiving',
            'private_archive' => 'Make archives private?',
            'link_archive_pdf' => 'PDF archiving (Web pages to PDF)',
            'node_bin' => 'Node.js binary',
            'link_archive_media' => 'Media archiving (Youtube, Soundcloud, ...)',
            'youtube_dl_bin' => 'Youtube-dl binary',
        ],

        'backup' => [
            'title' => 'Backup',
            'enabled' => 'Backup enabled?',
            'enabled_help' => 'Ensure that your backup configuration is correct.',
            'only_database' => 'Save only database?',
            'period' => 'Backup period',
            'period_daily' => 'Daily',
            'period_weekly' => 'Weekly',
        ]
    ],

    // Mails
    'mails' => [
        '2fa' => [
            'title' => 'Secure your login',
            'message' => 'Please use the following code :code to access your account.',
            'button' => 'Confirm login',
        ],
    ],
];

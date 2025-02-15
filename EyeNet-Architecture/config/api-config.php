<?php

/**
 * EyeNet API Configuration Template
 * This file contains configuration templates for all API integrations
 */

return [
    // OpenDaylight SDN Controller Configuration
    'opendaylight' => [
        'enabled' => false, // Enable only after testing
        'base_url' => 'http://localhost:8181',
        'credentials' => [
            'username' => 'admin',
            'password' => 'admin' // Change in production
        ],
        'settings' => [
            'timeout' => 30,
            'verify_ssl' => true,
            'retry_attempts' => 3
        ]
    ],

    // pfSense Configuration
    'pfsense' => [
        'enabled' => false, // Enable only after testing
        'base_url' => 'https://192.168.1.1',
        'api_key' => '', // Set during implementation
        'settings' => [
            'timeout' => 30,
            'verify_ssl' => true,
            'bandwidth_rules' => [
                'default_limit' => '10M', // 10 Mbps default
                'burst_limit' => '15M'    // 15 Mbps burst
            ]
        ]
    ],

    // MikroTik RouterOS Configuration
    'mikrotik' => [
        'enabled' => false, // Enable only after testing
        'host' => '192.168.1.1',
        'port' => 8728, // API port
        'credentials' => [
            'username' => 'admin',
            'password' => '' // Set during implementation
        ],
        'settings' => [
            'timeout' => 30,
            'ssl' => false,
            'debug' => false
        ]
    ],

    // Predictive Analytics Configuration
    'analytics' => [
        'enabled' => false, // Enable only after testing
        'data_retention' => [
            'hourly' => 168,  // 7 days
            'daily' => 90,    // 90 days
            'monthly' => 12   // 1 year
        ],
        'prediction_window' => [
            'short_term' => 24,   // 24 hours
            'medium_term' => 168, // 1 week
            'long_term' => 720    // 30 days
        ],
        'threshold_alerts' => [
            'cpu' => 80,      // 80% utilization
            'memory' => 80,   // 80% utilization
            'bandwidth' => 90 // 90% of allocated
        ]
    ]
];

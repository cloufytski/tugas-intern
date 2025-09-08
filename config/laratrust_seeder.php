<?php

return [
    /**
     * Control if the seeder should create a user per role while seeding the data.
     */
    'create_users' => true, // TODO: change to false if deploy

    /**
     * Control if all the laratrust tables should be truncated before running the seeder.
     */
    'truncate_tables' => true,

    'roles_structure' => [
        'super-admin' => [
            'users' => 'c,r,u,d',
            'developer' => 'u',

            'dashboard-sales' => 'r',
            'dashboard-production' => 'r',
            'dashboard-procurement' => 'r',

            'master-plant' => 'c,r,u,d',
            'master-material' => 'c,r,u,d',
            'master-order' => 'c,r,u,d',
            'master-customer' => 'c,r,u,d',
            'master-supplier' => 'c,r,u,d',
            'inventory-checkpoint' => 'c,r,u,d',
            'inventory-balance' => 'r,u',

            'inquiry' => 'c,r,u,d',
            'projection' => 'c,r,u,d',
            'order' => 'c,r,u,d',

            'mode' => 'c,r,u,d',
            'schedule' => 'c,r,u,d',
            'prodsum' => 'c,r,u,d',
            'procurement' => 'c,r,u,d',
            'tank-farm' => 'c,r,u,d',
        ],
        'admin' => [
            'users' => 'c,r,u,d',
            'master-plant' => 'c,r,u,d',
            'master-material' => 'c,r,u,d',
            'master-order' => 'c,r,u,d',
            'master-customer' => 'c,r,u,d',
            'master-supplier' => 'c,r,u,d',
            'inventory-checkpoint' => 'c,r,u,d',
            'inventory-balance' => 'r,u',
            'dashboard-sales' => 'r',
            'dashboard-production' => 'r',
            'dashboard-procurement' => 'r',
        ],
        'manager' => [
            'master-plant' => 'r',
            'master-material' => 'r',
            'master-order' => 'r',
            'master-customer' => 'r',
            'master-supplier' => 'r',
            'inventory-checkpoint' => 'r',
            'inventory-balance' => 'r',

            'dashboard-sales' => 'r',
            'dashboard-production' => 'r',
            'dashboard-procurement' => 'r',
            'procurement-price' => 'r',
        ],
        // NOTE: custom role as PSPA
        'sales-planner' => [
            'master-plant' => 'r',
            'master-material' => 'r',
            'master-order' => 'r',
            'master-customer' => 'r',
            'inventory-checkpoint' => 'r',
            'inventory-balance' => 'r,u',

            'dashboard-sales' => 'r',

            'inquiry' => 'c,r,u,d',
            'projection' => 'c,r,u,d',
            'order' => 'c,r,u,d',

        ],
        'production-planner' => [
            'master-plant' => 'r',
            'master-material' => 'r',
            'master-customer' => 'r',
            'master-supplier' => 'r',
            'inventory-checkpoint' => 'r',
            'inventory-balance' => 'r,u',

            'dashboard-production' => 'r',
            'dashboard-procurement' => 'r',

            'mode' => 'c,r,u,d',
            'schedule' => 'c,r,u,d',
            'prodsum' => 'c,r,u,d',
            'procurement' => 'c,r,u,d',

            'tank-farm' => 'c,r,u,d',
        ],
        'viewer' => [
            'dashboard-sales' => 'r',
            'dashboard-production' => 'r',
            'dashboard-procurement' => 'r',
        ],
        'material-procurement' => [
            'master-plant' => 'r',
            'master-material' => 'r',
            'master-supplier' => 'r',
            'dashboard-procurement' => 'r',
            'procurement' => 'c,r,u,d',
            'procurement-price' => 'c,r,u,d',
        ]
    ],

    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete',
    ],
];

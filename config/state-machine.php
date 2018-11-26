<?php

return [
    'registration' => [
        // class of your domain object
        'class' => App\Registration::class,

        // name of the graph (default is "default")
        'graph' => 'registration',

        // property of your object holding the actual state (default is "state")
        'property_path' => 'state',

        // list of all possible states
        'states' => [
            'new',
            'pending',
            'accepted',
            'verified',
            'rejected',
            'cancelled',
        ],

        // list of all possible transitions
        'transitions' => [
            'create' => [
                'from' => ['new'],
                'to' => 'pending',
            ],
            'approve' => [
                'from' => ['pending', 'rejected', 'cancelled'],
                'to' => 'accepted',
            ],
            'verify' => [
                'from' => ['accepted'],
                'to' => 'verified',
            ],
            'reject' => [
                'from' => ['pending'],
                'to' => 'rejected',
            ],
            'cancel' => [
                'from' => ['accepted', 'pending', 'rejected', 'new', 'verified'],
                'to' => 'cancelled',
            ],
        ],

        // list of all callbacks
        'callbacks' => [
            // will be called when testing a transition
            'guard' => [],

            // will be called before applying a transition
            'before' => [],

            // will be called after applying a transition
            'after' => [],
        ],
    ],
];

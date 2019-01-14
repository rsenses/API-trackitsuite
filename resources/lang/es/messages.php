<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Custom Language Lines
    |--------------------------------------------------------------------------
    |
    |
 */

    'not_found' => 'Inscripción no válida para este evento',
    'verified' => 'Inscripción verificada recientemente',
    'unauthorized' => 'No tiene acceso a esta sala',
    'duplicated' => 'Inscripción duplicada, el email usado ya está registrado en el evento',
    'verification_error' => 'Error verificando la inscripción, inténtalo de nuevo, por favor',
    'access_granted' => 'Acceso concedido',
    'inscription_error' => 'Error creando inscripción, inténtalo de nuevo, por favor',
    'inscription_saved' => 'Inscripción guardada correctamente',
    'sold_out' => 'Aforo completo',
    'no_product' => 'Ningún producto asignado',
    'inscription_removed' => 'Inscripción eliminada',
    'not_authorized' => 'Usuario no autorizado para realizar la petición',

    'subject' => [
        'registrations' => [
            'pending' => 'Inscripción a :product',
            'accepted' => 'Tu entrada a :product',
            'rejected' => 'Aforo completo en :product',
        ]
    ],
    'registration' => [
        'type' => [
            'attendee' => 'asistente',
            'guest' => 'invitado',
            'speaker' => 'speaker',
            'vip' => 'vip',
            'press' => 'prensa',
            'staff' => 'staff',
        ]
    ]
];

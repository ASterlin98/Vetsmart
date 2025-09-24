<?php
// app/config/routes.php
// Devuelve las rutas organizadas por método HTTP.
// Las claves son paths SIN el prefijo /vetsmart (ese prefijo lo gestiona public/index.php).

return [
    'GET' => [
        ''                      => 'HomeController@index',    // /vetsmart/
        'auth/login'            => 'AuthController@showLogin',// /vetsmart/auth/login
        'auth/forgot'           => 'AuthController@forgot',   // /vetsmart/auth/forgot
        'auth/reset'            => 'AuthController@reset',    // /vetsmart/auth/reset?token=...
        'auth/logout'           => 'AuthController@logout',

        // dashboards (ejemplo)
        'veterinario/dashboard' => 'VeterinarioController@dashboard',
        'recepcionista/dashboard'=> 'RecepcionController@dashboard',
        'cliente/dashboard'      => 'ClienteController@dashboard',
        // ... añade las que necesites
    ],

    'POST' => [
        'auth/login'            => 'AuthController@login',            // formulario de login
        'auth/sendResetLink'    => 'AuthController@sendResetLink',    // formulario forgot (envío email)
        'auth/updatePassword'   => 'AuthController@updatePassword',   // formulario reset (guardar nueva pass)
        // ... otras POST
    ],
];

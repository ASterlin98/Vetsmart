<?php
// app/config/routes.php
// Devuelve las rutas organizadas por método HTTP.
// Las claves son paths SIN el prefijo /vetsmart (ese prefijo lo gestiona public/index.php).

return [
    'GET' => [
        ''                       => 'HomeController@index',     // /vetsmart/
        'auth/login'             => 'AuthController@showLogin', // /vetsmart/auth/login
        'auth/forgot'            => 'AuthController@forgot',    // /vetsmart/auth/forgot
        'auth/reset'             => 'AuthController@reset',     // /vetsmart/auth/reset?token=...
        'auth/logout'            => 'AuthController@logout',

        // dashboards
        'veterinario/dashboard'  => 'VeterinarioController@dashboard',
        'recepcionista/dashboard'=> 'RecepcionController@dashboard',
        'cliente/dashboard'      => 'ClienteController@dashboard',
        'admin/dashboard'        => 'AdminController@dashboard',

        // ================= EMPLEADOS (Admin) =================
// dentro del array 'GET' =>
'admin/empleados'                     => 'AdminController@empleadosIndex',
'admin/empleados/crear'               => 'AdminController@crearEmpleado',
'admin/empleados/{id}/editar'         => 'AdminController@editarEmpleado',
'admin/empleados/{id}/eliminar'       => 'AdminController@eliminarEmpleado',

    ],

    'POST' => [
        'auth/login'             => 'AuthController@login',
        'auth/sendResetLink'     => 'AuthController@sendResetLink',
        'auth/updatePassword'    => 'AuthController@updatePassword',

        // ================= EMPLEADOS (Admin) =================
'admin/empleados/guardar'             => 'AdminController@guardarEmpleado',
'admin/empleados/{id}/actualizar'     => 'AdminController@actualizarEmpleado',

    ],
];

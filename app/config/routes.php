<?php
// app/config/routes.php

return [
    'GET' => [
        ''                       => 'HomeController@index',
        'auth/login'             => 'AuthController@showLogin',
        'auth/forgot'            => 'AuthController@forgot',
        'auth/reset'             => 'AuthController@reset',
        'auth/logout'            => 'AuthController@logout',

        // dashboards
        'veterinario/dashboard'  => 'VeterinarioController@dashboard',
        'recepcionista/dashboard'=> 'RecepcionController@dashboard',
        'cliente/dashboard'      => 'ClienteController@dashboard',
        'admin/dashboard'        => 'AdminController@dashboard',

        // ================= EMPLEADOS (Admin) =================
        'admin/empleados'                     => 'AdminController@empleadosIndex',
        'admin/empleados/crear'               => 'AdminController@crearEmpleado',
        'admin/empleados/{id}/editar'         => 'AdminController@editarEmpleado',
        'admin/empleados/{id}/eliminar'       => 'AdminController@eliminarEmpleado',

        'admin/horarios'                      => 'AdminController@horariosIndex',
        'admin/horarios/{id}/editarSemana'    => 'AdminController@editarHorarioSemana',
        'admin/horarios/{id}/editarTurno'     => 'AdminController@editarTurno',
        'admin/horarios/{id}/editarSolicitud' => 'AdminController@editarSolicitud',
        'admin/horarios/{id}/eliminarSemana'  => 'AdminController@eliminarSemana',
        'admin/horarios/{id}/eliminarTurno'   => 'AdminController@eliminarTurno',
        'admin/horarios/{id}/eliminarSolicitud'=> 'AdminController@eliminarSolicitud',

        // ================= CITAS (Veterinario) =================
        'veterinario/mis-citas'     => 'VeterinarioController@misCitas',
        'veterinario/citas/listar'  => 'VeterinarioController@listarCitasJson',
    ],

    'POST' => [
        'auth/login'             => 'AuthController@login',
        'auth/sendResetLink'     => 'AuthController@sendResetLink',
        'auth/updatePassword'    => 'AuthController@updatePassword',

        // ================= EMPLEADOS (Admin) =================
        'admin/empleados/guardar'             => 'AdminController@guardarEmpleado',
        'admin/empleados/{id}/actualizar'     => 'AdminController@actualizarEmpleado',

        'admin/horarios/guardar-semana'       => 'AdminController@guardarHorarioSemana',
        'admin/horarios/guardar-turno'        => 'AdminController@guardarTurno',
        'admin/horarios/guardar-solicitud'    => 'AdminController@guardarSolicitud',
        'admin/horarios/{id}/actualizarSemana'=> 'AdminController@actualizarSemana',
        'admin/horarios/{id}/actualizarTurno' => 'AdminController@actualizarTurno',
        'admin/horarios/{id}/actualizarSolicitud'=> 'AdminController@actualizarSolicitud',
        'admin/clientes/{cliente_id}/mascotas/{id}/actualizar' => 'AdminController@actualizarMascota',

        // ================= CITAS (Veterinario) =================
        'veterinario/citas/guardar'    => 'VeterinarioController@guardarCita',
        'veterinario/citas/actualizar' => 'VeterinarioController@actualizarCita',
        'veterinario/citas/eliminar'   => 'VeterinarioController@eliminarCitaAjax',
    ],
];

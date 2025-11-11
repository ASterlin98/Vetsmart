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
        'recepcionista/dashboard'=> 'RecepcionistaController@dashboard',
        'cliente/dashboard'      => 'ClienteController@dashboard',
        'cliente/perfil'         => 'ClienteController@perfil',
        'cliente/mascotas'       => 'ClienteController@mascotas',
        'cliente/citas'          => 'ClienteController@citas',
        'cliente/citas/agendar'  => 'ClienteController@agendar',
        'cliente/historial'      => 'ClienteController@historial',
        'cliente/historial/exportar' => 'ClienteController@historialExportar',
        'cliente/pagos'          => 'ClienteController@pagos',
        'cliente/reportes'       => 'ClienteController@reportes',
        'cliente/reportes/pdf'   => 'ClienteController@reportesPdf',
        // Reportes de cliente (PDF)
        'reportes/cliente/{id}/pdf' => 'ReportesController@clientePdf',
        'reportes/cliente/{id}/pdf/preview' => 'ReportesController@clientePdfPreview',
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

        // ================= SUPER ADMIN =================
        'super_admin/dashboard' => 'SuperAdminController@dashboard',
        'super_admin/usuarios'        => 'SuperAdminController@usuarios',
        'super_admin/usuarios/crear'  => 'SuperAdminController@crearUsuario',
        'super_admin/usuarios/{id}/editar' => 'SuperAdminController@editarUsuario',
        'super_admin/usuarios/{id}/eliminar' => 'SuperAdminController@eliminarUsuario',
        'super_admin/estadisticas'    => 'SuperAdminController@estadisticas',
        'super_admin/configuracion'   => 'SuperAdminController@configuracion',
        'super_admin/auditoria'       => 'SuperAdminController@auditoria',
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

        // ================= SUPER ADMIN =================
        'super_admin/usuarios/guardar' => 'SuperAdminController@guardarUsuario',
        'super_admin/usuarios/{id}/actualizar' => 'SuperAdminController@actualizarUsuario',
        'super_admin/configuracion/guardar' => 'SuperAdminController@guardarConfiguracion',
        // Cliente
        'cliente/perfil/actualizar' => 'ClienteController@actualizarPerfil',
        'cliente/mascotas/guardar'  => 'ClienteController@guardarMascota',
        'cliente/mascotas/editar'   => 'ClienteController@editarMascota',
        'cliente/mascotas/eliminar' => 'ClienteController@eliminarMascota',
        'cliente/citas/guardar'     => 'ClienteController@guardarCita',
        'cliente/citas/reagendar'   => 'ClienteController@reagendar',
    ],
];

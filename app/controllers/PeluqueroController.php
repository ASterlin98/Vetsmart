<?php

class PeluqueroController extends Controller
{
    public function __construct(PDO $db)
    {
        parent::__construct($db);
    }

    // Dashboard principal
    public function dashboard()
    {
        $this->render('peluquero/dashboard', []);
    }

    // Mi Calendario
    public function calendario()
    {
        // Aquí luego puedes traer las citas asignadas desde el modelo
        $citas = []; 
        $this->render('peluquero/calendario', ['citas' => $citas]);
    }

    // Gestión de citas
    public function citas()
    {
        // Consultar citas de peluquería asignadas
        $citas = []; 
        $this->render('peluquero/citas', ['citas' => $citas]);
    }

    // Fichas de Mascotas
    public function mascotas()
    {
        // Consultar fichas de mascotas
        $mascotas = [];
        $this->render('peluquero/mascotas', ['mascotas' => $mascotas]);
    }

    // Inventario de productos
    public function inventario()
    {
        // Consultar inventario de productos de estética
        $productos = [];
        $this->render('peluquero/inventario', ['productos' => $productos]);
    }
}

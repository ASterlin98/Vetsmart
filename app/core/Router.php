<?php
// app/core/Router.php

class Router
{
    protected $basePath;
    protected $routes;

    public function __construct(string $basePath = '/vetsmart')
    {
        $this->basePath = $basePath;
        $routesFile = __DIR__ . '/../config/routes.php';
        if (!file_exists($routesFile)) {
            throw new Exception("routes.php no encontrado en app/config");
        }
        $this->routes = require $routesFile;
    }

    protected function currentPath(): string
    {
        // obtiene la ruta sin query string
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // quitar basePath si existe
        if ($this->basePath !== '' && strpos($uri, $this->basePath) === 0) {
            $uri = substr($uri, strlen($this->basePath));
        }

        $uri = trim($uri, '/');
        return $uri; // ejemplo: "auth/forgot" o "" (raíz)
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = $this->currentPath();

        $routesForMethod = $this->routes[$method] ?? [];
        // Intenta ruta exacta
        if (isset($routesForMethod[$path])) {
            $handler = $routesForMethod[$path];
            $this->callHandler($handler);
            return;
        }

        // Si no coincide exactamente, intenta rutas que no requieren path (ej. auth/reset?token=...)
        if ($method === 'GET' && isset($routesForMethod['auth/reset']) && strpos($path, 'auth/reset') === 0) {
            $this->callHandler($routesForMethod['auth/reset']);
            return;
        }

        // No encontrada
        http_response_code(404);
        echo "Página no encontrada. <a href='{$this->basePath}/auth/login'>Ir a login</a>";
    }

    protected function callHandler(string $handler): void
    {
        // handler formato Controller@method
        if (strpos($handler, '@') === false) {
            throw new Exception("Handler inválido: {$handler}");
        }

        [$controllerName, $methodName] = explode('@', $handler, 2);
        $controllerFile = __DIR__ . "/../controllers/{$controllerName}.php";

        if (!file_exists($controllerFile)) {
            http_response_code(500);
            echo "Controlador no encontrado: {$controllerName}";
            return;
        }

        require_once $controllerFile;

        if (!class_exists($controllerName)) {
            http_response_code(500);
            echo "Clase controlador no encontrada: {$controllerName}";
            return;
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $methodName)) {
            http_response_code(500);
            echo "Método no encontrado: {$controllerName}::{$methodName}";
            return;
        }

        // Llama al método (no pasamos params; si necesitas, puedes modificar)
        $controller->{$methodName}();
    }
}

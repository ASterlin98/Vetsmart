<?php
// app/core/Router.php

declare(strict_types=1);

class Router
{
    protected string $basePath;
    protected array $routes;
    protected $pdo;

    public function __construct(string $basePath = '/vetsmart', $pdo = null)
    {
        $this->basePath = $basePath;
        $this->pdo = $pdo;

        $routesFile = __DIR__ . '/../config/routes.php';
        if (!file_exists($routesFile)) {
            throw new Exception("routes.php no encontrado en app/config");
        }
        $this->routes = require $routesFile;
        if (!is_array($this->routes)) {
            throw new Exception("routes.php debe devolver un array");
        }
    }

    protected function currentPath(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
        if ($this->basePath !== '' && strpos($uri, $this->basePath) === 0) {
            $uri = substr($uri, strlen($this->basePath));
        }
        $uri = trim($uri, '/');
        return $uri; // ej "admin/empleados/5/editar"
    }

    /**
     * Despacha la ruta actual.
     * @param bool $emitNotFound si es false devuelve false en vez de imprimir 404.
     * @return bool true si se despachó alguna ruta; false en caso contrario.
     */
    public function dispatch(bool $emitNotFound = true): bool
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = $this->currentPath();

        // Middleware CSRF básico para POST: si el token viene presente debe ser válido.
        if ($method === 'POST' && class_exists('CSRF')) {
            $token = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
            if ($token !== null && !CSRF::validate($token)) {
                http_response_code(419);
                echo "CSRF token inválido";
                return true;
            }
        }

        $routesForMethod = $this->routes[$method] ?? [];

        // 1) Try exact match first
        if (isset($routesForMethod[$path])) {
            $this->callHandler($routesForMethod[$path], []);
            return true;
        }

        // 2) Try pattern match (support routes with {param})
        foreach ($routesForMethod as $routePattern => $handler) {
            $regex = $this->convertRouteToRegex($routePattern);
            if (preg_match($regex, $path, $matches)) {
                // collect named params
                $params = [];
                foreach ($matches as $k => $v) {
                    if (!is_int($k)) $params[$k] = $v;
                }
                $this->callHandler($handler, $params);
                return true;
            }
        }

        // 3) fallback for auth/reset?token=...
        if ($method === 'GET' && isset($routesForMethod['auth/reset']) && strpos($path, 'auth/reset') === 0) {
            $this->callHandler($routesForMethod['auth/reset'], []);
            return true;
        }

        if ($emitNotFound) {
            http_response_code(404);
            echo "Página no encontrada. <a href='{$this->basePath}/auth/login'>Ir a login</a>";
        }
        return false;
    }

    protected function convertRouteToRegex(string $route): string
    {
        // route like "admin/empleados/{id}/editar" -> regex with named capture
        $escaped = preg_quote(trim($route, '/'), '#');
        // revert escaping for {param} so we can replace it
        $regex = preg_replace_callback('#\\\\\{([a-zA-Z0-9_]+)\\\\\}#', function($m){
            $name = $m[1];
            return '(?P<' . $name . '>[^/]+)';
        }, $escaped);
        return '#^' . $regex . '$#';
    }

    /**
     * $handler: "ControllerName@method"
     * $params: associative array of route params
     */
    protected function callHandler(string $handler, array $params = []): void
    {
        if (strpos($handler, '@') === false) {
            http_response_code(500);
            echo "Handler inválido: {$handler}";
            return;
        }

        [$controllerName, $methodName] = explode('@', $handler, 2);
        $controllerFile = __DIR__ . "/../controllers/{$controllerName}.php";

        if (!file_exists($controllerFile)) {
            http_response_code(500);
            echo "Controlador no encontrado: {$controllerName} (archivo faltante: {$controllerFile})";
            return;
        }

        require_once $controllerFile;

        if (!class_exists($controllerName)) {
            http_response_code(500);
            echo "Clase controlador no encontrada: {$controllerName} (revisa el nombre de la clase dentro del archivo).";
            return;
        }

        // Reflection para instanciar pasando $pdo si el constructor lo necesita
        $ref = new ReflectionClass($controllerName);
        $constructor = $ref->getConstructor();
        $instance = null;

        if ($constructor && $constructor->getNumberOfParameters() > 0) {
            // Preparar argumentos: si hay $this->pdo lo pasamos como primer arg
            $ctorParams = [];
            foreach ($constructor->getParameters() as $i => $p) {
                $ptype = $p->getType();
                // si existe pdo, se lo damos al primer parámetro
                if ($this->pdo !== null) {
                    $ctorParams[] = $this->pdo;
                    // si el constructor necesita más params podrías extender aquí
                    break;
                } else {
                    // si no tenemos pdo y el parámetro no es opcional, intentar instanciar sin args y fallará
                    break;
                }
            }
            try {
                $instance = $ref->newInstanceArgs($ctorParams);
            } catch (Throwable $e) {
                http_response_code(500);
                echo "Error instanciando controlador {$controllerName}: " . htmlspecialchars($e->getMessage());
                return;
            }
        } else {
            $instance = $ref->newInstance();
        }

        if (!method_exists($instance, $methodName)) {
            http_response_code(500);
            echo "Método no encontrado: {$controllerName}::{$methodName}";
            return;
        }

        // Llamar al método pasando params (si acepta parámetros posicionales, los pasamos por orden)
        try {
            // Si el método espera parámetros posicionales, pasarlos en orden:
            $methodRef = new ReflectionMethod($instance, $methodName);
            $callArgs = [];
            foreach ($methodRef->getParameters() as $paramRef) {
                $pname = $paramRef->getName();
                if (isset($params[$pname])) {
                    $callArgs[] = $params[$pname];
                } elseif ($paramRef->isOptional()) {
                    $callArgs[] = $paramRef->getDefaultValue();
                } else {
                    // no tenemos un valor para este parámetro -> intentar pasar null
                    $callArgs[] = null;
                }
            }
            $methodRef->invokeArgs($instance, $callArgs);
        } catch (Throwable $ex) {
            http_response_code(500);
            echo "Error al ejecutar handler: " . htmlspecialchars($ex->getMessage());
            return;
        }
    }
}

<?php
class Router {
    protected $routes = [];

    public function add($route, $params = []) {
        $this->routes[$route] = $params;
    }

    public function dispatch($url) {
        $route = trim($url, '/');
        if (isset($this->routes[$route])) {
            $controller = 'App\\Controllers\\' . $this->routes[$route]['controller'] . 'Controller';
            $action = $this->routes[$route]['action'];

            $modelClass = 'App\\Models\\' . $this->routes[$route]['controller'] . 'Model';
            $pdo = require __DIR__ . '/../config/database.php';
            $model = new $modelClass($pdo);

            $controllerInstance = new $controller($model);
            $controllerInstance->$action();
        } else {
            echo "Página não encontrada.";
        }
    }
}

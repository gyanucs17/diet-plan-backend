<?php 

namespace App\Lib;

class Router {
    private $routes = [];

    public function addRoute($method, $path, $handler) {
        $this->routes[] = compact('method', 'path', 'handler');
    }

    public function group($prefix, callable $callback) {
        $originalRoutes = $this->routes;
        

        // Call the callback function to add routes
        $callback($this);
        
      
        $this->routes = array_map(function($route) use ($prefix) {
             $route['path'] = rtrim($prefix, '/') . '/' . ltrim($route['path'], '/'); // Ensures no double slashes
            return $route;
        }, $this->routes);
    }

    public function match($method, $uri) {
        foreach ($this->routes as $route) {
            // Convert the path to a regex pattern
            $pathArr = explode("/", $route['path']);
            $pathArr = array_reverse( $pathArr );
            if($pathArr[0] == "{id}" || $pathArr[0] == "{search}"){
                $route['path'] = '/'. $pathArr[2].'/'. $pathArr[1] . '/' . $pathArr[0];
            } else {
                $route['path'] = '/'. $pathArr[1] . '/' . $pathArr[0];
            }
            if($pathArr[0] == "{id}" && $pathArr[1] == "{search}"){
               $route['path'] = '/'. $pathArr[3] .'/'. $pathArr[2].'/'. $pathArr[1] . '/' . $pathArr[0];
            }
            $pattern = preg_replace('/{(\w+)}/', '(?P<$1>[^/]+)?', $route['path']);
            $pattern = str_replace('/{search}', '(/(?P<search>[^/]+))?', $pattern);
            if ($route['method'] === $method && preg_match("#^" . $pattern . "$#", $uri, $matches)) {
                return [
                    'handler' => $route['handler'],
                    'params' => $matches
                ];
            }
        }
        // Return null if no match is found
        return null;
    }
}


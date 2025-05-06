<?php

namespace EhxDirectorist\Http\Router;

use WP_REST_Server;
use WP_REST_Request;

class API_Router {
    private static $routes = [];

    /**
     * Register a GET route
     */
    public static function get($route, $callback) {
        self::add_route('GET', $route, $callback);
    }

    /**
     * Register a POST route
     */
    public static function post($route, $callback) {
        self::add_route('POST', $route, $callback);
    }

    /**
     * Register a PUT route
     */
    public static function put($route, $callback) {
        self::add_route('PUT', $route, $callback);
    }

    /**
     * Register a DELETE route
     */
    public static function delete($route, $callback) {
        self::add_route('DELETE', $route, $callback);
    }

    /**
     * Add route to list
     */
    private static function add_route($method, $route, $callback) {
        self::$routes[] = [
            'method' => $method,
            'route' => $route,
            'callback' => self::resolve_callback($callback)
        ];
    }

    /**
     * Resolve route callback
     */
    private static function resolve_callback($callback) {
        return function (WP_REST_Request $wpRequest) use ($callback) {
            [$class, $method] = $callback;
    
            $refMethod = new \ReflectionMethod($class, $method);
            $args = [];
    
            foreach ($refMethod->getParameters() as $param) {
                $type = $param->getType();
    
                if ($type && !$type->isBuiltin()) {
                    $typeName = $type->getName();
    
                    if (is_subclass_of($typeName, \EhxDirectorist\Http\Requests\RequestGuard::class)) {
                        $args[] = new $typeName($wpRequest); // Inject your custom request
                    } elseif ($typeName === \WP_REST_Request::class) {
                        $args[] = $wpRequest;
                    } else {
                        $args[] = null;
                    }
                } else {
                    $args[] = null;
                }
            }
    
            return call_user_func_array([$class, $method], $args);
        };
    }
    

    /**
     * Register all stored routes
     */
    public static function register_routes() {
        foreach (self::$routes as $route) {
            register_rest_route('easy-restaurant-manage/v1', $route['route'], [
                'methods'  => $route['method'],
                'callback' => $route['callback'],
                'permission_callback' => '__return_true'
            ]);
        }
    }
}

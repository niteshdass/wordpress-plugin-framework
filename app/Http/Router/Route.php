<?php

namespace EhxDirectorist\Http\Router;

use EhxDirectorist\Http\Controllers\CategoryController;

use WP_REST_Request;

class Route {
    public static function register() {
        API_Router::post('/postCategory', [CategoryController::class, 'storeCategory']);
        API_Router::get('/get-categories', [CategoryController::class, 'getCategories']);
        API_Router::get('/get-category/{id}', [CategoryController::class, 'getCategory']);
    } 
}

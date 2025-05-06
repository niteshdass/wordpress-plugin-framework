<?php
namespace EhxDirectorist\Http\Controllers;

use WP_REST_Request;
use EhxDirectorist\Http\Requests\StoreCategoryRequest;
use EhxDirectorist\Resources\CategoryResource;

class CategoryController {
    public static function storeCategory(StoreCategoryRequest $request) {
        $res = CategoryResource::store($request->validated());
        if(!$res) {
            return rest_ensure_response([
                'message' => 'Failed to create category'
            ], 500);
        }
        return rest_ensure_response([
            'message' => 'Category created successfully',
            'category_data' => $res
        ]);
    }
}

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

    public static function updateCategory(StoreCategoryRequest $request, $id) {
        $res = CategoryResource::update($request->validated(), $id);
        if(!$res) {
            return rest_ensure_response([
                'message' => 'Failed to update category'
            ], 500);
        }
        return rest_ensure_response([
            'message' => 'Category updated successfully',
            'category_data' => $res
        ]);
    }

    public static function deleteCategory($id) {
        $res = CategoryResource::delete($id);
        if(!$res) {
            return rest_ensure_response([
                'message' => 'Failed to delete category'
            ], 500);
        }
        return rest_ensure_response([
            'message' => 'Category deleted successfully'
        ]);
    }

    public static function getCategory($id) {
        $res = CategoryResource::get($id);
        if(!$res) {
            return rest_ensure_response([
                'message' => 'Failed to get category'
            ], 500);
        }
        return rest_ensure_response([
            'message' => 'Category retrieved successfully',
            'category_data' => $res
        ]);
    }

    public static function getAllCategories() {
        $res = CategoryResource::getAll();
        if(!$res) {
            return rest_ensure_response([
                'message' => 'Failed to get categories'
            ], 500);
        }
        return rest_ensure_response([
            'message' => 'Categories retrieved successfully',
            'categories_data' => $res
        ]);
    }
}

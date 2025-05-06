<?php
namespace EhxDirectorist\Resources;
use EhxDirectorist\Models\Category;

class CategoryResource {
    public static function store($category) {
        return Category::store($category);
    }
}
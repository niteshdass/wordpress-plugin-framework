<?php
namespace EhxDirectorist\Resources;
use EhxDirectorist\Models\Category;

class CategoryResource {
    public static function store($category) {
        return Category::create($category);
    }

    public static function update($category, $id) {
        return (new Category())->where('id', $id)->update($category);
    }

    public static function delete($id) {
        return (new Category())->where('id', $id)->delete();
    }

    public static function get($id) {
        return Category::find($id);
    }

    public static function getAll() {
        $categories = (new Category)->where('id', '=', 3)->all();
        return $categories;
    }
}
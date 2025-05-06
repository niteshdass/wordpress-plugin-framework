<?php
namespace EhxDirectorist\Resources;
use EhxDirectorist\Models\Category;

class CategoryResource {
    public static function store($category) {
        $cat = self::getAll();
        $categories = Category::all();
        var_dump($categories);
        return Category::create($category);
    }

    public static function update($category, $id) {
        return Category::where('id', $id)->update($category);
    }

    public static function delete($id) {
        return Category::where('id', $id)->delete();
    }

    public static function get($id) {
        return Category::find($id);
    }

    public static function getAll() {
        return Category::all();
    }
}
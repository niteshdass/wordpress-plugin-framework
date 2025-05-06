<?php
namespace EhxDirectorist\Resources;
use EhxDirectorist\Models\Category;

class CategoryResource {
    public static function store($category) {
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
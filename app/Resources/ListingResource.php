<?php
namespace EhxDirectorist\Resources;
use EhxDirectorist\Models\Listing;

class ListingResource {
    public static function store($category) {
        return Listing::create($category);
    }

    public static function update($category, $id) {
        return (new Listing())->where('id', $id)->update($category);
    }

    public static function delete($id) {
        return (new Listing())->where('id', $id)->delete();
    }

    public static function get($id) {
        return Listing::find($id);
    }

    public static function getAll() {
        $categories = (new Listing)->where('id', '=', 3)->all();
        return $categories;
    }
}
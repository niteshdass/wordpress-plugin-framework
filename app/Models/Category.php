<?php

namespace EhxDirectorist\Models;

class Category extends Model {
    protected $model = 'ehxd_menu_categories';
    
    public static function store($category) {
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'ehxd_menu_categories', $category);
        return $wpdb->insert_id;
    }
}
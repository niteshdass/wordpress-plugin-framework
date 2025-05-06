<?php
namespace EhxDirectorist\Database;
use EhxDirectorist\Database\Migrations\CreateMenuCategoriesTable;

class Migrator {
    protected $migrations = [
        CreateMenuCategoriesTable::class,
    ];

    public function migrate() {
        
        foreach ($this->migrations as $migration) {
            if (class_exists($migration) && method_exists($migration, 'up')) {
                call_user_func([$migration, 'up']);
            }
        }
    }
}
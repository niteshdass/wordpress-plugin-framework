<?php

namespace EhxDirectorist\Core;
use EhxDirectorist\Hooks\Actions;

class Plugin {
    private static $instance = null;

    public function __construct() {
        $this->init_hooks();
    }

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function init_hooks() {
        // Admin menu
        new Actions();
    }
}

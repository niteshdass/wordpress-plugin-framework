<?php
/**
 * Plugin Name: Ehx Directorist
 * Plugin URI: https://example.com/ehx-irectorist
 * Description: Description of the plugin goes here.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * Text Domain: ehx-directorist
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('EHX_DIRECTORIST_VERSION')) {
    define('EHX_DIRECTORIST_VERSION', '1.0.0');
}

if (!defined('EHX_DIRECTORIST_PLUGIN_DIR')) {
    define('EHX_DIRECTORIST_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

if (!defined('EHX_DIRECTORIST_PLUGIN_URL')) {
    define('EHX_DIRECTORIST_PLUGIN_URL', plugin_dir_url(__FILE__));
}

// Load API routes
use EhxDirectorist\Http\Router\API_Router;
use EhxDirectorist\Http\Router\Route;
// Autoloader
require_once EHX_DIRECTORIST_PLUGIN_DIR . 'vendor/autoload.php';

// Register API routes on REST API initialization
add_action('rest_api_init', function () {
    Route::register();
    API_Router::register_routes();
});

// Initialize the plugin
function EHX_DIRECTORIST_init() {
    \EhxDirectorist\Core\Plugin::instance();
}

// Hook into WordPress init
add_action('plugins_loaded', 'EHX_DIRECTORIST_init');

add_action( 'init', function() {
    wp_enqueue_script(
        'ehx-google-maps-api',
        'https://maps.googleapis.com/maps/api/js?key=AIzaSyAdBAaTcZW8-4MCuVwzc7mcGS0OasoplgU&libraries=places',
        [],
        null,
        true
    );
});

// Activation hook
register_activation_hook(__FILE__, function() {
    require_once EHX_DIRECTORIST_PLUGIN_DIR . 'app/Database/Migrator.php';
    $installer = new \EhxDirectorist\Database\Migrator();
    $installer->migrate();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    // Clean up if needed
});



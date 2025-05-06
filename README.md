
# Ehx Directorist – WordPress Plugin Development Framework

A modern, Laravel-inspired framework to build modular and maintainable WordPress plugins. Designed for developers who want structure, separation of concerns, and a full-featured developer experience.

## 📦 Features

- MVC architecture
- Laravel-style routing and validation
- Database migrations and seeders
- Custom post types with models/resources
- Clean admin menu handler
- REST API support out of the box
- Vue 3 + SCSS based frontend architecture

## 📁 Folder Structure

```
ehx-directorist/
├── app/
│   ├── Core/
│   ├── Database/
│   │   ├── Migrations/         # Migration classes
│   │   ├── Seeders/            # Seeder classes
│   │   └── Migrator.php        # Migration runner
│   ├── Hooks/
│   │   ├── CLI/
│   │   └── Handler/AdminMenuHandler.php
│   ├── Http/
│   │   ├── Controllers/        # API controllers
│   │   ├── Requests/           # Form requests (validation)
│   │   └── Router/             # Laravel-style route handling
│   ├── Models/                 # Custom models (e.g., Category, Courier)
│   └── Resources/              # Data transformers for responses
│
├── assets/                     # Compiled assets (dist)
├── node_modules/               # Node dependencies
├── resources/
│   ├── js/
│   │   ├── admin/              # Admin panel-specific code
│   │   ├── components/         # Vue components
│   │   ├── modules/            # Feature modules
│   │   ├── app.js              # Entry point
│   │   └── App.vue             # Root Vue component
│   └── scss/                   # SCSS styles
├── routes/                     # Route files (e.g., api.php)
├── vendor/                     # Composer dependencies
├── composer.json
└── ehx-directorist.php         # Plugin bootstrap
```

## 🚀 Getting Started

### 1. Clone the Repo

```bash
git clone https://github.com/yourusername/ehx-directorist.git wp-content/plugins/ehx-directorist
cd wp-content/plugins/ehx-directorist
composer install
npm install
```

### 2. Build Frontend Assets

```bash
npm run dev   # For development
npm run build # For production
```

### 3. Activate Plugin

Activate `Ehx Directorist` from your WordPress admin dashboard.

## ⚙️ Define API Routes

In `routes/api.php`:

```php
API_Router::get('/categories', [CategoryController::class, 'index']);
API_Router::post('/categories', [CategoryController::class, 'store']);
```

## ✅ Form Request Validation

```php
class StoreCategoryRequest extends BaseRequest {
    public function rules(): array {
        return [
            'name' => 'required|string|max:255',
        ];
    }
}
```

In controller:

```php
public static function store(StoreCategoryRequest $request) {
    $validated = $request->validated();
    // process data
}
```

## 🛠 Migrations and Seeders

Migration: `app/Database/Migrations/CreateMenuCategoriesTable.php`

Seeder: `app/Database/Seeders/YourSeeder.php`

Run migrations:

```php
(new \EhxDirectorist\Database\Migrator())->run();
```

## 🖥 Admin Menu

Add admin menu via:

```php
new AdminMenuHandler();
```

## 🎨 Frontend

- Vue 3 single-file components
- SCSS for styling
- Laravel Mix/Vite supported build process

## 📌 License

[MIT License](LICENSE)

---

### 📬 Contributions

PRs are welcome! Please open an issue first to discuss significant changes or ideas.

---

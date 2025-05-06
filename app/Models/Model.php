<?php

namespace EhxDirectorist\Models;

/**
 * Base Model class that all models will extend
 */
abstract class Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Create a new model instance.
     *
     * @param array $attributes
     * @return void
     */
    public function __construct(array $attributes = []) {
        $this->fill($attributes);
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @param array $attributes
     * @return $this
     */
    public function fill(array $attributes) {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->attributes[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * Get an attribute from the model.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key) {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Set a given attribute on the model.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value) {
        if (in_array($key, $this->fillable)) {
            $this->attributes[$key] = $value;
        }
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable() {
        if (isset($this->table)) {
            return $this->table;
        }

        // Convert CamelCase class name to snake_case table name
        $class = get_class($this);
        $parts = explode('\\', $class);
        $className = end($parts);
        
        // Convert CamelCase to snake_case and pluralize
        $tableName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className)) . 's';
        
        return $tableName;
    }

    /**
     * Find a model by its primary key.
     *
     * @param mixed $id
     * @return static|null
     */
    public static function find($id) {
        $instance = new static;
        global $wpdb;
        
        $tableName = $wpdb->prefix . $instance->getTable();
        $primaryKey = $instance->primaryKey;
        
        $query = $wpdb->prepare("SELECT * FROM $tableName WHERE $primaryKey = %s LIMIT 1", $id);
        $result = $wpdb->get_row($query, ARRAY_A);
        
        if (!$result) {
            return null;
        }
        
        return new static($result);
    }

    /**
     * Get all records from the database.
     *
     * @return array
     */
    public static function all() {
        $instance = new static;
        global $wpdb;
        
        $tableName = $wpdb->prefix . $instance->getTable();
        
        $results = $wpdb->get_results("SELECT * FROM $tableName", ARRAY_A);
        
        $models = [];
        foreach ($results as $result) {
            $models[] = new static($result);
        }
        
        return $models;
    }

    /**
     * Get records based on where conditions.
     *
     * @param array $conditions
     * @return array
     */
    public static function where($conditions) {
        $instance = new static;
        global $wpdb;
        
        $tableName = $wpdb->prefix . $instance->getTable();
        
        $whereClause = [];
        $whereValues = [];
        
        foreach ($conditions as $column => $value) {
            $whereClause[] = "$column = %s";
            $whereValues[] = $value;
        }
        
        $whereString = implode(' AND ', $whereClause);
        
        $query = $wpdb->prepare("SELECT * FROM $tableName WHERE $whereString", $whereValues);
        $results = $wpdb->get_results($query, ARRAY_A);
        
        $models = [];
        foreach ($results as $result) {
            $models[] = new static($result);
        }
        
        return $models;
    }

    /**
     * Save the model to the database.
     *
     * @return bool
     */
    public function save() {
        global $wpdb;
        
        $tableName = $wpdb->prefix . $this->getTable();
        $primaryKey = $this->primaryKey;
        
        // Check if record exists
        if (isset($this->attributes[$primaryKey])) {
            // Update existing record
            $result = $wpdb->update(
                $tableName,
                $this->attributes,
                [$primaryKey => $this->attributes[$primaryKey]]
            );
        } else {
            // Insert new record
            $result = $wpdb->insert(
                $tableName,
                $this->attributes
            );
            
            if ($result) {
                $this->attributes[$primaryKey] = $wpdb->insert_id;
            }
        }
        
        return $result !== false;
    }

    /**
     * Delete the model from the database.
     *
     * @return bool
     */
    public function delete() {
        global $wpdb;
        
        $tableName = $wpdb->prefix . $this->getTable();
        $primaryKey = $this->primaryKey;
        
        if (!isset($this->attributes[$primaryKey])) {
            return false;
        }
        
        $result = $wpdb->delete(
            $tableName,
            [$primaryKey => $this->attributes[$primaryKey]]
        );
        
        return $result !== false;
    }

    /**
     * Create a new model in the database.
     *
     * @param array $attributes
     * @return static
     */
    public static function create(array $attributes) {
        $model = new static($attributes);
        $model->save();
        
        return $model;
    }
}

/**
 * DB Schema creator for models
 */
class Model_Schema {
    /**
     * Create a database table for a model.
     *
     * @param string $modelClass
     * @param callable $callback
     * @return void
     */
    public static function create($modelClass, callable $callback) {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        // Create instance of the model to get table name
        $model = new $modelClass();
        $tableName = $wpdb->prefix . $model->getTable();
        
        // Create blueprint object
        $blueprint = new Model_Blueprint($tableName);
        
        // Call the callback to define columns
        call_user_func($callback, $blueprint);
        
        // Generate the SQL
        $sql = $blueprint->toSql() . $charset_collate . ";";
        
        // Execute the SQL using dbDelta
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

/**
 * Blueprint class for defining table schema
 */
class Model_Blueprint {
    /**
     * The table name.
     *
     * @var string
     */
    protected $table;
    
    /**
     * The columns of the table.
     *
     * @var array
     */
    protected $columns = [];
    
    /**
     * The primary key of the table.
     *
     * @var string|null
     */
    protected $primaryKey = null;
    
    /**
     * Create a new blueprint instance.
     *
     * @param string $table
     * @return void
     */
    public function __construct($table) {
        $this->table = $table;
    }
    
    /**
     * Add an ID column to the table.
     *
     * @return $this
     */
    public function id() {
        $this->columns[] = "`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT";
        $this->primaryKey = 'id';
        return $this;
    }
    
    /**
     * Add an integer column to the table.
     *
     * @param string $name
     * @param bool $unsigned
     * @param bool $nullable
     * @return $this
     */
    public function integer($name, $unsigned = false, $nullable = false) {
        $column = "`$name` int" . ($unsigned ? ' unsigned' : '');
        $column .= ($nullable ? ' DEFAULT NULL' : ' NOT NULL');
        $this->columns[] = $column;
        return $this;
    }
    
    /**
     * Add a varchar column to the table.
     *
     * @param string $name
     * @param int $length
     * @param bool $nullable
     * @return $this
     */
    public function string($name, $length = 255, $nullable = false) {
        $column = "`$name` varchar($length)";
        $column .= ($nullable ? ' DEFAULT NULL' : ' NOT NULL');
        $this->columns[] = $column;
        return $this;
    }
    
    /**
     * Add a text column to the table.
     *
     * @param string $name
     * @param bool $nullable
     * @return $this
     */
    public function text($name, $nullable = false) {
        $column = "`$name` text";
        $column .= ($nullable ? ' DEFAULT NULL' : ' NOT NULL');
        $this->columns[] = $column;
        return $this;
    }
    
    /**
     * Add a datetime column to the table.
     *
     * @param string $name
     * @param bool $nullable
     * @return $this
     */
    public function datetime($name, $nullable = false) {
        $column = "`$name` datetime";
        $column .= ($nullable ? ' DEFAULT NULL' : ' NOT NULL');
        $this->columns[] = $column;
        return $this;
    }
    
    /**
     * Add timestamp columns to the table.
     *
     * @return $this
     */
    public function timestamps() {
        $this->columns[] = "`created_at` datetime DEFAULT NULL";
        $this->columns[] = "`updated_at` datetime DEFAULT NULL";
        return $this;
    }
    
    /**
     * Convert the blueprint to SQL.
     *
     * @return string
     */
    public function toSql() {
        $sql = "CREATE TABLE {$this->table} (\n";
        $sql .= implode(",\n", $this->columns);
        
        if ($this->primaryKey) {
            $sql .= ",\nPRIMARY KEY (`{$this->primaryKey}`)";
        }
        
        $sql .= "\n)";
        
        return $sql;
    }
}

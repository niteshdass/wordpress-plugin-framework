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
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [];

    /**
     * The loaded relationships for the model.
     *
     * @var array
     */
    protected $relations = [];

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
     * Get an attribute or relation from the model.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key) {
        // First check if the attribute exists
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        // Then check if it's a loaded relation
        if (array_key_exists($key, $this->relations)) {
            return $this->relations[$key];
        }

        // Check if the key is a relationship method
        if (method_exists($this, $key)) {
            // Load the relationship and cache it
            $this->relations[$key] = $this->$key();
            return $this->relations[$key];
        }

        return null;
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
     * Begin querying the model with eager loading.
     *
     * @param array|string $relations
     * @return \EhxDirectorist\Models\Builder
     */
    public static function with($relations) {
        $instance = new static;
        $query = new Builder($instance);
        
        return $query->with(is_string($relations) ? func_get_args() : $relations);
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
        
        $model = new static($result);
        
        // Load default eager loaded relationships
        if (!empty($instance->with)) {
            $model->loadRelations($instance->with);
        }
        
        return $model;
    }

    /**
     * Get all records from the database.
     *
     * @return Collection
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
        
        $collection = new Collection($models);
        
        // Load default eager loaded relationships
        if (!empty($instance->with)) {
            $collection->loadRelations($instance->with);
        }
        
        return $collection;
    }

    /**
     * Get records based on where conditions.
     *
     * @param array $conditions
     * @return Collection
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
        
        $collection = new Collection($models);
        
        // Load default eager loaded relationships
        if (!empty($instance->with)) {
            $collection->loadRelations($instance->with);
        }
        
        return $collection;
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

    /**
     * Load a set of relationships.
     *
     * @param array $relations
     * @return $this
     */
    public function loadRelations(array $relations) {
        foreach ($relations as $relation) {
            if (method_exists($this, $relation)) {
                $this->relations[$relation] = $this->$relation();
            }
        }
        
        return $this;
    }

    /**
     * Define a one-to-one relationship.
     *
     * @param string $related Related model class name
     * @param string $foreignKey Foreign key on related model
     * @param string $localKey Local key on this model
     * @return mixed
     */
    public function hasOne($related, $foreignKey = null, $localKey = null) {
        $instance = new $related();
        
        // Determine foreign key if not provided
        if ($foreignKey === null) {
            $class = get_class($this);
            $parts = explode('\\', $class);
            $className = end($parts);
            $foreignKey = strtolower($className) . '_id';
        }
        
        // Determine local key if not provided
        if ($localKey === null) {
            $localKey = $this->primaryKey;
        }
        
        // Check if local key exists
        if (!isset($this->attributes[$localKey])) {
            return null;
        }
        
        return $related::where([$foreignKey => $this->attributes[$localKey]])->first();
    }

    /**
     * Define a one-to-many relationship.
     *
     * @param string $related Related model class name
     * @param string $foreignKey Foreign key on related models
     * @param string $localKey Local key on this model
     * @return Collection
     */
    public function hasMany($related, $foreignKey = null, $localKey = null) {
        $instance = new $related();
        
        // Determine foreign key if not provided
        if ($foreignKey === null) {
            $class = get_class($this);
            $parts = explode('\\', $class);
            $className = end($parts);
            $foreignKey = strtolower($className) . '_id';
        }
        
        // Determine local key if not provided
        if ($localKey === null) {
            $localKey = $this->primaryKey;
        }
        
        // Check if local key exists
        if (!isset($this->attributes[$localKey])) {
            return new Collection([]);
        }
        
        return $related::where([$foreignKey => $this->attributes[$localKey]]);
    }

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @param string $related Related model class name
     * @param string $foreignKey Foreign key on this model
     * @param string $ownerKey Owner key on the related model
     * @return mixed
     */
    public function belongsTo($related, $foreignKey = null, $ownerKey = null) {
        $instance = new $related();
        
        // Determine foreign key if not provided
        if ($foreignKey === null) {
            $class = $related;
            $parts = explode('\\', $class);
            $className = end($parts);
            $foreignKey = strtolower($className) . '_id';
        }
        
        // Determine owner key if not provided
        if ($ownerKey === null) {
            $ownerKey = $instance->primaryKey;
        }
        
        // Check if foreign key exists
        if (!isset($this->attributes[$foreignKey])) {
            return null;
        }
        
        return $related::find($this->attributes[$foreignKey]);
    }

    /**
     * Define a many-to-many relationship.
     *
     * @param string $related Related model class name
     * @param string $table Pivot table name
     * @param string $foreignPivotKey Foreign key on pivot table for this model
     * @param string $relatedPivotKey Related key on pivot table for related model
     * @param string $parentKey Primary key on this model
     * @param string $relatedKey Primary key on related model
     * @return Collection
     */
    public function belongsToMany($related, $table = null, $foreignPivotKey = null, $relatedPivotKey = null, $parentKey = null, $relatedKey = null) {
        $instance = new $related();
        
        // Determine pivot table name if not provided
        if ($table === null) {
            $thisClass = get_class($this);
            $thisParts = explode('\\', $thisClass);
            $thisClassName = end($thisParts);
            
            $relatedClass = $related;
            $relatedParts = explode('\\', $relatedClass);
            $relatedClassName = end($relatedParts);
            
            $models = [
                strtolower($thisClassName),
                strtolower($relatedClassName)
            ];
            sort($models); // Alphabetically sort for consistency
            $table = implode('_', $models);
        }
        
        // Determine foreign pivot key if not provided
        if ($foreignPivotKey === null) {
            $thisClass = get_class($this);
            $thisParts = explode('\\', $thisClass);
            $thisClassName = end($thisParts);
            $foreignPivotKey = strtolower($thisClassName) . '_id';
        }
        
        // Determine related pivot key if not provided
        if ($relatedPivotKey === null) {
            $relatedClass = $related;
            $relatedParts = explode('\\', $relatedClass);
            $relatedClassName = end($relatedParts);
            $relatedPivotKey = strtolower($relatedClassName) . '_id';
        }
        
        // Determine parent key if not provided
        if ($parentKey === null) {
            $parentKey = $this->primaryKey;
        }
        
        // Determine related key if not provided
        if ($relatedKey === null) {
            $relatedKey = $instance->primaryKey;
        }
        
        // Check if parent key exists
        if (!isset($this->attributes[$parentKey])) {
            return new Collection([]);
        }
        
        global $wpdb;
        
        // Get related model IDs from pivot table
        $tableName = $wpdb->prefix . $table;
        $query = $wpdb->prepare(
            "SELECT $relatedPivotKey FROM $tableName WHERE $foreignPivotKey = %s",
            $this->attributes[$parentKey]
        );
        $pivotResults = $wpdb->get_results($query, ARRAY_A);
        
        // If no pivot results, return empty array
        if (empty($pivotResults)) {
            return new Collection([]);
        }
        
        // Get related model IDs
        $relatedIds = array_column($pivotResults, $relatedPivotKey);
        
        // Get related models
        $relatedModels = [];
        foreach ($relatedIds as $id) {
            $model = $related::find($id);
            if ($model) {
                $relatedModels[] = $model;
            }
        }
        
        return new Collection($relatedModels);
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray() {
        $array = $this->attributes;
        
        // Add loaded relations to array
        foreach ($this->relations as $key => $value) {
            if ($value instanceof Collection) {
                $array[$key] = $value->toArray();
            } elseif ($value instanceof Model) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = $value;
            }
        }
        
        return $array;
    }
}

/**
 * Query Builder class for models
 */
class Builder {
    /**
     * The model being queried.
     *
     * @var \EhxDirectorist\Models\Model
     */
    protected $model;
    
    /**
     * The relationships that should be eager loaded.
     *
     * @var array
     */
    protected $eagerLoad = [];
    
    /**
     * Where conditions for the query.
     *
     * @var array
     */
    protected $wheres = [];
    
    /**
     * Create a new query builder instance.
     *
     * @param \EhxDirectorist\Models\Model $model
     * @return void
     */
    public function __construct(Model $model) {
        $this->model = $model;
    }
    
    /**
     * Set the relationships that should be eager loaded.
     *
     * @param mixed $relations
     * @return $this
     */
    public function with($relations) {
        $relations = is_array($relations) ? $relations : func_get_args();
        
        foreach ($relations as $relation) {
            $this->eagerLoad[] = $relation;
        }
        
        return $this;
    }
    
    /**
     * Add a where clause to the query.
     *
     * @param array|string $column
     * @param mixed $operator
     * @param mixed $value
     * @return $this
     */
    public function where($column, $operator = null, $value = null) {
        // Handle array of conditions
        if (is_array($column)) {
            foreach ($column as $key => $value) {
                $this->wheres[$key] = $value;
            }
            return $this;
        }
        
        // Handle single condition with operator and value
        if ($value !== null) {
            $this->wheres[$column] = $value;
        } else {
            $this->wheres[$column] = $operator;
        }
        
        return $this;
    }
    
    /**
     * Execute the query and get the first result.
     *
     * @return \EhxDirectorist\Models\Model|null
     */
    public function first() {
        $results = $this->get();
        return $results->first();
    }
    
    /**
     * Execute the query and get the results.
     *
     * @return \EhxDirectorist\Models\Collection
     */
    public function get() {
        $modelClass = get_class($this->model);
        
        // Get models based on where conditions
        if (!empty($this->wheres)) {
            $results = $modelClass::where($this->wheres);
        } else {
            $results = $modelClass::all();
        }
        
        // Eager load relations
        if (!empty($this->eagerLoad)) {
            $this->eagerLoadRelations($results);
        }
        
        return $results;
    }
    
    /**
     * Eager load the relationships for the models.
     *
     * @param \EhxDirectorist\Models\Collection $models
     * @return void
     */
    protected function eagerLoadRelations(Collection $models) {
        foreach ($models as $model) {
            $model->loadRelations($this->eagerLoad);
        }
    }
}

/**
 * Collection class for storing model results
 */
class Collection implements \ArrayAccess, \Countable, \Iterator {
    /**
     * The items contained in the collection.
     *
     * @var array
     */
    protected $items = [];
    
    /**
     * The position of the iterator.
     *
     * @var int
     */
    protected $position = 0;
    
    /**
     * Create a new collection.
     *
     * @param array $items
     * @return void
     */
    public function __construct(array $items = []) {
        $this->items = $items;
    }
    
    /**
     * Get all of the items in the collection.
     *
     * @return array
     */
    public function all() {
        return $this->items;
    }
    
    /**
     * Get the first item from the collection.
     *
     * @return mixed|null
     */
    public function first() {
        return !empty($this->items) ? reset($this->items) : null;
    }
    
    /**
     * Determine if the collection is empty.
     *
     * @return bool
     */
    public function isEmpty() {
        return empty($this->items);
    }
    
    /**
     * Get the collection of items as a plain array.
     *
     * @return array
     */
    public function toArray() {
        $result = [];
        
        foreach ($this->items as $key => $value) {
            if ($value instanceof Model) {
                $result[$key] = $value->toArray();
            } elseif ($value instanceof Collection) {
                $result[$key] = $value->toArray();
            } else {
                $result[$key] = $value;
            }
        }
        
        return $result;
    }
    
    /**
     * Load a set of relationships onto the collection.
     *
     * @param array $relations
     * @return $this
     */
    public function loadRelations(array $relations) {
        foreach ($this->items as $item) {
            if ($item instanceof Model) {
                $item->loadRelations($relations);
            }
        }
        
        return $this;
    }
    
    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count(): int {
        return count($this->items);
    }
    
    /**
     * Get the current item.
     *
     * @return mixed
     */
    public function current(): mixed {
        return $this->items[$this->position];
    }
    
    /**
     * Get the key of the current item.
     *
     * @return int|string
     */
    public function key(): mixed {
        return $this->position;
    }
    
    /**
     * Move to the next item.
     *
     * @return void
     */
    public function next(): void {
        ++$this->position;
    }
    
    /**
     * Rewind the iterator to the first item.
     *
     * @return void
     */
    public function rewind(): void {
        $this->position = 0;
    }
    
    /**
     * Determine if the current position is valid.
     *
     * @return bool
     */
    public function valid(): bool {
        return isset($this->items[$this->position]);
    }
    
    /**
     * Determine if an item exists at an offset.
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool {
        return isset($this->items[$offset]);
    }
    
    /**
     * Get an item at a given offset.
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset): mixed {
        return $this->items[$offset];
    }
    
    /**
     * Set the item at a given offset.
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value): void {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }
    
    /**
     * Unset the item at a given offset.
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset): void {
        unset($this->items[$offset]);
    }
}
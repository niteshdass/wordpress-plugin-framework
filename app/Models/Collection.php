<?php

namespace EhxDirectorist\Models;

/**
 * Collection class for managing groups of models
 */
class Collection implements \ArrayAccess, \Countable, \IteratorAggregate {
    /**
     * The items contained in the collection.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Create a new collection.
     *
     * @param  array  $items
     * @return void
     */
    public function __construct(array $items = []) {
        $this->items = $items;
    }

    /**
     * Convert the collection to its string representation.
     *
     * @return string
     */
    public function __toString() {
        return $this->toJson();
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
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count(): int {
        return count($this->items);
    }

    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator {
        return new \ArrayIterator($this->items);
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key): bool {
        return isset($this->items[$key]);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $key
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($key) {
        return $this->items[$key];
    }

    /**
     * Set the item at a given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value): void {
        if (is_null($key)) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  mixed  $key
     * @return void
     */
    public function offsetUnset($key): void {
        unset($this->items[$key]);
    }

    /**
     * Get the collection of items as a plain array.
     *
     * @return array
     */
    public function toArray() {
        return array_map(function ($value) {
            // Check if the value is an object and has a toArray method
            if (is_object($value) && method_exists($value, 'toArray')) {
                return $value->toArray();
            }
            return $value;
        }, $this->items);
    }

    /**
     * Get the collection of items as JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0) {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Map the values in the collection.
     *
     * @param  callable  $callback
     * @return static
     */
    public function map(callable $callback) {
        $result = [];
        
        foreach ($this->items as $key => $value) {
            $result[$key] = $callback($value, $key);
        }
        
        return new static($result);
    }

    /**
     * Filter the collection.
     *
     * @param  callable|null  $callback
     * @return static
     */
    public function filter(callable $callback = null) {
        if ($callback) {
            return new static(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
        }
        
        return new static(array_filter($this->items));
    }

    /**
     * Get the first item from the collection.
     *
     * @param  callable|null  $callback
     * @param  mixed  $default
     * @return mixed
     */
    public function first(callable $callback = null, $default = null) {
        if (is_null($callback)) {
            if (empty($this->items)) {
                return $default;
            }
            
            foreach ($this->items as $item) {
                return $item;
            }
        }
        
        foreach ($this->items as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }
        
        return $default;
    }

    /**
     * Get the last item from the collection.
     *
     * @param  callable|null  $callback
     * @param  mixed  $default
     * @return mixed
     */
    public function last(callable $callback = null, $default = null) {
        if (is_null($callback)) {
            if (empty($this->items)) {
                return $default;
            }
            
            return end($this->items);
        }
        
        $items = $this->items;
        $result = $default;
        
        foreach ($items as $key => $value) {
            if ($callback($value, $key)) {
                $result = $value;
            }
        }
        
        return $result;
    }

    /**
     * Pluck a value from each item.
     *
     * @param  string  $value
     * @param  string|null  $key
     * @return static
     */
    public function pluck($value, $key = null) {
        $results = [];
        
        foreach ($this->items as $item) {
            if (is_object($item)) {
                $itemValue = $item->$value;
            } else {
                $itemValue = $item[$value];
            }
            
            if (is_null($key)) {
                $results[] = $itemValue;
            } else {
                if (is_object($item)) {
                    $itemKey = $item->$key;
                } else {
                    $itemKey = $item[$key];
                }
                
                $results[$itemKey] = $itemValue;
            }
        }
        
        return new static($results);
    }
}
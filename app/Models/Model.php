<?php

namespace EhxDirectorist\Models;

use wpdb;

abstract class Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $attributes = [];

    /** @var wpdb */
    protected $db;

    // New: query builder state
    protected $queryConditions = [];
    protected $orderBy = [];
    protected $limit = null;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;

        if (!isset($this->table)) {
            throw new \Exception('Table name must be defined.');
        }

        if (strpos($this->table, $wpdb->prefix) !== 0) {
            $this->table = $wpdb->prefix . $this->table;
        }
    }

    public function fill(array $data)
    {
        // foreach ($this->fillable as $field) {
        //     if (array_key_exists($field, $data)) {
        //         $this->attributes[$field] = $data[$field];
        //     }
        // }
        return $data;
    }

    public function get()
    {
        $sql = "SELECT * FROM {$this->table}";
        $bindings = [];

        if (!empty($this->queryConditions)) {
            $clauses = [];

            foreach ($this->queryConditions as $cond) {
                if ($cond['type'] === 'basic') {
                    $clauses[] = "{$cond['column']} {$cond['operator']} %s";
                    $bindings[] = $cond['value'];
                } elseif ($cond['type'] === 'between') {
                    $clauses[] = "{$cond['column']} BETWEEN %s AND %s";
                    $bindings[] = $cond['range'][0];
                    $bindings[] = $cond['range'][1];
                }
            }

            $sql .= " WHERE " . implode(' AND ', $clauses);
        }

        // Apply ORDER BY
        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }

        // Apply LIMIT
        if ($this->limit !== null) {
            $sql .= " LIMIT %d";
            $bindings[] = $this->limit;
        }

        $prepared = $this->db->prepare($sql, ...$bindings);
        $results = $this->db->get_results($prepared, ARRAY_A);

        $this->queryConditions = [];
        $this->orderBy = [];
        $this->limit = null;

        return array_map(fn($row) => (new static())->fill($row), $results);
    }

    public function first()
    {
        return $this->get()[0] ?? null;
    }

    public function save()
    {
        if (isset($this->attributes[$this->primaryKey])) {
            $id = $this->attributes[$this->primaryKey];
            unset($this->attributes[$this->primaryKey]);
            $this->db->update($this->table, $this->attributes, [$this->primaryKey => $id]);
            $this->attributes[$this->primaryKey] = $id;
        } else {
            $this->db->insert($this->table, $this->attributes);
            $this->attributes[$this->primaryKey] = $this->db->insert_id;
        }

        return $this;
    }

    public function where($column, $operator, $value = null)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->queryConditions[] = [
            'type' => 'basic',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
        ];

        return $this;
    }

    public function orWhere($column, $operator, $value = null)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        if (empty($this->queryConditions)) {
            $this->queryConditions[] = [
                'type' => 'basic',
                'column' => $column,
                'operator' => $operator,
                'value' => $value,
            ];
        } else {
            // Add OR condition
            $this->queryConditions[] = [
                'type' => 'or',
                'column' => $column,
                'operator' => $operator,
                'value' => $value,
            ];
        }

        return $this;
    }

    public function whereBetween($column, array $range)
    {
        if (count($range) !== 2) {
            throw new \InvalidArgumentException("Range must contain exactly two values.");
        }

        $this->queryConditions[] = [
            'type' => 'between',
            'column' => $column,
            'range' => $range,
        ];

        return $this;
    }

    public function orderBy($column, $direction = 'ASC')
    {
        $this->orderBy[] = "{$column} {$direction}";
        return $this;
    }

    public function limit($count)
    {
        $this->limit = (int)$count;
        return $this;
    }

    public function update(array $data)
    {
        if (empty($this->queryConditions)) {
            throw new \Exception('No WHERE clause defined for update.');
        }

        $sql = "UPDATE {$this->table} SET ";
        $setClauses = [];
        $bindings = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $setClauses[] = "{$key} = %s";
                $bindings[] = $value;
            }
        }

        $sql .= implode(', ', $setClauses);

        $whereClauses = [];
        foreach ($this->queryConditions as $cond) {
            if ($cond['type'] === 'basic') {
                $whereClauses[] = "{$cond['column']} {$cond['operator']} %s";
                $bindings[] = $cond['value'];
            } elseif ($cond['type'] === 'between') {
                $whereClauses[] = "{$cond['column']} BETWEEN %s AND %s";
                $bindings[] = $cond['range'][0];
                $bindings[] = $cond['range'][1];
            } elseif ($cond['type'] === 'or') {
                $whereClauses[] = "OR {$cond['column']} {$cond['operator']} %s";
                $bindings[] = $cond['value'];
            }
        }

        $sql .= " WHERE " . implode(' ', $whereClauses);

        $prepared = $this->db->prepare($sql, ...$bindings);
        $this->queryConditions = [];

        return $this->db->query($prepared);
    }

    public function delete()
    {
        if (empty($this->queryConditions)) {
            throw new \Exception('No WHERE clause defined for delete.');
        }

        $sql = "DELETE FROM {$this->table}";
        $bindings = [];

        $whereClauses = [];
        foreach ($this->queryConditions as $cond) {
            if ($cond['type'] === 'basic') {
                $whereClauses[] = "{$cond['column']} {$cond['operator']} %s";
                $bindings[] = $cond['value'];
            } elseif ($cond['type'] === 'between') {
                $whereClauses[] = "{$cond['column']} BETWEEN %s AND %s";
                $bindings[] = $cond['range'][0];
                $bindings[] = $cond['range'][1];
            } elseif ($cond['type'] === 'or') {
                $whereClauses[] = "OR {$cond['column']} {$cond['operator']} %s";
                $bindings[] = $cond['value'];
            }
        }

        $sql .= " WHERE " . implode(' ', $whereClauses);

        $prepared = $this->db->prepare($sql, ...$bindings);
        $this->queryConditions = [];

        return $this->db->query($prepared);
    }

    public function find($id)
    {
        return $this->where($this->primaryKey, $id)->first();
    }

    public function all()
    {
        return $this->get();
    }

    public function paginate($perPage = 10, $page = 1)
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$this->table}";
        $bindings = [];

        if (!empty($this->queryConditions)) {
            $clauses = [];

            foreach ($this->queryConditions as $cond) {
                if ($cond['type'] === 'basic') {
                    $clauses[] = "{$cond['column']} {$cond['operator']} %s";
                    $bindings[] = $cond['value'];
                } elseif ($cond['type'] === 'between') {
                    $clauses[] = "{$cond['column']} BETWEEN %s AND %s";
                    $bindings[] = $cond['range'][0];
                    $bindings[] = $cond['range'][1];
                }
            }

            $sql .= " WHERE " . implode(' AND ', $clauses);
        }

        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }

        $sql .= " LIMIT %d OFFSET %d";
        $bindings[] = $perPage;
        $bindings[] = $offset;

        $prepared = $this->db->prepare($sql, ...$bindings);
        $results = $this->db->get_results($prepared, ARRAY_A);
        $total = $this->db->get_var("SELECT FOUND_ROWS()");

        $this->queryConditions = [];
        $this->orderBy = [];
        $this->limit = null;

        return [
            'data' => array_map(fn($row) => (new static())->fill($row), $results),
            'total' => (int)$total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
        ];
    }

    // Relationships
    public function hasOne($relatedClass, $foreignKey, $localKey = null)
    {
        $instance = new $relatedClass();
        $localKey = $localKey ?: $this->primaryKey;

        if (!isset($this->attributes[$localKey])) {
            return null;
        }

        return $instance->where($foreignKey, $this->attributes[$localKey])[0] ?? null;
    }

    public function hasMany($relatedClass, $foreignKey, $localKey = null)
    {
        $instance = new $relatedClass();
        $localKey = $localKey ?: $this->primaryKey;

        if (!isset($this->attributes[$localKey])) {
            return [];
        }

        return $instance->where($foreignKey, $this->attributes[$localKey]);
    }
}

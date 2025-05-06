<?php

namespace EhxDirectorist\Models;

abstract class Model {
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . $this->table;
    }

    public function find($id) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = %d",
                $id
            )
        );
    }

    public function all() {
        return $this->wpdb->get_results("SELECT * FROM {$this->table}");
    }

    public function create($data) {
        $this->wpdb->insert($this->table, $data);
        return $this->wpdb->insert_id;
    }

    public function update($id, $data) {
        return $this->wpdb->update(
            $this->table,
            $data,
            [$this->primaryKey => $id]
        );
    }

    public function delete($id) {
        return $this->wpdb->delete(
            $this->table,
            [$this->primaryKey => $id]
        );
    }
}

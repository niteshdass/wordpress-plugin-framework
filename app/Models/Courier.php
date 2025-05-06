<?php

namespace EhxDirectorist\Models;

class Courier extends Model {
    protected $table = 'wc_couriers';
    
    protected $fillable = [
        'courier_name',
        'api_endpoint',
        'api_key',
        'tracking_url',
        'status'
    ];

    public function getActiveCouriers() {
        return $this->wpdb->get_results(
            "SELECT * FROM {$this->table} WHERE status = 'active'"
        );
    }

    public function getShipments() {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT s.* FROM {$this->wpdb->prefix}wc_shipments s
                WHERE s.courier_id = %d",
                $this->id
            )
        );
    }
}

<?php

namespace EhxDirectorist\Models;

class Category extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ehxd_menu_categories';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branch_id',
        'name',
        'description',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the branch associated with this category
     *
     * @return Branch|null
     */
    public function branch() {
        if (!$this->branch_id) {
            return null;
        }
        
        return Branch::find($this->branch_id);
    }

    /**
     * Get all products belonging to this category
     *
     * @return array
     */
    public function products() {
        return $this->belongsToMany(Courier::class);
    }
}
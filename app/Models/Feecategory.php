<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feecategory extends Model
{
    protected $fillable = [
        'id','name','brid'
    ];

    protected $table = 'feecategory';

    // Relationships
    public function branches()
    {
        return $this->belongsToMany(Branch::class);
    }
}

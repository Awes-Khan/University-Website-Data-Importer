<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
    ];

    // Relationships
    public function feecategories()
    {
        return $this->hasMany(Feecategory::class);
    }

    public function feecollectiontypes()
    {
        return $this->hasMany(Feecollectiontype::class);
    }
}

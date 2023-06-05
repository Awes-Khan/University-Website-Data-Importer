<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feecollectiontype extends Model
{
    protected $fillable = [
        'name',
    ];

    // Relationships
    public function branches()
    {
        return $this->belongsToMany(Branch::class);
    }
}

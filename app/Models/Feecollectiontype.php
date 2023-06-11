<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feecollectiontype extends Model
{
    protected $fillable = [
        'collectionhead','collectiondesc','br_id',
    ];

    protected $table = 'feecollectiontypes';
    
    // Relationships
    public function branches()
    {
        return $this->belongsToMany(Branch::class);
    }
}

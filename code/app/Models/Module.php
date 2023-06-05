<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // Relationships
    public function financialtrans()
    {
        return $this->hasMany(Financialtran::class);
    }

    public function commonfeecollections()
    {
        return $this->hasMany(Commonfeecollection::class);
    }

    public function commonfeecollectionheadwises()
    {
        return $this->hasMany(Commonfeecollectionheadwise::class);
    }
}

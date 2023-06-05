<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrymode extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'crdr',
        'entrymodeno',
    ];

    // Relationships
    public function financialtrans()
    {
        return $this->hasMany(Financialtran::class);
    }
}

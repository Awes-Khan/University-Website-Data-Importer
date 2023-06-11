<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrymode extends Model
{
    use HasFactory;

    protected $fillable = [
        'entry_modename',
        'crdr',
        'entrymodeno',
    ];
    protected $table = 'entrymode';


    // Relationships
    public function financialtrans()
    {
        return $this->hasMany(Financialtran::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Financialtrandetail extends Model
{
    protected $fillable = [
        'financialTranId',
        'moduleId',
        'amount',
        'headId',
        'crdr',
        'brid',
        'head_name',
    ];

    // Relationships
    public function financialtran()
    {
        return $this->belongsTo(Financialtran::class, 'financialTranId');
    }
}

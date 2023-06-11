<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Financialtran extends Model
{
    protected $fillable = [
        'moduleid',
        'tranid',
        'admno',
        'rollno',
        'amount',
        'crdr',
        'tranDate',
        'acadYear',
        'entrymode',
        'voucherno',
        'brid',
        'Type_of_concession',
    ];

    // Relationships
    public function module()
    {
        return $this->belongsTo(Module::class, 'moduleid');
    }

    public function entrymode()
    {
        return $this->belongsTo(Entrymode::class, 'entrymode');
    }

    public function financialtrandetails()
    {
        return $this->hasMany(Financialtrandetail::class, 'tranid');
    }
}

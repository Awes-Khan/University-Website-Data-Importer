<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Financialtran extends Model
{
    protected $fillable = [
        'moduleId',
        'transId',
        'admno',
        'rollno',
        'amount',
        'brId',
        'acadamicYear',
        'financialYear',
        'entrymode',
        'voucherno',
        'brid',
        'type_of_concession',
    ];

    // Relationships
    public function module()
    {
        return $this->belongsTo(Module::class, 'moduleId');
    }

    public function entrymode()
    {
        return $this->belongsTo(Entrymode::class, 'entrymode');
    }

    public function financialtrandetails()
    {
        return $this->hasMany(Financialtrandetail::class, 'financialTranId');
    }
}

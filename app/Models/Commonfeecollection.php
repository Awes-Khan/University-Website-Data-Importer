<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commonfeecollection extends Model
{
    protected $fillable = [
        'moduleId',
        'transId',
        'admno',
        'rollno',
        'brid',
        'amount',
        'acadamicYear',
        'financialYear',
        'displayReceiptNo',
        'entrymode',
        'paid_date',
        'inactive'
    ];
    protected $table = 'commonfeecollection';
    // Relationships
    public function module()
    {
        return $this->belongsTo(Module::class, 'moduleId');
    }

    public function commonfeecollectionheadwises()
    {
        return $this->hasMany(Commonfeecollectionheadwise::class, 'receiptId');
    }
}

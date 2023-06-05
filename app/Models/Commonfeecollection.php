<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commonfeecollection extends Model
{
    protected $fillable = [
        'moduleId',
        'receiptId',
        'headId',
        'headName',
        'brid',
        'amount',
    ];

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

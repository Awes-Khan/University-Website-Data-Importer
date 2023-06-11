<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commonfeecollectionheadwise extends Model
{
    protected $fillable = [
        'moduleId',
        'receiptId',
        'headId',
        'headName',
        'brid',
        'amount',
    ];

    protected $table='commonfeecollectionheadwise';
    // Relationships
    public function commonfeecollection()
    {
        return $this->belongsTo(Commonfeecollection::class, 'receiptId');
    }
}

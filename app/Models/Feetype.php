<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feetype extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_category','f_name','collection_id','br_id','seq_id','fee_type_ledger','fee_head_type'  
    ];

    protected $table = 'feetypes';

    // Relationships
    public function branches()
    {
        return $this->belongsToMany(Branch::class);
    }
}

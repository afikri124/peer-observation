<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criteria extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'id', 'title', 'weight', 'criteria_category_id','status'
    ];

    public function category()
    {
        return $this->belongsTo(Criteria_category::class, 'criteria_category_id');
    }
}

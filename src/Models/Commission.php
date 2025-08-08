<?php

namespace admin\commissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends Model
{
    use HasFactory, Sortable, SoftDeletes;

    protected $fillable = [
        'type', 'category_id', 'commission_type', 'commission_value', 'status'
    ];
    

    public $sortable = [
      'type', 'category_id', 'commission_type', 'commission_value', 'status', 'created_at'
    ];

    public static function getPerPageLimit()
    {
        return config('commission.constants.per_page_limit', 10);
    }

    public function category()
    {
        return $this->belongsTo('admin\categories\Models\Category', 'category_id');
    }   
}

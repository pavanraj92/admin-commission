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
        'type',
        'commission_type',
        'commission_value',
        'status'
    ];


    public $sortable = [
        'type',
        'commission_type',
        'commission_value',
        'status',
        'created_at'
    ];

    public static function getPerPageLimit()
    {
        return config('commission.constants.per_page_limit', 10);
    }

    public function categories()
    {
        return $this->belongsToMany(
            'admin\categories\Models\Category',
            'commission_category',
            'commission_id',
            'category_id'
        );
    }
}

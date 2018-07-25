<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'order';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bill_ref',
        'date',
        'date_',
        'activate',
        'customer_id',
        'branch_id',
        'user_id',
        'job_id',
        'amulet_id',
        'amount',
        'price',
        'desc'
    ];
}
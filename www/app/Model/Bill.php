<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $table = 'bill';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'date',
        'date_',
        'bill_id',
        'activate',
        'status',
        'process',
        'deliver',
        'pay',
        'allow_zero',
        'user_id',
        'customer_id',
        'job_type',
        'image_part',
        'craft_id',
        'branch_id',
        'cause_id',
        'gold',
        'cash',
        'desc'
    ];

    public function order()
    {
        return $this->hasMany('App\Model\Order','bill_ref', 'id');
    }

    public function part()
    {
        return $this->hasMany('App\Model\Part','bill_ref', 'id');
    }

    public function gold()
    {
        return $this->hasMany('App\Model\Gold','bill_ref', 'id');
    }

    public function customer()
    {
        return $this->hasMany('App\Model\Customer','id', 'customer_id');
    }

    public function payment()
    {
        return $this->hasMany('App\Model\Payment','bill_ref', 'id');
    }

    public function users()
    {
        return $this->hasMany('App\User','id', 'user_id');
    }
}

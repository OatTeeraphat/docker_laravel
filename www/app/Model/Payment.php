<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payment';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bill_ref',
        'method',
        'value',
        'cause',
        'user_recive',
        'user_void',
        'activate',
        'branch_id'
    ];

    public function u_recive()
    {
        return $this->hasOne('App\User','id','user_recive');
    }

    public function u_void()
    {
        return $this->hasOne('App\User','id','user_void');
    }

}

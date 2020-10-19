<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Customer extends Model
{

    protected $table = 'customer';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
        'customer_type',
        'address',
        'line',
        'activate',
        'increment',
        'already_used'
    ];


}

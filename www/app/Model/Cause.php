<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Cause extends Model
{
    protected $table = 'cause';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'desc'
    ];

}

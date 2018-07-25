<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Craft extends Model
{
    protected $table = 'craft';

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
        'name','branch_id','activate'
    ];
}

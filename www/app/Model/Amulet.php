<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Amulet extends Model
{
    protected $table = 'amulet';

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
        'name','order'
    ];
}

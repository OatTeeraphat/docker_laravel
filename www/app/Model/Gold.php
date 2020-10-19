<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Gold extends Model
{
    protected $table = 'gold';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bill_ref',
        'value',
        'craft_id',
        'activate',
        'branch_id',
    ];
}

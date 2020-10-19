<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Export extends Model
{
    protected $table = 'export';


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
        'name',
        'type',
        'file_name',
        'size',
        'no_record',
        'update_at'
    ];
}

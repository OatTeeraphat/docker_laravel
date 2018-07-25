<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BranchIP extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'branch_ip';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['ip','branch_id'];

}

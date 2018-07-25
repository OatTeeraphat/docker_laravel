<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'branch';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','address','time_open','date_open','phone','increment','activate'];

    /**
     * Get the user_id for the role_user table.
     */
    public function branchIp(){

        return $this->hasMany('App/BranchIP');

    }




}

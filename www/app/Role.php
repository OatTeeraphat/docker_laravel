<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'description','level'];
    
    /**
     * Get the user_id for the role_user table.
     */
    public function users(){

        return $this->belongsToMany('App\User', 'role_user', 'user_id', 'role_id');

    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Eloquent

{

   /**
   * The attributes that are mass assignable.
   *
   * @var array
   */

   protected $fillable = [

       'first_name', 'last_name', 'email', 'password', 'token',

   ];

   /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */

   protected $hidden = [

       'password', 'remember_token',

   ];

 }
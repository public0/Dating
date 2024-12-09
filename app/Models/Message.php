<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Message extends Eloquent

{
    

   /**
   * The attributes that are mass assignable.
   *
   * @var array
   */

   protected $fillable = [

       'conversation_id', 'body', 'user_id'

   ];
   
   protected $hidden = [

       'id', 'conversation_id'

   ];
 }
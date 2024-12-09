<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Eloquent

{

   /**
   * The attributes that are mass assignable.
   *
   * @var array
   */

   protected $fillable = [
    'user1_id',
    'user2_id'

   ];

   public function messages(): HasMany
   {
       return $this->hasMany(Message::class);
   }

 }
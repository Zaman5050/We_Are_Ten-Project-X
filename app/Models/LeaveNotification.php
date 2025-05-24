<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveNotification extends Model
{
   protected $table = 'notifications';

   protected $primaryKey = 'id';

   public $incrementing = false;

   protected $keyType = 'string';

   protected $fillable = [
       'type',
       'data',
       'read_at',
       'notifiable_id',
       'notifiable_type',
   ];

   protected $casts = [
       'data' => 'array',
       'read_at' => 'datetime',
   ];

   public function getFormattedReadAtAttribute()
   {
       return $this->read_at ? $this->read_at->format('d M Y, H:i') : 'Not Read';
   }

   public function notifiable()
   {
       return $this->morphTo();
   }

}

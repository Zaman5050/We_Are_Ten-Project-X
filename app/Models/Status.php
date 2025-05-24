<?php

namespace App\Models;

use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Status extends Model
{
    use HasFactory, HasUUID, SoftDeletes;

    protected $fillable = ['name','flag'];
    protected $casts = [];
    protected $table = 'statuses';
    protected $appends = ['title'];


    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }

    public function getTitleAttribute()
    {
        return Str::headline($this->name);
    }

}

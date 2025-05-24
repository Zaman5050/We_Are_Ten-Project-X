<?php

namespace App\Models;

use App\Models\User;
use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory, HasUUID;

    protected $table = 'companies';

    const STATUS_ACTIVE = 'active';
    const STATUS_IN_ACTIVE = 'in-active';
    const STATUS_PENDING = 'pending';

    protected $fillable = [
        'name',
        'email',
        'subdomain',
        'logo',
        'status',
    ];

    protected $appends = ['virtual_url'];

    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }

    public function admin()
    {
        return $this->belongsTo(User::class, "id", "company_id")
        ->whereHas('roles', function ($q) { $q->where('name', 'admin'); });
    }

    public function teams()
    {
        return $this->hasMany(User::class, "company_id", "id")
        ->whereHas('roles', function ($q) { $q->where('name', 'designer'); });
    }

    public function setSubdomainAttribute($value)
    {
        $this->attributes['subdomain'] = strtolower(explode('.',$value)[0] ?? null);
    }

    public function getVirtualUrlAttribute()
    {
        if (config('app.env') === 'local')
        { 
            return str_replace('http://','http://'.$this->subdomain.'.',config('app.url'));             
        }  
        return str_replace('https://','https://'.$this->subdomain.'.',config('app.url'));
    }



}

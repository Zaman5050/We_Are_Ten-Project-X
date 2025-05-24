<?php

namespace App\Models;

use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Relationships\SupplierLibraryRelations;
use Illuminate\Support\Facades\Storage;

class SupplierLibrary extends Model
{
    use HasFactory,
        HasUUID,
        SoftDeletes,
        SupplierLibraryRelations;

    protected $fillable = [
        'company_id',
        'currency_id',
        'company_name',
        'name',
        'email',
        'phone_number',
        'address',
        'timezone',
        'trade_discount',
        'website',
        'profile_picture',
    ];
    protected $casts = [];

    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }
    public function getProfilePictureAttribute($value)
    {
        return $value ? Storage::url($value) : "";
        // return $value ? Storage::url($value) : asset('assets/images/project-list-default.svg');
    }
}

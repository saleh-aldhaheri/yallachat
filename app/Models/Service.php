<?php

namespace App\Models;

use App\Enums\ServicesEnum;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'refresh_token',
        'access_token',
        'is_available',
        'is_active',
    ];

    protected $casts = [
        'name' => ServicesEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUseable($query): Builder
    {
        return $query->where('is_active', 1)->where('is_available', 1);
    }
}

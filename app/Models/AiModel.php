<?php

namespace App\Models;

use App\Enums\ModelsEnum;
use Database\Factories\AiModelFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiModel extends Model
{
    /** @use HasFactory<AiModelFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'is_active',
        'is_available',
        'persona',
        'tone',
        'api_key',
        'is_multi_language',
        'is_auto_language',
    ];

    protected $casts = [
        'name' => ModelsEnum::class,
    ];

    public function scopeUseable($query): Builder
    {
        return $query->where('is_active', 1)->where('is_available', 1);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scheduledMessages(): HasMany
    {
        return $this->hasMany(ScheduledMessage::class, 'ai_model_id');
    }
}

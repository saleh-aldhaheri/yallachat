<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Chat extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'created_by',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getAvatarAttribute(): ?string
    {
        if ($this->type === 'group') {
            return $this->getFirstMediaUrl('group-avatar') ?: null;
        }

        return null;
    }

    public function setPriorityFor(User $user, bool $priority): void
    {
        $this->participants()
            ->newPivotQuery()
            ->where('participant_id', $user->id)
            ->update(['is_priority' => $priority]);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('group-avatar')
            ->singleFile();
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_participants', 'chat_id', 'participant_id')
            ->withPivot(['last_read_message_id', 'is_priority'])
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function chatScheduledMessages(): HasMany
    {
        return $this->hasMany(ChatScheduledMessage::class);
    }

    public function scheduledMessages(): BelongsToMany
    {
        return $this->belongsToMany(
            ScheduledMessage::class,
            'chat_scheduled_message',
            'chat_id',
            'scheduled_message_id'
        );
    }

    public function unreadCountForUser(int $userId): int
    {
        $participant = $this->participants()
            ->where('participant_id', $userId)
            ->first();

        if (! $participant || ! $participant->pivot->last_read_message_id) {
            return $this->messages()->where('sender_id', '!=', $userId)->count();
        }

        return $this->messages()
            ->where('id', '>', $participant->pivot->last_read_message_id)
            ->where('sender_id', '!=', $userId)
            ->count();
    }
}

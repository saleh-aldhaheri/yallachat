<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable(['name', 'email', 'password',  'gender'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, InteractsWithMedia, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class, 'created_by');
    }

    public function joinedChats(): BelongsToMany
    {
        return $this->belongsToMany(Chat::class, 'chat_participants', 'participant_id', 'chat_id')
            ->withPivot(['left_at', 'last_read_message_id', 'is_priority'])
            ->withTimestamps();
    }

    public function registerMediaCollections(?Media $media = null): void
    {
        $this
            ->addMediaCollection('avatar')
            ->acceptsMimeTypes(['image/jpeg', 'image/png'])
            ->singleFile();
    }

    public function getAvatarAttribute(): ?string
    {
        return $this->getFirstMediaUrl('avatar') ?: null;
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function aiModels(): HasMany
    {
        return $this->hasMany(AiModel::class);
    }

    public function ScheduledMessages(): HasMany
    {
        return $this->hasMany(ScheduledMessage::class);
    }

    public function notificationChannels(): HasMany
    {
        return $this->hasMany(NotificationChannel::class);
    }
}

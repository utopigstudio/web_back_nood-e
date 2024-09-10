<?php

namespace App\Models;

use App\ImageTrait;
use App\Notifications\UserInviteNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, ImageTrait, SoftDeletes;

    protected $fillable = [
        'name',
        'surname',
        'email',
        'description',
        'password',
        'reset_password',
        'permissions',
        'role_id',
        'organization_id',
        'image'
    ];

    protected $hidden = [
        'password',
    ];

    protected $primaryKey = 'id';

    protected $image_fields = ['image'];

    protected $image_prefixes = [
        'image' => 'user-'
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'invite_accepted_at' => 'datetime',
        ];
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function eventsAuthored(): HasMany
    {
        return $this->hasMany(Event::class, 'author_id');
    }

    // TODO: events as member (belongsToMany)    

    public function discussionsAuthored(): HasMany
    {
        return $this->hasMany(Discussion::class, 'author_id');
    }

    // TODO: discussions as member (belongsToMany)

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class, 'author_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'author_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'owner_id');
    }

    public function sendInviteNotification()
    {
        $frontUrl = $this->getInviteUrl();

        $this->notify(new UserInviteNotification($frontUrl));
    }

    public function getInviteUrl(): string
    {
        $expirationInSeconds = null;
        // 7 days
        // $expirationInSeconds = 60 * 60 * 24 * 7;

        $url = URL::signedRoute('invitation', $this, $expirationInSeconds, false);

        $userId = $this->id;
        $signature = substr($url, strpos($url, '?signature=') + 11);
        
        return config('app.frontend_url') . '/invitation?user_id=' . $userId . '&signature=' . $signature;
    }
}

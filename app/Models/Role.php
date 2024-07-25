<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['role'];
    private $role;

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function setRole () {
        if ($this->role == 0) {
            return 'User';
        } elseif ($this->role == 1) {
            return 'Organization User';
        } elseif ($this->role == 2) {
            return 'Admin';
        } elseif ($this->role == 3) {
            return 'Super Admin';
        }
    }
}

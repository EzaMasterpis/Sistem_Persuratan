<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;   
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password','role', 'nip', 'bagian'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSekretariat(): bool
    {
        return $this->bagian === 'sekretariat';
    }

    public function getBagianLabelAttribute(): string
    {
        return config('bpkad.bagian.' . $this->bagian, '-');
    }

    public function simpanan(): BelongsToMany
    {
        return $this->belongsToMany(Surat::class, 'surat_simpanan')->withTimestamps();
    }

    public function opsiTujuan(): array
    {
        return collect(config('bpkad.bagian'))
            ->except($this->bagian)
            ->map(fn ($label, $key) => ['key' => $key, 'label' => $label])
            ->values()
            ->all();
    }
    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

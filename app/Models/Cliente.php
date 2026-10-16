<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'telefono',
        'correo',
    ];

    protected function casts(): array
    {
        return [
            'portal_token' => 'encrypted',
        ];
    }

    public function servicios(): HasMany
    {
        return $this->hasMany(Servicio::class);
    }

    public function emitirTokenPortal(): string
    {
        if ($this->portal_token && $this->portal_token_hash) {
            return $this->portal_token;
        }

        $token = Str::random(64);
        $this->forceFill([
            'portal_token' => $token,
            'portal_token_hash' => hash('sha256', $token),
        ])->save();

        return $token;
    }
}

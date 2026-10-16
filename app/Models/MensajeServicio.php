<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MensajeServicio extends Model
{
    protected $table = 'mensajes_servicio';

    protected $fillable = [
        'servicio_id',
        'user_id',
        'remitente',
        'mensaje',
    ];

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

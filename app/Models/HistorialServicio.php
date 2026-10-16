<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialServicio extends Model
{
    use HasFactory;

    protected $table = 'historial_servicios';

    protected $fillable = [
        'servicio_id',
        'estado_anterior',
        'estado_nuevo',
        'comentario',
    ];

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }
}

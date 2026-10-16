<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'folio',
        'tipo_equipo',
        'marca',
        'modelo',
        'serie',
        'accesorios',
        'fecha_recepcion',
        'estado_fisico',
        'falla_reportada',
        'diagnostico',
        'observaciones',
        'estado_servicio',
        'precio',
        'anticipo',
        'saldo',
        'autorizacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_recepcion' => 'datetime',
            'precio' => 'decimal:2',
            'anticipo' => 'decimal:2',
            'saldo' => 'decimal:2',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function historial(): HasMany
    {
        return $this->hasMany(HistorialServicio::class);
    }

    public function mensajes(): HasMany
    {
        return $this->hasMany(MensajeServicio::class, 'servicio_id');
    }
}

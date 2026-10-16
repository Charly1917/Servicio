<?php

namespace App\Services;

use App\Models\Servicio;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class HojaServicioPdf
{
    public function guardar(Servicio $servicio): string
    {
        $servicio->loadMissing('cliente');
        $cliente = $servicio->cliente;
        $nombre = Str::slug($cliente->nombre) ?: 'cliente';
        $telefono = preg_replace('/\D+/', '', $cliente->telefono ?? '');
        $identificador = $telefono !== '' ? $telefono : 'id-'.$cliente->id;
        $anio = $servicio->fecha_recepcion->format('Y');
        $ruta = 'hojas-servicio/'.$anio.'/'.$nombre.'-'.$identificador.'/'.$servicio->folio.'.pdf';
        $contenido = Pdf::loadView('servicios.imprimir', [
            'servicio' => $servicio,
            'pdf' => true,
        ])->setPaper('letter', 'portrait')->output();

        if (! Storage::disk('local')->put($ruta, $contenido)) {
            throw new RuntimeException('No se pudo guardar la hoja de servicio en el almacenamiento privado.');
        }

        return $ruta;
    }
}

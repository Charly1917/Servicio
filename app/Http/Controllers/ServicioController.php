<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\HistorialServicio;
use App\Models\Servicio;
use App\Services\HojaServicioPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServicioController extends Controller
{
    public function crear()
    {
        return view('servicios.crear');
    }

    public function guardar(Request $request)
    {
        $serviciosSolicitados = $request->input('servicios_solicitados', []);
        $diagnosticoPendiente = is_array($serviciosSolicitados)
            && in_array('Diagnóstico', $serviciosSolicitados, true);
        if ($diagnosticoPendiente && count($serviciosSolicitados) !== 1) {
            return back()
                ->withErrors(['servicios_solicitados' => 'Diagnóstico debe seleccionarse por separado, sin combinarlo con otros servicios.'])
                ->withInput();
        }
        if ($diagnosticoPendiente && $request->boolean('cotizacion_pendiente')) {
            return back()
                ->withErrors(['cotizacion_pendiente' => 'La cotización queda pendiente automáticamente al seleccionar Diagnóstico.'])
                ->withInput();
        }

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'correo' => ['nullable', 'email', 'max:255'],

            'tipo_equipo' => ['required', 'string', 'max:100'],
            'marca' => ['required', 'string', 'max:100'],
            'modelo' => ['nullable', 'string', 'max:150'],
            'serie' => ['nullable', 'string', 'max:150'],
            'accesorios' => ['nullable', 'string'],
            'sin_accesorios' => ['sometimes', 'boolean'],
            'cotizacion_pendiente' => ['sometimes', 'boolean'],
            'estado_fisico' => ['required', 'in:Bueno,Regular,Dañado'],
            'falla_reportada' => $diagnosticoPendiente
                ? ['required', 'string', 'max:5000']
                : ['nullable', 'string', 'max:5000'],

            'servicios_solicitados' => ['required', 'array', 'min:1'],
            'servicios_solicitados.*' => ['required', 'string', 'max:150'],

            'precio' => ($diagnosticoPendiente || $request->boolean('cotizacion_pendiente'))
                ? ['nullable', 'numeric', 'min:0']
                : ['required', 'numeric', 'min:0'],
        ]);

        $servicio = DB::transaction(function () use ($datos, $diagnosticoPendiente, $request) {
            $cliente = $this->buscarOCrearCliente($datos);
            $cotizacionPendiente = ! $diagnosticoPendiente && $request->boolean('cotizacion_pendiente');
            $cotizacionAcordada = ! $diagnosticoPendiente && ! $cotizacionPendiente;
            $estadoInicial = $diagnosticoPendiente
                ? 'En diagnóstico'
                : ($cotizacionPendiente ? 'Cotización pendiente' : 'En reparación');

            $servicio = Servicio::create([
                'cliente_id' => $cliente->id,
                'folio' => 'TEMP-'.uniqid(),
                'tipo_equipo' => $datos['tipo_equipo'],
                'marca' => $datos['marca'] ?? null,
                'modelo' => $datos['modelo'] ?? null,
                'serie' => $datos['serie'] ?? null,
                'accesorios' => ($datos['sin_accesorios'] ?? false)
                    ? 'Sin accesorios'
                    : ($datos['accesorios'] ?? null),
                'fecha_recepcion' => now(),
                'estado_fisico' => $datos['estado_fisico'],
                'falla_reportada' => $datos['falla_reportada'] ?? null,
                'observaciones' => implode(
                    ', ',
                    $datos['servicios_solicitados']
                ),
                'estado_servicio' => $estadoInicial,
                'precio' => $cotizacionAcordada ? $datos['precio'] : null,
                'anticipo' => 0,
                'saldo' => $cotizacionAcordada ? $datos['precio'] : null,
                'autorizacion' => $cotizacionAcordada ? 'Aprobado' : 'Pendiente',
            ]);

            $servicio->update([
                'folio' => 'TI-'.now()->format('Y').'-'
                    .str_pad((string) $servicio->id, 4, '0', STR_PAD_LEFT),
            ]);

            HistorialServicio::create([
                'servicio_id' => $servicio->id,
                'estado_anterior' => null,
                'estado_nuevo' => $estadoInicial,
                'comentario' => $diagnosticoPendiente
                    ? 'Servicio recibido para diagnóstico.'
                    : ($cotizacionPendiente
                        ? 'Servicio recibido sin cotización; TI debe revisar y registrar un precio.'
                        : 'El cliente aceptó el precio acordado al recibir el equipo.'),
            ]);

            $servicio->load(['cliente', 'historial']);
            $cliente->emitirTokenPortal();

            return $servicio;
        });

        app(HojaServicioPdf::class)->guardar($servicio->load('cliente'));

        return redirect()
            ->route(
                $request->input('accion') === 'imprimir'
                    ? 'ti.servicios.imprimir'
                    : 'ti.servicios.mostrar',
                $servicio
            )
            ->with('success', 'Servicio registrado correctamente.');
    }

    public function imprimir(Servicio $servicio)
    {
        $servicio->load('cliente');

        return view('servicios.imprimir', compact('servicio'));
    }

    private function buscarOCrearCliente(array $datos): Cliente
    {
        $telefono = trim($datos['telefono'] ?? '');
        $telefonoNormalizado = preg_replace('/\D+/', '', $telefono);
        $nombre = trim($datos['nombre']);

        if ($telefonoNormalizado !== '') {
            $clientesCoincidentes = Cliente::query()
                ->whereRaw('LOWER(TRIM(nombre)) = ?', [mb_strtolower($nombre)])
                ->get()
                ->filter(fn (Cliente $cliente) => preg_replace(
                    '/\D+/',
                    '',
                    $cliente->telefono ?? ''
                ) === $telefonoNormalizado)
                ->sortBy('id')
                ->values();

            if ($clientesCoincidentes->isNotEmpty()) {
                $clientesConPortal = $clientesCoincidentes
                    ->filter(fn (Cliente $cliente) => filled($cliente->portal_token_hash));
                $clientesPrincipales = $clientesConPortal->isNotEmpty()
                    ? $clientesConPortal
                    : $clientesCoincidentes;
                $clientePrincipal = $clientesPrincipales->sortBy('id')->first();

                foreach ($clientesCoincidentes as $clienteDuplicado) {
                    if ($clienteDuplicado->id === $clientePrincipal->id) {
                        continue;
                    }

                    $clienteDuplicado->servicios()->update([
                        'cliente_id' => $clientePrincipal->id,
                    ]);

                    if (! $clientePrincipal->correo && $clienteDuplicado->correo) {
                        $clientePrincipal->correo = $clienteDuplicado->correo;
                        $clientePrincipal->save();
                    }

                    if (! $clienteDuplicado->portal_token_hash) {
                        $clienteDuplicado->delete();
                    }
                }

                return $clientePrincipal;
            }
        }

        return Cliente::create([
            'nombre' => $nombre,
            'telefono' => $telefono !== '' ? $telefono : null,
            'correo' => $datos['correo'] ?? null,
        ]);
    }
}

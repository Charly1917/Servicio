<?php

namespace App\Http\Controllers;

use App\Models\HistorialServicio;
use App\Models\Servicio;
use App\Services\HojaServicioPdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PanelServicioController extends Controller
{
    public function index(Request $request): View
    {
        $driver = DB::connection()->getDriverName();
        $yearExpression = $driver === 'sqlite'
            ? "strftime('%Y', fecha_recepcion)"
            : 'YEAR(fecha_recepcion)';
        $anios = Servicio::query()
            ->selectRaw($yearExpression.' as anio')
            ->distinct()
            ->orderByDesc('anio')
            ->pluck('anio')
            ->map(fn ($anio) => (int) $anio)
            ->prepend((int) now()->format('Y'))
            ->unique()
            ->values();
        $anioSolicitado = (string) $request->query('anio', now()->format('Y'));
        $anio = preg_match('/^\d{4}$/', $anioSolicitado) === 1
            && $anios->contains((int) $anioSolicitado)
            ? $anioSolicitado
            : (string) now()->format('Y');
        $busqueda = trim((string) $request->query('buscar', ''));

        $consultaBase = Servicio::query()
            ->with('cliente')
            ->whereBetween('fecha_recepcion', [
                Carbon::create((int) $anio)->startOfYear(),
                Carbon::create((int) $anio)->endOfYear(),
            ])
            ->when($busqueda !== '', function ($query) use ($busqueda): void {
                $query->where(function ($query) use ($busqueda): void {
                    $query->where('folio', 'like', '%'.$busqueda.'%')
                        ->orWhereHas('cliente', fn ($clienteQuery) => $clienteQuery
                            ->where('nombre', 'like', '%'.$busqueda.'%'));
                });
            });

        $grupos = [
            'diagnostico' => [
                'nombre' => 'Diagnósticos',
                'filtro' => static fn ($query) => $query
                    ->whereIn('estado_servicio', ['En diagnóstico', 'Cotización pendiente'])
                    ->where('autorizacion', '!=', 'Rechazado'),
            ],
            'autorizacion' => [
                'nombre' => 'En autorización',
                'filtro' => static fn ($query) => $query
                    ->where('estado_servicio', 'En autorización')
                    ->where('autorizacion', '!=', 'Rechazado'),
            ],
            'reparacion' => [
                'nombre' => 'En reparación',
                'filtro' => static fn ($query) => $query
                    ->whereIn('estado_servicio', ['En reparación', 'Seguimiento final'])
                    ->where('autorizacion', '!=', 'Rechazado'),
            ],
            'recoleccion' => [
                'nombre' => 'Pendientes de recolección',
                'filtro' => static fn ($query) => $query
                    ->whereIn('estado_servicio', ['Listo para entregar', 'Pendiente de recolección'])
                    ->where('autorizacion', '!=', 'Rechazado'),
            ],
            'entregados' => [
                'nombre' => 'Entregados',
                'filtro' => static fn ($query) => $query
                    ->where('estado_servicio', 'Entregado')
                    ->where('autorizacion', '!=', 'Rechazado'),
            ],
            'no_autorizados' => [
                'nombre' => 'No autorizados',
                'filtro' => static fn ($query) => $query->where('autorizacion', 'Rechazado'),
            ],
        ];

        $grupoSolicitado = (string) $request->query('grupo', 'todos');
        $grupo = array_key_exists($grupoSolicitado, $grupos) ? $grupoSolicitado : 'todos';
        $conteosGrupos = ['todos' => (clone $consultaBase)->count()];

        foreach ($grupos as $clave => $datosGrupo) {
            $consultaConteo = clone $consultaBase;
            $datosGrupo['filtro']($consultaConteo);
            $conteosGrupos[$clave] = $consultaConteo->count();
        }

        $consultaServicios = clone $consultaBase;
        if ($grupo !== 'todos') {
            $grupos[$grupo]['filtro']($consultaServicios);
        }

        $servicios = $consultaServicios
            ->orderByDesc('fecha_recepcion')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('ti.servicios.index', compact(
            'servicios',
            'anios',
            'anio',
            'busqueda',
            'grupos',
            'grupo',
            'conteosGrupos',
        ));
    }

    public function mostrar(Servicio $servicio): View
    {
        $servicio->load([
            'cliente',
            'historial' => fn ($query) => $query->latest(),
            'mensajes' => fn ($query) => $query->oldest(),
        ]);

        $token = $servicio->cliente->emitirTokenPortal();
        $urlCliente = route('portal.servicio', $token);
        $whatsappUrl = $this->urlWhatsAppEntrega($servicio, $urlCliente);
        $whatsappPortalUrl = $this->urlWhatsAppSeguimiento($servicio, $urlCliente);
        $estados = $this->estadosDisponibles($servicio);

        return view('ti.servicios.mostrar', [
            'servicio' => $servicio,
            'estados' => $estados,
            'urlCliente' => $urlCliente,
            'whatsappUrl' => $whatsappUrl,
            'whatsappPortalUrl' => $whatsappPortalUrl,
            'googleReviewConfigured' => filled(config('services.customer_reviews.google')),
            'storeReviewConfigured' => filled(config('services.customer_reviews.store')),
        ]);
    }

    public function actualizar(Request $request, Servicio $servicio): RedirectResponse
    {
        $servicioRechazado = $servicio->autorizacion === 'Rechazado'
            && in_array($servicio->estado_servicio, ['Pendiente de recolección', 'Entregado'], true);
        $estadosDisponibles = $this->estadosDisponibles($servicio);
        $datos = $request->validate([
            'estado_servicio' => [
                'required',
                'in:'.implode(',', $estadosDisponibles),
            ],
            'diagnostico' => [$servicioRechazado ? 'prohibited' : 'nullable', 'string', 'max:5000'],
            'precio' => [$servicioRechazado ? 'prohibited' : 'nullable', 'numeric', 'min:0'],
            'comentario' => ['nullable', 'string', 'max:2000'],
        ]);

        $nuevoEstado = $datos['estado_servicio'];
        $diagnostico = array_key_exists('diagnostico', $datos)
            ? $datos['diagnostico']
            : $servicio->diagnostico;
        $precio = array_key_exists('precio', $datos)
            ? $datos['precio']
            : $servicio->precio;

        if ($servicio->estado_servicio === 'Cotización pendiente'
            && $nuevoEstado === 'Cotización pendiente'
            && $precio !== null) {
            $nuevoEstado = 'En autorización';
        }

        if ($nuevoEstado === 'En autorización' && $precio === null) {
            throw ValidationException::withMessages([
                'estado_servicio' => 'Registra el precio antes de solicitar autorización.',
            ]);
        }

        if ($servicio->estado_servicio === 'En diagnóstico'
            && $nuevoEstado === 'En autorización'
            && ! filled($diagnostico)) {
            throw ValidationException::withMessages([
                'diagnostico' => 'Registra el diagnóstico técnico antes de enviar la cotización.',
            ]);
        }

        if ($nuevoEstado === 'En reparación' && $servicio->autorizacion !== 'Aprobado') {
            throw ValidationException::withMessages([
                'estado_servicio' => 'El cliente debe autorizar la cotización antes de iniciar la reparación.',
            ]);
        }

        DB::transaction(function () use (
            $datos,
            $servicio,
            $nuevoEstado,
            $diagnostico,
            $precio,
            $servicioRechazado
        ): void {
            $estadoAnterior = $servicio->estado_servicio;
            $precioAnterior = $servicio->precio;
            $diagnosticoAnterior = $servicio->diagnostico;

            if (! $servicioRechazado) {
                $servicio->diagnostico = $diagnostico;
                $servicio->precio = $precio;
                $servicio->saldo = $precio === null
                    ? null
                    : max(0, (float) $precio - (float) $servicio->anticipo);
            }
            $servicio->estado_servicio = $nuevoEstado;

            if ($nuevoEstado === 'En autorización' && $estadoAnterior !== 'En autorización') {
                $servicio->autorizacion = 'Pendiente';
            } elseif ($estadoAnterior === 'En autorización'
                && ($precioAnterior != $precio || $diagnosticoAnterior !== $diagnostico)) {
                $servicio->autorizacion = 'Pendiente';
            }

            $servicio->save();

            $comentarios = array_filter([
                $estadoAnterior !== $nuevoEstado ? 'Estado actualizado: '.$nuevoEstado.'.' : null,
                ! $servicioRechazado && $diagnosticoAnterior !== $diagnostico ? 'Diagnóstico actualizado.' : null,
                ! $servicioRechazado && $precioAnterior != $precio ? 'Cotización actualizada.' : null,
                filled($datos['comentario'] ?? null) ? trim($datos['comentario']) : null,
            ]);

            if ($comentarios !== []) {
                HistorialServicio::create([
                    'servicio_id' => $servicio->id,
                    'estado_anterior' => $estadoAnterior,
                    'estado_nuevo' => $nuevoEstado,
                    'comentario' => implode(' ', $comentarios),
                ]);
            }

        });

        app(HojaServicioPdf::class)->guardar($servicio->fresh()->load('cliente'));

        return back()->with('success', 'El seguimiento del servicio fue actualizado.');
    }

    public function responder(Request $request, Servicio $servicio): RedirectResponse
    {
        $datos = $request->validate([
            'mensaje' => ['required', 'string', 'max:2000'],
        ]);

        $servicio->mensajes()->create([
            'user_id' => $request->user()->id,
            'remitente' => 'ti',
            'mensaje' => $datos['mensaje'],
        ]);

        return back()->with('success', 'La respuesta fue enviada al cliente.');
    }

    private function estadosDisponibles(Servicio $servicio): array
    {
        if ($servicio->autorizacion === 'Rechazado'
            && in_array($servicio->estado_servicio, ['Pendiente de recolección', 'Entregado'], true)) {
            return $servicio->estado_servicio === 'Pendiente de recolección'
                ? ['Pendiente de recolección', 'Entregado']
                : ['Entregado'];
        }

        return match ($servicio->estado_servicio) {
            'En diagnóstico' => ['En diagnóstico', 'En autorización', 'Cancelado'],
            'Cotización pendiente' => ['Cotización pendiente', 'En autorización', 'Cancelado'],
            'En autorización' => $servicio->autorizacion === 'Aprobado'
                ? ['En autorización', 'En reparación', 'Cancelado']
                : ['En autorización', 'Cancelado'],
            'En reparación' => ['En reparación', 'Seguimiento final', 'Cancelado'],
            'Seguimiento final' => ['Seguimiento final', 'Listo para entregar', 'Cancelado'],
            'Listo para entregar' => ['Listo para entregar', 'Entregado'],
            'Entregado' => ['Entregado'],
            'Pendiente de recolección' => ['Pendiente de recolección'],
            'Cancelado' => ['Cancelado'],
            default => [$servicio->estado_servicio],
        };
    }

    private function urlWhatsAppEntrega(Servicio $servicio, string $urlCliente): ?string
    {
        if ($servicio->estado_servicio !== 'Entregado' || ! $servicio->cliente->telefono) {
            return null;
        }

        $google = config('services.customer_reviews.google');
        $tienda = config('services.customer_reviews.store');
        if (! $google && ! $tienda) {
            return null;
        }

        $mensaje = 'Hola '.$servicio->cliente->nombre
            .', tu equipo '.$servicio->folio.' ya fue entregado. '
            .'Gracias por confiar en PCS Solutions TI. '
            .'¿Nos compartirías tu opinión?';
        if ($google) {
            $mensaje .= "\nGoogle Maps: ".$google;
        }
        if ($tienda) {
            $mensaje .= "\nNuestra tienda: ".$tienda;
        }
        $mensaje .= "\nSeguimiento del servicio: ".$urlCliente;

        return $this->crearUrlWhatsApp($servicio, $mensaje);
    }

    private function urlWhatsAppSeguimiento(Servicio $servicio, string $urlCliente): ?string
    {
        if (! $servicio->cliente->telefono) {
            return null;
        }

        $mensaje = 'Hola '.$servicio->cliente->nombre
            .', aquí puedes consultar el avance del servicio '.$servicio->folio
            .' y enviarnos preguntas: '.$urlCliente;

        return $this->crearUrlWhatsApp($servicio, $mensaje);
    }

    private function crearUrlWhatsApp(Servicio $servicio, string $mensaje): ?string
    {
        $telefono = preg_replace('/\D+/', '', $servicio->cliente->telefono ?? '');
        if ($telefono === '') {
            return null;
        }

        $prefijo = preg_replace('/\D+/', '', config('services.customer_reviews.country_code', '52'));
        if ($prefijo !== '' && ! Str::startsWith($telefono, $prefijo)) {
            $telefono = $prefijo.$telefono;
        }

        return 'https://wa.me/'.$telefono.'?text='.urlencode($mensaje);
    }
}

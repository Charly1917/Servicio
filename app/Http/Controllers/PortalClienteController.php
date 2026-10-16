<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\HistorialServicio;
use App\Models\Servicio;
use App\Services\HojaServicioPdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PortalClienteController extends Controller
{
    public function mostrar(Request $request, string $token): Response
    {
        $cliente = $this->buscarCliente($token);
        $idsClientes = $this->idsClientesRelacionados($cliente);
        $servicios = Servicio::query()
            ->whereIn('cliente_id', $idsClientes)
            ->with([
                'historial' => fn ($query) => $query->oldest(),
                'mensajes' => fn ($query) => $query->oldest(),
            ])
            ->latest()
            ->latest('id')
            ->get();

        $servicioId = $request->query('servicio');
        if ($servicioId !== null) {
            abort_unless(is_string($servicioId) && ctype_digit($servicioId), 404);
            $servicioSeleccionado = $servicios->firstWhere('id', (int) $servicioId);
            abort_unless($servicioSeleccionado, 404);
        } else {
            $servicioSeleccionado = $servicios->first();
        }

        $telefonoWhatsApp = preg_replace(
            '/\D+/',
            '',
            (string) config('services.customer_reviews.business_phone', '')
        );
        $prefijoWhatsApp = preg_replace(
            '/\D+/',
            '',
            (string) config('services.customer_reviews.country_code', '52')
        );
        if ($telefonoWhatsApp !== '' && $prefijoWhatsApp !== ''
            && ! str_starts_with($telefonoWhatsApp, $prefijoWhatsApp)) {
            $telefonoWhatsApp = $prefijoWhatsApp.$telefonoWhatsApp;
        }
        $whatsappBusinessUrl = $telefonoWhatsApp !== '' && $servicioSeleccionado
            ? 'https://wa.me/'.$telefonoWhatsApp.'?text='.urlencode(
                'Hola, necesito ayuda con mi servicio '.$servicioSeleccionado->folio.'.'
            )
            : null;

        return response()->view('portal.servicio', [
            'cliente' => $cliente,
            'servicios' => $servicios,
            'servicio' => $servicioSeleccionado,
            'token' => $token,
            'whatsappBusinessUrl' => $whatsappBusinessUrl,
            'googleReviewUrl' => config('services.customer_reviews.google'),
            'storeReviewUrl' => config('services.customer_reviews.store'),
        ])->header('Cache-Control', 'private, no-store')
            ->header('Referrer-Policy', 'no-referrer');
    }

    public function enviarMensaje(
        Request $request,
        string $token,
        Servicio $servicio
    ): RedirectResponse {
        $cliente = $this->buscarCliente($token);
        abort_unless(
            in_array((int) $servicio->cliente_id, $this->idsClientesRelacionados($cliente), true),
            404
        );

        $datos = $request->validate([
            'mensaje' => ['required', 'string', 'max:2000'],
        ]);

        $servicio->mensajes()->create([
            'remitente' => 'cliente',
            'mensaje' => $datos['mensaje'],
        ]);

        return redirect()->route('portal.servicio', [
            'token' => $token,
            'servicio' => $servicio->id,
        ])->with('success', 'Tu mensaje fue enviado al equipo técnico.');
    }

    public function responderAutorizacion(
        Request $request,
        string $token,
        Servicio $servicio
    ): RedirectResponse {
        $cliente = $this->buscarCliente($token);
        abort_unless(
            in_array((int) $servicio->cliente_id, $this->idsClientesRelacionados($cliente), true),
            404
        );

        $datos = $request->validate([
            'decision' => ['required', 'in:Aprobado,Rechazado'],
        ]);

        abort_unless(
            $servicio->estado_servicio === 'En autorización'
                && $servicio->autorizacion === 'Pendiente',
            409,
            'Esta cotización ya no está pendiente de autorización.'
        );

        abort_unless(
            $datos['decision'] !== 'Rechazado' || (float) $servicio->anticipo === 0.0,
            409,
            'Este servicio tiene un anticipo registrado. Contacta al equipo técnico para coordinarlo.'
        );

        $estadoAnterior = $servicio->estado_servicio;
        $estadoNuevo = $datos['decision'] === 'Aprobado'
            ? 'En reparación'
            : 'Pendiente de recolección';
        $mensaje = $datos['decision'] === 'Aprobado'
            ? 'El cliente autorizó la cotización. El equipo pasa a reparación.'
            : 'No autorizaste la cotización. El equipo está pendiente de recolección; no se realizará la reparación ni se cobrará el servicio.';

        DB::transaction(function () use ($datos, $servicio, $estadoAnterior, $estadoNuevo, $mensaje): void {
            $servicio->autorizacion = $datos['decision'];
            $servicio->estado_servicio = $estadoNuevo;

            if ($datos['decision'] === 'Rechazado') {
                $servicio->saldo = 0;
            }

            $servicio->save();

            HistorialServicio::create([
                'servicio_id' => $servicio->id,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $estadoNuevo,
                'comentario' => $mensaje,
            ]);

            $servicio->mensajes()->create([
                'remitente' => 'ti',
                'mensaje' => $mensaje,
            ]);
        });

        app(HojaServicioPdf::class)->guardar($servicio->fresh()->load('cliente'));

        return redirect()->route('portal.servicio', [
            'token' => $token,
            'servicio' => $servicio->id,
        ])->with(
            'success',
            $datos['decision'] === 'Aprobado'
                ? 'Autorizaste la cotización. El servicio pasó automáticamente a reparación.'
                : 'Registramos tu decisión. No se realizará la reparación ni se cobrará el servicio; puedes pasar por tu equipo.'
        );
    }

    private function buscarCliente(string $token): Cliente
    {
        abort_unless(preg_match('/^[A-Za-z0-9]{64}$/', $token) === 1, 404);

        return Cliente::query()
            ->where('portal_token_hash', hash('sha256', $token))
            ->firstOrFail();
    }

    private function idsClientesRelacionados(Cliente $cliente): array
    {
        $telefonoNormalizado = preg_replace('/\D+/', '', $cliente->telefono ?? '');
        if ($telefonoNormalizado === '') {
            return [(int) $cliente->id];
        }

        return Cliente::query()
            ->whereRaw('LOWER(TRIM(nombre)) = ?', [mb_strtolower(trim($cliente->nombre))])
            ->get()
            ->filter(fn (Cliente $coincidente) => preg_replace(
                '/\D+/',
                '',
                $coincidente->telefono ?? ''
            ) === $telefonoNormalizado)
            ->map(fn (Cliente $coincidente) => (int) $coincidente->id)
            ->values()
            ->all();
    }
}

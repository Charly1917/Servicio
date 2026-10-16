<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ServicioControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $usuarioTi;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->usuarioTi = User::factory()->create();
        $this->actingAs($this->usuarioTi);
    }

    public function test_service_index_filters_services_by_stage_and_separates_rejected_authorizations(): void
    {
        $cliente = Cliente::create([
            'nombre' => 'Cliente filtros',
            'telefono' => '5551234567',
        ]);
        $servicios = [
            ['TI-FILTRO-DIAG', 'En diagnóstico', 'Pendiente'],
            ['TI-FILTRO-COTI', 'Cotización pendiente', 'Pendiente'],
            ['TI-FILTRO-AUTO', 'En autorización', 'Pendiente'],
            ['TI-FILTRO-REPA', 'En reparación', 'Aprobado'],
            ['TI-FILTRO-SEGU', 'Seguimiento final', 'Aprobado'],
            ['TI-FILTRO-LIST', 'Listo para entregar', 'Aprobado'],
            ['TI-FILTRO-RECO', 'Pendiente de recolección', 'Aprobado'],
            ['TI-FILTRO-ENTR', 'Entregado', 'Aprobado'],
            ['TI-FILTRO-NORE', 'Pendiente de recolección', 'Rechazado'],
            ['TI-FILTRO-NOEN', 'Entregado', 'Rechazado'],
            ['TI-FILTRO-CANC', 'Cancelado', 'Pendiente'],
        ];

        foreach ($servicios as $indice => [$folio, $estado, $autorizacion]) {
            Servicio::create([
                'cliente_id' => $cliente->id,
                'folio' => $folio,
                'tipo_equipo' => 'Laptop',
                'marca' => 'Dell',
                'fecha_recepcion' => now()->subMinutes($indice),
                'estado_fisico' => 'Bueno',
                'estado_servicio' => $estado,
                'autorizacion' => $autorizacion,
                'precio' => 0,
                'anticipo' => 0,
                'saldo' => 0,
            ]);
        }

        $this->get(route('ti.servicios.index'))
            ->assertOk()
            ->assertSee('Todos <span class="group-count">11</span>', false)
            ->assertSee('Diagnósticos <span class="group-count">2</span>', false)
            ->assertSee('En reparación <span class="group-count">2</span>', false)
            ->assertSee('Pendientes de recolección <span class="group-count">2</span>', false)
            ->assertSee('Entregados <span class="group-count">1</span>', false)
            ->assertSee('No autorizados <span class="group-count">2</span>', false);

        $this->get(route('ti.servicios.index', ['grupo' => 'diagnostico']))
            ->assertOk()
            ->assertSee('TI-FILTRO-DIAG')
            ->assertSee('TI-FILTRO-COTI')
            ->assertDontSee('TI-FILTRO-AUTO');

        $this->get(route('ti.servicios.index', ['grupo' => 'recoleccion']))
            ->assertOk()
            ->assertSee('TI-FILTRO-LIST')
            ->assertSee('TI-FILTRO-RECO')
            ->assertDontSee('TI-FILTRO-NORE');

        $this->get(route('ti.servicios.index', ['grupo' => 'reparacion']))
            ->assertOk()
            ->assertSee('TI-FILTRO-REPA')
            ->assertSee('TI-FILTRO-SEGU')
            ->assertDontSee('TI-FILTRO-LIST');

        $this->get(route('ti.servicios.index', ['grupo' => 'no_autorizados']))
            ->assertOk()
            ->assertSee('TI-FILTRO-NORE')
            ->assertSee('TI-FILTRO-NOEN')
            ->assertDontSee('TI-FILTRO-RECO')
            ->assertDontSee('TI-FILTRO-ENTR');
    }

    public function test_diagnostic_service_keeps_price_pending_and_records_no_accessories(): void
    {
        $datos = [
            'nombre' => 'Cliente de prueba',
            'tipo_equipo' => 'Laptop',
            'marca' => 'Dell',
            'estado_fisico' => 'Bueno',
            'accesorios' => 'Cargador accidental',
            'sin_accesorios' => '1',
            'servicios_solicitados' => ['Diagnóstico'],
            'falla_reportada' => 'Se apaga después de unos minutos.',
            'precio' => '999',
            'anticipo' => '0',
        ];

        $this->post(route('ti.servicios.guardar'), array_diff_key($datos, [
            'falla_reportada' => true,
        ]))->assertSessionHasErrors('falla_reportada');

        $response = $this->post(route('ti.servicios.guardar'), $datos);

        $response->assertRedirect();

        $servicio = Servicio::firstOrFail();
        $this->assertSame('Sin accesorios', $servicio->accesorios);
        $this->assertNull($servicio->precio);
        $this->assertNull($servicio->saldo);
        $this->assertSame('En diagnóstico', $servicio->estado_servicio);
        $this->assertSame('Pendiente', $servicio->autorizacion);
        $this->assertSame('Se apaga después de unos minutos.', $servicio->falla_reportada);
        Storage::disk('local')->assertExists(
            'hojas-servicio/'.$servicio->fecha_recepcion->format('Y').'/cliente-de-prueba-id-'
                .$servicio->cliente_id.'/'.$servicio->folio.'.pdf'
        );
        $this->get(route('ti.servicios.mostrar', $servicio))
            ->assertOk()
            ->assertSee('Falla que reporta:')
            ->assertSee('Se apaga después de unos minutos.')
            ->assertSee(route('ti.servicios.imprimir', $servicio));
    }

    public function test_price_accepted_at_reception_starts_repair_without_repeated_approval(): void
    {
        $response = $this->post(route('ti.servicios.guardar'), [
            'nombre' => 'Cliente de prueba',
            'telefono' => '5551234567',
            'tipo_equipo' => 'Laptop',
            'marca' => 'Dell',
            'estado_fisico' => 'Bueno',
            'accesorios' => 'Cargador',
            'servicios_solicitados' => ['Limpieza interna'],
            'precio' => '500',
            'anticipo' => '0',
            'accion' => 'imprimir',
        ])->assertRedirect();

        $this->assertDatabaseHas('servicios', [
            'accesorios' => 'Cargador',
            'precio' => '500.00',
            'anticipo' => '0.00',
            'saldo' => '500.00',
            'estado_servicio' => 'En reparación',
            'autorizacion' => 'Aprobado',
        ]);
        $servicio = Servicio::firstOrFail();
        $token = $servicio->cliente->portal_token;
        $this->get(route('portal.servicio', $token))
            ->assertOk()
            ->assertDontSee('Tu autorización es necesaria');

        $this->patch(route('ti.servicios.actualizar', $servicio), [
            'estado_servicio' => 'Seguimiento final',
            'precio' => '550',
            'diagnostico' => 'Servicio realizado.',
        ])->assertRedirect();
        $servicio->refresh();
        $this->assertSame('Seguimiento final', $servicio->estado_servicio);
        $this->assertSame('Aprobado', $servicio->autorizacion);
        $this->assertSame('550.00', $servicio->precio);
        $this->post(route('portal.autorizacion', [
            'token' => $token,
            'servicio' => $servicio,
        ]), ['decision' => 'Aprobado'])->assertStatus(409);

        $this->assertSame(route('ti.servicios.imprimir', $servicio), $response->headers->get('Location'));
        $this->get(route('ti.servicios.imprimir', $servicio))
            ->assertOk()
            ->assertSee('Imprimir hoja')
            ->assertSee($servicio->folio)
            ->assertDontSee('Contactar por WhatsApp')
            ->assertDontSee('https://wa.me/525551234567?text=')
            ->assertSee('$ 550.00')
            ->assertDontSee('Guardar PDF');
        Storage::disk('local')->assertExists(
            'hojas-servicio/'.$servicio->fecha_recepcion->format('Y').'/cliente-de-prueba-5551234567/'
                .$servicio->folio.'.pdf'
        );
        $this->get('/ti/servicios/'.$servicio->id.'/pdf')->assertNotFound();
    }

    public function test_printed_service_sheet_displays_reception_time_in_mexico_city_timezone(): void
    {
        $cliente = Cliente::create([
            'nombre' => 'Cliente hora local',
            'telefono' => '5551234567',
        ]);
        $servicio = Servicio::create([
            'cliente_id' => $cliente->id,
            'folio' => 'TI-2026-HORA',
            'tipo_equipo' => 'Laptop',
            'marca' => 'Dell',
            'fecha_recepcion' => \Illuminate\Support\Carbon::create(2026, 10, 13, 16, 35, 0, 'UTC'),
            'estado_fisico' => 'Bueno',
            'estado_servicio' => 'En reparación',
            'precio' => 500,
            'anticipo' => 0,
            'saldo' => 500,
            'autorizacion' => 'Aprobado',
        ]);

        $this->get(route('ti.servicios.imprimir', $servicio))
            ->assertOk()
            ->assertSee('13/10/2026 10:35');
    }

    public function test_pending_quote_waits_for_ti_price_and_client_decision(): void
    {
        $this->post(route('ti.servicios.guardar'), [
            'nombre' => 'Cliente cotización',
            'tipo_equipo' => 'Laptop',
            'marca' => 'Dell',
            'estado_fisico' => 'Bueno',
            'servicios_solicitados' => ['Limpieza interna'],
            'cotizacion_pendiente' => '1',
        ])->assertRedirect();

        $servicio = Servicio::firstOrFail();
        $token = $servicio->cliente->portal_token;
        $this->assertSame('Cotización pendiente', $servicio->estado_servicio);
        $this->assertNull($servicio->precio);
        $this->get(route('portal.servicio', $token))
            ->assertOk()
            ->assertSee('Pendiente de cotización')
            ->assertDontSee('Tu autorización es necesaria');

        $this->patch(route('ti.servicios.actualizar', $servicio), [
            'estado_servicio' => 'Cotización pendiente',
            'precio' => '750',
            'diagnostico' => 'Requiere mantenimiento.',
        ])->assertRedirect();

        $servicio->refresh();
        $this->assertSame('En autorización', $servicio->estado_servicio);
        $this->assertSame('Pendiente', $servicio->autorizacion);
        $this->assertSame('750.00', $servicio->precio);
        $this->get(route('portal.servicio', $token))
            ->assertOk()
            ->assertSee('Tu autorización es necesaria')
            ->assertSee('$750.00 MXN');

        $this->post(route('portal.autorizacion', [
            'token' => $token,
            'servicio' => $servicio,
        ]), ['decision' => 'Aprobado'])->assertRedirect();

        $this->assertSame('En reparación', $servicio->fresh()->estado_servicio);
    }

    public function test_ti_follow_up_only_offers_current_or_next_states_and_hides_completed_diagnosis(): void
    {
        $cliente = Cliente::create([
            'nombre' => 'Cliente en reparación',
            'telefono' => '5551234567',
        ]);
        $servicio = Servicio::create([
            'cliente_id' => $cliente->id,
            'folio' => 'TI-2026-TEST',
            'tipo_equipo' => 'Laptop',
            'marca' => 'Dell',
            'fecha_recepcion' => now(),
            'estado_fisico' => 'Bueno',
            'diagnostico' => 'Requiere cambio de unidad.',
            'estado_servicio' => 'En reparación',
            'precio' => 850,
            'anticipo' => 0,
            'saldo' => 850,
            'autorizacion' => 'Aprobado',
        ]);

        $this->get(route('ti.servicios.mostrar', $servicio))
            ->assertOk()
            ->assertSee('value="En reparación" selected', false)
            ->assertSee('value="Seguimiento final"', false)
            ->assertDontSee('value="En diagnóstico"', false)
            ->assertDontSee('value="Cotización pendiente"', false)
            ->assertDontSee('value="En autorización"', false)
            ->assertDontSee('id="diagnostico"', false)
            ->assertDontSee('Requiere cambio de unidad.');
    }

    public function test_diagnostic_cannot_be_combined_with_other_requested_services(): void
    {
        $this->post(route('ti.servicios.guardar'), [
            'nombre' => 'Cliente diagnóstico',
            'tipo_equipo' => 'Laptop',
            'marca' => 'Dell',
            'estado_fisico' => 'Bueno',
            'servicios_solicitados' => ['Diagnóstico', 'Limpieza interna'],
            'falla_reportada' => 'No enciende.',
        ])->assertSessionHasErrors('servicios_solicitados');

        $this->assertDatabaseCount('servicios', 0);
    }

    public function test_ti_service_list_is_filtered_by_year_searchable_and_paginated_by_ten(): void
    {
        $cliente = Cliente::create([
            'nombre' => 'María López',
            'telefono' => '5551234567',
        ]);

        for ($numero = 1; $numero <= 12; $numero++) {
            Servicio::create([
                'cliente_id' => $cliente->id,
                'folio' => sprintf('TI-2026-%04d', $numero),
                'tipo_equipo' => 'Laptop',
                'marca' => 'Dell',
                'fecha_recepcion' => now()->setDate(2026, 6, $numero),
                'estado_fisico' => 'Bueno',
                'estado_servicio' => 'En reparación',
                'precio' => 500,
                'anticipo' => 0,
                'saldo' => 500,
                'autorizacion' => 'Aprobado',
            ]);
        }

        $this->get(route('ti.servicios.index', ['anio' => '2026']))
            ->assertOk()
            ->assertSee('Carpeta de servicios · 2026')
            ->assertSee('TI-2026-0012')
            ->assertDontSee('TI-2026-0001')
            ->assertSee('page=2');

        $this->get(route('ti.servicios.index', ['anio' => '2026', 'page' => 2]))
            ->assertOk()
            ->assertSee('TI-2026-0001')
            ->assertDontSee('TI-2026-0012');

        $this->get(route('ti.servicios.index', ['anio' => '2026', 'buscar' => 'María López']))
            ->assertOk()
            ->assertSee('Resultados para')
            ->assertSee('12');

        $this->get(route('ti.servicios.index', ['anio' => '2026', 'buscar' => 'TI-2026-0007']))
            ->assertOk()
            ->assertSee('TI-2026-0007')
            ->assertDontSee('TI-2026-0006');
    }

    public function test_rejected_quote_sets_no_charge_and_pending_pickup_with_automatic_message(): void
    {
        $this->post(route('ti.servicios.guardar'), [
            'nombre' => 'Cliente que rechaza',
            'tipo_equipo' => 'Laptop',
            'marca' => 'Dell',
            'estado_fisico' => 'Bueno',
            'servicios_solicitados' => ['Limpieza interna'],
            'cotizacion_pendiente' => '1',
            'anticipo' => '0',
        ])->assertRedirect();

        $servicio = Servicio::firstOrFail();
        $this->patch(route('ti.servicios.actualizar', $servicio), [
            'estado_servicio' => 'Cotización pendiente',
            'precio' => '500',
        ])->assertRedirect();

        $servicio->refresh();
        $token = $servicio->cliente->portal_token;
        $portal = route('portal.servicio', [
            'token' => $token,
            'servicio' => $servicio->id,
        ]);

        $this->post(route('portal.autorizacion', [
            'token' => $token,
            'servicio' => $servicio,
        ]), [
            'decision' => 'Rechazado',
        ])->assertRedirect($portal);

        $servicio->refresh();
        $this->assertSame('Rechazado', $servicio->autorizacion);
        $this->assertSame('Pendiente de recolección', $servicio->estado_servicio);
        $this->assertSame('500.00', $servicio->precio);
        $this->assertSame('0.00', $servicio->saldo);
        $this->assertSame('0.00', $servicio->anticipo);

        $mensaje = 'No autorizaste la cotización. El equipo está pendiente de recolección; no se realizará la reparación ni se cobrará el servicio.';
        $this->assertDatabaseHas('mensajes_servicio', [
            'servicio_id' => $servicio->id,
            'remitente' => 'ti',
            'mensaje' => $mensaje,
        ]);

        $this->get(route('portal.servicio', $token))
            ->assertOk()
            ->assertSee('Tu equipo está pendiente de recolección')
            ->assertSeeText('puedes pasar por tu equipo')
            ->assertSeeText('No autorizaste la cotización.')
            ->assertSee('Sin cobro')
            ->assertSee($mensaje);
        $this->get(route('ti.servicios.mostrar', $servicio))
            ->assertOk()
            ->assertSee('Pendiente de recolección')
            ->assertSee('Entregado')
            ->assertDontSee('Seguimiento final');

        $this->patch(route('ti.servicios.actualizar', $servicio), [
            'estado_servicio' => 'En reparación',
        ])->assertSessionHasErrors('estado_servicio');

        $this->patch(route('ti.servicios.actualizar', $servicio), [
            'estado_servicio' => 'Entregado',
        ])->assertRedirect();
        $this->assertSame('Entregado', $servicio->fresh()->estado_servicio);

        $this->get(route('ti.servicios.mostrar', $servicio))
            ->assertOk()
            ->assertSee('value="Entregado" selected', false)
            ->assertDontSee('value="Pendiente de recolección"');

        $this->post(route('portal.autorizacion', [
            'token' => $token,
            'servicio' => $servicio,
        ]), [
            'decision' => 'Aprobado',
        ])->assertStatus(409);
    }

    public function test_client_portal_only_exposes_service_with_matching_private_token(): void
    {
        $datos = [
            'nombre' => 'Cliente uno',
            'telefono' => '5551234567',
            'tipo_equipo' => 'Laptop',
            'marca' => 'Dell',
            'estado_fisico' => 'Bueno',
            'servicios_solicitados' => ['Diagnóstico'],
            'falla_reportada' => 'El equipo no enciende.',
            'anticipo' => '0',
        ];
        $this->post(route('ti.servicios.guardar'), $datos)->assertRedirect();

        $servicio = Servicio::firstOrFail();
        $token = $servicio->cliente->portal_token;
        config([
            'services.customer_reviews.business_phone' => '55 5123-4567',
            'services.customer_reviews.country_code' => '52',
        ]);

        $this->post(route('ti.servicios.guardar'), array_replace($datos, [
            'tipo_equipo' => 'Monitor',
            'marca' => 'LG',
            'servicios_solicitados' => ['Limpieza'],
            'precio' => '300',
        ]))->assertRedirect();
        $segundoServicio = Servicio::latest('id')->firstOrFail();
        Storage::disk('local')->assertExists(
            'hojas-servicio/'.$servicio->fecha_recepcion->format('Y').'/cliente-uno-5551234567/'.$servicio->folio.'.pdf'
        );
        Storage::disk('local')->assertExists(
            'hojas-servicio/'.$segundoServicio->fecha_recepcion->format('Y').'/cliente-uno-5551234567/'.$segundoServicio->folio.'.pdf'
        );

        $this->post(route('ti.servicios.guardar'), array_replace($datos, [
            'nombre' => 'Otro cliente',
            'telefono' => '5559999999',
        ]))->assertRedirect();
        $servicioDeOtroCliente = Servicio::latest('id')->firstOrFail();

        $this->get(route('portal.servicio', $token))
            ->assertOk()
            ->assertSee('Cliente uno')
            ->assertSee('Contactar por WhatsApp')
            ->assertSee(
                'https://wa.me/525551234567?text='
                    .urlencode('Hola, necesito ayuda con mi servicio '.$segundoServicio->folio.'.'),
                false
            )
            ->assertSee($servicio->folio)
            ->assertSee($segundoServicio->folio)
            ->assertDontSee($servicioDeOtroCliente->folio);

        $this->get(route('portal.servicio', [
            'token' => $token,
            'servicio' => $servicio->id,
        ]))
            ->assertOk()
            ->assertViewHas('servicio', fn (Servicio $seleccionado) => $seleccionado->is($servicio))
            ->assertSee('Folio '.$servicio->folio)
            ->assertSee('Dell')
            ->assertDontSee('Monitor · LG');

        $this->get(route('portal.servicio', [
            'token' => $token,
            'servicio' => $servicioDeOtroCliente->id,
        ]))->assertNotFound();

        $this->post(route('portal.mensajes', [
            'token' => $token,
            'servicio' => $servicioDeOtroCliente,
        ]), [
            'mensaje' => 'Este mensaje no debe cruzar clientes.',
        ])->assertNotFound();

        $this->get(route('portal.servicio', Str::random(64)))
            ->assertNotFound()
            ->assertDontSee($servicio->folio);
    }

    public function test_matching_legacy_customer_records_are_merged_without_losing_services(): void
    {
        $clienteAntiguo = Cliente::create([
            'nombre' => 'Cliente legado',
            'telefono' => '555-222-3333',
        ]);
        $servicioAntiguo = Servicio::create([
            'cliente_id' => $clienteAntiguo->id,
            'folio' => 'LEGACY-1',
            'tipo_equipo' => 'Laptop',
            'marca' => 'Dell',
            'fecha_recepcion' => now(),
            'estado_fisico' => 'Bueno',
            'estado_servicio' => 'En diagnóstico',
            'precio' => null,
            'anticipo' => 0,
            'saldo' => null,
            'autorizacion' => 'Pendiente',
        ]);

        $clienteDuplicado = Cliente::create([
            'nombre' => ' cliente legado ',
            'telefono' => '(555) 222-3333',
        ]);
        $servicioDuplicado = Servicio::create([
            'cliente_id' => $clienteDuplicado->id,
            'folio' => 'LEGACY-2',
            'tipo_equipo' => 'Monitor',
            'marca' => 'LG',
            'fecha_recepcion' => now(),
            'estado_fisico' => 'Bueno',
            'estado_servicio' => 'En diagnóstico',
            'precio' => null,
            'anticipo' => 0,
            'saldo' => null,
            'autorizacion' => 'Pendiente',
        ]);

        $this->post(route('ti.servicios.guardar'), [
            'nombre' => 'Cliente legado',
            'telefono' => '555 222 3333',
            'tipo_equipo' => 'Laptop',
            'marca' => 'Lenovo',
            'estado_fisico' => 'Bueno',
            'servicios_solicitados' => ['Diagnóstico'],
            'falla_reportada' => 'El equipo se reinicia.',
            'anticipo' => '0',
        ])->assertRedirect();

        $this->assertDatabaseMissing('clientes', ['id' => $clienteDuplicado->id]);
        $this->assertDatabaseHas('servicios', [
            'id' => $servicioAntiguo->id,
            'cliente_id' => $clienteAntiguo->id,
        ]);
        $this->assertDatabaseHas('servicios', [
            'id' => $servicioDuplicado->id,
            'cliente_id' => $clienteAntiguo->id,
        ]);

        $token = $clienteAntiguo->fresh()->portal_token;
        $this->get(route('portal.servicio', $token))
            ->assertOk()
            ->assertSee('LEGACY-1')
            ->assertSee('LEGACY-2');
    }

    public function test_customer_can_ask_a_question_and_authorize_quote_before_repair(): void
    {
        $this->post(route('ti.servicios.guardar'), [
            'nombre' => 'Cliente seguimiento',
            'tipo_equipo' => 'Laptop',
            'marca' => 'Dell',
            'estado_fisico' => 'Bueno',
            'servicios_solicitados' => ['Diagnóstico'],
            'falla_reportada' => 'El equipo se apaga.',
            'anticipo' => '0',
        ])->assertRedirect();

        $servicio = Servicio::firstOrFail();
        $token = $servicio->cliente->portal_token;
        $portal = route('portal.servicio', [
            'token' => $token,
            'servicio' => $servicio->id,
        ]);

        $this->from($portal)->post(route('portal.mensajes', [
            'token' => $token,
            'servicio' => $servicio,
        ]), [
            'mensaje' => '¿Cómo va el diagnóstico?',
        ])->assertRedirect($portal);

        $this->patch(route('ti.servicios.actualizar', $servicio), [
            'estado_servicio' => 'En autorización',
            'diagnostico' => 'Requiere cambio de unidad.',
            'precio' => '850',
            'comentario' => 'Diagnóstico terminado.',
        ])->assertRedirect();

        $servicio->refresh();
        $this->assertSame('En autorización', $servicio->estado_servicio);
        $this->assertSame('Pendiente', $servicio->autorizacion);

        $this->from($portal)->post(route('portal.autorizacion', [
            'token' => $token,
            'servicio' => $servicio,
        ]), [
            'decision' => 'Aprobado',
        ])->assertRedirect($portal);

        $servicio->refresh();
        $this->assertSame('Aprobado', $servicio->autorizacion);

        $this->patch(route('ti.servicios.actualizar', $servicio), [
            'estado_servicio' => 'En reparación',
            'diagnostico' => 'Requiere cambio de unidad.',
            'precio' => '850',
        ])->assertRedirect();

        $servicio->refresh();
        $this->assertSame('En reparación', $servicio->estado_servicio);
        $this->get($portal)
            ->assertOk()
            ->assertSee('¿Cómo va el diagnóstico?')
            ->assertSee('PCS Solutions TI');
    }

    public function test_technician_routes_require_login(): void
    {
        auth()->logout();

        $this->get(route('ti.servicios.index'))->assertRedirect(route('login'));
        $this->get(route('ti.servicios.crear'))->assertRedirect(route('login'));
        $this->get('/')->assertRedirect('/ti/ingreso');
    }

    public function test_ti_can_log_in_with_an_account(): void
    {
        auth()->logout();

        $this->post(route('ti.ingresar'), [
            'email' => $this->usuarioTi->email,
            'password' => 'password',
        ])->assertRedirect(route('ti.servicios.index'));

        $this->assertAuthenticatedAs($this->usuarioTi);
    }

    public function test_customer_cannot_be_moved_to_repair_without_authorization_and_can_receive_review_message(): void
    {
        config()->set('services.customer_reviews.google', 'https://maps.example/review');
        config()->set('services.customer_reviews.store', 'https://shop.example/opinion');

        $this->post(route('ti.servicios.guardar'), [
            'nombre' => 'Cliente autorizado',
            'telefono' => '5551234567',
            'tipo_equipo' => 'Laptop',
            'marca' => 'Dell',
            'estado_fisico' => 'Bueno',
            'servicios_solicitados' => ['Diagnóstico'],
            'falla_reportada' => 'El cliente reporta falla de encendido.',
            'anticipo' => '0',
        ])->assertRedirect();

        $servicio = Servicio::firstOrFail();
        $token = $servicio->cliente->portal_token;

        $this->patch(route('ti.servicios.actualizar', $servicio), [
            'estado_servicio' => 'En autorización',
            'diagnostico' => 'Cambio de unidad.',
            'precio' => '850',
        ])->assertRedirect();

        $this->patch(route('ti.servicios.actualizar', $servicio), [
            'estado_servicio' => 'En reparación',
            'diagnostico' => 'Cambio de unidad.',
            'precio' => '850',
        ])->assertSessionHasErrors('estado_servicio');

        $this->post(route('portal.autorizacion', [
            'token' => $token,
            'servicio' => $servicio,
        ]), [
            'decision' => 'Aprobado',
        ]);
        $this->patch(route('ti.servicios.actualizar', $servicio), [
            'estado_servicio' => 'En reparación',
            'diagnostico' => 'Cambio de unidad.',
            'precio' => '850',
        ])->assertRedirect();

        foreach (['Seguimiento final', 'Listo para entregar', 'Entregado'] as $estado) {
            $this->patch(route('ti.servicios.actualizar', $servicio), [
                'estado_servicio' => $estado,
                'diagnostico' => 'Cambio de unidad.',
                'precio' => '850',
            ])->assertRedirect();
        }

        $this->get(route('ti.servicios.mostrar', $servicio))
            ->assertOk()
            ->assertSee('WhatsApp: pedir opinión')
            ->assertSee('Enviar enlace por WhatsApp');

        $this->get(route('portal.servicio', $token))
            ->assertOk()
            ->assertSee('Opinar en Google Maps')
            ->assertSee('Opinar en nuestra tienda');
    }
}

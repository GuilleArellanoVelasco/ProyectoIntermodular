<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcesoEstadosSeeder extends Seeder
{
    /**
     * Seed de los autómatas de estados para cada tipo de procedimiento
     *
     * Tipos de procedimiento:
     * 1 = LSO sin masa
     * 2 = LSO con plan de pagos
     * 3 = Otros (sin seguimiento de proceso)
     */
    public function run(): void
    {
        // ============================================
        // AUTÓMATA: LSO SIN MASA (tipo_procedimiento_id = 1)
        // ============================================
        $this->seedLsoSinMasa();

        // ============================================
        // AUTÓMATA: LSO CON PLAN DE PAGOS (tipo_procedimiento_id = 2)
        // ============================================
        $this->seedLsoConPlanPagos();
    }

    private function seedLsoConPlanPagos(): void
    {
        $tipoProcedimientoId = 2; // LSO con plan de pagos

        // Crear estados
        $estados = [
            // Estados del flujo principal
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'recopilar_docs',
                'nombre' => 'Recopilar Documentación',
                'descripcion' => 'Recopilación de documentación necesaria para iniciar el proceso',
                'tipo_accion' => 'gestor',
                'es_inicial' => true,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 1,
                'color' => 'primary',
                'icono' => 'document',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'presentar_solicitud',
                'nombre' => 'Presentar Solicitud',
                'descripcion' => 'Presentación de la solicitud ante el juzgado',
                'tipo_accion' => 'gestor',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 2,
                'color' => 'primary',
                'icono' => 'send',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'esperar_admision',
                'nombre' => 'Esperar Admisión',
                'descripcion' => 'Esperando resolución del juzgado sobre la admisión',
                'tipo_accion' => 'espera_juzgado',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 3,
                'color' => 'info',
                'icono' => 'clock',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'esperando_publicaciones',
                'nombre' => 'Esperando Publicaciones',
                'descripcion' => 'Pendiente publicación en BOE y Registro Público Concursal',
                'tipo_accion' => 'espera_tiempo',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 4,
                'color' => 'info',
                'icono' => 'newspaper',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'periodo_alegaciones',
                'nombre' => 'Período de Alegaciones',
                'descripcion' => 'Período de 15 días hábiles para presentar alegaciones (inicia con primera publicación)',
                'tipo_accion' => 'espera_tiempo',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 5,
                'color' => 'warning',
                'icono' => 'calendar',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'sin_alegaciones',
                'nombre' => 'Sin Alegaciones',
                'descripcion' => 'No se presentaron alegaciones durante el período',
                'tipo_accion' => 'decision',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 6,
                'color' => 'success',
                'icono' => 'check',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'con_alegaciones',
                'nombre' => 'Con Alegaciones',
                'descripcion' => 'Se presentaron alegaciones durante el período',
                'tipo_accion' => 'decision',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 6,
                'color' => 'warning',
                'icono' => 'alert',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'resolucion_alegaciones',
                'nombre' => 'Resolución de Alegaciones',
                'descripcion' => 'Preparar respuesta a las alegaciones presentadas',
                'tipo_accion' => 'gestor',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 7,
                'color' => 'primary',
                'icono' => 'edit',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'alegaciones_resueltas',
                'nombre' => 'Alegaciones Resueltas',
                'descripcion' => 'Esperando resolución del juzgado sobre las alegaciones',
                'tipo_accion' => 'espera_juzgado',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 8,
                'color' => 'info',
                'icono' => 'clock',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'presentar_solicitud_epi_plan',
                'nombre' => 'Presentar Solicitud EPI con Plan de Pagos',
                'descripcion' => 'Presentar solicitud de Exoneración del Pasivo Insatisfecho con plan de pagos',
                'tipo_accion' => 'gestor',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 9,
                'color' => 'primary',
                'icono' => 'document',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'esperar_epi_provisional',
                'nombre' => 'Esperar EPI Provisional',
                'descripcion' => 'Esperando resolución del EPI provisional con aprobación del plan de pagos',
                'tipo_accion' => 'espera_juzgado',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 10,
                'color' => 'info',
                'icono' => 'clock',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'cumplimiento_plan',
                'nombre' => 'Cumplimiento del Plan',
                'descripcion' => 'Período de cumplimiento del plan de pagos (hasta 5 años)',
                'tipo_accion' => 'espera_tiempo',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 11,
                'color' => 'info',
                'icono' => 'calendar',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'recurrir_modificar',
                'nombre' => 'Recurrir/Modificar',
                'descripcion' => 'Recurrir o solicitar modificación del plan de pagos denegado',
                'tipo_accion' => 'gestor',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 10,
                'color' => 'warning',
                'icono' => 'refresh',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'esperar_resolucion_recurso',
                'nombre' => 'Esperar Resolución del Recurso',
                'descripcion' => 'Esperando resolución del juzgado sobre el recurso',
                'tipo_accion' => 'espera_juzgado',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 10,
                'color' => 'info',
                'icono' => 'clock',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'presentar_informe',
                'nombre' => 'Presentar Informe',
                'descripcion' => 'Presentar informe de cumplimiento (cada 6 meses)',
                'tipo_accion' => 'gestor',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 12,
                'color' => 'primary',
                'icono' => 'document',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'esperar_valoracion',
                'nombre' => 'Esperar Valoración',
                'descripcion' => 'Esperando valoración del juzgado sobre el cumplimiento',
                'tipo_accion' => 'espera_juzgado',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 13,
                'color' => 'info',
                'icono' => 'clock',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'solicitar_epi_definitivo',
                'nombre' => 'Solicitar EPI Definitivo',
                'descripcion' => 'Solicitar la Exoneración del Pasivo Insatisfecho definitiva',
                'tipo_accion' => 'gestor',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 14,
                'color' => 'primary',
                'icono' => 'document',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'esperar_auto_definitivo',
                'nombre' => 'Esperar Auto Definitivo',
                'descripcion' => 'Esperando el auto definitivo del juzgado',
                'tipo_accion' => 'espera_juzgado',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 15,
                'color' => 'info',
                'icono' => 'clock',
            ],
            // Estados finales
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'inadmitido',
                'nombre' => 'Inadmitido',
                'descripcion' => 'La solicitud fue inadmitida por el juzgado',
                'tipo_accion' => 'decision',
                'es_inicial' => false,
                'es_final' => true,
                'resultado_final' => 'fracaso',
                'orden' => 99,
                'color' => 'error',
                'icono' => 'x-circle',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'desestimado',
                'nombre' => 'Desestimado',
                'descripcion' => 'El recurso fue desestimado',
                'tipo_accion' => 'decision',
                'es_inicial' => false,
                'es_final' => true,
                'resultado_final' => 'fracaso',
                'orden' => 99,
                'color' => 'error',
                'icono' => 'x-circle',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'revocacion_epi',
                'nombre' => 'Revocación EPI',
                'descripcion' => 'El EPI fue revocado por incumplimiento',
                'tipo_accion' => 'decision',
                'es_inicial' => false,
                'es_final' => true,
                'resultado_final' => 'fracaso',
                'orden' => 99,
                'color' => 'error',
                'icono' => 'x-circle',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'exoneracion_definitiva',
                'nombre' => 'Exoneración Definitiva',
                'descripcion' => 'Se concedió la exoneración definitiva del pasivo insatisfecho',
                'tipo_accion' => 'decision',
                'es_inicial' => false,
                'es_final' => true,
                'resultado_final' => 'exito',
                'orden' => 100,
                'color' => 'success',
                'icono' => 'check-circle',
            ],
        ];

        // Insertar estados y guardar IDs
        $estadoIds = [];
        foreach ($estados as $estado) {
            $estadoIds[$estado['codigo']] = DB::table('estados_proceso')->insertGetId(
                array_merge($estado, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // Crear transiciones según el diagrama del autómata
        $transiciones = [
            // Flujo principal
            ['recopilar_docs', 'presentar_solicitud', 'Documentación completa', true],
            ['presentar_solicitud', 'esperar_admision', 'Solicitud presentada', true],

            // Desde esperar admisión
            ['esperar_admision', 'esperando_publicaciones', 'Admitido', true],
            ['esperar_admision', 'inadmitido', 'Inadmitido', false],

            // Primera publicación (BOE o RPC) inicia el período de alegaciones
            // Las fechas de publicación se registran en lso_con_plan.fecha_publicacion_boe/rpc
            ['esperando_publicaciones', 'periodo_alegaciones', 'Primera publicación (BOE o RPC)', true],

            // Decisión sobre alegaciones
            ['periodo_alegaciones', 'sin_alegaciones', 'Sin alegaciones', true],
            ['periodo_alegaciones', 'con_alegaciones', 'Con alegaciones', false],

            // Flujo con alegaciones
            ['con_alegaciones', 'resolucion_alegaciones', 'Preparar respuesta', true],
            ['resolucion_alegaciones', 'alegaciones_resueltas', 'Respuesta presentada', true],
            ['alegaciones_resueltas', 'presentar_solicitud_epi_plan', 'Continuar proceso', true],

            // Flujo sin alegaciones
            ['sin_alegaciones', 'presentar_solicitud_epi_plan', 'Continuar proceso', true],

            // Solicitud EPI
            ['presentar_solicitud_epi_plan', 'esperar_epi_provisional', 'Solicitud presentada', true],

            // Desde esperar EPI provisional
            ['esperar_epi_provisional', 'cumplimiento_plan', 'EPI provisional aprobado', true],
            ['esperar_epi_provisional', 'recurrir_modificar', 'Plan de pagos denegado', false],

            // Recurso
            ['recurrir_modificar', 'esperar_resolucion_recurso', 'Recurso presentado', true],
            ['esperar_resolucion_recurso', 'cumplimiento_plan', 'Recurso estimado', true],
            ['esperar_resolucion_recurso', 'desestimado', 'Recurso desestimado', false],

            // Cumplimiento del plan
            ['cumplimiento_plan', 'presentar_informe', 'Presentar informe (6 meses)', true],
            ['presentar_informe', 'esperar_valoracion', 'Informe presentado', true],

            // Desde valoración
            ['esperar_valoracion', 'cumplimiento_plan', 'Cumple - continuar', true],
            ['esperar_valoracion', 'revocacion_epi', 'Incumple - revocación', false],
            ['esperar_valoracion', 'solicitar_epi_definitivo', 'Plan completado', true],

            // Final exitoso
            ['solicitar_epi_definitivo', 'esperar_auto_definitivo', 'Solicitud presentada', true],
            ['esperar_auto_definitivo', 'exoneracion_definitiva', 'Exoneración concedida', true],
        ];

        foreach ($transiciones as [$origen, $destino, $etiqueta, $esPrincipal]) {
            DB::table('transiciones_proceso')->insert([
                'estado_origen_id' => $estadoIds[$origen],
                'estado_destino_id' => $estadoIds[$destino],
                'etiqueta' => $etiqueta,
                'descripcion' => null,
                'requiere_confirmacion' => !$esPrincipal, // Las alternativas requieren confirmación
                'es_principal' => $esPrincipal,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedLsoSinMasa(): void
    {
        $tipoProcedimientoId = 1; // LSO sin masa

        // Crear estados
        $estados = [
            // Estado inicial
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'recopilar_docs',
                'nombre' => 'Recopilar Documentación',
                'descripcion' => 'Recopilación de documentación necesaria para iniciar el proceso',
                'tipo_accion' => 'gestor',
                'es_inicial' => true,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 1,
                'color' => 'primary',
                'icono' => 'document',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'presentar_solicitud',
                'nombre' => 'Presentar Solicitud',
                'descripcion' => 'Presentación de la solicitud de concurso sin masa ante el juzgado',
                'tipo_accion' => 'gestor',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 2,
                'color' => 'primary',
                'icono' => 'send',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'esperar_admision',
                'nombre' => 'Esperar Admisión',
                'descripcion' => 'Esperando resolución del juzgado sobre la admisión',
                'tipo_accion' => 'espera_juzgado',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 3,
                'color' => 'info',
                'icono' => 'clock',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'esperando_publicaciones',
                'nombre' => 'Publicaciones BOE/RPC',
                'descripcion' => 'Pendiente publicación simultánea en BOE y Registro Público Concursal',
                'tipo_accion' => 'espera_tiempo',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 4,
                'color' => 'info',
                'icono' => 'newspaper',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'periodo_alegaciones',
                'nombre' => 'Período de Alegaciones',
                'descripcion' => 'Período de 15 días hábiles para presentar alegaciones',
                'tipo_accion' => 'espera_tiempo',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 5,
                'color' => 'warning',
                'icono' => 'calendar',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'sin_alegaciones',
                'nombre' => 'Sin Alegaciones',
                'descripcion' => 'No se presentaron alegaciones durante el período',
                'tipo_accion' => 'decision',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 6,
                'color' => 'success',
                'icono' => 'check',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'con_alegaciones',
                'nombre' => 'Con Alegaciones',
                'descripcion' => 'Se presentaron alegaciones durante el período',
                'tipo_accion' => 'decision',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 6,
                'color' => 'warning',
                'icono' => 'alert',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'contestar_oposicion',
                'nombre' => 'Contestar Oposición',
                'descripcion' => 'Preparar y presentar contestación a la oposición',
                'tipo_accion' => 'gestor',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 7,
                'color' => 'primary',
                'icono' => 'edit',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'esperar_vista_resolucion',
                'nombre' => 'Esperar Vista/Resolución',
                'descripcion' => 'Esperando vista o resolución del juzgado sobre las alegaciones',
                'tipo_accion' => 'espera_juzgado',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 8,
                'color' => 'info',
                'icono' => 'clock',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'solicitar_epi',
                'nombre' => 'Solicitar EPI',
                'descripcion' => 'Presentar solicitud de Exoneración del Pasivo Insatisfecho',
                'tipo_accion' => 'gestor',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 9,
                'color' => 'primary',
                'icono' => 'document',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'esperar_resolucion_epi',
                'nombre' => 'Esperar Resolución EPI',
                'descripcion' => 'Esperando resolución del juzgado sobre el EPI',
                'tipo_accion' => 'espera_juzgado',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 10,
                'color' => 'info',
                'icono' => 'clock',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'valorar_recurso',
                'nombre' => 'Valorar Recurso',
                'descripcion' => 'Valorar si procede recurrir la denegación del EPI',
                'tipo_accion' => 'gestor',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 11,
                'color' => 'warning',
                'icono' => 'scale',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'recurrir',
                'nombre' => 'Recurrir',
                'descripcion' => 'Presentar recurso contra la denegación del EPI',
                'tipo_accion' => 'gestor',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 12,
                'color' => 'primary',
                'icono' => 'refresh',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'esperar_resolucion_recurso',
                'nombre' => 'Esperar Resolución Recurso',
                'descripcion' => 'Esperando resolución del juzgado sobre el recurso',
                'tipo_accion' => 'espera_juzgado',
                'es_inicial' => false,
                'es_final' => false,
                'resultado_final' => null,
                'orden' => 13,
                'color' => 'info',
                'icono' => 'clock',
            ],
            // Estados finales
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'inadmitido',
                'nombre' => 'Inadmitido',
                'descripcion' => 'La solicitud fue inadmitida por el juzgado',
                'tipo_accion' => 'decision',
                'es_inicial' => false,
                'es_final' => true,
                'resultado_final' => 'fracaso',
                'orden' => 99,
                'color' => 'error',
                'icono' => 'x-circle',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'no_recurre',
                'nombre' => 'No Recurre',
                'descripcion' => 'Se decide no recurrir la denegación del EPI',
                'tipo_accion' => 'decision',
                'es_inicial' => false,
                'es_final' => true,
                'resultado_final' => 'fracaso',
                'orden' => 99,
                'color' => 'error',
                'icono' => 'x-circle',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'desestimado',
                'nombre' => 'Desestimado',
                'descripcion' => 'El recurso fue desestimado',
                'tipo_accion' => 'decision',
                'es_inicial' => false,
                'es_final' => true,
                'resultado_final' => 'fracaso',
                'orden' => 99,
                'color' => 'error',
                'icono' => 'x-circle',
            ],
            [
                'tipo_procedimiento_id' => $tipoProcedimientoId,
                'codigo' => 'exoneracion_definitiva',
                'nombre' => 'Exoneración Definitiva',
                'descripcion' => 'Se concedió la exoneración definitiva del pasivo insatisfecho',
                'tipo_accion' => 'decision',
                'es_inicial' => false,
                'es_final' => true,
                'resultado_final' => 'exito',
                'orden' => 100,
                'color' => 'success',
                'icono' => 'check-circle',
            ],
        ];

        // Insertar estados y guardar IDs
        $estadoIds = [];
        foreach ($estados as $estado) {
            $estadoIds[$estado['codigo']] = DB::table('estados_proceso')->insertGetId(
                array_merge($estado, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // Crear transiciones según el diagrama del autómata
        $transiciones = [
            // Flujo inicial
            ['recopilar_docs', 'presentar_solicitud', 'Documentación completa', true],

            // Presentar solicitud
            ['presentar_solicitud', 'esperar_admision', 'Solicitud presentada', true],

            // Desde esperar admisión
            ['esperar_admision', 'esperando_publicaciones', 'Admitido', true],
            ['esperar_admision', 'inadmitido', 'Inadmitido', false],

            // Publicaciones -> Período alegaciones
            ['esperando_publicaciones', 'periodo_alegaciones', 'Primera publicación (BOE o RPC)', true],

            // Decisión sobre alegaciones
            ['periodo_alegaciones', 'sin_alegaciones', 'Sin alegaciones', true],
            ['periodo_alegaciones', 'con_alegaciones', 'Con alegaciones', false],

            // Flujo con alegaciones
            ['con_alegaciones', 'contestar_oposicion', 'Contestar oposición', true],
            ['contestar_oposicion', 'esperar_vista_resolucion', 'Contestación presentada', true],
            ['esperar_vista_resolucion', 'solicitar_epi', 'Resuelta', true],

            // Flujo sin alegaciones
            ['sin_alegaciones', 'solicitar_epi', 'Continuar proceso', true],

            // Solicitar EPI
            ['solicitar_epi', 'esperar_resolucion_epi', 'Solicitud presentada', true],

            // Desde esperar resolución EPI
            ['esperar_resolucion_epi', 'exoneracion_definitiva', 'EPI concedido', true],
            ['esperar_resolucion_epi', 'valorar_recurso', 'EPI denegado', false],

            // Valorar recurso
            ['valorar_recurso', 'recurrir', 'Recurrir', true],
            ['valorar_recurso', 'no_recurre', 'No recurrir', false],

            // Recurso
            ['recurrir', 'esperar_resolucion_recurso', 'Recurso presentado', true],
            ['esperar_resolucion_recurso', 'exoneracion_definitiva', 'Estimado', true],
            ['esperar_resolucion_recurso', 'desestimado', 'Desestimado', false],
        ];

        foreach ($transiciones as [$origen, $destino, $etiqueta, $esPrincipal]) {
            DB::table('transiciones_proceso')->insert([
                'estado_origen_id' => $estadoIds[$origen],
                'estado_destino_id' => $estadoIds[$destino],
                'etiqueta' => $etiqueta,
                'descripcion' => null,
                'requiere_confirmacion' => !$esPrincipal,
                'es_principal' => $esPrincipal,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\EventoCalendarioController;
use App\Http\Controllers\ExpedienteController;
use App\Http\Controllers\PlanPagoAcreedoresController;
use App\Http\Controllers\PlanPagoHonorariosController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;


// ============================================================================
// RUTAS PARA INVITADOS (usuarios NO autenticados)
// ============================================================================

Route::middleware('guest')->group(function () {
    
    // --- LOGIN ---
    // Muestra el formulario de inicio de sesión
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    
    // Procesa el formulario de login (valida credenciales y autentica)
    Route::post('/login', [LoginController::class, 'login']);

    // --- RECUPERAR CONTRASEÑA ---
    // Muestra el formulario para solicitar el enlace de recuperación
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');
    
    // Envía el email con el enlace para resetear la contraseña
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');

    // --- RESETEAR CONTRASEÑA ---
    // Muestra el formulario para establecer una nueva contraseña
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    
    // Procesa el formulario y actualiza la contraseña en la base de datos
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

// ============================================================================
// RUTAS PARA USUARIOS AUTENTICADOS
// ============================================================================

Route::middleware('auth')->group(function () {
    
    // --- CERRAR SESIÓN ---
    // Cierra la sesión del usuario actual (invalida la sesión y el token CSRF)
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // --- CERRAR SESIÓN EN OTROS DISPOSITIVOS ---
    // Invalida todas las sesiones del usuario excepto la actual
    Route::post('/logout-other-devices', [LoginController::class, 'logoutOtherDevices'])
        ->name('logout.other.devices');

    // --- DASHBOARD ---
    // Muestra el panel principal de la aplicación
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- EVENTOS CALENDARIO (recordatorios y alertas) ---
    Route::post('/recordatorios', [EventoCalendarioController::class, 'store'])->name('recordatorios.store');
    Route::patch('/eventos/{evento}/resolver', [EventoCalendarioController::class, 'update'])->name('eventos.resolver');
    Route::delete('/recordatorios/{evento}', [EventoCalendarioController::class, 'destroy'])->name('recordatorios.destroy');

    // --- EXPEDIENTES ---
    Route::get('/expedientes', [ExpedienteController::class, 'index'])->name('expedientes');
    Route::get('/expedientes/export', [ExpedienteController::class, 'export'])->name('expedientes.export');
    Route::get('/expedientes/create', [ExpedienteController::class, 'create'])->name('expedientes.create');
    Route::post('/expedientes', [ExpedienteController::class, 'store'])->name('expedientes.store');
    Route::get('/expedientes/{expediente}', [ExpedienteController::class, 'show'])->name('expedientes.show');
    Route::post('/expedientes/{expediente}/iniciar-proceso', [ExpedienteController::class, 'iniciarProceso'])->name('expedientes.iniciar-proceso');
    Route::post('/expedientes/{expediente}/avanzar-proceso', [ExpedienteController::class, 'avanzarProceso'])->name('expedientes.avanzar-proceso');
    Route::post('/expedientes/{expediente}/registrar-publicacion', [ExpedienteController::class, 'registrarPublicacion'])->name('expedientes.registrar-publicacion');
    Route::post('/expedientes/{expediente}/archivar', [ExpedienteController::class, 'archivar'])->name('expedientes.archivar');
    Route::put('/expedientes/{expediente}/historial/{historial}', [ExpedienteController::class, 'actualizarFechaHistorial'])->name('expedientes.actualizar-fecha-historial');

    // --- PLAN DE PAGO DE HONORARIOS ---
    Route::post('/expedientes/{expediente}/plan-pago-honorarios', [PlanPagoHonorariosController::class, 'store'])->name('plan-pago-honorarios.store');
    Route::post('/expedientes/{expediente}/plan-pago-honorarios/registrar-pago', [PlanPagoHonorariosController::class, 'registrarPago'])->name('plan-pago-honorarios.registrar-pago');
    Route::delete('/expedientes/{expediente}/plan-pago-honorarios/{plan}', [PlanPagoHonorariosController::class, 'destroy'])->name('plan-pago-honorarios.destroy');

    // --- FACTURAS DE HONORARIOS ---
    Route::post('/facturas-honorarios/{factura}/subir-pdf', [\App\Http\Controllers\FacturaHonorariosController::class, 'subirPdf'])->name('facturas-honorarios.subir-pdf');
    Route::get('/facturas-honorarios/{factura}/descargar', [\App\Http\Controllers\FacturaHonorariosController::class, 'descargar'])->name('facturas-honorarios.descargar');

    // --- PLAN DE PAGO A ACREEDORES ---
    Route::post('/expedientes/{expediente}/plan-pago-acreedores', [PlanPagoAcreedoresController::class, 'store'])->name('plan-pago-acreedores.store');
    Route::put('/expedientes/{expediente}/plan-pago-acreedores/{linea}', [PlanPagoAcreedoresController::class, 'update'])->name('plan-pago-acreedores.update');
    Route::post('/expedientes/{expediente}/plan-pago-acreedores/{linea}/registrar-pago', [PlanPagoAcreedoresController::class, 'registrarPago'])->name('plan-pago-acreedores.registrar-pago');

    // --- CLIENTES ---
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes');
    Route::get('/clientes/export', [ClienteController::class, 'export'])->name('clientes.export');
    Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
    Route::get('/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
    
    // --- DOCUMENTOS ---
    Route::post('/documentos', [DocumentoController::class, 'store'])->name('documentos.store');
    Route::get('/documentos/{documento}/download', [DocumentoController::class, 'download'])->name('documentos.download');
    Route::get('/documentos/{documento}/view', [DocumentoController::class, 'view'])->name('documentos.view');
    Route::delete('/documentos/{documento}', [DocumentoController::class, 'destroy'])->name('documentos.destroy');

    // --- PERFIL ---
    Route::get('/perfil', function () {
        return view('perfil.index');
    })->name('perfil');
    Route::put('/perfil', [\App\Http\Controllers\PerfilController::class, 'update'])->name('perfil.update');

    // --- GESTIÓN DE USUARIOS (solo admin) ---
    Route::middleware('role:admin')->group(function () {
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios');
        Route::get('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{usuario}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
        Route::post('/usuarios/{id}/restore', [UsuarioController::class, 'restore'])->name('usuarios.restore');
    });
});

// ============================================================================
// RUTA RAÍZ
// ============================================================================

Route::get('/', function () {
    return redirect()->route('login');
});

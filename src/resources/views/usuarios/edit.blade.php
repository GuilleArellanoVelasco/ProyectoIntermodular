@extends('layouts.app')

@section('title', 'Editar Usuario')
@section('page-title', 'Editar Usuario')
@section('page-subtitle', $usuario->nombreCompleto)

@section('content')
    @include('usuarios.partials._form', [
        'usuario' => $usuario,
        'action' => route('usuarios.update', $usuario),
        'method' => 'PUT',
    ])
@endsection

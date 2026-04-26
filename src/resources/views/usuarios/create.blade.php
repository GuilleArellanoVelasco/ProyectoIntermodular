@extends('layouts.app')

@section('title', 'Nuevo Usuario')
@section('page-title', 'Crear Usuario')
@section('page-subtitle', 'Registrar un nuevo usuario del sistema')

@section('content')
    @include('usuarios.partials._form', [
        'usuario' => null,
        'action' => route('usuarios.store'),
        'method' => 'POST',
    ])
@endsection

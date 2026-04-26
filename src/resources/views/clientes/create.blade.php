@extends('layouts.app')

@section('title', 'Nuevo Cliente')
@section('page-title', 'Crear Cliente')
@section('page-subtitle', 'Registrar un nuevo cliente en el sistema')

@section('content')
    @include('clientes.partials._form', [
        'cliente' => null,
        'action' => route('clientes.store'),
        'method' => 'POST',
    ])
@endsection

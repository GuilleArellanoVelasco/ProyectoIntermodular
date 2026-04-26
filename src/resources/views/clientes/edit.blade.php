@extends('layouts.app')

@section('title', 'Editar Cliente')
@section('page-title', 'Editar Cliente')
@section('page-subtitle', trim("{$cliente->nombre} {$cliente->apellido1} {$cliente->apellido2}"))

@section('content')
    @include('clientes.partials._form', [
        'cliente' => $cliente,
        'action' => route('clientes.update', $cliente),
        'method' => 'PUT',
    ])
@endsection

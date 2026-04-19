@extends('emails.layouts.pct')

@section('title', 'Diretório ativado')

@section('content')
    <h2>Diretório ativado ✅</h2>
    <p>O diretório <strong>{{ $diretorio ?? 'PCT' }}</strong> está ativo no SaaS.</p>
    <p>Protocolo: <strong>{{ $protocolo ?? 'N/A' }}</strong>.</p>
@endsection

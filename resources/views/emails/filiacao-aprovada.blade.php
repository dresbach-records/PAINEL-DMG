@extends('emails.layouts.pct')

@section('title', 'Filiação aprovada')

@section('content')
    <h2>Filiação aprovada 🎉</h2>
    <p>Olá, {{ $nome ?? 'filiado(a)' }}.</p>
    <p>Sua filiação foi aprovada com sucesso no sistema PCT.</p>
@endsection

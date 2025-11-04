@extends('adminlte::page')

@section('title', 'Registrasi Pasien - edokter')

@section('content_header')
    <h1>Registrasi Pasien</h1>
@endsection

@section('content')
    <!-- CSRF token diperlukan oleh React fetch -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Root untuk aplikasi React Registrasi -->
    <div id="reg-periksa-root" class="mt-2"></div>
@endsection

@section('js')
    {{-- App.jsx is loaded globally by adminlte::master. No per-page injection to avoid duplicate evaluation. --}}
@endsection
@extends('adminlte::page')

@section('title', 'Menu Antrol BPJS')

@section('css')
  @vite('resources/css/app.css')
  <style>
    /* Hide global React overlay on this page */
    #react-root { display: none !important; }
    /* Smooth page background */
    body { background-color: #f7fafc; }
  </style>
@endsection

@section('content')
<div class="container-fluid p-0">
  <div class="px-4 py-2">
    <div id="mobile-jkn-home-root"></div>
  </div>
</div>
@endsection

@section('js')
  {{-- App.jsx is loaded globally by adminlte::master. No per-page injection to avoid duplicate evaluation. --}}
@endsection
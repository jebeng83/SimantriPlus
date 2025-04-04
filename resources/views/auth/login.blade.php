{{-- @extends('adminlte::auth.login') --}}
@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
<link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@php( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') )
@php( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') )
@php( $password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url',
'password/reset') )

@if (config('adminlte.use_route_url', false))
@php( $login_url = $login_url ? route($login_url) : '' )
@php( $register_url = $register_url ? route($register_url) : '' )
@php( $password_reset_url = $password_reset_url ? route($password_reset_url) : '' )
@else
@php( $login_url = $login_url ? url($login_url) : '' )
@php( $register_url = $register_url ? url($register_url) : '' )
@php( $password_reset_url = $password_reset_url ? url($password_reset_url) : '' )
@endif

@section('auth_header', __('adminlte::adminlte.login_message'))

@section('auth_body')
@error('message')
<x-adminlte-alert theme="danger" title="{{ $message }}" dismissable />
@enderror
<form action="{{ route('customlogin') }}" method="post">
    @csrf
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    {{-- ID Khanza field --}}
    <div class="input-group">
        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
            value="{{ old('username') }}" placeholder="ID Khanza" autofocus>

        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-user-md"></span>
            </div>
        </div>

        @error('username')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    {{-- Password field --}}
    <div class="input-group mb-3">
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
            placeholder="{{ __('adminlte::adminlte.password') }}">

        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
            </div>
        </div>

        @error('password')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <x-adminlte-select-bs id="poli" name="poli" :config="$config">
        @foreach($poli as $p)
        <option value="{{ $p->kd_poli }}">{{ $p->nm_poli }}</option>
        @endforeach
    </x-adminlte-select-bs>

    {{-- Login field --}}
    <div class="row">
        <div class="col-7">

        </div>

        <div class="col-5">
            <button type=submit class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                <span class="fas fa-sign-in-alt"></span>
                {{ __('adminlte::adminlte.sign_in') }}
            </button>
        </div>
    </div>
    <div class="row">
        <b>NB:</b>
        <ol>
            <li>Login Menggunakan ID masing-Masing</li>
            <li>Pilih POLIKLINIK Kesayangan Anda</li>
            <li>Sesi Login 30 Menit auto logout ketika tidak ada transaksi</li>
        </ol>
    </div>

</form>
@stop
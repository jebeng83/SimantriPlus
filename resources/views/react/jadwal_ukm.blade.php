@extends('adminlte::page')

@section('title', 'Jadwal UKM')

@section('css')
    @vite('resources/css/app.css')
    <style>
        /* Sembunyikan HelloMotion global agar tidak mengganggu tampilan */
        #react-root { display: none !important; }
    </style>
@endsection

@section('content')
    <div class="container-fluid p-0">
        <div class="max-w-7xl mx-auto">
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="h-1 bg-gradient-to-r from-indigo-500 to-blue-600"></div>
                <div class="px-5 pt-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-xl font-semibold tracking-tight text-gray-900">Jadwal UKM</h1>
                            <p class="mt-1 text-sm text-gray-600">Lihat dan kelola jadwal dalam tampilan kartu yang ringkas.</p>
                        </div>
                    </div>
                    <!-- React Root -->
                    <div id="jadwal-ukm-react-root"
                         data-meta-url="{{ route('jadwal-ukm.meta') }}"
                         data-list-url="{{ route('jadwal-ukm.data') }}"
                         data-store-url="{{ route('jadwal-ukm.store') }}"
                         data-update-url-template="{{ route('jadwal-ukm.update', ['id' => '__ID__']) }}"
                         data-delete-url-template="{{ route('jadwal-ukm.destroy', ['id' => '__ID__']) }}"
                         data-csrf-token="{{ csrf_token() }}"
                         class="mt-4"
                    ></div>
                </div>
                <div class="pb-4"></div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    {{-- App.jsx is loaded globally by adminlte::master. No per-page injection to avoid duplicate evaluation. --}}
@endsection
@extends('adminlte::page')

@section('title', 'Kegiatan UKM')

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
                <div class="h-1 bg-gradient-to-r from-emerald-500 to-teal-600"></div>
                <div class="px-5 pt-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-xl font-semibold tracking-tight text-gray-900">Kegiatan UKM</h1>
                            <p class="mt-1 text-sm text-gray-600">Kelola data kegiatan dengan tampilan kartu yang modern.</p>
                        </div>
                    </div>
                    <!-- React Root -->
                    <div id="kegiatan-ukm-react-root"
                        data-meta-url="{{ route('kegiatan-ukm.meta') }}"
                        data-list-url="{{ route('kegiatan-ukm.data') }}"
                        data-store-url="{{ route('kegiatan-ukm.store') }}"
                        data-update-url-template="{{ route('kegiatan-ukm.update', ['id' => '__ID__']) }}"
                        data-delete-url-template="{{ route('kegiatan-ukm.destroy', ['id' => '__ID__']) }}"
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
    {{-- Inject modul halaman secara eksplisit untuk mengisolasi error dari bundle global jika ada --}}
    @vite('resources/js/pages/MatrikKegiatanUkm/KegiatanUkm.jsx')
@endsection
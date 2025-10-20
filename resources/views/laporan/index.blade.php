@extends('adminlte::page')

@section('title', 'Menu Laporan')

@section('css')
    <!-- Tailwind CSS CDN untuk styling kartu Laporan -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        slate: {
                            850: '#2b3340'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Sembunyikan HelloMotion global agar tidak mengganggu tampilan Laporan */
        #react-root { display: none !important; }
        body { background-color: #f7fafc; }
    </style>
@endsection

@section('content')
    <div class="container-fluid p-0">
        <div class="w-full">
            <!-- React Root -->
            <div id="laporan-menu-root" class="mt-2"></div>
        </div>
    </div>
@endsection

@section('js')
    @vite('resources/js/app.jsx')
@endsection
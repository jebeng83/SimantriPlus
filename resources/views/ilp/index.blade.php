@extends('adminlte::page')

@section('title', 'Menu ILP')

@section('css')
    <!-- Tailwind CSS CDN for styling the ILP cards -->
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
        /* Sembunyikan HelloMotion global agar tidak mengganggu tampilan ILP */
        #react-root { display: none !important; }
    </style>
@endsection

@section('content')
    <div class="container-fluid p-0">
        <div class="w-full">
            <!-- React Root -->
            <div id="ilp-menu-root" class="mt-2"></div>
        </div>
    </div>
@endsection

@section('js')
    @vite('resources/js/app.jsx')
@endsection
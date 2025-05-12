<?php
$frekuensiOlahraga = [
    '0 kali',
    '1-2 kali',
    '3-4 kali',
    '5 kali atau lebih'
];

$durasiOlahraga = [
    'Kurang dari 30 menit',
    '30-60 menit',
    'Lebih dari 60 menit'
];
?>

<form id="aktivitasFisikForm">
   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">1. Dalam seminggu terakhir, berapa kali Anda melakukan aktivitas
               fisik/olahraga dalam seminggu? <span class="text-danger">*</span></label>
            <div class="mt-2">
               @foreach($frekuensiOlahraga as $frekuensi)
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="frekuensi_{{ Str::slug($frekuensi) }}" name="frekuensi_olahraga"
                     value="{{ $frekuensi }}" class="custom-control-input" required>
                  <label class="custom-control-label" for="frekuensi_{{ Str::slug($frekuensi) }}">{{ $frekuensi
                     }}</label>
               </div>
               @endforeach
            </div>
         </div>
      </div>
   </div>

   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">2. Rata-rata berapa lama waktu yang Anda habiskan ketika melakukan aktivitas
               fisik/olahraga? <span class="text-danger">*</span></label>
            <div class="mt-2">
               @foreach($durasiOlahraga as $durasi)
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="durasi_{{ Str::slug($durasi) }}" name="durasi_olahraga" value="{{ $durasi }}"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="durasi_{{ Str::slug($durasi) }}">{{ $durasi }}</label>
               </div>
               @endforeach
            </div>
         </div>
      </div>
   </div>

   {{-- <div class="text-center mt-4">
      <button type="submit" class="btn btn-success px-5">Kirim</button>
   </div> --}}
</form>
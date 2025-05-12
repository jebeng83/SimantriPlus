<?php
$frekuensiPilihan = [
    'Tidak sama sekali',
    'Kurang dari 1 minggu',
    'Lebih dari 1 minggu',
    'Hampir setiap hari'
];
?>

<form id="kesehatanJiwaForm">
   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">1. Pernahkah dalam 2 minggu terakhir, Anda merasa tidak memiliki minat atau
               kesenangan dalam melakukan sesuatu hal? <span class="text-danger">*</span></label>
            <div class="mt-2">
               @foreach($frekuensiPilihan as $index => $pilihan)
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="minat_{{ $index }}" name="minat" value="{{ $pilihan }}"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="minat_{{ $index }}">{{ $pilihan }}</label>
               </div>
               @endforeach
            </div>
         </div>
      </div>
   </div>

   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">2. Pernahkah dalam 2 minggu terakhir, Anda merasa murung, sedih, atau putus
               asa? <span class="text-danger">*</span></label>
            <div class="mt-2">
               @foreach($frekuensiPilihan as $index => $pilihan)
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="sedih_{{ $index }}" name="sedih" value="{{ $pilihan }}"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="sedih_{{ $index }}">{{ $pilihan }}</label>
               </div>
               @endforeach
            </div>
         </div>
      </div>
   </div>

   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">3. Dalam 2 minggu terakhir, seberapa sering anda merasa gugup, cemas, atau
               gelisah? <span class="text-danger">*</span></label>
            <div class="mt-2">
               @foreach($frekuensiPilihan as $index => $pilihan)
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="cemas_{{ $index }}" name="cemas" value="{{ $pilihan }}"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="cemas_{{ $index }}">{{ $pilihan }}</label>
               </div>
               @endforeach
            </div>
         </div>
      </div>
   </div>

   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">4. Dalam 2 minggu terakhir, seberapa sering anda tidak mampu mengendalikan
               rasa khawatir? <span class="text-danger">*</span></label>
            <div class="mt-2">
               @foreach($frekuensiPilihan as $index => $pilihan)
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="khawatir_{{ $index }}" name="khawatir" value="{{ $pilihan }}"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="khawatir_{{ $index }}">{{ $pilihan }}</label>
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
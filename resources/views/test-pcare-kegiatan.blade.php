<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Test Add Kegiatan Kelompok</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
   <div class="container mt-5">
      <h1>Test PCare Add Kegiatan Kelompok</h1>

      <div class="row">
         <div class="col-md-6">
            <div class="card">
               <div class="card-header">
                  Form Input
               </div>
               <div class="card-body">
                  <form id="kegiatanForm">
                     <div class="mb-3">
                        <label class="form-label">Club ID</label>
                        <input type="number" class="form-control" name="clubId" value="5298">
                     </div>
                     <div class="mb-3">
                        <label class="form-label">Tanggal Pelayanan (DD-MM-YYYY)</label>
                        <input type="text" class="form-control" name="tglPelayanan" value="07-04-2023">
                     </div>
                     <div class="mb-3">
                        <label class="form-label">Kode Kegiatan</label>
                        <select class="form-select" name="kdKegiatan">
                           <option value="01">01 - Senam</option>
                           <option value="10">10 - Penyuluhan</option>
                           <option value="11">11 - Penyuluhan dan Senam</option>
                        </select>
                     </div>
                     <div class="mb-3">
                        <label class="form-label">Kode Kelompok</label>
                        <select class="form-select" name="kdKelompok">
                           <option value="01">01 - Diabetes Melitus</option>
                           <option value="02" selected>02 - Hipertensi</option>
                           <option value="03">03 - Asthma</option>
                        </select>
                     </div>
                     <div class="mb-3">
                        <label class="form-label">Materi</label>
                        <input type="text" class="form-control" name="materi" value="Senam Kesehatan">
                     </div>
                     <div class="mb-3">
                        <label class="form-label">Pembicara</label>
                        <input type="text" class="form-control" name="pembicara" value="Gagarini">
                     </div>
                     <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" class="form-control" name="lokasi" value="Halaman Puskesmas">
                     </div>
                     <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" class="form-control" name="keterangan" value="Senam rutin mingguan">
                     </div>
                     <div class="mb-3">
                        <label class="form-label">Biaya</label>
                        <input type="number" class="form-control" name="biaya" value="250000">
                     </div>
                     <button type="submit" class="btn btn-primary">Submit</button>
                  </form>
               </div>
            </div>
         </div>
         <div class="col-md-6">
            <div class="card">
               <div class="card-header">
                  Response
               </div>
               <div class="card-body">
                  <pre id="responseArea">Response will appear here...</pre>
               </div>
            </div>
         </div>
      </div>
   </div>

   <script>
      document.getElementById('kegiatanForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const formObject = {};
            
            formData.forEach((value, key) => {
                if (key === 'clubId' || key === 'biaya') {
                    formObject[key] = parseInt(value);
                } else {
                    formObject[key] = value;
                }
            });
            
            // Add eduId as null explicitly
            formObject.eduId = null;
            
            try {
                const response = await fetch('/api/pcare/kelompok/kegiatan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'text/plain'
                    },
                    body: JSON.stringify(formObject)
                });
                
                const data = await response.json();
                document.getElementById('responseArea').textContent = JSON.stringify(data, null, 2);
            } catch (error) {
                document.getElementById('responseArea').textContent = 'Error: ' + error.message;
            }
        });
   </script>
</body>

</html>
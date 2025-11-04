<?php
require_once('../conf/conf.php');
date_default_timezone_set('Asia/Jakarta');


if (isset($_GET['p'])) {
    //jam reset antrian

    //kode poli yang ingin ditampilkan
    $jamreset = '23:00:00';
    // query hapus atau reset data



    switch ($_GET['p']) {
        case 'pengaturan':
            $_sql = "select nama_instansi,email from setting";
            $hasil = bukaquery($_sql);
            $data = array();
            while ($r = mysqli_fetch_array($hasil)) {
                $r['text'] = "Selamat Datang di UPT Puskesmas Kerjo || We Serve Better";
                $data = $r;
            }
            echo json_encode($data);
            break;

        case 'panggil':
            // Menampilkan nomor yang sedang dipanggil (status='2')
            $_sql = "SELECT nomor,loket FROM antripendaftaran_nomor WHERE status='2' ORDER BY jam ASC LIMIT 1";
            $hasil = bukaquery($_sql);
            
            $data = array();
            if (mysqli_num_rows($hasil) > 0) {
                while ($r = mysqli_fetch_array($hasil)) {
                    $data[] = $r;
                }
            } else {
                // Jika tidak ada nomor yang sedang dipanggil, tampilkan nomor 0
                $data[] = array('nomor' => '0', 'loket' => '-');
            }
            echo json_encode($data);
            break;

        case 'panggil_sekarang':
            // Fungsi untuk benar-benar memanggil nomor dan mengubah status
            if (isset($_GET['nomor']) && isset($_GET['loket'])) {
                $nomor = $_GET['nomor'];
                $loket = $_GET['loket'];
                // Ubah status dari '1' ke '2' (sedang dilayani) dan set loket
                bukaquery2("UPDATE antripendaftaran_nomor SET status = '2', loket = '$loket' WHERE nomor = '$nomor' AND status = '1'");
                echo json_encode(["status" => "success", "message" => "Nomor $nomor sedang dipanggil di $loket"]);
            } else {
                // Otomatis panggil nomor berikutnya dengan loket default
                $loket_default = isset($_GET['loket']) ? $_GET['loket'] : 'LOKET 1';
                // Cari nomor dengan status='1' terlebih dahulu
                $_sql = "SELECT nomor FROM antripendaftaran_nomor WHERE status='1' ORDER BY jam ASC LIMIT 1";
                $hasil = bukaquery($_sql);
                
                if (mysqli_num_rows($hasil) > 0) {
                    // Ada nomor dengan status='1', ubah ke status='2'
                    $row = mysqli_fetch_array($hasil);
                    $nomor = $row['nomor'];
                    bukaquery2("UPDATE antripendaftaran_nomor SET status = '2', loket = '$loket_default' WHERE nomor = '$nomor'");
                    echo json_encode(["status" => "success", "message" => "Nomor $nomor sedang dipanggil di $loket_default", "nomor" => $nomor]);
                } else {
                    // Tidak ada status='1', ambil dari status='0' dan ubah langsung ke '2'
                    $_sql = "SELECT nomor FROM antripendaftaran_nomor WHERE status='0' ORDER BY CONVERT(nomor,signed) ASC LIMIT 1";
                    $hasil = bukaquery($_sql);
                    
                    if (mysqli_num_rows($hasil) > 0) {
                        $row = mysqli_fetch_array($hasil);
                        $nomor = $row['nomor'];
                        bukaquery2("UPDATE antripendaftaran_nomor SET status = '2', loket = '$loket_default' WHERE nomor = '$row[nomor]'");
                        echo json_encode(["status" => "success", "message" => "Nomor $nomor sedang dipanggil di $loket_default", "nomor" => $nomor]);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Tidak ada antrian"]);
                    }
                }
            }
            break;


        case 'nomor':
            $_sql = "SELECT nomor,loket FROM antripendaftaran_nomor WHERE status < '3' AND status > '0'  ORDER BY jam ASC LIMIT 1";
            $hasil = bukaquery($_sql);
            $data = array();

            if (mysqli_num_rows($hasil) > 0) {
                while ($row = mysqli_fetch_array($hasil)) {
                    $data[] = $row;
                }
            } else {
                $row['nomor'] = '0';
                $row['loket'] = '-';
                $data[] = $row;
            }
            echo json_encode($data);
            break;

        case 'selesai':
            // Mengubah status nomor yang sedang dilayani (status='2') menjadi selesai (status='3')
            if (isset($_GET['nomor'])) {
                $nomor = $_GET['nomor'];
                bukaquery2("UPDATE antripendaftaran_nomor SET status = '3' WHERE nomor = '$nomor' AND status = '2'");
                echo json_encode(["status" => "success", "message" => "Nomor $nomor telah selesai dilayani"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Nomor tidak ditemukan"]);
            }
            break;

        case 'ulangi':
            // Mengubah status nomor dan set loket untuk diulangi
            if (isset($_GET['nomor']) && isset($_GET['loket'])) {
                $nomor = $_GET['nomor'];
                $loket = $_GET['loket'];
                bukaquery2("UPDATE antripendaftaran_nomor SET status = '1', loket = '$loket' WHERE nomor = '$nomor'");
                echo json_encode(["status" => "success", "message" => "Nomor $nomor diulangi untuk dilayani di loket $loket"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Nomor atau loket tidak ditemukan"]);
            }
            break;

        case 'sisa_antrian':
            // Menghitung sisa antrian dengan status='0' pada hari ini
            $_sql = "SELECT (COUNT(nomor)-1) as sisa FROM antripendaftaran_nomor WHERE status='0' AND DATE_FORMAT(jam, '%Y-%m-%d') = CURDATE()";
            $hasil = bukaquery($_sql);
            $data = array();
            
            if (mysqli_num_rows($hasil) > 0) {
                while ($row = mysqli_fetch_array($hasil)) {
                    $sisa = $row['sisa'] < 0 ? 0 : $row['sisa'];
                    $data[] = ["sisa_antrian" => $sisa];
                }
            } else {
                $data[] = ["sisa_antrian" => 0];
            }
            echo json_encode($data);
            break;

        case 'panggil_antrian':
            // Mengambil nomor antrian berikutnya dengan status='0' pada hari ini
            $_sql = "SELECT nomor FROM antripendaftaran_nomor WHERE status='0' AND DATE_FORMAT(jam, '%Y-%m-%d') = CURDATE() ORDER BY jam ASC LIMIT 1";
            $hasil = bukaquery($_sql);
            $data = array();
            
            if (mysqli_num_rows($hasil) > 0) {
                while ($row = mysqli_fetch_array($hasil)) {
                    $data[] = $row;
                }
            } else {
                $data[] = ["nomor" => ""];
            }
            echo json_encode($data);
            break;

        case 'loket1':
            // Menginisialisasi array data di luar loop
            $data = array();

            $_sql = "select ifnull(MAX(CONVERT(antripendaftaran_nomor.nomor,signed)),0) as nomor, loket from antripendaftaran_nomor WHERE loket<>'' GROUP BY loket";
            $h = bukaquery($_sql);

            if (mysqli_num_rows($h) > 0) {
                while ($row = mysqli_fetch_array($h)) {
                    $data[] = $row;
                }
            } else {
                $row['nomor'] = '-';
                $row['loket'] = '-';
                $data[] = $row;
            }

            // Menampilkan hasil sebagai JSON
            echo json_encode($data);
            break;

        case 'getnomor':
            bukaquery2("delete from antripendaftaran_nomor where jam not like '" . date("Y-m-d") . "%' ");
            $_sql = "select ifnull(MAX(CONVERT(antripendaftaran_nomor.nomor,signed)),0) as nomor from antripendaftaran_nomor WHERE jam LIKE '" . date("Y-m-d") . " %' ";
            $hasil = bukaquery($_sql);
            $data = array();

            if (mysqli_num_rows($hasil) > 0) {
                while ($row = mysqli_fetch_array($hasil)) {
                    $_sql = "INSERT INTO antripendaftaran_nomor (nomor, status, jam) VALUES ('" . ($row['nomor'] + 1) . "','0','" . date("Y-m-d H:i:s") . "')";
                    $hasil = bukaquery($_sql);
                    $data[] = $row;
                }
            } else {
                $_sql = "INSERT INTO antripendaftaran_nomor (nomor, status, jam) VALUES ('1','0','" . date("Y-m-d H:i:s") . "')";
                $hasil = bukaquery($_sql);
                $data[] = 1;
            }

            echo json_encode($data);
            break;

        case 'ceknomorterakhir':
            $_sql = "select ifnull(MAX(CONVERT(antripendaftaran_nomor.nomor,signed)),0) as nomor from antripendaftaran_nomor WHERE jam LIKE '" . date("Y-m-d") . " %' ";
            $hasil = bukaquery($_sql);
            $data = array();

            while ($row = mysqli_fetch_array($hasil)) {
                $data[] = $row;
            }

            echo json_encode($data);
            break;

        case 'panggil_dengan_loket':
            // Fungsi untuk memanggil nomor tertentu dengan loket tertentu
            if (isset($_GET['nomor']) && isset($_GET['loket'])) {
                $nomor = $_GET['nomor'];
                $loket = $_GET['loket'];
                
                // Update status menjadi '2' (sedang dipanggil) dan set loket
                $result = bukaquery2("UPDATE antripendaftaran_nomor SET status='2', loket='$loket' WHERE nomor='$nomor' AND DATE_FORMAT(jam,'%Y-%m-%d')=CURDATE()");
                
                if ($result) {
                    echo json_encode(["status" => "success", "message" => "Nomor $nomor berhasil dipanggil ke $loket", "nomor" => $nomor, "loket" => $loket]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Gagal memanggil nomor $nomor"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Parameter nomor dan loket harus diisi"]);
            }
            break;
    }
}

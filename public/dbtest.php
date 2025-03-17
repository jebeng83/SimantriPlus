<?php try { $pdo = new PDO('mysql:host=127.0.0.1;dbname=kerjo', 'root', ''); echo 'Koneksi database berhasil'; } catch(PDOException $e) { echo 'Koneksi gagal: ' . $e->getMessage(); } ?>

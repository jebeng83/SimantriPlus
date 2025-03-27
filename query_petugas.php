<?php require "vendor/autoload.php"; $app = require_once "bootstrap/app.php"; $app->make("Illuminate\Contracts\Console\Kernel")->bootstrap(); use Illuminate\Support\Facades\DB; $petugas1 = DB::table("petugas")->where([["kd_jbtn", "=", "j008"], ["status", "=", "1"]])->orderBy("nama", "asc")->get(); echo "Dengan filter status = 1:
"; print_r($petugas1->toArray()); $petugas2 = DB::table("petugas")->where("kd_jbtn", "j008")->orderBy("nama", "asc")->get(); echo "
Tanpa filter status:
"; print_r($petugas2->toArray()); echo "
Jumlah dengan filter status = 1: " . count($petugas1) . "
"; echo "Jumlah tanpa filter status: " . count($petugas2) . "
";

<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "proyekprakweb";

$konek = new mysqli($hostname, $username, $password, $database);

if ($konek->connect_error) {
    die("Koneksi Gagal" . $konek->connect_error);
}

define('CLOUDINARY_CLOUD_NAME', 'dglpxhav2'); // Ganti dengan cloud name Anda
define('CLOUDINARY_API_KEY', '114984844595184');       // Ganti dengan API key Anda
define('CLOUDINARY_API_SECRET', 'x3qRHjXkcUJW2E9IpLXf6X8pCFI'); // Ganti dengan API secret Anda
define('CLOUDINARY_UPLOAD_PRESET', 'upload_novel'); // Optional, untuk unsigned upload

?>
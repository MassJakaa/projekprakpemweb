<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "proyekprakweb";

$konek = new mysqli($hostname, $username, $password, $database);

if ($konek->connect_error) {
    die("Koneksi Gagal" . $konek->connect_error);
}
?>
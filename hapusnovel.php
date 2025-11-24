<?php
session_start();
require_once 'koneksi.php';

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Cek apakah ada ID novel
if (!isset($_GET['id'])) {
    die("ID novel tidak ditemukan.");
}

$novel_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Ambil data novel (hanya milik user)
$sql = "SELECT * FROM stories WHERE id_novel='$novel_id' AND user_id='$user_id'";
$data = mysqli_query($konek, $sql);

if (mysqli_num_rows($data) == 0) {
    die("Novel tidak ditemukan atau bukan milik Anda.");
}

$novel = mysqli_fetch_assoc($data);

// Ambil public_id dari database
$cover_public_id = $novel['cover_id'];
$pdf_public_id   = $novel['pdf_id'];

// FUNGSI HAPUS CLOUDINARY Ea (Ini sulit cik)
function hapusCloudinary($public_id, $resource_type)
{
    if (!$public_id) return;

    $cloud_name = CLOUDINARY_CLOUD_NAME;
    $api_key    = CLOUDINARY_API_KEY;
    $api_secret = CLOUDINARY_API_SECRET;

    // Endpoint API Delete
    $url = "https://api.cloudinary.com/v1_1/$cloud_name/resources/$resource_type/upload?public_ids[]=$public_id";

    $ch = curl_init($url);

    // Validasi untuk PHP 8.2+
    if ($ch instanceof CurlHandle) {
        curl_setopt($ch, CURLOPT_USERPWD, "$api_key:$api_secret");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    return false;
}

// HAPUS COVER Ea
if (!empty($cover_public_id)) {
    hapusCloudinary($cover_public_id, "image");
}

// HAPUS PDF Ea
if (!empty($pdf_public_id)) {
    hapusCloudinary($pdf_public_id, "raw");
}

// HAPUS DATA NOVEL DI DATABASE Ea
$query = "DELETE FROM stories WHERE id_novel='$novel_id' AND user_id=$user_id";

if (mysqli_query($konek, $query)) {
    echo "<script>alert('Novel berhasil dihapus!'); window.location='novelsaya.php';</script>";
} else {
    echo "Gagal menghapus novel: " . mysqli_error($konek);
}

?>

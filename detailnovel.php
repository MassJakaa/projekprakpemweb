<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: berandanew.php');
    exit;
}

$query = "SELECT s.*, k.nama_kategori
          FROM stories s
          LEFT JOIN kategori k ON k.id_kategori = s.kategori_id
          WHERE s.id_novel = '$id'
          LIMIT 1";
$result = mysqli_query($konek, $query);
$novel = mysqli_fetch_assoc($result);

if (!$novel) {
    echo "<p>Novel tidak ditemukan. <a href='berandanew.php'>Kembali</a></p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Novel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-danger">
    <div class="container-fluid justify-content-start gap-4">
        <a href="#" onclick="goBack(); return false;" class="btn btn-outline-light btn-sm">Kembali</a>
        <span class="navbar-brand mb-0 h1">Detail Novel</span>
    </div>
</nav>

<div class="container my-4" style="max-width: 700px;">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <!-- Cover dan Judul di Atas -->
            <div class="text-center mb-4">
                <img src="<?= $novel['cover_url']; ?>" class="img-fluid rounded shadow mb-3" style="max-width: 250px;" alt="Cover">
                <h2 class="mb-3"><?= htmlspecialchars($novel['judul']); ?></h2>
                <?php
                    if ($_GET['from'] === 'kategori') {
                ?>
                <a href="reader.php?id=<?= $novel['id_novel']; ?>&from=<?= $_GET['from'] ?>&kategori_id=<?= $_GET['kategori_id'] ?>&kategori_nama=<?= $_GET['kategori_nama'] ?>" class="btn btn-primary">Baca Novel</a>
                <?php
                    ;}else{
                ?>
                <a href="reader.php?id=<?= $novel['id_novel']; ?>&from=<?= $_GET['from'] ?>" class="btn btn-primary">Baca Novel</a>
                <?php
                    ;}
                ?>
                
            </div>
            <hr>
            <!-- Informasi Novel di Bawah -->
            <h5 class="mb-3">Informasi Novel</h5>
            
            <div class="row mb-3">
                <div class="col-4">
                    <strong>Author&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</strong>
                </div>
                <div class="col-8">
                    <?= htmlspecialchars($novel['author']); ?>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-4">
                    <strong>Kategori&nbsp;&nbsp;:</strong>
                </div>
                <div class="col-8">
                    <span class="badge bg-secondary"><?= $novel['nama_kategori']; ?></span>
                </div>
            </div>
            
            <div class="row">
                <div class="col-4">
                    <strong>Deskripsi&nbsp;:</strong>
                </div>
                <div class="col-8">
                    <p class="text-muted mb-0"><?= nl2br(htmlspecialchars($novel['deskripsi'] ?: 'Tidak ada deskripsi.')); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function goBack() {
    const urlParams = new URLSearchParams(window.location.search);
    const from = urlParams.get('from');
    
    // cek isi parameter from, lalu redirect sesuai string
    if (from === 'berandanew') {
        window.location.href = 'berandanew.php';
    } else if (from === 'kategori') {
        const kategoriId = urlParams.get('kategori_id');
        const kategoriNama = urlParams.get('kategori_nama');
        window.location.href = 'kategori.php?kategori_id=' + kategoriId + '&kategori_nama=' + kategoriNama;
    } else if (from === 'novelsaya') {
        window.location.href = 'novelsaya.php';
    } else if (from === 'seluruhnovel') {
        window.location.href = 'seluruhnovel.php';
    } else if (document.referrer && document.referrer.indexOf('reader.php') !== -1) {
        // kalo dari reader tapi ga ada from, default ke seluruhnovel
        window.location.href = 'seluruhnovel.php';
    } else if (document.referrer) {
        // fallback pakai history back
        history.back();
    } else {
        // default terakhir
        window.location.href = 'seluruhnovel.php';
    }
}
</script>
</body>
</html>
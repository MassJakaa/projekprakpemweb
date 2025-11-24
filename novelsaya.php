<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "SELECT stories.*, kategori.nama_kategori AS kategori_nama
          FROM stories 
          JOIN kategori ON kategori.id_kategori = stories.kategori_id
          WHERE user_id = '$user_id'
          ORDER BY id_novel DESC";

$result = mysqli_query($konek, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karya Anda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Iki Navbar -->
<nav class="navbar navbar-dark bg-danger">
    <div class="container-fluid justify-content-start gap-4">
        <a href="berandanew.html" class="btn btn-outline-light btn-sm">Kembali</a>
        <span class="navbar-brand mb-0 h1">Karya Anda</span>
    </div>
</nav>

<!-- Iki Isi konten -->
<div class="container my-4">
    <h2 class="mb-4">Daftar Karya Anda</h2>
    <?php while ($data = mysqli_fetch_assoc($result)){ ?>
        <div class="card mb-3 shadow-sm">
            <div class="row g-0">
                <div class="col-md-2">
                    <img src="<?= $data['cover_url']; ?>" 
                         class="img-fluid rounded-start h-100 object-fit-cover" 
                         alt="Cover">
                </div>
                <div class="col-md-10">
                    <div class="card-body">
                        <h5 class="card-title"><?= $data['judul']; ?></h5>
                        <p class="card-text mb-1">
                            <strong>Author:</strong> <?= $data['author']; ?>
                        </p>
                        <p class="card-text mb-2">
                            <strong>Kategori:</strong> 
                            <span class="badge bg-secondary"><?= $data['kategori_nama']; ?></span>
                        </p>
                        <p class="card-text text-muted">
                            <?= substr($data['deskripsi'], 0, 120); ?>...
                        </p>
                        
                        <div class="mt-3">
                            <a href="editnovel.php?id=<?= $data['id_novel']; ?>" 
                               class="btn btn-primary btn-sm">
                               Edit
                            </a>
                            <a href="hapusnovel.php?id=<?= $data['id_novel']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Yakin ingin menghapus novel ini?');">
                               Hapus
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
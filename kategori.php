<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$kategori_id = $_GET['kategori_id'];
$kategori_nama = $_GET['kategori_nama'];
// pagination setup
$limit = 5; // jumlah novel per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// hitung total novel user
$count_query = "SELECT COUNT(*) as total FROM stories WHERE kategori_id = '$kategori_id'";
$count_result = mysqli_query($konek, $count_query);
$total_novels = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_novels / $limit);

// ambil data novel dengan limit
$query = "SELECT stories.*, kategori.nama_kategori AS kategori_nama
          FROM stories 
          JOIN kategori ON kategori.id_kategori = stories.kategori_id
          WHERE kategori_id = '$kategori_id'
          ORDER BY id_novel DESC
          LIMIT $limit OFFSET $offset";

$result = mysqli_query($konek, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-danger">
    <div class="container-fluid justify-content-start gap-4">
        <a href="berandanew.php" class="btn btn-outline-light btn-sm">Kembali</a>
        <span class="navbar-brand mb-0 h1"><?= $kategori_nama ?></span>
    </div>
</nav>

<div class="container my-4">
    <h2 class="mb-4">Daftar Novel Dengan Kategori <?= $kategori_nama ?></h2>
    
    <?php 
    if(mysqli_num_rows($result) == 0){
        echo '<div class="alert alert-info">Belum ada karya yang ditambahkan.</div>';
    }
    
    while ($data = mysqli_fetch_assoc($result)){ 
    ?>
    <div class="card mb-3 shadow-sm">
        <div class="row g-0">
            <div class="col-md-2">
                <div class="ratio" style="--bs-aspect-ratio:134%;">
                    <a href="detailnovel.php?id=<?php echo (int)$data['id_novel']; ?>&from=kategorikategori_id=<?= $kategori_id ?>&kategori_nama=<?= $kategori_nama ?>">
                        <img src="<?php echo htmlspecialchars($data['cover_url']); ?>" class="img-fluid object-fit-cover w-100 h-100" alt="<?php echo htmlspecialchars($data['judul']); ?>">
                    </a>
                </div>
            </div>
            <div class="col-md-10">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($data['judul']); ?></h5>
                    <p class="card-text mb-1">
                        <strong>Author:</strong> <?= htmlspecialchars($data['author']); ?>
                    </p>
                    <p class="card-text mb-2">
                        <strong>Kategori:</strong> 
                        <span class="badge bg-secondary"><?= $data['kategori_nama']; ?></span>
                    </p>
                    <p class="card-text text-muted">
                        <?= substr($data['deskripsi'], 0, 120); ?>...
                    </p>
                    
                    <div class="mt-3">
                        <a href="detailnovel.php?id=<?= $data['id_novel'] ?>&from=kategori&kategori_id=<?= $kategori_id ?>&kategori_nama=<?= $kategori_nama ?>" class="btn btn-primary">Baca Novel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <nav>
        <ul class="pagination justify-content-center">
            <!-- tombol Previous -->
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
            </li>
            
            <?php 
            // tampilkan nomor halaman
            for ($i = 1; $i <= $total_pages; $i++): 
                if ($i == 1 || $i == $total_pages || ($i >= $page - 2 && $i <= $page + 2)):
            ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php 
                elseif ($i == $page - 3 || $i == $page + 3):
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                endif;
            endfor; 
            ?>
            
            <!-- tombol Next -->
            <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
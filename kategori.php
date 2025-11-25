<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// ambil semua kategori
$kategori_query = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
$kategori_result = mysqli_query($konek, $kategori_query);

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
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column" style="min-height: 100vh;">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-danger border-bottom sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center fw-bold" href="berandanew.php">
            <img src="gambar/MU.png" alt="Logo" height="50" class="me-2">
            <span class="fs-4">The Read Devils</span>
        </a>

        <!-- Kategori Dropdown -->
        <div class="dropdown">
            <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                Kategori
            </button>
            <ul class="dropdown-menu">
                <li><h6 class="dropdown-header">KATEGORI</h6></li>
                <li><hr class="dropdown-divider"></li>
                <?php 
                mysqli_data_seek($kategori_result, 0);
                while($kat = mysqli_fetch_assoc($kategori_result)): 
                ?>
                    <li><a class="dropdown-item" href="kategori.php?kategori_id=<?= $kat['id_kategori'] ?>&kategori_nama=<?= $kat['nama_kategori'] ?>"><?= $kat['nama_kategori'] ?></a></li>
                <?php endwhile; ?>
            </ul>
        </div>

        <!-- Search Bar -->
        <div class="search-container">
            <form class="d-flex" role="search" action="cari.php" method="GET">
                <input type="search" name="search" class="form-control" placeholder="Cari novel..." aria-label="Search">
            </form>
        </div>

        <!-- Right Side: Create & User -->
        <div class="d-flex align-items-center gap-3">
            <?php if(isset($_SESSION['user_id'])): ?>
            <div class="dropdown">
                <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    Create
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="novelsaya.php">Karya Anda</a></li>
                    <li><a class="dropdown-item" href="tambahnovel.php">Tambah Novel</a></li>
                </ul>
            </div>

            <div class="dropdown">
                <a href="#" class="d-block" data-bs-toggle="dropdown">
                    <img src="https://github.com/mdo.png" alt="User" width="40" height="40" class="rounded-circle">
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="profil.php">Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="logout.php">Log out</a></li>
                </ul>
            </div>
            <?php else: ?>
            <a href="login.php" class="btn btn-light">Login</a>
            <a href="register.php" class="btn btn-outline-light">Register</a>
            <?php endif; ?>
            </div>
        </div>
</nav>

<div class="container my-4 flex-grow-1">
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
                    <a href="detailnovel.php?id=<?php echo (int)$data['id_novel']; ?>&from=kategori&kategori_id=<?= $kategori_id ?>&kategori_nama=<?= $kategori_nama ?>">
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
                <a class="page-link" href="?kategori_id=<?= $kategori_id ?>&kategori_nama=<?= $kategori_nama ?>&page=<?= $page - 1 ?>">Previous</a>
            </li>
            
            <?php 
            // tampilkan nomor halaman
            for ($i = 1; $i <= $total_pages; $i++): 
                if ($i == 1 || $i == $total_pages || ($i >= $page - 2 && $i <= $page + 2)):
            ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?kategori_id=<?= $kategori_id ?>&kategori_nama=<?= $kategori_nama ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php 
                elseif ($i == $page - 3 || $i == $page + 3):
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                endif;
            endfor; 
            ?>
            
            <!-- tombol Next -->
            <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                <a class="page-link" href="?kategori_id=<?= $kategori_id ?>&kategori_nama=<?= $kategori_nama ?>&page=<?= $page + 1 ?>">Next</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-4 mt-auto">
    <p class="mb-0">&copy; 2024 The Read Devils. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
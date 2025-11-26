<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

// ambil semua kategori
$kategori_query = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
$kategori_result = mysqli_query($konek, $kategori_query);

// ambil novel random untuk rekomendasi (6 novel)
$rekomendasi_query = "SELECT s.*, k.nama_kategori 
                      FROM stories s
                      LEFT JOIN kategori k ON k.id_kategori = s.kategori_id
                      ORDER BY RAND()
                      LIMIT 8";
$rekomendasi_result = mysqli_query($konek, $rekomendasi_query);
$rekomendasi_novels = [];
while($row = mysqli_fetch_assoc($rekomendasi_result)){
    $rekomendasi_novels[] = $row;
}

// ambil novel terbaru untuk pilihan (6 novel)
$pilihan_query = "SELECT s.*, k.nama_kategori 
                  FROM stories s
                  LEFT JOIN kategori k ON k.id_kategori = s.kategori_id
                  ORDER BY s.id_novel DESC
                  LIMIT 8";
$pilihan_result = mysqli_query($konek, $pilihan_query);
$pilihan_novels = [];
while($row = mysqli_fetch_assoc($pilihan_result)){
    $pilihan_novels[] = $row;
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Home</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
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
            <img src="gambar/profil.jpg" alt="User" width="40" height="40" class="rounded-circle">
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

  <!-- CAROUSEL 1: Hero Promo Banner -->
  <section class="container my-5">
    <div id="hero-promo-carousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-indicators">
        <button type="button" data-bs-target="#hero-promo-carousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#hero-promo-carousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#hero-promo-carousel" data-bs-slide-to="2"></button>
      </div>

      <div class="carousel-inner" style="overflow: hidden; border-radius: 10px;">
        <div class="carousel-item active">
          <img src="gambar/promo1.jpg" class="d-block w-100" alt="Promo 1" style="height: auto; aspect-ratio: 16/9; object-fit: cover; display: block;">
        </div>
        <div class="carousel-item">
          <img src="gambar/promo2.jpg" class="d-block w-100" alt="Promo 2" style="height: auto; aspect-ratio: 16/9; object-fit: cover; display: block;">
        </div>
        <div class="carousel-item">
          <img src="gambar/promo3.jpg" class="d-block w-100" alt="Promo 3" style="height: auto; aspect-ratio: 16/9; object-fit: cover; display: block;">
        </div>
      </div>

      <button class="carousel-control-prev" type="button" data-bs-target="#hero-promo-carousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#hero-promo-carousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </section>

  <!-- CAROUSEL 2:  Novel Pilihan -->
  <section class="novel-carousel-section bg-light py-5">
    <div class="container novel-carousel-section">
      <h2 class="text-center text-danger fw-bold mb-4">Novel Pilihan</h2>

      <div id="recommended-novels-carousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
          <?php 
          $chunks = array_chunk($rekomendasi_novels, 4);
          foreach($chunks as $index => $chunk): 
          ?>
          <div class="carousel-item <?= $index == 0 ? 'active' : '' ?>">
            <div class="row g-4">
              <?php foreach($chunk as $novel): ?>
              <div class="col-md-3">
                <div class="card novel-card shadow-sm">
                  <a href="detailnovel.php?id=<?php echo (int)$novel['id_novel']; ?>&from=berandanew">
                    <img src="<?= $novel['cover_url'] ?>" class="card-img-top" alt="<?= htmlspecialchars($novel['judul']) ?>">
                  </a>
                  <div class="card-body text-center">
                    <h5 class="card-title"><?= htmlspecialchars($novel['judul']) ?></h5>
                    <a href="detailnovel.php?id=<?= $novel['id_novel'] ?>&from=berandanew" class="btn btn-danger">Baca Sekarang</a>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <button class="carousel-control-prev carousel-nav-btn" type="button" data-bs-target="#recommended-novels-carousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next carousel-nav-btn" type="button" data-bs-target="#recommended-novels-carousel" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
        </button>
      </div>
    </div>
  </section>

  <!-- CAROUSEL 3: Novel Terbaru -->
  <section class="novel-carousel-section bg-white py-5">
    <div class="container novel-carousel-section">
      <h2 class="text-center text-danger fw-bold mb-4">Novel Terkini</h2>

      <div id="pilihan-novels-carousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
          <?php 
          $chunks2 = array_chunk($pilihan_novels, 4);
          foreach($chunks2 as $index => $chunk): 
          ?>
          <div class="carousel-item <?= $index == 0 ? 'active' : '' ?>">
            <div class="row g-4">
              <?php foreach($chunk as $novel): ?>
              <div class="col-md-3">
                <div class="card novel-card shadow-sm">
                  <a href="detailnovel.php?id=<?php echo (int)$novel['id_novel']; ?>&from=berandanew">
                    <img src="<?= $novel['cover_url'] ?>" class="card-img-top" alt="<?= htmlspecialchars($novel['judul']) ?>">
                  </a>
                  <div class="card-body text-center">
                    <h5 class="card-title"><?= htmlspecialchars($novel['judul']) ?></h5>
                    <a href="detailnovel.php?id=<?= $novel['id_novel'] ?>&from=berandanew" class="btn btn-dark">Baca Sekarang</a>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <button class="carousel-control-prev carousel-nav-btn" type="button" data-bs-target="#pilihan-novels-carousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next carousel-nav-btn" type="button" data-bs-target="#pilihan-novels-carousel" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
        </button>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-dark text-white text-center py-4 mt-5">
    <p class="mb-0">&copy; 2025 The Read Devils. All rights reserved.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
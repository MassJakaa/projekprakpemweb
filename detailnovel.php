<?php
session_start();
require_once 'koneksi.php';

// Pilihan overlay: 'secondary' atau 'danger' (ganti sesuai selera)
$overlay_choice = 'secondary'; // default saya sarankan 'secondary' untuk keterbacaan

// Map nama warna ke rgba untuk overlay
$overlay_map = [
    'secondary' => 'rgba(108,117,125,0.60)', // #6c757d
    'danger'    => 'rgba(220,53,69,0.55)'     // #dc3545
];
$overlay_rgba = $overlay_map[$overlay_choice] ?? $overlay_map['secondary'];

// Ambil ID novel dari query string
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: seluruhnovel.php');
    exit;
}

// Ambil data novel (prepared statement)
$stmt = $konek->prepare("
    SELECT s.*, k.nama_kategori
    FROM stories s
    LEFT JOIN kategori k ON k.id_kategori = s.kategori_id
    WHERE s.id_novel = ?
    LIMIT 1
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$novel = $result->fetch_assoc();
$stmt->close();

if (!$novel) {
    echo "<p>Novel tidak ditemukan. <a href='seluruhnovel.php'>Kembali</a></p>";
    exit;
}

// Helper untuk output aman
function e($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

// Cek apakah user pemilik (opsional)
$is_owner = false;
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $novel['user_id']) {
    $is_owner = true;
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo e($novel['judul']); ?> — Detail</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* efek blur background (sedikit CSS diperlukan untuk blur) */
    .hero {
      position: relative;
      color: #fff;
      overflow: hidden;
      border-radius: .375rem;
    }
    .hero .bg-blur {
      position: absolute;
      inset: 0;
      background-position: center;
      background-size: cover;
      filter: blur(6px) saturate(.9);
      transform: scale(1.06);
    }
    .hero .overlay {
      position: absolute;
      inset: 0;
    }
    .hero-content { position: relative; z-index: 2; }
    .cover-ratio { --bs-aspect-ratio:133.33333%; } /* 3:4 */
    .cover-img { object-fit: cover; border-radius: .375rem; }
    @media (max-width: 767.98px) {
      .hero { border-radius: 0; }
    }
  </style>
</head>
<body class="bg-light">

<!-- Navbar sama seperti sebelumnya -->
<nav class="navbar navbar-dark bg-danger">
  <div class="container-fluid justify-content-start gap-4">
    <a href="seluruhnovel.php" class="btn btn-outline-light btn-sm">Kembali</a>
    <span class="navbar-brand mb-0 h1">Karya Anda</span>
  </div>
</nav>

<div class="container my-4">
  <!-- HERO: hanya cover (klik), judul, tombol "Baca Novel" -->
  <div class="hero mb-4">
    <?php $bg = !empty($novel['cover_url']) ? $novel['cover_url'] : ''; ?>
    <div class="bg-blur" style="background-color: rgba(131, 131, 131, 0.55);"></div>

    <!-- overlay menggunakan rgba sesuai pilihan di atas -->
    <div class="overlay" style="background: linear-gradient(180deg, <?php echo $overlay_rgba; ?> 0%, rgba(0,0,0,0.55) 100%);"></div>

    <div class="container hero-content py-4">
      <div class="row align-items-center g-3">
        <div class="col-auto">
          <div class="ratio cover-ratio" style="width:140px; max-width:22vw; min-width:100px;">
            <a href="reader.php?id=<?php echo (int)$novel['id_novel']; ?>">
              <?php if (!empty($novel['cover_url'])): ?>
                <img src="<?php echo e($novel['cover_url']); ?>" alt="<?php echo e($novel['judul']); ?>" class="img-fluid w-100 h-100 cover-img">
              <?php else: ?>
                <svg class="bd-placeholder-img w-100 h-100" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="No cover" preserveAspectRatio="xMidYMid slice" focusable="false">
                  <rect width="100%" height="100%" fill="#e9ecef"></rect>
                  <text x="50%" y="50%" fill="#6c757d" dy=".3em" text-anchor="middle">No Cover</text>
                </svg>
              <?php endif; ?>
            </a>
          </div>
        </div>

        <div class="col">
          <h1 class="h3 fw-bold mb-2"><?php echo e($novel['judul']); ?></h1>

          <div>
            <a href="reader.php?id=<?php echo (int)$novel['id_novel']; ?>" class="btn btn-primary btn-sm me-2">Baca Novel</a>
          </div>
        </div>
      </div>
    </div> <!-- /hero-content -->
  </div> <!-- /hero -->

  <!-- BAGIAN BAWAH: Author, Kategori, Deskripsi -->
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="row">
        <div class="col-md-4">
          <h6>Author</h6>
          <p class="mb-3"><span class="badge bg-secondary"><?php echo e($novel['author'] ?: '-'); ?></span></p>

          <h6>Kategori</h6>
          <p class="mb-3">
            <?php if (!empty($novel['nama_kategori'])): ?>
              <span class="badge bg-dark text-white"><?php echo e($novel['nama_kategori']); ?></span>
            <?php else: ?>
              <span class="text-muted">-</span>
            <?php endif; ?>
          </p>
        </div>

        <div class="col-md-8">
          <h6>Deskripsi</h6>
          <p class="text-muted"><?php echo nl2br(e($novel['deskripsi'] ?: 'Tidak ada deskripsi.')); ?></p>
        </div>
      </div>
    </div>
  </div>
</div> <!-- /container -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
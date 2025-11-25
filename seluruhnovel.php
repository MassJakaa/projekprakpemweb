<?php
require_once 'koneksi.php';

// Pagination
$novel_per_page = 30;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$start = ($page - 1) * $novel_per_page;

// Hitung total novel
$total_result = mysqli_query($konek, "SELECT COUNT(*) AS total FROM stories");
$total_row = mysqli_fetch_assoc($total_result);
$total_novel = (int)$total_row['total'];
$total_pages = ($total_novel > 0) ? ceil($total_novel / $novel_per_page) : 1;

// Ambil data novel untuk halaman ini
$query = "
  SELECT id_novel, judul, cover_url
  FROM stories
  ORDER BY id_novel DESC
  LIMIT $start, $novel_per_page
";
$result = mysqli_query($konek, $query);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Semua Novel</title>
  <!-- Pure Bootstrap (tidak ada CSS custom) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar disamakan struktur dan kelasnya dengan novelsaya.php -->
<nav class="navbar navbar-dark bg-danger">
  <div class="container-fluid justify-content-start gap-4">
    <a href="berandanew.php" class="btn btn-outline-light btn-sm">Kembali</a>
    <span class="navbar-brand mb-0 h1">Semua Novel</span>
  </div>
</nav>

<div class="container mb-5 py-5">
  <!-- Grid pure Bootstrap: 2 cols xs, 3 cols md, 5 cols lg -->
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
      <div class="col">
        <div class="card h-100 shadow-sm">
          <!-- Aspect ratio 3:4 menggunakan utilitas Bootstrap ratio dengan style inline untuk variabel aspek -->
          <div class="ratio" style="--bs-aspect-ratio:133.33333%;">
            <a href="detailnovel.php?id=<?php echo (int)$row['id_novel']; ?>">
              <?php if (!empty($row['cover_url'])): ?>
                <img src="<?php echo htmlspecialchars($row['cover_url']); ?>" class="img-fluid object-fit-cover w-100 h-100" alt="<?php echo htmlspecialchars($row['judul']); ?>">
              <?php else: ?>
                <svg class="bd-placeholder-img w-100 h-100" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="No cover" preserveAspectRatio="xMidYMid slice" focusable="false">
                  <rect width="100%" height="100%" fill="#e9ecef"></rect>
                  <text x="50%" y="50%" fill="#6c757d" dy=".3em" text-anchor="middle">No Cover</text>
                </svg>
              <?php endif; ?>
            </a>
          </div>

          <div class="card-body d-flex flex-column">
            <h6 class="card-title mb-0">
              <a href="detailnovel.php?id=<?php echo (int)$row['id_novel']; ?>" class="text-decoration-none text-dark">
                <?php echo htmlspecialchars($row['judul']); ?>
              </a>
            </h6>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
  </div>

  <!-- Pagination Ea Ruwet iki pak-->
  <nav aria-label="Page navigation" class="mt-4">
    <ul class="pagination justify-content-center">
      <li class="page-item <?php if($page <= 1) echo 'disabled'; ?>">
        <a class="page-link" href="?page=<?php echo max(1, $page - 1); ?>">Previous</a>
      </li>

      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
          <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        </li>
      <?php endfor; ?>

      <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
        <a class="page-link" href="?page=<?php echo min($total_pages, $page + 1); ?>">Next</a>
      </li>
    </ul>
  </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
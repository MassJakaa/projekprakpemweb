<?php
// reader.php - direct PDF embed via url from DB
// usage: reader.php?id=123

require_once 'koneksi.php';

// ambil id
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: seluruhnovel.php');
    exit;
}

// ambil pdf_url & judul dari DB (prepared statement)
$stmt = $konek->prepare("SELECT judul, pdf_url FROM stories WHERE id_novel = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row || empty($row['pdf_url'])) {
    echo "<p>File PDF tidak ditemukan. <a href='seluruhnovel.php'>Kembali</a></p>";
    exit;
}

function e($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

// Tentukan src iframe:
// - jika pdf_url dimulai dengan http/https -> pakai langsung (tapi bisa diblokir dari embed oleh server remote)
// - jika bukan -> anggap file lokal di uploads/pdfs/ dan validasi path
$pdf_url = $row['pdf_url'];
$iframe_src = '';
if (preg_match('#^https?://#i', $pdf_url)) {
    $iframe_src = $pdf_url;
} else {
    // file lokal: pastikan aman dan ada di folder uploads/pdfs/
    $baseDir = realpath(__DIR__ . '/uploads/pdfs/') . DIRECTORY_SEPARATOR;
    $file = basename($pdf_url); // hapus directory traversal
    $fullPath = realpath($baseDir . $file);
    if ($fullPath && strpos($fullPath, $baseDir) === 0 && is_file($fullPath)) {
        // gunakan URL relatif supaya webserver bisa serve file langsung
        $iframe_src = 'uploads/pdfs/' . rawurlencode($file);
    } else {
        echo "<p>File PDF lokal tidak ditemukan. <a href='seluruhnovel.php'>Kembali</a></p>";
        exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Baca: <?php echo e($row['judul']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>html,body{height:100%;margin:0} .topbar{height:56px} .viewer{width:100%;height:calc(100vh - 56px);border:0}</style>
</head>
<body>
  <nav class="navbar navbar-light bg-light topbar">
    <div class="container-fluid d-flex align-items-center justify-content-between">
      <a href="detailnovel.php?id=<?php echo (int)$id; ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
      <div class="text-truncate" style="max-width:60%;"><strong><?php echo e($row['judul']); ?></strong></div>
      <div>
        <a href="<?php echo e($iframe_src); ?>" class="btn btn-primary btn-sm" download>Unduh</a>
      </div>
    </div>
  </nav>

  <!-- iframe atau embed -->
  <iframe src="<?php echo e($iframe_src); ?>" class="viewer" title="PDF Reader"></iframe>

  <!-- catatan: jika iframe kosong/blank, kemungkinan remote server blok embed (X-Frame-Options) -->
</body>
</html>
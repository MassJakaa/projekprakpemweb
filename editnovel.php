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

$novel_id = $_GET['id'];

$query = "SELECT * FROM stories WHERE id_novel='$novel_id'";
$data = mysqli_fetch_assoc(mysqli_query($konek, $query));

if (!$data) {
    die("Novel tidak ditemukan.");
}

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = mysqli_real_escape_string($konek, $_POST['judul']);
    $author = mysqli_real_escape_string($konek, $_POST['author']);
    $kategori = $_POST['kategori'];
    $deskripsi = mysqli_real_escape_string($konek, $_POST['deskripsi']);

    $cover_url = $_POST['cover_url'];
    $cover_id = $_POST['cover_public_id'];
    $pdf_url = $_POST['pdf_url'];
    $pdf_id = $_POST['pdf_public_id'];

    $update = "UPDATE stories SET 
               judul='$judul',
               author='$author',
               kategori_id='$kategori',
               deskripsi='$deskripsi',
               cover_url='$cover_url',
               cover_id='$cover_id',
               pdf_url='$pdf_url',
               pdf_id='$pdf_id'
               WHERE id_novel='$novel_id'";

    if (mysqli_query($konek, $update)) {
        $success = "Novel berhasil diperbarui!";
        header("refresh:2;url=novelsaya.php");
    } else {
        $error = "Gagal update: " . mysqli_error($konek);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Novel</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://upload-widget.cloudinary.com/global/all.js"></script>
</head>
<body class="bg-light">

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

<div class="container my-4" style="max-width: 800px;">
    <div class="card shadow-sm">
        <div class="card-body p-4">

        <?php if($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Judul</label>
                <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($data['judul']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Author</label>
                <input type="text" name="author" class="form-control" value="<?= htmlspecialchars($data['author']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Kategori</label>
                <select name="kategori" class="form-select">
                    <?php
                    $k = mysqli_query($konek, "SELECT * FROM kategori");
                    while ($row = mysqli_fetch_assoc($k)):
                    ?>
                    <option value="<?= $row['id_kategori']; ?>" <?= $row['id_kategori'] == $data['kategori_id'] ? 'selected' : '' ?>>
                        <?= $row['nama_kategori']; ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="5"><?= htmlspecialchars($data['deskripsi']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Cover Saat Ini</label><br>
                <img src="<?= $data['cover_url'] ?>" class="img-thumbnail mb-2" style="max-width: 200px;">
                <br>
                <button type="button" onclick="coverWidget.open()" class="btn btn-primary btn-sm">Upload Cover Baru</button>
            </div>
            <input type="hidden" name="cover_url" id="coverUrl" value="<?= $data['cover_url'] ?>">
            <input type="hidden" name="cover_public_id" id="coverPublicId" value="<?= $data['cover_id'] ?>">
            <div class="mb-3">
                <label class="form-label fw-bold">File PDF Saat Ini</label>
                <p class="text-muted"><?= basename($data['pdf_url']); ?></p>
                <button type="button" onclick="pdfWidget.open()" class="btn btn-primary btn-sm">Upload PDF Baru</button>
            </div>
            <input type="hidden" name="pdf_url" id="pdfUrl" value="<?= $data['pdf_url'] ?>">
            <input type="hidden" name="pdf_public_id" id="pdfPublicId" value="<?= $data['pdf_id'] ?>">
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary flex-fill">Simpan Perubahan</button>
                <a href="novelsaya.php" class="btn btn-danger flex-fill">Batal</a>
            </div>
        </form>

        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-4 mt-5">
    <p class="mb-0">&copy; 2024 The Read Devils. All rights reserved.</p>
</footer>

<!-- Buat Upload Ke Cloudinary -->
<script>
const coverWidget = cloudinary.createUploadWidget({
    cloudName: "<?= CLOUDINARY_CLOUD_NAME ?>",
    uploadPreset: "<?= CLOUDINARY_UPLOAD_PRESET ?>",
    clientAllowedFormats: ["jpg","png"],
}, (err,res)=>{
    if(res.event === "success"){
        document.getElementById("coverUrl").value = res.info.secure_url;
        document.getElementById("coverPublicId").value = res.info.public_id;
    }
});

const pdfWidget = cloudinary.createUploadWidget({
    cloudName: "<?= CLOUDINARY_CLOUD_NAME ?>",
    uploadPreset: "<?= CLOUDINARY_UPLOAD_PRESET ?>",
    clientAllowedFormats: ["pdf"],
}, (err,res)=>{
    if(res.event === "success"){
        document.getElementById("pdfUrl").value = res.info.secure_url;
        document.getElementById("pdfPublicId").value = res.info.public_id;
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
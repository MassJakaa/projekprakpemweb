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

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = mysqli_real_escape_string($konek, $_POST['judul']);
    $author = mysqli_real_escape_string($konek, $_POST['author']);
    $kategori = $_POST['kategori'];
    $deskripsi = mysqli_real_escape_string($konek, $_POST['deskripsi']);
    $user_id = $_SESSION['user_id'];
    
    $cover_url = '';
    $cover_public_id = '';
    if (isset($_POST['cover_url']) && !empty($_POST['cover_url'])) {
        $cover_url = $_POST['cover_url'];
        $cover_public_id = $_POST['cover_public_id'];
    }
    
    $pdf_url = '';
    $pdf_public_id = '';
    if (isset($_POST['pdf_url']) && !empty($_POST['pdf_url'])) {
        $pdf_url = $_POST['pdf_url'];
        $pdf_public_id = $_POST['pdf_public_id'];
    }
    
    $query = "INSERT INTO stories (user_id, kategori_id, judul, author, deskripsi, cover_url, cover_id, pdf_url, pdf_id) 
              VALUES ('$user_id', '$kategori', '$judul', '$author', '$deskripsi', '$cover_url', '$cover_public_id', '$pdf_url', '$pdf_public_id')";
    
    if (mysqli_query($konek, $query)) {
        $success = "Novel berhasil ditambahkan!";
        header("refresh:2;url=novelsaya.php");
    } else {
        $error = "Gagal menambahkan novel: " . mysqli_error($konek);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Novel</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://upload-widget.cloudinary.com/global/all.js"></script>
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

<div class="container my-4 flex-grow-1" style="max-width: 800px;">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" id="novelForm">
                <div class="mb-3">
                    <label class="form-label fw-bold">Judul Novel <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Author <span class="text-danger">*</span></label>
                    <input type="text" name="author" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Kategori</label>
                    <select name="kategori" class="form-select">
                        <?php
                        $kat = mysqli_query($konek, "SELECT * FROM kategori");
                        while ($k = mysqli_fetch_assoc($kat)){
                            echo '<option value="'.$k['id_kategori'].'">'.$k['nama_kategori'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="5" placeholder="Ceritakan tentang novel Anda..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Cover Novel <span class="text-danger">*</span></label>
                    <div class="border rounded p-4 text-center" style="cursor: pointer; border-style: dashed !important;" id="coverUpload">
                        <div style="font-size: 48px;">🖼️</div>
                        <div class="mt-2">Klik untuk upload cover</div>
                        <small class="text-muted">Format: JPG, PNG (Max 10MB)</small>
                    </div>
                    <div id="coverPreview" class="mt-3"></div>
                    <input type="hidden" name="cover_url" id="coverUrl">
                    <input type="hidden" name="cover_public_id" id="coverPublicId">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">File PDF Novel <span class="text-danger">*</span></label>
                    <div class="border rounded p-4 text-center" style="cursor: pointer; border-style: dashed !important;" id="pdfUpload">
                        <div style="font-size: 48px;">📄</div>
                        <div class="mt-2">Klik untuk upload PDF</div>
                        <small class="text-muted">Format: PDF (Max 50MB)</small>
                    </div>
                    <div id="pdfPreview" class="mt-3"></div>
                    <input type="hidden" name="pdf_url" id="pdfUrl">
                    <input type="hidden" name="pdf_public_id" id="pdfPublicId">
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary flex-fill">Simpan Novel</button>
                    <a href="berandanew.php" class="btn btn-danger flex-fill">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-4 mt-5">
    <p class="mb-0">&copy; 2024 The Read Devils. All rights reserved.</p>
</footer>

<script>
const coverWidget = cloudinary.createUploadWidget({
    cloudName: '<?= CLOUDINARY_CLOUD_NAME ?>',
    uploadPreset: '<?= CLOUDINARY_UPLOAD_PRESET ?>',
    sources: ['local', 'url'],
    multiple: false,
    maxFileSize: 10000000,
    clientAllowedFormats: ['jpg', 'jpeg', 'png'],
    folder: 'covers'
}, (error, result) => {
    if (!error && result && result.event === "success") {
        document.getElementById('coverUrl').value = result.info.secure_url;
        document.getElementById('coverPublicId').value = result.info.public_id;
        document.getElementById('coverPreview').innerHTML =
            `<img src="${result.info.secure_url}" class="img-thumbnail" style="max-width: 200px;">`;
    }
});

document.getElementById('coverUpload').addEventListener('click', () => {
    coverWidget.open();
});

const pdfWidget = cloudinary.createUploadWidget({
    cloudName: '<?= CLOUDINARY_CLOUD_NAME ?>',
    uploadPreset: '<?= CLOUDINARY_UPLOAD_PRESET ?>',
    sources: ['local'],
    multiple: false,
    maxFileSize: 50000000,
    clientAllowedFormats: ['pdf'],
    folder: 'pdfs'
}, (error, result) => {
    if (!error && result && result.event === "success") {
        document.getElementById('pdfUrl').value = result.info.secure_url;
        document.getElementById('pdfPublicId').value = result.info.public_id;
        document.getElementById('pdfPreview').innerHTML =
            `<div class="text-success fw-bold">✓ ${result.info.original_filename}</div>`;
    }
});

document.getElementById('pdfUpload').addEventListener('click', () => {
    pdfWidget.open();
});

document.getElementById('novelForm').addEventListener('submit', (e) => {
    if (!document.getElementById('coverUrl').value) {
        alert('Harap upload cover novel!');
        e.preventDefault();
        return false;
    }
    if (!document.getElementById('pdfUrl').value) {
        alert('Harap upload file PDF novel!');
        e.preventDefault();
        return false;
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
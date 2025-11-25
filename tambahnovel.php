<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

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
        header("refresh:2;url=berandanew.php");
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://upload-widget.cloudinary.com/global/all.js"></script>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-danger">
    <div class="container-fluid justify-content-start gap-4">
        <a href="berandanew.php" class="btn btn-outline-light btn-sm">Kembali</a>
        <span class="navbar-brand mb-0 h1">Tambah Novel Baru</span>
    </div>
</nav>

<div class="container my-4" style="max-width: 800px;">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
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

<script>
// buat upload cover novel, sulit bet ini ea
const coverWidget = cloudinary.createUploadWidget({
    cloudName: '<?= CLOUDINARY_CLOUD_NAME ?>',
    uploadPreset: '<?= CLOUDINARY_UPLOAD_PRESET ?>',
    sources: ['local', 'url'], // bisa dari komputer atau link url
    multiple: false, // cuma bisa upload 1 file
    maxFileSize: 10000000, // maksimal 10MB
    clientAllowedFormats: ['jpg', 'jpeg', 'png'], // format yang dibolehkan
    folder: 'covers' // simpan di folder covers
}, (error, result) => {
    // kalo upload sukses
    if (!error && result && result.event === "success") {
       
        document.getElementById('coverUrl').value = result.info.secure_url; // simpan url gambar buat database
        document.getElementById('coverPublicId').value = result.info.public_id; // simpan id bisr bisa hapus file  
        document.getElementById('coverPreview').innerHTML =  // tampilkan preview gambar
            `<img src="${result.info.secure_url}" class="img-thumbnail" style="max-width: 200px;">`;
    }
});

// pas diklik area cover, buka widget upload
document.getElementById('coverUpload').addEventListener('click', () => {
    coverWidget.open();
});

// sama kaya di atas tapi buat pdf
const pdfWidget = cloudinary.createUploadWidget({
    cloudName: '<?= CLOUDINARY_CLOUD_NAME ?>',
    uploadPreset: '<?= CLOUDINARY_UPLOAD_PRESET ?>',
    sources: ['local'], // cuma dari komputer
    multiple: false, //1 file
    maxFileSize: 50000000, // max 50MB untuk pdf
    clientAllowedFormats: ['pdf'], // cuma bisa pdf
    folder: 'pdfs'
}, (error, result) => {
    if (!error && result && result.event === "success") {
        document.getElementById('pdfUrl').value = result.info.secure_url;
        document.getElementById('pdfPublicId').value = result.info.public_id;
        document.getElementById('pdfPreview').innerHTML = // tampilkan nama file yang udah diupload
            `<div class="text-success fw-bold">✓ ${result.info.original_filename}</div>`;
    }
});

document.getElementById('pdfUpload').addEventListener('click', () => {
    pdfWidget.open();
});

// validasi sebelum submit form
document.getElementById('novelForm').addEventListener('submit', (e) => {
    // cek apakah cover sudah diupload
    if (!document.getElementById('coverUrl').value) {
        alert('Harap upload cover novel!');
        e.preventDefault();
        return false;
    }
    // cek apakah pdf sudah diupload
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
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
    
    // Upload Cover ke Cloudinary
    $cover_url = '';
    $cover_public_id = '';
    if (isset($_POST['cover_url']) && !empty($_POST['cover_url'])) {
        $cover_url = $_POST['cover_url']; // URL dari Cloudinary widget
        $cover_public_id = $_POST['cover_public_id'];
    }
    
    // Upload PDF ke Cloudinary
    $pdf_url = '';
    $pdf_public_id = '';
    if (isset($_POST['pdf_url']) && !empty($_POST['pdf_url'])) {
        $pdf_url = $_POST['pdf_url'];
        $pdf_public_id = $_POST['pdf_public_id'];
    }
    
    // Insert ke database
    $query = "INSERT INTO stories (user_id, kategori_id, judul, author, deskripsi,  cover_url, cover_id, pdf_url, pdf_id) 
              VALUES ('$user_id', '$kategori', '$judul', '$author', '$deskripsi', '$cover_url', '$cover_public_id', '$pdf_url', '$pdf_public_id')";
    
    if (mysqli_query($konek, $query)) {
        $success = "Novel berhasil ditambahkan!";
        // Redirect setelah 2 detik
        header("refresh:2;url=berandanew.html");
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
    <title>Tambah Novel - Web Novel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .navbar { background: #667eea; color: white; padding: 15px 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .navbar h1 { font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 800px; margin: 30px auto; padding: 0 20px; }
        .form-card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { color: #333; margin-bottom: 25px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #555; font-weight: bold; }
        input[type="text"], textarea, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; font-family: Arial, sans-serif; }
        textarea { min-height: 120px; resize: vertical; }
        input:focus, textarea:focus, select:focus { outline: none; border-color: #667eea; }
        .upload-area { border: 2px dashed #ddd; border-radius: 5px; padding: 30px; text-align: center; cursor: pointer; transition: all 0.3s; margin-top: 8px; }
        .upload-area:hover { border-color: #667eea; background: #f9f9f9; }
        .upload-area.active { border-color: #667eea; background: #e8eaf6; }
        .upload-icon { font-size: 48px; color: #999; margin-bottom: 10px; }
        .upload-text { color: #666; margin-bottom: 5px; }
        .upload-hint { color: #999; font-size: 12px; }
        .preview { margin-top: 15px; }
        .preview img { max-width: 200px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .file-name { color: #667eea; font-weight: bold; margin-top: 10px; }
        .btn-group { display: flex; gap: 15px; margin-top: 30px; }
        .btn { flex: 1; padding: 12px 24px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; font-weight: bold; text-decoration: none; text-align: center; }
        .btn-primary { background: #667eea; color: white; }
        .btn-primary:hover { background: #5568d3; }
        .btn-secondary { background: #999; color: white; }
        .btn-secondary:hover { background: #777; }
        .alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .required { color: red; }
    </style>
    <script src="https://upload-widget.cloudinary.com/global/all.js" type="text/javascript"></script>
</head>
<body>
    <div class="navbar">
        <h1>📚 Web Novel</h1>
        <a href="index.php">← Kembali</a>
    </div>

    <div class="container">
        <div class="form-card">
            <h2>Tambah Novel Baru</h2>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" id="novelForm">
                <div class="form-group">
                    <label>Judul Novel <span class="required">*</span></label>
                    <input type="text" name="judul" required>
                </div>

                <div class="form-group">
                    <label>Author <span class="required">*</span></label>
                    <input type="text" name="author" required>
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori">
                        <option value="1">Romance</option>
                        <option value="2">Fantasy</option>
                        <option value="3">Mystery</option>
                        <option value="4">Horror</option>
                        <option value="5">Sci-Fi</option>
                        <option value="6">Action</option>
                        <option value="7">Drama</option>
                        <option value="8">Comedy</option>
                        <option value="9">Adventure</option>
                        <option value="10">Thriler</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" placeholder="Ceritakan tentang novel Anda..."></textarea>
                </div>

                <div class="form-group">
                    <label>Cover Novel <span class="required">*</span></label>
                    <div class="upload-area" id="coverUpload">
                        <div class="upload-icon">🖼️</div>
                        <div class="upload-text">Klik untuk upload cover</div>
                        <div class="upload-hint">Format: JPG, PNG (Max 10MB)</div>
                    </div>
                    <div class="preview" id="coverPreview"></div>
                    <input type="hidden" name="cover_url" id="coverUrl">
                    <input type="hidden" name="cover_public_id" id="coverPublicId">
                </div>

                <div class="form-group">
                    <label>File PDF Novel <span class="required">*</span></label>
                    <div class="upload-area" id="pdfUpload">
                        <div class="upload-icon">📄</div>
                        <div class="upload-text">Klik untuk upload PDF</div>
                        <div class="upload-hint">Format: PDF (Max 50MB)</div>
                    </div>
                    <div class="preview" id="pdfPreview"></div>
                    <input type="hidden" name="pdf_url" id="pdfUrl">
                    <input type="hidden" name="pdf_public_id" id="pdfPublicId">
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Simpan Novel</button>
                    <a href="berandanew.html" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Cloudinary Upload Widget untuk Cover
        const coverWidget = cloudinary.createUploadWidget({
            cloudName: '<?php echo CLOUDINARY_CLOUD_NAME; ?>',
            uploadPreset: '<?php echo CLOUDINARY_UPLOAD_PRESET; ?>',
            sources: ['local', 'url'],
            multiple: false,
            maxFileSize: 10000000,
            clientAllowedFormats: ['jpg', 'jpeg', 'png'],
            folder: 'covers' // Jika di preset sudah set folder 'web_novel', hapus prefix web_novel/
        }, (error, result) => {
            if (!error && result && result.event === "success") {
                document.getElementById('coverUrl').value = result.info.secure_url;
                document.getElementById('coverPublicId').value = result.info.public_id;
                document.getElementById('coverPreview').innerHTML = 
                    `<img src="${result.info.secure_url}" alt="Cover Preview">`;
            }
        });

        document.getElementById('coverUpload').addEventListener('click', () => {
            coverWidget.open();
        });

        // Cloudinary Upload Widget untuk PDF
        const pdfWidget = cloudinary.createUploadWidget({
            cloudName: '<?php echo CLOUDINARY_CLOUD_NAME; ?>',
            uploadPreset: '<?php echo CLOUDINARY_UPLOAD_PRESET; ?>',
            sources: ['local'],
            multiple: false,
            maxFileSize: 50000000,
            clientAllowedFormats: ['pdf'],
            folder: 'pdfs' // Jika di preset sudah set folder 'web_novel', hapus prefix web_novel/
        }, (error, result) => {
            if (!error && result && result.event === "success") {
                document.getElementById('pdfUrl').value = result.info.secure_url;
                document.getElementById('pdfPublicId').value = result.info.public_id;
                document.getElementById('pdfPreview').innerHTML = 
                    `<div class="file-name">✓ ${result.info.original_filename}</div>`;
            }
        });

        document.getElementById('pdfUpload').addEventListener('click', () => {
            pdfWidget.open();
        });

        // Validasi form
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
</body>
</html>
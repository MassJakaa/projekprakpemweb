<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$novel_id = $_GET['id'];

// Ambil data novel
$query = "SELECT * FROM stories WHERE id_novel='$novel_id'";
$data = mysqli_fetch_assoc(mysqli_query($konek, $query));

if (!$data) {
    die("Novel tidak ditemukan.");
}

$success = "";
$error = "";

// Proses update
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

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .navbar { background: #667eea; color: white; padding: 15px 30px; }
        .navbar h1 { font-size: 24px; display: inline-block; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }

        .container { max-width: 800px; margin: 30px auto; padding: 0 20px; }
        .form-card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { font-weight: bold; display: block; margin-bottom: 8px; }
        input, textarea, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; }

        .btn { padding: 12px; border: none; font-weight: bold; border-radius: 5px; cursor: pointer; }
        .btn-primary { background: #667eea; color: white; }
        .btn-secondary { background: #777; color: white; }
    </style>

    <script src="https://upload-widget.cloudinary.com/global/all.js"></script>
</head>
<body>

<div class="navbar">
    <h1>Edit Novel</h1>
    <a href="novelsaya.php">← Kembali</a>
</div>

<div class="container">
    <div class="form-card">

        <?php if($success): ?>
            <div style="padding:10px;background:#d4edda;color:#155724;margin-bottom:10px;">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Judul</label>
                <input type="text" name="judul" value="<?= $data['judul'] ?>" required>
            </div>

            <div class="form-group">
                <label>Author</label>
                <input type="text" name="author" value="<?= $data['author'] ?>" required>
            </div>

            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori">
                    <?php
                    $k = mysqli_query($konek, "SELECT * FROM kategori");
                    while ($row = mysqli_fetch_assoc($k)):
                    ?>
                    <option value="<?= $row['id_kategori']; ?>" 
                        <?= $row['id_kategori'] == $data['kategori_id'] ? 'selected' : '' ?>>
                        <?= $row['nama_kategori']; ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi"><?= $data['deskripsi'] ?></textarea>
            </div>

            <div class="form-group">
                <label>Cover Saat Ini</label>
                <img src="<?= $data['cover_url'] ?>" width="150"><br><br>
                <button type="button" onclick="coverWidget.open()" class="btn btn-primary">Upload Cover Baru</button>
            </div>

            <input type="hidden" name="cover_url" id="coverUrl" value="<?= $data['cover_url'] ?>">
            <input type="hidden" name="cover_public_id" id="coverPublicId" value="<?= $data['cover_id'] ?>">

            <div class="form-group">
                <label>File PDF Saat Ini</label>
                <p><?= basename($data['pdf_url']); ?></p>
                <button type="button" onclick="pdfWidget.open()" class="btn btn-primary">Upload PDF Baru</button>
            </div>

            <input type="hidden" name="pdf_url" id="pdfUrl" value="<?= $data['pdf_url'] ?>">
            <input type="hidden" name="pdf_public_id" id="pdfPublicId" value="<?= $data['pdf_id'] ?>">

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="karya_saya.php" class="btn btn-secondary">Batal</a>
        </form>

    </div>
</div>

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

</body>
</html>

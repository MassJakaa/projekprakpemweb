<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "SELECT stories.*, kategori.nama_kategori AS kategori_nama
          FROM stories 
          JOIN kategori ON kategori.id_kategori = stories.kategori_id
          WHERE user_id = '$user_id'
          ORDER BY id_novel DESC";

$result = mysqli_query($konek, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karya Saya - Web Novel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .navbar { background: #667eea; color: white; padding: 15px 30px; }
        .navbar h1 { font-size: 24px; display: inline-block; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }

        .container { max-width: 900px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 20px; margin-bottom: 20px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); display: flex; gap: 20px; }
        .card img { width: 120px; border-radius: 5px; }
        .card-info { flex: 1; }
        .btn { display: inline-block; padding: 8px 14px; border-radius: 5px; font-weight: bold; text-decoration: none; cursor: pointer; }
        .btn-edit { background: #667eea; color: white; }
        .btn-delete { background: #e53e3e; color: white; }
    </style>
</head>
<body>

<div class="navbar">
    <h1>📚 Karya Saya</h1>
    <a href="berandanew.html">← Kembali</a>
</div>

<div class="container">

    <h2>Daftar Karya Anda</h2>
    <br>

    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="card">
            <img src="<?php echo $row['cover_url']; ?>" alt="Cover">

            <div class="card-info">
                <h3><?php echo $row['judul']; ?></h3>
                <p><b>Author:</b> <?php echo $row['author']; ?></p>
                <p><b>Kategori:</b> <?php echo $row['kategori_nama']; ?></p>
                <p><?php echo substr($row['deskripsi'], 0, 120); ?>...</p>

                <br>

                <a href="editnovel.php?id=<?php echo $row['id_novel']; ?>" class="btn btn-edit">✏ Edit</a>
                <a href="hapusnovel.php?id=<?php echo $row['id_novel']; ?>"
                   class="btn btn-delete"
                   onclick="return confirm('Yakin ingin menghapus novel ini?');">
                   🗑 Hapus
                </a>
            </div>
        </div>
    <?php endwhile; ?>

</div>

</body>
</html>

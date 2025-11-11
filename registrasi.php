<?php
include "koneksi.php";

if (isset($_POST['register'])) {
  $nama = $_POST['nama'];
  $umur = $_POST['umur'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Enkripsi password
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  // Cek apakah email sudah terdaftar
  $check = mysqli_query($konek, "SELECT * FROM users WHERE email='$email'");
  if (mysqli_num_rows($check) > 0) {
    $error = "Email sudah digunakan.";
  } else {
    $query = mysqli_query($konek, "INSERT INTO users (username, umur, email, password) VALUES ('$nama', '$umur', '$email', '$hashed_password')");
    if ($query) {
      header("Location: login.php");
      exit;
    } else {
      $error = "Gagal registrasi. Coba lagi.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrasi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  <!-- Background dengan gambar -->
  <div class="container-fluid vh-100 d-flex justify-content-center align-items-center bg-dark bg-opacity-75" 
       style="background: url('gambar/hero.jpeg') center/cover no-repeat;">
    
    <!-- Card Registrasi -->
    <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
      <div class="text-center mb-3">
        <img src="gambar/MU.png" alt="Logo" class="mb-2" width="60">
        <h4 class="fw-bold">Buat Akun</h4>
      </div>

      <form method="POST">
        <div class="mb-3">
          <label for="nama" class="form-label">Nama Lengkap</label>
          <input type="text" class="form-control" id="name" name="nama" placeholder="Masukkan nama anda" required>
        </div>

        <div class="mb-3">
          <label for="umur" class="form-label">Umur</label>
          <input type="number" class="form-control" id="age" name="umur" placeholder="Masukkan umur anda" required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email" required>
        </div>

        <div class="mb-4">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
        </div>

        <div class="d-grid">
          <button type="submit" class="btn btn-danger" name="register">Daftar</button>
        </div>
      </form>

      <p class="text-center mt-3 mb-0">Sudah punya akun? <a href="#">Masuk</a></p>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

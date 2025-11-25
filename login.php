<?php
include "koneksi.php";
session_start();

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($konek, $_POST['email']);
    $password = mysqli_real_escape_string($konek, $_POST['password']);

    $query = mysqli_query($konek, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($query);

    if (mysqli_num_rows($query) > 0) {

        // Cek apakah password cocok
        if (password_verify($password, $user['password'])) {
            // Password benar -> login sukses
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            header("Location: berandanew.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  <!-- Background dengan gambar -->
  <div class="container-fluid vh-100 d-flex justify-content-center align-items-center bg-dark bg-opacity-75" 
       style="background: url('gambar/hero.jpeg') center/cover no-repeat;">
    
    <!-- Card Login -->
    <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
      <div class="text-center mb-3">
        <img src="gambar/MU.png" alt="Logo" class="mb-2" width="60">
        <h4 class="fw-bold">Login</h4>
      </div>

      <?php if (isset($error)) { ?>
        <div class="alert alert-danger text-center py-2"><?= $error ?></div>
      <?php } ?>

      <form action="" method="post">
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="text" class="form-control" id="email" name="email" placeholder="Masukkan email" required>
        </div>

        <div class="mb-4">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
        </div>

        <div class="d-grid">
          <button type="submit" name="login" class="btn btn-danger">Masuk</button>
        </div>
      </form>

      <p class="text-center mt-3 mb-0">Belum punya akun? <a href="registrasi.php">Daftar</a></p>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

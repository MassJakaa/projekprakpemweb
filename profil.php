<?php
session_start();
include "koneksi.php";

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$id = $_SESSION['user_id'];
$query = mysqli_query($konek, "SELECT * FROM users WHERE id_user='$id'");
$user = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Saya</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary">

<nav class="navbar navbar-dark bg-danger">
    <div class="container-fluid justify-content-start gap-4">
        <a href="berandanew.php" class="btn btn-outline-light btn-sm">Kembali</a>
        <span class="navbar-brand mb-0 h1">Profil</span>
    </div>
</nav>

<div class="container py-5">
  <div class="card mx-auto" style="max-width: 500px;">
    <div class="card-body text-center">
      <img src="https://github.com/mdo.png" alt="Avatar" class="rounded-circle mb-3" width="120" height="120">
      <h4 class="card-title mb-1"><?= htmlspecialchars($user['username']); ?></h4>
      <p class="text-muted mb-3"><?= htmlspecialchars($user['email']); ?></p>

      <ul class="list-group list-group-flush text-start mb-3">
        <li class="list-group-item">ID: <?= $user['id_user']; ?></li>
        <li class="list-group-item">Nama: <?= htmlspecialchars($user['username']); ?></li>
        <li class="list-group-item">Umur: <?= htmlspecialchars($user['umur']); ?></li>
        <li class="list-group-item">Email: <?= htmlspecialchars($user['email']); ?></li>
      </ul>

      <form action="logout.php" method="post">
        <button type="submit" class="btn btn-danger w-100">Logout</button>
      </form>
    </div>
  </div>
</div>

<footer class="bg-dark text-white text-center py-4 mt-5">
    <p class="mb-0">&copy; 2024 The Read Devils. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

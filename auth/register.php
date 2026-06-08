<?php
session_start();
require_once '../config/db.php';
if(cekLogin()) redirect('../index.php');

$err = $ok = '';
if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $nama    = trim($_POST['name']     ?? '');
  $email   = trim($_POST['email']    ?? '');
  $pass    = $_POST['password']      ?? '';
  $konfirm = $_POST['confirm']       ?? '';
  if(!$nama||!$email||!$pass||!$konfirm){ $err='Semua kolom wajib diisi.'; }
  elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)){ $err='Format email tidak valid.'; }
  elseif(strlen($pass)<6){ $err='Password minimal 6 karakter.'; }
  elseif($pass!=$konfirm){ $err='Konfirmasi password tidak cocok.'; }
  else{
    $cek=$conn->prepare("SELECT id FROM users WHERE email=?");
    $cek->bind_param('s',$email); $cek->execute();
    if($cek->get_result()->num_rows>0){ $err='Email sudah terdaftar.'; }
    else{
      $words=explode(' ',$nama); $av=strtoupper(substr($words[0],0,1)).strtoupper(substr($words[1]??'X',0,1));
      $hash=password_hash($pass,PASSWORD_DEFAULT);
      $ins=$conn->prepare("INSERT INTO users(name,email,password,role,avatar)VALUES(?,?,?,'jobseeker',?)");
      $ins->bind_param('ssss',$nama,$email,$hash,$av);
      if($ins->execute()){ $ok='Akun berhasil dibuat! Silakan masuk.'; }
      else{ $err='Terjadi kesalahan server, coba lagi.'; }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar — ZenirWork</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="../assets/style.css">
<style>.blob1{position:fixed;width:500px;height:500px;top:-150px;left:-100px;background:radial-gradient(circle,rgba(59,130,246,.18) 0%,transparent 68%);pointer-events:none;}.blob2{position:fixed;width:400px;height:400px;bottom:-100px;right:-80px;background:radial-gradient(circle,rgba(139,92,246,.15) 0%,transparent 68%);pointer-events:none;}</style>
</head>
<body>
<div class="blob1"></div><div class="blob2"></div>
<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-logo">
      <a href="../index.php" class="logo" style="justify-content:center">
        <div class="logo-box" style="width:36px;height:36px"><i class="bi bi-briefcase-fill" style="color:#fff;font-size:15px"></i></div>
        Zenir<span style="color:#60a5fa">Work</span>
      </a>
      <div class="auth-title">Buat Akun Baru</div>
      <div class="auth-sub">Gratis, selamanya</div>
    </div>
    <?php if($err): ?><div class="err-box"><i class="bi bi-exclamation-circle" style="margin-right:6px"></i><?= e($err) ?></div><?php endif; ?>
    <?php if($ok): ?><div class="ok-box"><i class="bi bi-check-circle" style="margin-right:6px"></i><?= e($ok) ?> <a href="login.php" style="color:#34d399;font-weight:600">Masuk →</a></div><?php endif; ?>
    <form method="POST" novalidate>
      <div class="fg"><label>Nama Lengkap</label>
        <input type="text" name="name" class="fi" placeholder="Nama lengkapmu" value="<?= e($_POST['name']??'') ?>"></div>
      <div class="fg"><label>Email</label>
        <input type="email" name="email" class="fi" placeholder="email@contoh.com" value="<?= e($_POST['email']??'') ?>"></div>
      <div class="fg"><label>Password <span style="color:rgba(255,255,255,.3);font-size:11px">(min. 6 karakter)</span></label>
        <input type="password" name="password" class="fi" placeholder="Buat password"></div>
      <div class="fg"><label>Konfirmasi Password</label>
        <input type="password" name="confirm" class="fi" placeholder="Ulangi password"></div>
      <button type="submit" class="sub-btn"><i class="bi bi-person-plus" style="margin-right:8px"></i>Daftar Sekarang</button>
    </form>
    <div class="auth-link">Sudah punya akun? <a href="login.php">Masuk di sini</a></div>
    <div class="auth-link" style="margin-top:6px"><a href="../index.php" style="color:rgba(255,255,255,.3)">← Beranda</a></div>
  </div>
</div>
</body>
</html>

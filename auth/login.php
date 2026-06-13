<?php
session_start();
require_once '../config/db.php';
if(cekLogin()) redirect(cekAdmin() ? '../admin/index.php' : '../jobseeker/index.php');

$err = '';
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$email = trim($_POST['email'] ?? '');
	$pass  = $_POST['password'] ?? '';
	if(!$email || !$pass){
		$err = 'Email dan password harus diisi.';
	} else {
		$st = $conn->prepare("SELECT id,name,password,role,avatar FROM users WHERE email=?");
		$st->bind_param('s', $email);
		$st->execute();
		$u = $st->get_result()->fetch_assoc();
		if($u && password_verify($pass, $u['password'])){
			$_SESSION['user_id'] = $u['id'];
			$_SESSION['name']    = $u['name'];
			$_SESSION['role']    = $u['role'];
			$_SESSION['avatar']  = $u['avatar'];
			redirect($u['role'] == 'admin' ? '../admin/index.php' : '../jobseeker/index.php');
		} else {
			$err = 'Email atau password salah. Coba lagi.';
		}
	}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk — ZenirWork</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="../assets/style.css">
<style>
  /* override khusus halaman ini */
  .blob1{position:fixed;width:500px;height:500px;top:-150px;left:-100px;background:radial-gradient(circle,rgba(59,130,246,.18) 0%,transparent 68%);pointer-events:none;}
  .blob2{position:fixed;width:400px;height:400px;bottom:-100px;right:-80px;background:radial-gradient(circle,rgba(139,92,246,.15) 0%,transparent 68%);pointer-events:none;}
</style>
</head>
<body>
<div class="blob1"></div>
<div class="blob2"></div>

<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-logo">
      <a href="../index.php" class="logo" style="justify-content:center">
        <div class="logo-box" style="width:36px;height:36px"><i class="bi bi-briefcase-fill" style="color:#fff;font-size:15px"></i></div>
        Zenir<span style="color:#60a5fa">Work</span>
      </a>
      <div class="auth-title">Selamat Datang Kembali</div>
      <div class="auth-sub">Masuk untuk melanjutkan ke platform</div>
    </div>

    <?php if($err): ?>
    <div class="err-box"><i class="bi bi-exclamation-circle" style="margin-right:6px"></i><?= e($err) ?></div>
    <?php endif; ?>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'logout'): ?>
    <div class="ok-box"><i class="bi bi-check-circle" style="margin-right:6px"></i>Berhasil keluar.</div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="fg">
        <label>Email</label>
        <div style="position:relative">
          <i class="bi bi-envelope" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,.3);font-size:14px"></i>
          <input type="email" name="email" class="fi" style="padding-left:36px" placeholder="email@contoh.com" value="<?= e($_POST['email'] ?? '') ?>">
        </div>
      </div>
      <div class="fg">
        <label>Password</label>
        <div style="position:relative">
          <i class="bi bi-lock" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,.3);font-size:14px"></i>
          <input type="password" name="password" id="pw-field" class="fi" style="padding-left:36px;padding-right:36px" placeholder="Masukkan password">
          <button type="button" onclick="togglePw()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,.35);cursor:pointer;font-size:14px">
            <i class="bi bi-eye" id="pw-eye"></i>
          </button>
        </div>
      </div>
      <button type="submit" class="sub-btn">
        <i class="bi bi-box-arrow-in-right" style="margin-right:8px"></i>Masuk
      </button>
    </form>

    <!-- <div class="demo-box">
      <strong>Demo:</strong><br>
      Admin: admin@zenirwork.id / password<br>
      User:&nbsp;&nbsp; daffa@zenirwork.id / password
    </div> -->

    <div class="auth-link">
      Belum punya akun? <a href="register.php">Daftar gratis</a>
    </div>
    <div class="auth-link" style="margin-top:8px">
      <a href="../index.php" style="color:rgba(255,255,255,.3)">← Kembali ke Beranda</a>
    </div>
  </div>
</div>

<script>
function togglePw(){
  var f = document.getElementById('pw-field');
  var ic = document.getElementById('pw-eye');
  if(f.type == 'password'){
    f.type = 'text';
    ic.className = 'bi bi-eye-slash';
  } else {
    f.type = 'password';
    ic.className = 'bi bi-eye';
  }
}
</script>
</body>
</html>

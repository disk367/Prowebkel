<?php
// admin/training_create.php
session_start();
require_once '../config/db.php';
harusAdmin();

$err='';
$katList  = ['Web Development','Desain','Pemasaran','Data & Analitik','Pengembangan Diri','Teknologi','Keuangan','Lainnya'];
$lvlList  = ['Pemula','Menengah','Lanjutan'];
$ikoList  = ['📚','🌐','⚡','🎨','📣','🐍','✨','💡','🔧','📊','🎯','💼'];

if($_SERVER['REQUEST_METHOD']=='POST'){
  $judul    = trim($_POST['judul']      ?? '');
  $kat      = trim($_POST['kategori']   ?? '');
  $desk     = trim($_POST['deskripsi']  ?? '');
  $inst     = trim($_POST['instruktur'] ?? '');
  $dur      = trim($_POST['durasi']     ?? '');
  $lvl      = trim($_POST['level']      ?? 'Pemula');
  $iko      = trim($_POST['icon_emoji'] ?? '📚');
  $gratis   = isset($_POST['is_gratis']) ? 1 : 0;

  if(!$judul||!$kat||!$desk||!$inst||!$dur){
    $err='Semua kolom wajib diisi.';
  } else {
    $ins=$conn->prepare("INSERT INTO trainings(judul,kategori,deskripsi,instruktur,durasi,level,icon_emoji,is_gratis)VALUES(?,?,?,?,?,?,?,?)");
    $ins->bind_param('sssssssi',$judul,$kat,$desk,$inst,$dur,$lvl,$iko,$gratis);
    if($ins->execute()){
      $_SESSION['flash']="Materi \"$judul\" berhasil ditambahkan!";
      redirect('training.php');
    } else { $err='Gagal menyimpan, coba lagi.'; }
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Tambah Materi — Admin ZenirWork</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<nav class="navbar" style="position:fixed">
  <div class="nav-inner">
    <a href="index.php" class="logo"><div class="logo-box"><i class="bi bi-briefcase-fill" style="color:#fff;font-size:14px"></i></div>ZenirWork <span style="background:rgba(59,130,246,.2);color:#60a5fa;font-size:11px;padding:2px 8px;border-radius:5px;margin-left:4px">Admin</span></a>
    <a href="../auth/logout.php" style="font-size:13px;color:rgba(239,68,68,.8);text-decoration:none"><i class="bi bi-box-arrow-right"></i> Keluar</a>
  </div>
</nav>
<div class="dash-wrap">
  <aside class="sidebar">
    <div class="sb-avatar"><?= e($_SESSION['avatar']??'AD') ?></div>
    <div class="sb-name"><?= e($_SESSION['name']) ?></div>
    <div class="sb-role">Administrator</div>
    <hr class="sb-hr">
    <a href="index.php" class="sb-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="create.php" class="sb-link"><i class="bi bi-plus-circle"></i> Tambah Lowongan</a>
    <a href="applications.php" class="sb-link"><i class="bi bi-file-earmark-check"></i> Semua Lamaran</a>
    <a href="training.php" class="sb-link on"><i class="bi bi-mortarboard"></i> Pelatihan Skill</a>
    <hr class="sb-hr"><a href="../index.php" class="sb-link"><i class="bi bi-globe"></i> Lihat Website</a>
  </aside>
  <main class="dash-main">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:22px">
      <a href="training.php" style="width:34px;height:34px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:10px;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.6);text-decoration:none">
        <i class="bi bi-arrow-left"></i>
      </a>
      <div>
        <h1 style="font-family:'Plus Jakarta Sans',sans-serif;font-size:20px;font-weight:700;color:#fff">Tambah Materi Baru</h1>
        <p style="font-size:13px;color:rgba(255,255,255,.35)">Isi informasi materi pelatihan</p>
      </div>
    </div>

    <?php if($err): ?>
    <div style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);border-radius:10px;padding:12px 16px;font-size:13px;color:#fca5a5;margin-bottom:18px">
      <i class="bi bi-exclamation-circle" style="margin-right:6px"></i><?= e($err) ?>
    </div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
        <div class="form-card">
          <h3>Informasi Materi</h3>
          <div class="fg2">
            <label>Judul Materi <span style="color:#ef4444">*</span></label>
            <input type="text" name="judul" class="fi2" placeholder="cth: Belajar HTML & CSS dari Nol" value="<?= e($_POST['judul']??'') ?>">
          </div>
          <div class="fg2">
            <label>Instruktur <span style="color:#ef4444">*</span></label>
            <input type="text" name="instruktur" class="fi2" placeholder="Nama instruktur" value="<?= e($_POST['instruktur']??'') ?>">
          </div>
          <div class="form-row">
            <div class="fg2">
              <label>Kategori <span style="color:#ef4444">*</span></label>
              <select name="kategori" class="fi2">
                <option value="">-- Pilih --</option>
                <?php foreach($katList as $k): ?>
                <option value="<?= $k ?>" <?= ($_POST['kategori']??'')==$k?'selected':'' ?>><?= $k ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="fg2">
              <label>Level</label>
              <select name="level" class="fi2">
                <?php foreach($lvlList as $l): ?>
                <option value="<?= $l ?>" <?= ($_POST['level']??'Pemula')==$l?'selected':'' ?>><?= $l ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="fg2">
              <label>Durasi <span style="color:#ef4444">*</span></label>
              <input type="text" name="durasi" class="fi2" placeholder="cth: 8 jam 30 menit" value="<?= e($_POST['durasi']??'') ?>">
            </div>
            <div class="fg2">
              <label>Icon Emoji</label>
              <select name="icon_emoji" class="fi2" style="font-size:18px">
                <?php foreach($ikoList as $ik): ?>
                <option value="<?= $ik ?>" <?= ($_POST['icon_emoji']??'📚')==$ik?'selected':'' ?>><?= $ik ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin-top:4px">
            <input type="checkbox" name="is_gratis" value="1" <?= isset($_POST['is_gratis'])?'checked':'' ?> style="width:16px;height:16px;accent-color:#3b82f6">
            <span style="font-size:13px;color:rgba(255,255,255,.6)">Materi gratis (semua pengguna bisa akses)</span>
          </label>
        </div>
        <div class="form-card">
          <h3>Deskripsi Materi</h3>
          <div class="fg2" style="height:calc(100% - 40px)">
            <label>Deskripsi <span style="color:#ef4444">*</span></label>
            <textarea name="deskripsi" class="fi2 ta2" style="min-height:200px" placeholder="Jelaskan isi materi, apa yang akan dipelajari, dan siapa target audiensnya..."><?= e($_POST['deskripsi']??'') ?></textarea>
          </div>
        </div>
      </div>
      <div style="display:flex;gap:10px;margin-top:20px">
        <button type="submit" class="btn-sm" style="padding:10px 24px;font-size:14px">
          <i class="bi bi-check-lg"></i> Simpan Materi
        </button>
        <a href="training.php" style="padding:10px 20px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:10px;font-size:13px;color:rgba(255,255,255,.6);text-decoration:none">Batal</a>
      </div>
    </form>
  </main>
</div>
</body>
</html>

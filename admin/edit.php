<?php
session_start();
require_once '../config/db.php';
harusAdmin();

$id = (int)($_GET['id'] ?? 0);
if($id <= 0) redirect('index.php');

$st = $conn->prepare("SELECT * FROM jobs WHERE id=?");
$st->bind_param('i',$id); $st->execute();
$job = $st->get_result()->fetch_assoc();
if(!$job) redirect('index.php');

$err = '';
$kategori = ['Teknologi','Desain','Pemasaran','Kreatif','Data & Analitik','Layanan','Keuangan','Lainnya'];
$tipe     = ['Full-time','Part-time','Internship','Remote','Freelance'];

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$d = array_map('trim', $_POST);
	if(!$d['title']||!$d['company']||!$d['location']||!$d['category']||!$d['description']||!$d['requirements']){
		$err = 'Semua kolom wajib diisi.';
	} else {
		$aktif = isset($_POST['is_active']) ? 1 : 0;
		$smin  = (int)$d['salary_min'];
		$smax  = (int)$d['salary_max'];
		$up = $conn->prepare("UPDATE jobs SET title=?,company=?,location=?,type=?,category=?,description=?,requirements=?,salary_min=?,salary_max=?,is_active=? WHERE id=?");
		$up->bind_param('sssssssssii',$d['title'],$d['company'],$d['location'],$d['type'],$d['category'],$d['description'],$d['requirements'],$smin,$smax,$aktif,$id);
		if($up->execute()){
			$_SESSION['flash'] = "Lowongan \"{$d['title']}\" berhasil diperbarui!";
			redirect('index.php');
		} else { $err = 'Gagal update, coba lagi.'; }
	}
	$job = array_merge($job, $_POST);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit Lowongan — ZenirWork</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<nav class="navbar" style="position:fixed">
  <div class="nav-inner">
    <a href="index.php" class="logo">
      <div class="logo-box"><i class="bi bi-briefcase-fill" style="color:#fff;font-size:14px"></i></div>
      Zenir<span style="color:#60a5fa">Work</span>
      <span style="background:rgba(59,130,246,.2);color:#60a5fa;font-size:11px;padding:2px 8px;border-radius:5px;font-weight:600">Admin</span>
    </a>
    <a href="../auth/logout.php" style="font-size:13px;color:rgba(239,68,68,.8);text-decoration:none">
      <i class="bi bi-box-arrow-right"></i> Keluar
    </a>
  </div>
</nav>

<div class="dash-wrap">
  <aside class="sidebar">
    <div class="sb-avatar"><?= e($_SESSION['avatar']??'AD') ?></div>
    <div class="sb-name"><?= e($_SESSION['name']) ?></div>
    <div class="sb-role">Administrator</div>
    <hr class="sb-hr">
    <a href="index.php" class="sb-link on"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="create.php" class="sb-link"><i class="bi bi-plus-circle"></i> Tambah Lowongan</a>
    <a href="applications.php" class="sb-link"><i class="bi bi-file-earmark-check"></i> Semua Lamaran</a>
    <hr class="sb-hr">
    <a href="../index.php" class="sb-link"><i class="bi bi-globe"></i> Lihat Website</a>
  </aside>

  <main class="dash-main">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:22px">
      <a href="index.php" style="width:34px;height:34px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:10px;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.6);text-decoration:none;font-size:15px">
        <i class="bi bi-arrow-left"></i>
      </a>
      <div>
        <h1 style="font-family:'Plus Jakarta Sans',sans-serif;font-size:20px;font-weight:700;color:#fff">Edit Lowongan</h1>
        <p style="font-size:13px;color:rgba(255,255,255,.35)"><?= e($job['title']) ?></p>
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
          <h3>Informasi Dasar</h3>
          <div class="fg2">
            <label>Judul Posisi</label>
            <input type="text" name="title" class="fi2" value="<?= e($job['title']) ?>">
          </div>
          <div class="fg2">
            <label>Nama Perusahaan</label>
            <input type="text" name="company" class="fi2" value="<?= e($job['company']) ?>">
          </div>
          <div class="fg2">
            <label>Lokasi</label>
            <input type="text" name="location" class="fi2" value="<?= e($job['location']) ?>">
          </div>
          <div class="form-row">
            <div class="fg2">
              <label>Tipe</label>
              <select name="type" class="fi2">
                <?php foreach($tipe as $t): ?>
                <option value="<?= $t ?>" <?= $job['type']==$t?'selected':'' ?>><?= $t ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="fg2">
              <label>Kategori</label>
              <select name="category" class="fi2">
                <?php foreach($kategori as $k): ?>
                <option value="<?= $k ?>" <?= $job['category']==$k?'selected':'' ?>><?= $k ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="fg2">
              <label>Gaji Min (Rp)</label>
              <input type="number" name="salary_min" class="fi2" value="<?= e($job['salary_min']) ?>">
            </div>
            <div class="fg2">
              <label>Gaji Max (Rp)</label>
              <input type="number" name="salary_max" class="fi2" value="<?= e($job['salary_max']) ?>">
            </div>
          </div>
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin-top:4px">
            <input type="checkbox" name="is_active" value="1" <?= $job['is_active']?'checked':'' ?> style="width:16px;height:16px;accent-color:#3b82f6">
            <span style="font-size:13px;color:rgba(255,255,255,.6)">Lowongan aktif (tampil di website)</span>
          </label>
        </div>

        <div class="form-card">
          <h3>Detail Pekerjaan</h3>
          <div class="fg2">
            <label>Deskripsi</label>
            <textarea name="description" class="fi2 ta2"><?= e($job['description']) ?></textarea>
          </div>
          <div class="fg2">
            <label>Persyaratan</label>
            <textarea name="requirements" class="fi2 ta2" style="min-height:130px"><?= e($job['requirements']) ?></textarea>
          </div>
        </div>
      </div>

      <div style="display:flex;gap:10px;margin-top:20px">
        <button type="submit" class="btn-sm" style="padding:10px 24px;font-size:14px">
          <i class="bi bi-check-lg"></i> Perbarui Lowongan
        </button>
        <a href="index.php" style="padding:10px 20px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:10px;font-size:13px;color:rgba(255,255,255,.6);text-decoration:none">
          Batal
        </a>
      </div>
    </form>
  </main>
</div>
</body>
</html>

<?php
session_start();
require_once '../config/db.php';
harusAdmin();

$total_j = $conn->query("SELECT COUNT(*) FROM jobs")->fetch_row()[0];
$total_u = $conn->query("SELECT COUNT(*) FROM users WHERE role='jobseeker'")->fetch_row()[0];
$total_a = $conn->query("SELECT COUNT(*) FROM applications")->fetch_row()[0];
$pending = $conn->query("SELECT COUNT(*) FROM applications WHERE status='pending'")->fetch_row()[0];

$jobs = $conn->query("SELECT j.*,COUNT(a.id) AS jml_lmr FROM jobs j
  LEFT JOIN applications a ON j.id=a.job_id GROUP BY j.id ORDER BY j.created_at DESC");

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard Admin — ZenirWork</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<!-- navbar admin -->
<nav class="navbar" style="position:fixed">
  <div class="nav-inner">
    <a href="../index.php" class="logo">
      <div class="logo-box"><i class="bi bi-briefcase-fill" style="color:#fff;font-size:14px"></i></div>
      Zenir<span style="color:#60a5fa">Work</span>
      <span style="background:rgba(59,130,246,.2);color:#60a5fa;font-size:11px;padding:2px 8px;border-radius:5px;font-weight:600">Admin</span>
    </a>
    <div style="display:flex;align-items:center;gap:14px">
      <div style="display:flex;align-items:center;gap:8px">
        <div style="width:28px;height:28px;background:linear-gradient(135deg,#3b82f6,#8b5cf6);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff">
          <?= e($_SESSION['avatar'] ?? 'AD') ?>
        </div>
        <span style="font-size:13px;color:rgba(255,255,255,.7)"><?= e($_SESSION['name']) ?></span>
      </div>
      <a href="../auth/logout.php" style="font-size:13px;color:rgba(239,68,68,.8);text-decoration:none">
        <i class="bi bi-box-arrow-right"></i> Keluar
      </a>
    </div>
  </div>
</nav>

<div class="dash-wrap">
  <!-- sidebar -->
  <aside class="sidebar">
    <div class="sb-avatar"><?= e($_SESSION['avatar'] ?? 'AD') ?></div>
    <div class="sb-name"><?= e($_SESSION['name']) ?></div>
    <div class="sb-role">Administrator</div>
    <hr class="sb-hr">
    <a href="index.php" class="sb-link on"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="create.php" class="sb-link"><i class="bi bi-plus-circle"></i> Tambah Lowongan</a>
    <a href="applications.php" class="sb-link">
      <i class="bi bi-file-earmark-check"></i> Semua Lamaran
      <?php if($pending > 0): ?><span class="sb-badge"><?= $pending ?></span><?php endif; ?>
    </a>
    <hr class="sb-hr">
    <a href="../index.php" class="sb-link"><i class="bi bi-globe"></i> Lihat Website</a>
  </aside>

  <!-- konten -->
  <main class="dash-main">
    <?php if($flash): ?>
    <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25);border-radius:10px;padding:12px 16px;font-size:13px;color:#6ee7b7;margin-bottom:18px;display:flex;align-items:center;gap:8px" id="flash-msg">
      <i class="bi bi-check-circle-fill"></i> <?= e($flash) ?>
      <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;color:rgba(110,231,183,.5);cursor:pointer;font-size:16px">×</button>
    </div>
    <?php endif; ?>

    <div style="margin-bottom:20px">
      <h1 style="font-family:'Plus Jakarta Sans',sans-serif;font-size:22px;font-weight:700;color:#fff;margin-bottom:4px">Dashboard Admin</h1>
      <p style="font-size:13px;color:rgba(255,255,255,.38)">Selamat datang, <?= e($_SESSION['name']) ?></p>
    </div>

    <!-- stats -->
    <div class="stats-row">
      <div class="st-card">
        <div class="st-icon" style="background:rgba(59,130,246,.14)"><i class="bi bi-briefcase" style="color:#60a5fa;font-size:20px"></i></div>
        <div><div class="st-val"><?= $total_j ?></div><div class="st-label">Total Lowongan</div></div>
      </div>
      <div class="st-card">
        <div class="st-icon" style="background:rgba(139,92,246,.14)"><i class="bi bi-people" style="color:#c4b5fd;font-size:20px"></i></div>
        <div><div class="st-val"><?= $total_u ?></div><div class="st-label">Pencari Kerja</div></div>
      </div>
      <div class="st-card">
        <div class="st-icon" style="background:rgba(16,185,129,.14)"><i class="bi bi-send" style="color:#34d399;font-size:20px"></i></div>
        <div><div class="st-val"><?= $total_a ?></div><div class="st-label">Total Lamaran</div></div>
      </div>
      <div class="st-card">
        <div class="st-icon" style="background:rgba(245,158,11,.14)"><i class="bi bi-clock-history" style="color:#fbbf24;font-size:20px"></i></div>
        <div><div class="st-val" style="color:#fbbf24"><?= $pending ?></div><div class="st-label">Pending Review</div></div>
      </div>
    </div>

    <!-- tabel -->
    <div class="tbl-card">
      <div class="tbl-header">
        <h3>Manajemen Lowongan</h3>
        <div style="display:flex;gap:8px;align-items:center">
          <input type="text" id="tbl-search" placeholder="🔍 Cari..." class="search-input" style="width:180px;padding:7px 12px;font-size:12px">
          <a href="create.php" class="btn-sm" style="font-size:12px;padding:7px 14px">
            <i class="bi bi-plus-lg"></i> Tambah
          </a>
        </div>
      </div>
      <div style="overflow-x:auto">
        <table class="data-table">
          <thead>
            <tr>
              <th style="width:40px">#</th>
              <th>Posisi & Perusahaan</th>
              <th>Kategori</th>
              <th>Tipe</th>
              <th>Lamaran</th>
              <th>Status</th>
              <th style="text-align:right">Aksi</th>
            </tr>
          </thead>
          <tbody id="tbl-body">
            <?php $no=1; while($j=$jobs->fetch_assoc()): ?>
            <tr>
              <td style="color:rgba(255,255,255,.28);font-size:12px"><?= $no++ ?></td>
              <td>
                <div style="font-weight:600;color:#fff;font-size:13px"><?= e($j['title']) ?></div>
                <div style="font-size:11px;color:rgba(255,255,255,.35);margin-top:2px">
                  <i class="bi bi-building"></i> <?= e($j['company']) ?> · <?= e($j['location']) ?>
                </div>
              </td>
              <td><span class="bdg" style="background:rgba(139,92,246,.12);color:#c4b5fd"><?= e($j['category']) ?></span></td>
              <td><span class="type-badge <?= warnaType($j['type']) ?>"><?= e($j['type']) ?></span></td>
              <td>
                <span style="font-weight:600;color:#fff"><?= $j['jml_lmr'] ?></span>
                <span style="font-size:11px;color:rgba(255,255,255,.3)"> lmr</span>
              </td>
              <td>
                <?php if($j['is_active']): ?>
                <span class="bdg st-aktif"><i class="bi bi-circle-fill" style="font-size:7px;margin-right:4px"></i>Aktif</span>
                <?php else: ?>
                <span class="bdg" style="background:rgba(255,255,255,.06);color:rgba(255,255,255,.4)">Nonaktif</span>
                <?php endif; ?>
              </td>
              <td style="text-align:right">
                <div style="display:flex;gap:6px;justify-content:flex-end">
                  <a href="edit.php?id=<?= $j['id'] ?>" class="btn-edit"><i class="bi bi-pencil"></i></a>
                  <button onclick="konfHapus(<?= $j['id'] ?>,'<?= addslashes(e($j['title'])) ?>')" class="btn-danger"><i class="bi bi-trash"></i></button>
                </div>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

<script>
document.getElementById('tbl-search').addEventListener('input', function(){
  var q = this.value.toLowerCase();
  document.querySelectorAll('#tbl-body tr').forEach(function(r){
    r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
});

function konfHapus(id, judul){
  Swal.fire({
    title: 'Hapus Lowongan?',
    html: '<span style="color:rgba(255,255,255,.6)">Lowongan "<strong style=\'color:#fff\'>' + judul + '</strong>" akan dihapus permanen.</span>',
    icon: 'warning',
    background: '#111827',
    color: '#e2e8f0',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#374151',
    confirmButtonText: 'Ya, Hapus!',
    cancelButtonText: 'Batal'
  }).then(function(r){
    if(r.isConfirmed) window.location.href = 'delete.php?id=' + id;
  });
}
</script>
</body>
</html>

<?php
// admin/training.php - manajemen pelatihan skill
session_start();
require_once '../config/db.php';
harusAdmin();

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

// hapus training
if(isset($_GET['del'])){
  $did = (int)$_GET['del'];
  if($did > 0){
    $ck = $conn->prepare("SELECT judul FROM trainings WHERE id=?");
    $ck->bind_param('i',$did); $ck->execute();
    $tr = $ck->get_result()->fetch_assoc();
    if($tr){
      $dl = $conn->prepare("DELETE FROM trainings WHERE id=?");
      $dl->bind_param('i',$did); $dl->execute();
      $_SESSION['flash'] = "Materi \"{$tr['judul']}\" berhasil dihapus.";
    }
  }
  redirect('training.php');
}

$allTr = $conn->query("SELECT * FROM trainings ORDER BY created_at DESC");
$tot   = $allTr->num_rows;
$grts  = $conn->query("SELECT COUNT(*) FROM trainings WHERE is_gratis=1")->fetch_row()[0];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Pelatihan Skill — Admin ZenirWork</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<nav class="navbar" style="position:fixed">
  <div class="nav-inner">
    <a href="../index.php" class="logo">
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
    <a href="index.php" class="sb-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="create.php" class="sb-link"><i class="bi bi-plus-circle"></i> Tambah Lowongan</a>
    <a href="applications.php" class="sb-link"><i class="bi bi-file-earmark-check"></i> Semua Lamaran</a>
    <a href="training.php" class="sb-link on"><i class="bi bi-mortarboard"></i> Pelatihan Skill</a>
    <hr class="sb-hr">
    <a href="../index.php" class="sb-link"><i class="bi bi-globe"></i> Lihat Website</a>
  </aside>

  <main class="dash-main">

    <?php if($flash): ?>
    <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25);border-radius:10px;padding:12px 16px;font-size:13px;color:#6ee7b7;margin-bottom:18px;display:flex;align-items:center;gap:8px" id="fl">
      <i class="bi bi-check-circle-fill"></i><?= e($flash) ?>
      <button onclick="document.getElementById('fl').remove()" style="margin-left:auto;background:none;border:none;color:inherit;opacity:.6;cursor:pointer;font-size:16px">×</button>
    </div>
    <?php endif; ?>

    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;flex-wrap:wrap;gap:12px">
      <div>
        <h1 style="font-family:'Plus Jakarta Sans',sans-serif;font-size:22px;font-weight:700;color:#fff;margin-bottom:4px">🎓 Pelatihan Skill</h1>
        <p style="font-size:13px;color:rgba(255,255,255,.38)">Kelola materi pelatihan untuk pengguna ZenirWork</p>
      </div>
      <a href="training_create.php" class="btn-sm" style="padding:10px 18px">
        <i class="bi bi-plus-lg"></i> Tambah Materi
      </a>
    </div>

    <!-- mini stats -->
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px">
      <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:16px;text-align:center">
        <div style="font-size:24px;font-weight:700;color:#fff;font-family:'Plus Jakarta Sans',sans-serif"><?= $tot ?></div>
        <div style="font-size:12px;color:rgba(255,255,255,.4);margin-top:3px">Total Materi</div>
      </div>
      <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:16px;text-align:center">
        <div style="font-size:24px;font-weight:700;color:#34d399;font-family:'Plus Jakarta Sans',sans-serif"><?= $grts ?></div>
        <div style="font-size:12px;color:rgba(255,255,255,.4);margin-top:3px">Materi Gratis</div>
      </div>
      <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:16px;text-align:center">
        <div style="font-size:24px;font-weight:700;color:#fbbf24;font-family:'Plus Jakarta Sans',sans-serif"><?= $tot - $grts ?></div>
        <div style="font-size:12px;color:rgba(255,255,255,.4);margin-top:3px">Materi Premium</div>
      </div>
    </div>

    <!-- tabel training -->
    <div class="tbl-card">
      <div class="tbl-header">
        <h3>Daftar Materi Pelatihan</h3>
        <input type="text" id="srch" placeholder="🔍 Cari materi..." class="search-input" style="width:180px;padding:7px 12px;font-size:12px">
      </div>
      <div style="overflow-x:auto">
        <table class="data-table" id="tr-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Judul & Instruktur</th>
              <th>Kategori</th>
              <th>Level</th>
              <th>Durasi</th>
              <th>Tipe</th>
              <th style="text-align:right">Aksi</th>
            </tr>
          </thead>
          <tbody id="tr-body">
            <?php $no=1; $allTr->data_seek(0); while($tr=$allTr->fetch_assoc()):
              $lvlcl = ['Pemula'=>'st-diterima','Menengah'=>'st-pending','Lanjutan'=>'st-ditolak'];
              $lc = $lvlcl[$tr['level']] ?? 'st-pending';
            ?>
            <tr>
              <td style="color:rgba(255,255,255,.28);font-size:12px"><?= $no++ ?></td>
              <td>
                <div style="display:flex;align-items:center;gap:10px">
                  <span style="font-size:24px"><?= $tr['icon_emoji'] ?></span>
                  <div>
                    <div style="font-weight:600;color:#fff;font-size:13px"><?= e($tr['judul']) ?></div>
                    <div style="font-size:11px;color:rgba(255,255,255,.35)"><i class="bi bi-person-circle" style="margin-right:3px"></i><?= e($tr['instruktur']) ?></div>
                  </div>
                </div>
              </td>
              <td><span class="bdg" style="background:rgba(139,92,246,.1);color:#c4b5fd"><?= e($tr['kategori']) ?></span></td>
              <td><span class="bdg <?= $lc ?>"><?= e($tr['level']) ?></span></td>
              <td style="font-size:12px;color:rgba(255,255,255,.5)"><?= e($tr['durasi']) ?></td>
              <td>
                <?php if($tr['is_gratis']): ?>
                <span class="bdg" style="background:rgba(16,185,129,.12);color:#34d399">Gratis</span>
                <?php else: ?>
                <span class="bdg" style="background:rgba(245,158,11,.12);color:#fbbf24">Premium</span>
                <?php endif; ?>
              </td>
              <td style="text-align:right">
                <div style="display:flex;gap:6px;justify-content:flex-end">
                  <a href="training_edit.php?id=<?= $tr['id'] ?>" class="btn-edit"><i class="bi bi-pencil"></i></a>
                  <button onclick="hapusTr(<?= $tr['id'] ?>,'<?= addslashes(e($tr['judul'])) ?>')" class="btn-danger"><i class="bi bi-trash"></i></button>
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
document.getElementById('srch').addEventListener('input',function(){
  var q=this.value.toLowerCase();
  document.querySelectorAll('#tr-body tr').forEach(function(r){
    r.style.display = r.textContent.toLowerCase().includes(q)?'':'none';
  });
});

function hapusTr(id,judul){
  Swal.fire({
    title:'Hapus Materi?',
    html:'<span style="color:rgba(255,255,255,.6)">Materi "<strong style="color:#fff">'+judul+'</strong>" akan dihapus permanen.</span>',
    icon:'warning',background:'#111827',color:'#e2e8f0',
    showCancelButton:true,confirmButtonColor:'#ef4444',cancelButtonColor:'#374151',
    confirmButtonText:'Hapus',cancelButtonText:'Batal'
  }).then(function(r){
    if(r.isConfirmed) window.location.href='training.php?del='+id;
  });
}
</script>
</body>
</html>

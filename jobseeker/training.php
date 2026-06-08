<?php
// jobseeker/training.php
// halaman pelatihan skill - fitur tambahan ZenirWork
session_start();
require_once '../config/db.php';
harusLogin('../auth/login.php');
if(cekAdmin()) redirect('../admin/index.php');

// filter berdasarkan kategori atau level
$kat   = trim($_GET['kat']   ?? '');
$lvl   = trim($_GET['level'] ?? '');
$cari2 = trim($_GET['q']     ?? '');

$where = "WHERE 1=1";
$par = []; $tp = '';

if($kat){   $where.=" AND kategori=?";   $par[]=$kat; $tp.='s'; }
if($lvl){   $where.=" AND level=?";      $par[]=$lvl; $tp.='s'; }
if($cari2){ $where.=" AND (judul LIKE ? OR instruktur LIKE ? OR deskripsi LIKE ?)";
            $s="%$cari2%"; $par[]=$s;$par[]=$s;$par[]=$s; $tp.='sss'; }

$st = $conn->prepare("SELECT * FROM trainings $where ORDER BY created_at DESC");
if($tp) $st->bind_param($tp,...$par);
$st->execute();
$trainings = $st->get_result();

// ambil semua kategori yang ada
$allKat = $conn->query("SELECT DISTINCT kategori FROM trainings ORDER BY kategori");

$lvlList = ['Pemula','Menengah','Lanjutan'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Pelatihan Skill — ZenirWork</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
<link rel="stylesheet" href="../assets/style.css">
<style>
/* style khusus halaman training - agak beda dari halaman lain */
.train-card {
  background: rgba(255,255,255,.04);
  border: 1px solid rgba(255,255,255,.08);
  border-radius: 14px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  transition: border-color .25s, transform .2s;
  cursor: pointer;
}
.train-card:hover {
  border-color: rgba(59,130,246,.4);
  transform:translateY(-4px);
  background: rgba(59,130,246,.04);
}
.train-icon {
  font-size: 2.5rem;
  margin-bottom: 14px;
  display: block;
}
.train-title { font-size:15px; font-weight:600; color:#fff; margin-bottom:6px; }
.train-inst  { font-size:12px; color:rgba(255,255,255,.4); margin-bottom:10px; }
.train-desc  {
  font-size:12px; color:rgba(255,255,255,.38); line-height:1.65;
  margin-bottom:14px; flex-grow:1;
  display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;
}
.train-meta { display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:12px; }
.lvl-badge { font-size:11px; font-weight:600; padding:3px 10px; border-radius:20px; }
.lvl-pemula   { background:rgba(16,185,129,.12); color:#34d399; }
.lvl-menengah { background:rgba(245,158,11,.12);  color:#fbbf24; }
.lvl-lanjutan { background:rgba(239,68,68,.12);   color:#fca5a5; }
.dur-text { font-size:11px; color:rgba(255,255,255,.4); display:flex; align-items:center; gap:4px; }
.gratis-badge {
  font-size:10px; font-weight:700; letter-spacing:.05em;
  background:rgba(16,185,129,.15); color:#34d399;
  border:1px solid rgba(16,185,129,.25); padding:2px 8px; border-radius:20px;
}
.bayar-badge {
  font-size:10px; font-weight:700;
  background:rgba(245,158,11,.12); color:#fbbf24;
  border:1px solid rgba(245,158,11,.25); padding:2px 8px; border-radius:20px;
}
.mulai-btn {
  width:100%; background:linear-gradient(135deg,#3b82f6,#6d28d9);
  color:#fff; border:none; padding:10px; border-radius:10px;
  font-size:13px; font-weight:600; cursor:pointer; font-family:inherit;
  transition:opacity .2s; margin-top:auto;
}
.mulai-btn:hover { opacity:.85; }
</style>
</head>
<body>

<!-- navbar -->
<nav class="navbar" style="position:fixed">
  <div class="nav-inner">
    <a href="../index.php" class="logo">
      <div class="logo-box"><i class="bi bi-briefcase-fill" style="color:#fff;font-size:14px"></i></div>
      Zenir<span style="color:#60a5fa">Work</span>
    </a>
    <div style="display:flex;align-items:center;gap:14px">
      <span style="font-size:13px;color:rgba(255,255,255,.6)"><?= e($_SESSION['name']) ?></span>
      <a href="../auth/logout.php" style="font-size:13px;color:rgba(239,68,68,.75);text-decoration:none">
        <i class="bi bi-box-arrow-right"></i>
      </a>
    </div>
  </div>
</nav>

<div class="dash-wrap">
  <!-- sidebar sama kayak halaman lain -->
  <aside class="sidebar">
    <div class="sb-avatar"><?= e($_SESSION['avatar']??'U') ?></div>
    <div class="sb-name"><?= e($_SESSION['name']) ?></div>
    <div class="sb-role">Pencari Kerja</div>
    <hr class="sb-hr">
    <a href="index.php" class="sb-link"><i class="bi bi-search"></i> Cari Lowongan</a>
    <a href="index.php?tab=lamaran" class="sb-link"><i class="bi bi-file-earmark-check"></i> Lamaranku</a>
    <a href="training.php" class="sb-link on"><i class="bi bi-mortarboard"></i> Pelatihan Skill</a>
    <hr class="sb-hr">
    <a href="../index.php" class="sb-link"><i class="bi bi-house"></i> Beranda</a>
  </aside>

  <main class="dash-main">

    <!-- header halaman -->
    <div style="margin-bottom:22px" data-aos="fade-right">
      <h1 style="font-family:'Plus Jakarta Sans',sans-serif;font-size:22px;font-weight:800;color:#fff;margin-bottom:4px">
        🎓 Pelatihan Skill
      </h1>
      <p style="font-size:13px;color:rgba(255,255,255,.4)">
        Tingkatkan kemampuanmu dengan materi yang dikurasi khusus untuk Gen Z
      </p>
    </div>

    <!-- filter -->
    <form method="GET" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:14px;padding:16px;margin-bottom:20px">
      <div class="filter-row">
        <div style="position:relative;flex:1;min-width:160px">
          <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,.3);font-size:13px"></i>
          <input type="text" name="q" value="<?= e($cari2) ?>"
                 class="search-input" style="width:100%;padding-left:34px"
                 placeholder="Cari materi, instruktur...">
        </div>
        <select name="kat" class="filter-select">
          <option value="">Semua Kategori</option>
          <?php $allKat->data_seek(0); while($k=$allKat->fetch_assoc()): ?>
          <option value="<?= $k['kategori'] ?>" <?= $kat==$k['kategori']?'selected':'' ?>><?= e($k['kategori']) ?></option>
          <?php endwhile; ?>
        </select>
        <select name="level" class="filter-select">
          <option value="">Semua Level</option>
          <?php foreach($lvlList as $lv): ?>
          <option value="<?= $lv ?>" <?= $lvl==$lv?'selected':'' ?>><?= $lv ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn-sm"><i class="bi bi-funnel"></i> Filter</button>
        <?php if($kat||$lvl||$cari2): ?>
        <a href="training.php" style="padding:8px 14px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:10px;font-size:13px;color:rgba(255,255,255,.5);text-decoration:none">
          <i class="bi bi-x"></i>
        </a>
        <?php endif; ?>
      </div>
      <p style="font-size:12px;color:rgba(255,255,255,.28);margin-top:8px"><?= $trainings->num_rows ?> materi tersedia</p>
    </form>

    <!-- grid training -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">

      <?php if($trainings->num_rows == 0): ?>
      <div style="grid-column:1/-1;text-align:center;padding:60px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.07);border-radius:14px">
        <span style="font-size:40px;display:block;margin-bottom:12px">😕</span>
        <p style="color:rgba(255,255,255,.35)">Materi tidak ditemukan.</p>
      </div>
      <?php endif; ?>

      <?php while($tr = $trainings->fetch_assoc()):
        $lvlClass = 'lvl-'.strtolower($tr['level']);
      ?>
      <article class="train-card" data-aos="fade-up" onclick="lihatDetail(<?= $tr['id'] ?>,'<?= addslashes(e($tr['judul'])) ?>','<?= addslashes(e($tr['deskripsi'])) ?>','<?= addslashes(e($tr['instruktur'])) ?>','<?= $tr['level'] ?>','<?= $tr['durasi'] ?>','<?= $tr['is_gratis']?'Gratis':'Berbayar' ?>')">
        <span class="train-icon"><?= $tr['icon_emoji'] ?></span>

        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px">
          <span style="font-size:11px;background:rgba(139,92,246,.1);color:#c4b5fd;padding:2px 8px;border-radius:20px">
            <?= e($tr['kategori']) ?>
          </span>
          <?php if($tr['is_gratis']): ?>
          <span class="gratis-badge">GRATIS</span>
          <?php else: ?>
          <span class="bayar-badge">PREMIUM</span>
          <?php endif; ?>
        </div>

        <div class="train-title"><?= e($tr['judul']) ?></div>
        <div class="train-inst"><i class="bi bi-person-circle" style="margin-right:4px"></i><?= e($tr['instruktur']) ?></div>
        <p class="train-desc"><?= e($tr['deskripsi']) ?></p>

        <div class="train-meta">
          <span class="lvl-badge <?= $lvlClass ?>"><?= e($tr['level']) ?></span>
          <span class="dur-text"><i class="bi bi-clock"></i><?= e($tr['durasi']) ?></span>
        </div>

        <button class="mulai-btn" onclick="event.stopPropagation(); lihatDetail(<?= $tr['id'] ?>,'<?= addslashes(e($tr['judul'])) ?>','<?= addslashes(e($tr['deskripsi'])) ?>','<?= addslashes(e($tr['instruktur'])) ?>','<?= $tr['level'] ?>','<?= $tr['durasi'] ?>','<?= $tr['is_gratis']?'Gratis':'Berbayar' ?>')">
          <i class="bi bi-play-circle" style="margin-right:6px"></i>Mulai Belajar
        </button>
      </article>
      <?php endwhile; ?>

    </div>
  </main>
</div>

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
AOS.init({ once:true, duration:550, offset:40 });

function lihatDetail(id, judul, deskripsi, instruktur, level, durasi, harga){
  Swal.fire({
    title: judul,
    html: '<div style="text-align:left;padding:4px 0">' +
          '<p style="font-size:13px;color:rgba(255,255,255,.55);margin-bottom:14px;line-height:1.6">' + deskripsi + '</p>' +
          '<div style="display:flex;flex-wrap:wrap;gap:10px">' +
          '<div style="background:rgba(255,255,255,.06);border-radius:8px;padding:8px 12px;font-size:12px">' +
            '<span style="color:rgba(255,255,255,.4)">Instruktur</span><br>' +
            '<strong style="color:#fff">' + instruktur + '</strong>' +
          '</div>' +
          '<div style="background:rgba(255,255,255,.06);border-radius:8px;padding:8px 12px;font-size:12px">' +
            '<span style="color:rgba(255,255,255,.4)">Level</span><br>' +
            '<strong style="color:#60a5fa">' + level + '</strong>' +
          '</div>' +
          '<div style="background:rgba(255,255,255,.06);border-radius:8px;padding:8px 12px;font-size:12px">' +
            '<span style="color:rgba(255,255,255,.4)">Durasi</span><br>' +
            '<strong style="color:#fff">' + durasi + '</strong>' +
          '</div>' +
          '<div style="background:rgba(255,255,255,.06);border-radius:8px;padding:8px 12px;font-size:12px">' +
            '<span style="color:rgba(255,255,255,.4)">Harga</span><br>' +
            '<strong style="color:#34d399">' + harga + '</strong>' +
          '</div>' +
          '</div></div>',
    background: '#111827',
    color: '#e2e8f0',
    confirmButtonColor: '#3b82f6',
    confirmButtonText: '<i class="bi bi-play-circle"></i> Mulai Sekarang',
    showCancelButton: true,
    cancelButtonText: 'Nanti Saja',
    cancelButtonColor: '#374151'
  }).then(function(r){
    if(r.isConfirmed){
      Swal.fire({
        icon: 'success',
        title: 'Selamat Belajar! 🎉',
        text: 'Fitur streaming materi akan segera tersedia.',
        background: '#111827',
        color: '#e2e8f0',
        confirmButtonColor: '#3b82f6',
        timer: 3000
      });
    }
  });
}
</script>
</body>
</html>

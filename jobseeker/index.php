<?php
session_start();
require_once '../config/db.php';
harusLogin('../auth/login.php');
if(cekAdmin()) redirect('../admin/index.php');

$uid = $_SESSION['user_id'];
$tab = $_GET['tab'] ?? 'browse';
$flash_msg = $flash_type = '';

// proses lamaran
if(isset($_GET['apply'])){
	$jid = (int)$_GET['apply'];

	// cek job ada dan aktif
	$cj = $conn->prepare("SELECT id,title,company FROM jobs WHERE id=? AND is_active=1");
	$cj->bind_param('i',$jid); $cj->execute();
	$the_job = $cj->get_result()->fetch_assoc();

	if(!$the_job){
		$flash_msg = 'Lowongan tidak ditemukan atau sudah tidak aktif.'; $flash_type = 'err';
	} else {
		// cek sudah pernah lamar belum
		$cd = $conn->prepare("SELECT id FROM applications WHERE user_id=? AND job_id=?");
		$cd->bind_param('ii',$uid,$jid); $cd->execute();
		if($cd->get_result()->num_rows > 0){
			$flash_msg = 'Kamu sudah pernah melamar posisi '.$the_job['title'].' di '.$the_job['company'].'.';
			$flash_type = 'warn';
		} else {
			$note = trim($_GET['note'] ?? '');
			$ins  = $conn->prepare("INSERT INTO applications(user_id,job_id,cover_note)VALUES(?,?,?)");
			$ins->bind_param('iis',$uid,$jid,$note);
			if($ins->execute()){
				$flash_msg  = 'Lamaran ke '.$the_job['company'].' berhasil dikirim! Pantau statusnya di tab Lamaranku.';
				$flash_type = 'ok';
			} else {
				$flash_msg = 'Gagal mengirim lamaran, coba lagi.'; $flash_type = 'err';
			}
		}
	}
	$tab = 'browse';
}

// filter lowongan
$cari     = trim($_GET['search']   ?? '');
$kategori = trim($_GET['category'] ?? '');
$tipe     = trim($_GET['type']     ?? '');

$where  = "WHERE j.is_active=1";
$params = [];
$types  = '';

if($cari){
	$where .= " AND (j.title LIKE ? OR j.company LIKE ? OR j.location LIKE ?)";
	$s = "%$cari%";
	$params[] = $s; $params[] = $s; $params[] = $s;
	$types .= 'sss';
}
if($kategori){
	$where .= " AND j.category=?"; $params[] = $kategori; $types .= 's';
}
if($tipe){
	$where .= " AND j.type=?"; $params[] = $tipe; $types .= 's';
}

$st = $conn->prepare("SELECT j.* FROM jobs j $where ORDER BY j.created_at DESC");
if($types) $st->bind_param($types, ...$params);
$st->execute();
$jobs = $st->get_result();

// lamaran user ini
$mySt = $conn->prepare(
	"SELECT a.*,j.title,j.company,j.location,j.type,j.category,j.salary_min,j.salary_max
	 FROM applications a JOIN jobs j ON a.job_id=j.id
	 WHERE a.user_id=? ORDER BY a.applied_at DESC"
);
$mySt->bind_param('i',$uid); $mySt->execute();
$my_apps = $mySt->get_result();

// id lowongan yg sudah dilamar (untuk disable tombol)
$applied_ids = [];
$ar = $conn->prepare("SELECT job_id FROM applications WHERE user_id=?");
$ar->bind_param('i',$uid); $ar->execute();
foreach($ar->get_result() as $r) $applied_ids[] = $r['job_id'];

// kategori untuk filter
$qCats = $conn->query("SELECT DISTINCT category FROM jobs WHERE is_active=1 ORDER BY category");
$tipeList = ['Full-time','Part-time','Internship','Remote','Freelance'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard — ZenirWork</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
<link rel="stylesheet" href="../assets/style.css">
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
      <div style="display:flex;align-items:center;gap:8px">
        <div style="width:30px;height:30px;background:linear-gradient(135deg,#3b82f6,#8b5cf6);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff">
          <?= e($_SESSION['avatar']??'U') ?>
        </div>
        <span style="font-size:13px;color:rgba(255,255,255,.75);display:none" class="hide-mobile"><?= e($_SESSION['name']) ?></span>
      </div>
      <a href="../auth/logout.php" style="font-size:13px;color:rgba(239,68,68,.75);text-decoration:none">
        <i class="bi bi-box-arrow-right"></i>
      </a>
    </div>
  </div>
</nav>

<div class="dash-wrap">

  <!-- sidebar -->
  <aside class="sidebar">
    <div class="sb-avatar"><?= e($_SESSION['avatar']??'U') ?></div>
    <div class="sb-name"><?= e($_SESSION['name']) ?></div>
    <div class="sb-role">Pencari Kerja</div>
    <hr class="sb-hr">
    <a href="?tab=browse" class="sb-link <?= $tab=='browse'?'on':'' ?>">
      <i class="bi bi-search"></i> Cari Lowongan
    </a>
    <a href="?tab=lamaran" class="sb-link <?= $tab=='lamaran'?'on':'' ?>">
      <i class="bi bi-file-earmark-check"></i> Lamaranku
      <?php if($my_apps->num_rows > 0): ?>
      <span class="sb-badge"><?= $my_apps->num_rows ?></span>
      <?php endif; ?>
    </a>
    <hr class="sb-hr">
    <a href="training.php" class="sb-link"><i class="bi bi-mortarboard"></i> Pelatihan Skill</a>
    <hr class="sb-hr">
    <a href="../index.php" class="sb-link"><i class="bi bi-house"></i> Beranda</a>
  </aside>

  <!-- konten utama -->
  <main class="dash-main">

    <!-- flash message -->
    <?php if($flash_msg):
      $bg  = $flash_type=='ok'  ? 'rgba(16,185,129,.1)'  : ($flash_type=='warn' ? 'rgba(245,158,11,.1)' : 'rgba(239,68,68,.1)');
      $brd = $flash_type=='ok'  ? 'rgba(16,185,129,.25)' : ($flash_type=='warn' ? 'rgba(245,158,11,.25)' : 'rgba(239,68,68,.25)');
      $clr = $flash_type=='ok'  ? '#6ee7b7' : ($flash_type=='warn' ? '#fcd34d' : '#fca5a5');
      $ic  = $flash_type=='ok'  ? 'bi-check-circle' : 'bi-exclamation-circle';
    ?>
    <div style="background:<?= $bg ?>;border:1px solid <?= $brd ?>;border-radius:10px;padding:12px 16px;font-size:13px;color:<?= $clr ?>;margin-bottom:18px;display:flex;align-items:flex-start;gap:8px" id="flash-el">
      <i class="bi <?= $ic ?>" style="margin-top:1px;flex-shrink:0"></i>
      <?= e($flash_msg) ?>
      <button onclick="document.getElementById('flash-el').remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:inherit;opacity:.6;font-size:16px">×</button>
    </div>
    <?php endif; ?>

    <!-- ====== TAB BROWSE ====== -->
    <?php if($tab == 'browse'): ?>

    <!-- filter bar -->
    <form method="GET" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:14px;padding:16px;margin-bottom:18px">
      <input type="hidden" name="tab" value="browse">
      <div class="filter-row">
        <div style="position:relative;flex:1;min-width:180px">
          <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,.3);font-size:13px"></i>
          <input type="text" name="search" value="<?= e($cari) ?>"
                 class="search-input" style="width:100%;padding-left:34px"
                 placeholder="Cari posisi, perusahaan, lokasi...">
        </div>
        <select name="category" class="filter-select">
          <option value="">Semua Kategori</option>
          <?php $qCats->data_seek(0); while($c=$qCats->fetch_assoc()): ?>
          <option value="<?= $c['category'] ?>" <?= $kategori==$c['category']?'selected':'' ?>>
            <?= e($c['category']) ?>
          </option>
          <?php endwhile; ?>
        </select>
        <select name="type" class="filter-select">
          <option value="">Semua Tipe</option>
          <?php foreach($tipeList as $tp): ?>
          <option value="<?= $tp ?>" <?= $tipe==$tp?'selected':'' ?>><?= $tp ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn-sm" style="padding:8px 16px">
          <i class="bi bi-funnel"></i> Filter
        </button>
        <?php if($cari||$kategori||$tipe): ?>
        <a href="?tab=browse" style="padding:8px 14px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:10px;font-size:13px;color:rgba(255,255,255,.5);text-decoration:none">
          <i class="bi bi-x"></i> Reset
        </a>
        <?php endif; ?>
      </div>
      <div style="font-size:12px;color:rgba(255,255,255,.3);margin-top:8px">
        Menampilkan <?= $jobs->num_rows ?> lowongan
      </div>
    </form>

    <!-- grid lowongan -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:14px">

      <?php if($jobs->num_rows == 0): ?>
      <div style="grid-column:1/-1;text-align:center;padding:60px 20px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.07);border-radius:14px">
        <i class="bi bi-search" style="font-size:40px;color:rgba(255,255,255,.15);display:block;margin-bottom:12px"></i>
        <p style="color:rgba(255,255,255,.35);font-size:14px">Tidak ada lowongan yang cocok.</p>
        <a href="?tab=browse" style="color:#60a5fa;font-size:13px;text-decoration:none;margin-top:8px;display:inline-block">Reset pencarian</a>
      </div>
      <?php endif; ?>

      <?php while($job = $jobs->fetch_assoc()):
        $sudah = in_array($job['id'], $applied_ids);
      ?>
      <article class="jcard-user" data-aos="fade-up">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px">
          <div style="width:38px;height:38px;background:rgba(59,130,246,.14);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#60a5fa;font-size:16px">
            <i class="bi bi-building"></i>
          </div>
          <span class="type-badge <?= warnaType($job['type']) ?>"><?= e($job['type']) ?></span>
        </div>

        <div style="font-weight:600;color:#fff;font-size:14px;margin-bottom:3px"><?= e($job['title']) ?></div>
        <div style="font-size:12px;color:rgba(255,255,255,.4);margin-bottom:2px">
          <i class="bi bi-building" style="margin-right:4px"></i><?= e($job['company']) ?>
        </div>
        <div style="font-size:12px;color:rgba(255,255,255,.35);margin-bottom:10px">
          <i class="bi bi-geo-alt" style="margin-right:4px"></i><?= e($job['location']) ?>
        </div>
        <p style="font-size:12px;color:rgba(255,255,255,.35);line-height:1.6;margin-bottom:10px;flex-grow:1;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
          <?= e($job['description']) ?>
        </p>
        <div style="font-size:11px;font-weight:600;background:rgba(16,185,129,.09);color:#34d399;border-radius:8px;padding:6px 10px;margin-bottom:10px">
          <i class="bi bi-cash-stack" style="margin-right:4px"></i><?= gajiRange($job['salary_min'], $job['salary_max']) ?>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;padding-top:10px;border-top:1px solid rgba(255,255,255,.06)">
          <span style="font-size:11px;background:rgba(139,92,246,.1);color:#c4b5fd;padding:3px 10px;border-radius:20px">
            <?= e($job['category']) ?>
          </span>
          <?php if($sudah): ?>
          <span style="font-size:12px;color:#34d399;font-weight:600;display:flex;align-items:center;gap:4px">
            <i class="bi bi-check-circle-fill"></i> Sudah Dilamar
          </span>
          <?php else: ?>
          <button onclick="konfLamar(<?= $job['id'] ?>,'<?= addslashes(e($job['title'])) ?>','<?= addslashes(e($job['company'])) ?>')"
                  class="btn-sm" style="padding:5px 14px;font-size:12px">
            <i class="bi bi-send"></i> Lamar
          </button>
          <?php endif; ?>
        </div>
      </article>
      <?php endwhile; ?>
    </div>

    <!-- ====== TAB LAMARANKU ====== -->
    <?php else: ?>

    <div style="margin-bottom:16px">
      <h2 style="font-family:'Plus Jakarta Sans',sans-serif;font-size:18px;font-weight:700;color:#fff">Riwayat Lamaranku</h2>
      <p style="font-size:13px;color:rgba(255,255,255,.35);margin-top:4px"><?= $my_apps->num_rows ?> lamaran terkirim</p>
    </div>

    <div class="tbl-card">
      <div style="overflow-x:auto">
        <table class="data-table">
          <thead>
            <tr>
              <th>Posisi & Perusahaan</th>
              <th>Tipe</th>
              <th>Gaji</th>
              <th>Tanggal Lamar</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php $my_apps->data_seek(0); while($a = $my_apps->fetch_assoc()): ?>
            <tr>
              <td>
                <div style="font-weight:600;color:#fff;font-size:13px"><?= e($a['title']) ?></div>
                <div style="font-size:11px;color:rgba(255,255,255,.35);margin-top:2px">
                  <i class="bi bi-building" style="margin-right:3px"></i><?= e($a['company']) ?>
                  <span style="margin:0 4px;opacity:.3">·</span>
                  <i class="bi bi-geo-alt" style="margin-right:3px"></i><?= e($a['location']) ?>
                </div>
              </td>
              <td>
                <span class="type-badge <?= warnaType($a['type']) ?>"><?= e($a['type']) ?></span>
              </td>
              <td style="font-size:12px;color:#34d399;font-weight:500">
                <?= gajiRange($a['salary_min'],$a['salary_max']) ?>
              </td>
              <td style="font-size:12px;color:rgba(255,255,255,.4)">
                <?= date('d M Y', strtotime($a['applied_at'])) ?>
              </td>
              <td>
                <?php
                $icons = ['pending'=>'bi-clock','review'=>'bi-eye','diterima'=>'bi-check-circle-fill','ditolak'=>'bi-x-circle'];
                $ic = $icons[$a['status']] ?? 'bi-clock';
                ?>
                <span class="bdg <?= warnaStatus($a['status']) ?>">
                  <i class="bi <?= $ic ?>" style="margin-right:4px"></i><?= ucfirst($a['status']) ?>
                </span>
              </td>
            </tr>
            <?php endwhile; ?>
            <?php if($my_apps->num_rows == 0): ?>
            <tr>
              <td colspan="5" style="text-align:center;padding:48px 20px;color:rgba(255,255,255,.3)">
                <i class="bi bi-inbox" style="font-size:36px;display:block;margin-bottom:12px;opacity:.4"></i>
                Belum ada lamaran.
                <a href="?tab=browse" style="color:#60a5fa;text-decoration:none;margin-left:4px">Cari lowongan sekarang!</a>
              </td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>

  </main>
</div>

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
AOS.init({ once:true, offset:40, duration:600 });

function konfLamar(id, judul, company){
	Swal.fire({
		title: 'Kirim Lamaran?',
		html: '<div style="text-align:left;padding:4px 0">' +
			  '<p style="font-size:15px;font-weight:600;color:#fff;margin-bottom:4px">' + judul + '</p>' +
			  '<p style="font-size:13px;color:rgba(255,255,255,.5)"><i class="bi bi-building" style="margin-right:4px"></i>' + company + '</p>' +
			  '</div>',
		icon: 'question',
		background: '#111827',
		color: '#e2e8f0',
		showCancelButton: true,
		confirmButtonColor: '#3b82f6',
		cancelButtonColor: '#374151',
		confirmButtonText: '<i class="bi bi-send"></i> Ya, Lamar!',
		cancelButtonText: 'Batal'
	}).then(function(r){
		if(r.isConfirmed){
			window.location.href = '?apply=' + id + '&tab=browse';
		}
	});
}
</script>
</body>
</html>

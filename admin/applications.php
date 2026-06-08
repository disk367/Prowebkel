<?php
session_start();
require_once '../config/db.php';
harusAdmin();

// update status via POST (ajax)
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['app_id'], $_POST['status'])){
	$st_allowed = ['pending','review','diterima','ditolak'];
	$new_status = $_POST['status'];
	if(in_array($new_status, $st_allowed)){
		$up = $conn->prepare("UPDATE applications SET status=? WHERE id=?");
		$up->bind_param('si', $new_status, $_POST['app_id']);
		$up->execute();
	}
	header('Content-Type: application/json');
	echo json_encode(['ok'=>true]);
	exit;
}

$apps = $conn->query(
	"SELECT a.*, u.name AS nm_user, u.email AS em_user,
	        j.title AS jdl, j.company
	 FROM applications a
	 JOIN users u ON a.user_id = u.id
	 JOIN jobs  j ON a.job_id  = j.id
	 ORDER BY a.applied_at DESC"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Semua Lamaran — ZenirWork</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    <a href="index.php" class="sb-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="create.php" class="sb-link"><i class="bi bi-plus-circle"></i> Tambah Lowongan</a>
    <a href="applications.php" class="sb-link on"><i class="bi bi-file-earmark-check"></i> Semua Lamaran</a>
    <hr class="sb-hr">
    <a href="../index.php" class="sb-link"><i class="bi bi-globe"></i> Lihat Website</a>
  </aside>

  <main class="dash-main">
    <div style="margin-bottom:20px">
      <h1 style="font-family:'Plus Jakarta Sans',sans-serif;font-size:22px;font-weight:700;color:#fff;margin-bottom:4px">Semua Lamaran</h1>
      <p style="font-size:13px;color:rgba(255,255,255,.38)"><?= $apps->num_rows ?> lamaran masuk</p>
    </div>

    <div class="tbl-card">
      <div style="overflow-x:auto">
        <table class="data-table">
          <thead>
            <tr>
              <th>Pelamar</th>
              <th>Posisi</th>
              <th>Tanggal</th>
              <th>Catatan</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php while($a = $apps->fetch_assoc()): ?>
            <tr>
              <td>
                <div style="font-weight:600;color:#fff;font-size:13px"><?= e($a['nm_user']) ?></div>
                <div style="font-size:11px;color:rgba(255,255,255,.35)"><?= e($a['em_user']) ?></div>
              </td>
              <td>
                <div style="font-weight:500;color:#e2e8f0;font-size:13px"><?= e($a['jdl']) ?></div>
                <div style="font-size:11px;color:rgba(255,255,255,.35)"><?= e($a['company']) ?></div>
              </td>
              <td style="font-size:12px;color:rgba(255,255,255,.4)">
                <?= date('d M Y', strtotime($a['applied_at'])) ?>
              </td>
              <td style="font-size:12px;color:rgba(255,255,255,.4);max-width:200px">
                <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:180px">
                  <?= e($a['cover_note'] ?? '-') ?>
                </div>
              </td>
              <td>
                <select onchange="ubahStatus(<?= $a['id'] ?>, this.value, this)"
                        class="filter-select" style="padding:5px 8px;font-size:12px;border-radius:8px">
                  <?php foreach(['pending','review','diterima','ditolak'] as $s): ?>
                  <option value="<?= $s ?>" <?= $a['status']==$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                  <?php endforeach; ?>
                </select>
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
function ubahStatus(id, status, el){
	fetch('applications.php', {
		method: 'POST',
		headers: {'Content-Type':'application/x-www-form-urlencoded'},
		body: 'app_id='+id+'&status='+status
	}).then(function(r){ return r.json(); }).then(function(d){
		if(d.ok){
			Swal.fire({
				toast: true, position: 'top-end', icon: 'success',
				title: 'Status diperbarui ke "'+status+'"',
				showConfirmButton: false, timer: 2000,
				background: '#111827', color: '#e2e8f0'
			});
		}
	});
}
</script>
</body>
</html>

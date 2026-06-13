<?php
session_start();
require_once 'config/db.php';


$jml_job  = $conn->query("SELECT COUNT(*) FROM jobs WHERE is_active=1")->fetch_row()[0];
$jml_user = $conn->query("SELECT COUNT(*) FROM users WHERE role='jobseeker'")->fetch_row()[0];
$jml_app  = $conn->query("SELECT COUNT(*) FROM applications")->fetch_row()[0];
$jml_comp = $conn->query("SELECT COUNT(DISTINCT company) FROM jobs")->fetch_row()[0];


$res_ticker = $conn->query("SELECT title, company, salary_min, salary_max FROM jobs WHERE is_active=1 ORDER BY created_at DESC");
$ticker_data = $res_ticker->fetch_all(MYSQLI_ASSOC);


$qJobs = $conn->query("SELECT * FROM jobs WHERE is_active=1 ORDER BY created_at DESC LIMIT 6");


$qCats = $conn->query("SELECT category, COUNT(*) as total FROM jobs WHERE is_active=1 GROUP BY category ORDER BY total DESC LIMIT 6");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ZenirWork - Platform Karier Gen Z Indonesia</title>
<meta name="description" content="Temukan karier impianmu di ZenirWork. Platform lowongan kerja khusus Gen Z Indonesia.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<!-- navbar -->
<header>
<nav class="navbar">
  <div class="nav-inner">
    <a href="index.php" class="logo">
      <div class="logo-box">
        <i class="bi bi-briefcase-fill" style="color:#fff;font-size:14px"></i>
      </div>
      Zenir<span>Work</span>
    </a>

    <ul class="nav-menu" style="list-style:none">
      <li><a href="#lowongan">Lowongan</a>
  <a href="#pelatihan">Pelatihan Skill</a></li>
      <li><a href="#pelatihan">Pelatihan</a></li>
      <li><a href="#kategori">Kategori</a></li>
      <li><a href="#fitur">Fitur</a></li>
      <li><a href="#tentang">Tentang</a></li>
    </ul>

    <div class="nav-btns">
      <?php if(cekLogin()): ?>
        <?php $dest = cekAdmin() ? 'admin/index.php' : 'jobseeker/index.php'; ?>
        <a href="<?= $dest ?>" class="btn-daftar">
          <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="auth/logout.php" style="color:rgba(255,255,255,.55);font-size:13px;text-decoration:none;margin-left:4px">Keluar</a>
      <?php else: ?>
        <a href="auth/login.php" class="btn-masuk">Masuk</a>
        <a href="auth/register.php" class="btn-daftar">Daftar Gratis →</a>
      <?php endif; ?>
      <button class="hamburger" id="ham-btn" aria-label="Menu">
        <i class="bi bi-list"></i>
      </button>
    </div>
  </div>
</nav>

<!-- mobile menu -->
<div class="mobile-menu" id="mob-menu">
  <a href="#lowongan">Lowongan</a>
  <a href="#pelatihan">Pelatihan Skill</a>
  <a href="#kategori">Kategori</a>
  <a href="#fitur">Fitur</a>
  <a href="auth/login.php">Masuk</a>
  <a href="auth/register.php">Daftar Gratis</a>
</div>
</header>

<main>

<!-- ===== HERO ===== -->
<section class="hero" aria-label="Hero Section">
  <div class="blob1"></div>
  <div class="blob2"></div>
  <div class="hero-content">
    <div class="hero-badge" data-aos="fade-down">
      <span class="badge-dot"></span>
      Career Platform untuk Gen Z Indonesia
    </div>
    <h1 data-aos="fade-up" data-aos-delay="100">
      Build Your Future<br>
      <span class="grad">Career</span> with ZenirWork
    </h1>
    <p class="hero-sub" data-aos="fade-up" data-aos-delay="200">
      Find jobs, improve your skills, and prepare for your professional future in one platform. Lowongan terpercaya, khusus Gen Z.
    </p>
    <div class="hero-btns" data-aos="fade-up" data-aos-delay="300">
      <a href="<?= cekLogin() ? (cekAdmin() ? 'admin/index.php' : 'jobseeker/index.php') : 'auth/register.php' ?>" class="btn-primary">
        <i class="bi bi-rocket-takeoff"></i> Explore Jobs →
      </a>
      <a href="#fitur" class="btn-outline2">
        Start Learning <i class="bi bi-arrow-down"></i>
      </a>
    </div>
  </div>
</section>


<!-- ===== STATS ===== -->
<section class="stats-sec" aria-label="Statistik">
  <div class="stats-wrap">
    <?php
    $statData = [
      [$jml_job.'+'  , 'Job Vacancies',     '💼'],
      [$jml_user.'+' , 'Active Users',       '👥'],
      [$jml_comp.'+' , 'Partner Companies',  '🏢'],
      [$jml_app.'+'  , 'Applications Sent',  '📨'],
    ];
    foreach($statData as $i => $st): ?>
    <div class="stat-box" data-aos="zoom-in" data-aos-delay="<?= $i * 100 ?>">
      <span class="stat-num"><?= $st[0] ?></span>
      <div class="stat-lbl"><?= $st[2] ?> <?= $st[1] ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ===== FITUR ===== -->
<section id="fitur" class="features-sec" aria-label="Fitur Platform">
  <div class="sec-head" data-aos="fade-up">
    <h2><span class="grad-text">Powerful Features</span></h2>
    <p>Everything you need to kickstart and develop your career journey</p>
  </div>
  <div class="feat-grid">
    <?php
    $fitur = [
      ['bi-search'       , 'bg:rgba(59,130,246,.15);color:#60a5fa' , 'Job Vacancy Search',  'Cari lowongan berdasarkan posisi, kategori, lokasi, dan rentang gaji dengan filter canggih.'],
      ['bi-mortarboard'  , 'bg:rgba(139,92,246,.15);color:#c4b5fd' , 'Skill Training',      'Tingkatkan kemampuanmu dengan sumber belajar yang dikurasi sesuai bidang kariermu.'],
      ['bi-graph-up'     , 'bg:rgba(16,185,129,.15);color:#34d399' , 'Career Development',  'Pantau perkembangan kariermu dan dapatkan insight untuk terus berkembang di industri.'],
      ['bi-person-check' , 'bg:rgba(245,158,11,.15);color:#fbbf24' , 'User Dashboard',      'Dashboard personal untuk melacak lamaran, menyimpan lowongan, dan mengelola profil.'],
    ];
    foreach($fitur as $i => $f): ?>
    <article class="feat-card" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
      <div class="feat-icon" style="<?= $f[1] ?>">
        <i class="bi <?= $f[0] ?>"></i>
      </div>
      <h4><?= $f[2] ?></h4>
      <p><?= $f[3] ?></p>
    </article>
    <?php endforeach; ?>
  </div>
</section>

<!-- ===== LOWONGAN TERBARU ===== -->
<section id="lowongan" class="jobs-sec" aria-label="Lowongan Terbaru">
  <div style="max-width:1200px;margin:0 auto;padding:0 24px">
    <div class="sec-head" data-aos="fade-up">
      <h2>Latest <span class="grad-text">Job Openings</span></h2>
      <p>Peluang kerja terpilih dari perusahaan teknologi dan kreatif Indonesia</p>
    </div>
    <div class="jobs-grid">
      <?php while($job = $qJobs->fetch_assoc()):
        $tc = warnaType($job['type']);
      ?>
      <article class="job-card" data-aos="fade-up">
        <div class="job-top">
          <div class="job-ico"><i class="bi bi-building"></i></div>
          <span class="type-badge <?= $tc ?>"><?= e($job['type']) ?></span>
        </div>
        <div class="job-title"><?= e($job['title']) ?></div>
        <div class="job-meta">
          <i class="bi bi-building" style="margin-right:4px"></i><?= e($job['company']) ?>
          &nbsp;·&nbsp;
          <i class="bi bi-geo-alt" style="margin-right:4px"></i><?= e($job['location']) ?>
        </div>
        <p class="job-desc"><?= e($job['description']) ?></p>
        <div class="job-sal">
          <i class="bi bi-cash-stack" style="margin-right:6px"></i><?= gajiRange($job['salary_min'], $job['salary_max']) ?>
        </div>
        <div class="job-footer">
          <span class="cat-pill"><?= e($job['category']) ?></span>
          <a href="<?= cekLogin() ? 'jobseeker/index.php?apply='.$job['id'] : 'auth/login.php' ?>" class="lamar-btn-link">
            Apply Now <i class="bi bi-arrow-right"></i>
          </a>
        </div>
      </article>
      <?php endwhile; ?>
    </div>
    <div style="text-align:center;margin-top:36px" data-aos="fade-up">
      <a href="<?= cekLogin() ? 'jobseeker/index.php' : 'auth/register.php' ?>" class="btn-primary">
        View All Jobs <i class="bi bi-arrow-right"></i>
      </a>
    </div>
  </div>
</section>

<!-- ===== PELATIHAN SKILL ===== -->
<?php

$qTrain = $conn->query("SELECT * FROM trainings ORDER BY created_at DESC LIMIT 4");
?>
<section id="pelatihan" class="features-sec" style="background:var(--dark2)" aria-label="Pelatihan Skill">
  <div style="max-width:1200px;margin:0 auto;padding:0 24px">

    <div class="sec-head" data-aos="fade-up">
      <h2>Skill <span class="grad-text">Training</span></h2>
      <p>Tingkatkan kemampuanmu dengan materi yang dikurasi khusus untuk Gen Z Indonesia</p>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:18px">
      <?php while($trn = $qTrain->fetch_assoc()):
        $lvBg = ['Pemula'=>'rgba(16,185,129,.12)','Menengah'=>'rgba(245,158,11,.12)','Lanjutan'=>'rgba(239,68,68,.12)'];
        $lvCl = ['Pemula'=>'#34d399','Menengah'=>'#fbbf24','Lanjutan'=>'#fca5a5'];
        $bg = $lvBg[$trn['level']] ?? 'rgba(16,185,129,.12)';
        $cl = $lvCl[$trn['level']] ?? '#34d399';
      ?>
      <article class="feat-card" data-aos="fade-up" style="cursor:pointer" onclick="window.location='<?= cekLogin() ? 'jobseeker/training.php' : 'auth/register.php' ?>'">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px">
          <span style="font-size:2rem"><?= $trn['icon_emoji'] ?></span>
          <?php if($trn['is_gratis']): ?>
          <span style="font-size:10px;font-weight:700;background:rgba(16,185,129,.15);color:#34d399;border:1px solid rgba(16,185,129,.3);padding:2px 8px;border-radius:20px">GRATIS</span>
          <?php else: ?>
          <span style="font-size:10px;font-weight:700;background:rgba(245,158,11,.12);color:#fbbf24;border:1px solid rgba(245,158,11,.25);padding:2px 8px;border-radius:20px">PREMIUM</span>
          <?php endif; ?>
        </div>

        <div style="font-size:11px;background:rgba(139,92,246,.1);color:#c4b5fd;padding:2px 8px;border-radius:20px;display:inline-block;margin-bottom:8px">
          <?= e($trn['kategori']) ?>
        </div>

        <h4 style="font-size:14px"><?= e($trn['judul']) ?></h4>
        <p style="font-size:12px;color:rgba(255,255,255,.38);line-height:1.65;margin-bottom:12px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
          <?= e($trn['deskripsi']) ?>
        </p>

        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:auto">
          <span style="font-size:11px;font-weight:600;background:<?= $bg ?>;color:<?= $cl ?>;padding:2px 8px;border-radius:20px">
            <?= e($trn['level']) ?>
          </span>
          <span style="font-size:11px;color:rgba(255,255,255,.35);display:flex;align-items:center;gap:4px">
            <i class="bi bi-clock"></i><?= e($trn['durasi']) ?>
          </span>
        </div>

        <div style="margin-top:12px;padding-top:10px;border-top:1px solid rgba(255,255,255,.06);display:flex;justify-content:space-between;align-items:center">
          <span style="font-size:11px;color:rgba(255,255,255,.35)">
            <i class="bi bi-person-circle" style="margin-right:3px"></i><?= e($trn['instruktur']) ?>
          </span>
          <span style="font-size:12px;color:#60a5fa;font-weight:600">
            Mulai Belajar →
          </span>
        </div>
      </article>
      <?php endwhile; ?>
    </div>

    <div style="text-align:center;margin-top:36px" data-aos="fade-up">
      <a href="<?= cekLogin() ? 'jobseeker/training.php' : 'auth/register.php' ?>" class="btn-primary">
        <i class="bi bi-mortarboard"></i> Lihat Semua Materi
      </a>
    </div>

  </div>
</section>

<!-- ===== KATEGORI ===== -->
<section id="kategori" class="cat-sec" aria-label="Kategori Pekerjaan">
  <div style="max-width:1100px;margin:0 auto;padding:0 24px">
    <div class="sec-head" data-aos="fade-up">
      <h2>Browse by <span class="grad-text">Category</span></h2>
      <p>Temukan pekerjaan sesuai bidang yang kamu minati</p>
    </div>
    <?php
    $ic_map = [
      'Teknologi'     => '💻',
      'Desain'        => '🎨',
      'Pemasaran'     => '📣',
      'Kreatif'       => '✨',
      'Data & Analitik' => '📊',
      'Layanan'       => '🎧',
      'Keuangan'      => '💰',
      'Lainnya'       => '🔹',
    ];
    ?>
    <div class="cat-grid">
      <?php $qCats->data_seek(0); $ci = 0; while($cat = $qCats->fetch_assoc()):
        $ico = $ic_map[$cat['category']] ?? '🔹';
        $link = cekLogin() ? 'jobseeker/index.php?category='.urlencode($cat['category']) : 'auth/register.php';
      ?>
      <a href="<?= $link ?>" class="cat-card" data-aos="zoom-in" data-aos-delay="<?= $ci++ * 80 ?>">
        <span class="cat-ic"><?= $ico ?></span>
        <div class="cat-nm"><?= e($cat['category']) ?></div>
        <div class="cat-ct"><?= $cat['total'] ?> lowongan</div>
      </a>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<!-- ===== TRUSTED BY ===== -->
<section id="tentang" class="partners-sec" aria-label="Partner Perusahaan">
  <div style="max-width:1100px;margin:0 auto;padding:0 24px">
    <div class="sec-head" data-aos="fade-up">
      <h2><span class="grad-text">Trusted Partners</span></h2>
      <p>Collaborating with leading companies to bring you jobs that best support our Gen Z</p>
    </div>
    <div class="partners-wrap" data-aos="fade-up">
      <?php foreach(['Tokopedia','Gojek','Shopee','Traveloka','Bukalapak','Grab'] as $p): ?>
      <span class="partner-nm"><?= $p ?></span>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===== CTA ===== -->
<section class="cta-sec" aria-label="Call to Action">
  <div class="cta-glow"></div>
  <div class="cta-inner" data-aos="zoom-in">
    <h2>Siap Mulai Kariermu?</h2>
    <p>Bergabunglah dengan ribuan Gen Z yang sudah menemukan pekerjaan impian mereka di ZenirWork.</p>
    <div class="cta-btns">
      <a href="auth/register.php" class="btn-primary">
        <i class="bi bi-rocket-takeoff"></i> Daftar Gratis Sekarang
      </a>
      <a href="auth/login.php" class="btn-outline2">Masuk ke Akun</a>
    </div>
  </div>
</section>

</main>

<!-- ===== FOOTER ===== -->
<footer>
  <div class="footer-inner">
    <div class="ft-brand">
      <a href="index.php" class="logo" style="margin-bottom:0">
        <div class="logo-box"><i class="bi bi-briefcase-fill" style="color:#fff;font-size:14px"></i></div>
        Zenir<span style="color:#60a5fa">Work</span>
      </a>
      <p>Platform karier digital untuk Gen Z Indonesia. Mendukung SDG 8: Pekerjaan Layak dan Pertumbuhan Ekonomi.</p>
      <p style="margin-top:8px;font-size:12px;color:rgba(255,255,255,.2)"</p>
    </div>
    <nav class="ft-col">
      <h5>Platform</h5>
      <a href="#lowongan">Lowongan</a>
  <a href="#pelatihan">Pelatihan Skill</a>
      <a href="#kategori">Kategori</a>
      <a href="auth/register.php">Daftar</a>
      <a href="auth/login.php">Masuk</a>
    </nav>
    <nav class="ft-col">
      <h5>Perusahaan</h5>
      <a href="#tentang">Tentang Kami</a>
      <a href="#">Tim Pengembang</a>
      <a href="#">Kebijakan Privasi</a>
    </nav>
    
  </div>
  <div class="footer-bottom">
      Copyright © <?= date('Y') ?> Tim PWEB-1C
  </div>
</footer>

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ once: true, offset: 60, duration: 700 });

  
  var hamBtn = document.getElementById('ham-btn');
  var mobMenu = document.getElementById('mob-menu');
  hamBtn.addEventListener('click', function(){
    mobMenu.classList.toggle('open');
  });

  
  document.querySelectorAll('a[href^="#"]').forEach(function(a){
    a.addEventListener('click', function(e){
      var target = document.querySelector(this.getAttribute('href'));
      if(target){
        e.preventDefault();
        target.scrollIntoView({ behavior:'smooth' });
        mobMenu.classList.remove('open');
      }
    });
  });
</script>
</body>
</html>

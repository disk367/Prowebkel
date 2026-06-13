-- =====================================================
-- ZENIRWORK Database
-- Platform Karier Gen Z Indonesia
-- Tim PWEB-1C | SDG 8
-- =====================================================

CREATE DATABASE IF NOT EXISTS zenirwork_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE zenirwork_db;

-- =====================================================
-- TABEL 1: users
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('admin','jobseeker') NOT NULL DEFAULT 'jobseeker',
    avatar     VARCHAR(10) NOT NULL DEFAULT 'FA',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABEL 2: jobs
-- =====================================================
CREATE TABLE IF NOT EXISTS jobs (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    title        VARCHAR(150) NOT NULL,
    company      VARCHAR(100) NOT NULL,
    location     VARCHAR(120) NOT NULL,
    type         ENUM('Full-time','Part-time','Internship','Remote','Freelance') NOT NULL DEFAULT 'Full-time',
    category     VARCHAR(80) NOT NULL,
    description  TEXT NOT NULL,
    requirements TEXT NOT NULL,
    salary_min   INT NOT NULL DEFAULT 0,
    salary_max   INT NOT NULL DEFAULT 0,
    is_active    TINYINT(1) NOT NULL DEFAULT 1,
    created_by   INT NOT NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_jobs_usr FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABEL 3: applications
-- =====================================================
CREATE TABLE IF NOT EXISTS applications (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    job_id      INT NOT NULL,
    cover_note  TEXT,
    status      ENUM('pending','review','diterima','ditolak') NOT NULL DEFAULT 'pending',
    applied_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_app_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_app_job  FOREIGN KEY (job_id)  REFERENCES jobs(id)  ON DELETE CASCADE,
    UNIQUE KEY uk_lmr (user_id, job_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABEL 4: trainings (Pelatihan Skill)
-- =====================================================
CREATE TABLE IF NOT EXISTS trainings (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    judul       VARCHAR(200) NOT NULL,
    kategori    VARCHAR(80)  NOT NULL,
    deskripsi   TEXT         NOT NULL,
    instruktur  VARCHAR(100) NOT NULL,
    durasi      VARCHAR(50)  NOT NULL,
    level       ENUM('Pemula','Menengah','Lanjutan') NOT NULL DEFAULT 'Pemula',
    icon_emoji  VARCHAR(10)  NOT NULL DEFAULT '📚',
    is_gratis   TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- DATA USERS (password semua = "password")
-- Email: admin@zenirwork.id / daffa@zenirwork.id dll
-- =====================================================
INSERT INTO users (id, name, email, password, role, avatar) VALUES
(1, 'Admin FADENARA', 'admin@zenirwork.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'AF'),
(2, 'Daffa Muhtarom', 'daffa@zenirwork.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'jobseeker', 'DM'),
(3, 'Rizal Priyono', 'rizal@zenirwork.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'jobseeker', 'RP'),
(4, 'Naufal Wicaksono', 'naufal@zenirwork.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'jobseeker', 'NW'),
(5, 'Faiz Suryana', 'faiz@zenirwork.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'jobseeker', 'FS');

-- =====================================================
-- DATA JOBS (10 lowongan)
-- =====================================================
INSERT INTO jobs (id, title, company, location, type, category, description, requirements, salary_min, salary_max, created_by) VALUES
(1, 'UI/UX Designer', 'Tokopedia', 'Jakarta Selatan', 'Full-time', 'Desain',
 'Bergabunglah dengan tim desain Tokopedia dan ciptakan pengalaman pengguna yang intuitif untuk jutaan pelanggan di seluruh Indonesia.',
 'Menguasai Figma atau Adobe XD\nPortofolio desain yang kuat\nPengalaman minimal 1 tahun\nPaham design system',
 6000000, 10000000, 1),

(2, 'Frontend Developer (React)', 'Gojek', 'Yogyakarta (Remote)', 'Remote', 'Teknologi',
 'Bangun antarmuka web yang cepat dan responsif untuk produk Gojek. Bekerja langsung dengan tim product dan backend.',
 'Mahir React.js dan TypeScript\nMengerti REST API dan GraphQL\nPengalaman dengan Tailwind CSS\nGit workflow yang rapi',
 7000000, 13000000, 1),

(3, 'Content Creator', 'Shopee Indonesia', 'Bandung', 'Full-time', 'Kreatif',
 'Produksi konten video dan tulisan yang relevan dan engaging untuk audiens Gen Z Shopee Indonesia.',
 'Pengalaman membuat konten TikTok dan Reels\nKreativitas tinggi\nDasar editing video\nKomunikatif',
 4500000, 7500000, 1),

(4, 'Data Analyst Intern', 'Traveloka', 'Jakarta (Hybrid)', 'Internship', 'Data & Analitik',
 'Posisi magang ideal untuk mahasiswa semester akhir. Analisis data pengguna dan buat laporan bisnis untuk tim.',
 'Mahasiswa semester 6 keatas atau fresh graduate\nDasar Python (Pandas)\nFamiliar dengan SQL\nBisa Tableau atau Power BI',
 3000000, 4500000, 1),

(5, 'Digital Marketing Specialist', 'Bukalapak', 'Surabaya', 'Full-time', 'Pemasaran',
 'Kelola kampanye digital di Google Ads, Meta Ads, dan TikTok Ads. Optimasi ROI dan laporan performa mingguan.',
 'Pengalaman minimal 2 tahun\nMahir Google Analytics\nKreativitas copy dan visual\nData-driven mindset',
 5500000, 9000000, 1),

(6, 'Backend Developer (Laravel)', 'Blibli', 'Yogyakarta (Remote)', 'Remote', 'Teknologi',
 'Bangun dan pelihara API yang scalable menggunakan Laravel. Kolaborasi dengan tim frontend dan DevOps.',
 'Mahir PHP dan Laravel\nPaham MySQL dan Redis\nPengalaman dengan RESTful API\nFamiliar dengan Docker',
 8000000, 15000000, 1),

(7, 'Graphic Designer', 'Grab Indonesia', 'Jakarta Pusat', 'Full-time', 'Desain',
 'Buat aset visual yang memukau untuk kampanye pemasaran Grab. Banner digital, OOH, hingga motion graphic.',
 'Mahir Adobe Illustrator dan Photoshop\nAfter Effects jadi nilai plus\nPortofolio kreatif yang kuat',
 5000000, 8500000, 1),

(8, 'Customer Success Specialist', 'Xendit', 'Jakarta (Hybrid)', 'Full-time', 'Layanan',
 'Berikan dukungan terbaik kepada merchant Xendit. Identifikasi masalah dan koordinasi dengan tim teknis.',
 'Komunikasi tertulis dan lisan yang baik\nProblem-solver\nDasar pemahaman fintech',
 5000000, 7500000, 1),

(9, 'Mobile Developer (Flutter)', 'Dana Indonesia', 'Yogyakarta (Remote)', 'Remote', 'Teknologi',
 'Kembangkan aplikasi mobile lintas platform menggunakan Flutter. Pastikan performa dan keamanan di iOS dan Android.',
 'Mahir Flutter dan Dart\nPengalaman state management (Bloc atau Riverpod)\nIntegrasi REST API',
 9000000, 16000000, 1),

(10, 'Social Media Manager', 'Ruangguru', 'Jakarta Selatan', 'Full-time', 'Pemasaran',
 'Kelola dan kembangkan komunitas Ruangguru di semua platform media sosial. Buat kalender konten dan monitor engagement.',
 'Pengalaman minimal 1 tahun social media\nAnalitik media sosial\nKreativitas konten edukasi',
 5000000, 8000000, 1);

-- =====================================================
-- DATA APPLICATIONS (5 lamaran)
-- =====================================================
INSERT INTO applications (id, user_id, job_id, cover_note, status) VALUES
(1, 2, 1, 'Saya sangat tertarik dengan posisi UI/UX di Tokopedia. Saya punya pengalaman 1 tahun di bidang desain.', 'pending'),
(2, 2, 3, 'Membuat konten adalah passion saya. Saya aktif di TikTok dengan engagement rate 8%.', 'diterima'),
(3, 3, 2, 'Saya mahir React dan TypeScript, dan antusias bergabung dengan tim Gojek.', 'review'),
(4, 4, 5, 'Saya memiliki pengalaman digital marketing dan familiar dengan Meta Ads Manager.', 'ditolak'),
(5, 5, 4, 'Saya mahasiswa semester 7 dan ingin menerapkan ilmu data science di industri nyata.', 'pending');

-- =====================================================
-- DATA TRAININGS (6 materi pelatihan)
-- =====================================================
INSERT INTO trainings (id, judul, kategori, deskripsi, instruktur, durasi, level, icon_emoji, is_gratis) VALUES
(1, 'Belajar HTML & CSS dari Nol', 'Web Development',
 'Pelajari dasar-dasar pembuatan website dari nol. Mulai dari struktur HTML yang benar, styling dengan CSS, hingga membuat halaman web yang responsif dan menarik. Cocok banget buat kamu yang baru pertama kali belajar coding.',
 'Daffa Muhtarom', '8 jam 30 menit', 'Pemula', '🌐', 1),

(2, 'JavaScript Modern untuk Gen Z', 'Web Development',
 'Kuasai JavaScript ES6+ dengan cara yang fun dan praktis. Belajar arrow function, async/await, fetch API, dan DOM manipulation. Kamu bakal bisa bikin website interaktif setelah selesai kursus ini.',
 'Faiz Imam Suryana', '12 jam', 'Menengah', '⚡', 1),

(3, 'Desain UI/UX dengan Figma', 'Desain',
 'Dari wireframe sampai prototype, belajar semua alur desain profesional menggunakan Figma. Materi mencakup design system, komponen reusable, dan cara presentasi desain ke stakeholder.',
 'Rizal Priyono', '10 jam 15 menit', 'Pemula', '🎨', 1),

(4, 'Digital Marketing untuk Bisnis Online', 'Pemasaran',
 'Strategi pemasaran digital yang relevan di era sekarang. Belajar SEO, Google Ads, Meta Ads, email marketing, dan cara menganalisis performa kampanye pakai data.',
 'Naufal Wicaksono', '9 jam', 'Menengah', '📣', 0),

(5, 'Python untuk Data Science', 'Data & Analitik',
 'Mulai perjalanan data science kamu dengan Python. Materi meliputi Pandas, NumPy, visualisasi data dengan Matplotlib, dan pengenalan machine learning dasar.',
 'Muhammad Rama', '15 jam', 'Menengah', '🐍', 0),

(6, 'Soft Skills & Personal Branding', 'Pengembangan Diri',
 'Skill yang sering dilupakan tapi paling dicari perusahaan. Belajar komunikasi efektif, manajemen waktu, personal branding di LinkedIn, dan cara bikin CV yang menarik perhatian recruiter.',
 'Admin FADENARA', '6 jam', 'Pemula', '✨', 1);

-- =====================================================
-- RESET AUTO_INCREMENT KE ID YANG BENAR
-- =====================================================
ALTER TABLE users AUTO_INCREMENT = 6;
ALTER TABLE jobs AUTO_INCREMENT = 11;
ALTER TABLE applications AUTO_INCREMENT = 6;
ALTER TABLE trainings AUTO_INCREMENT = 7;

-- =====================================================
-- SELESAI
-- =====================================================
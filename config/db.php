<?php
// koneksi database
// tim PWEB-1C - zenirwork

$host = 'localhost';
$user = 'root';
$pass = '';  // laragon default kosong
$db   = 'zenirwork_db';

$conn = new mysqli($host, $user, $pass, $db);
if($conn->connect_error){
	die('<div style="font-family:sans-serif;padding:2rem;color:#fca5a5;background:#080d1a;min-height:100vh;display:flex;align-items:center;justify-content:center">
	<div style="text-align:center">
	<p style="font-size:2rem;margin-bottom:1rem">⚠️</p>
	<h2 style="margin-bottom:.5rem">Koneksi DB Gagal</h2>
	<p style="font-size:.875rem;opacity:.6">'.$conn->connect_error.'</p>
	<p style="font-size:.8rem;opacity:.4;margin-top:.5rem">Pastikan MySQL aktif di Laragon</p>
	</div></div>');
}
$conn->set_charset('utf8mb4');

// fungsi helper
function e($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function rupiah($n){
	return 'Rp '.number_format($n,0,',','.');
}

function gajiRange($min, $max){
	return rupiah($min).' – '.rupiah($max);
}

function redirect($url){
	header("Location: $url");
	exit();
}

function cekLogin(){
	return isset($_SESSION['user_id']);
}

function cekAdmin(){
	return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

function harusLogin($back = '../auth/login.php'){
	if(!cekLogin()) redirect($back);
}

function harusAdmin(){
	if(!cekLogin() || !cekAdmin()) redirect('../auth/login.php');
}

// warna badge tipe pekerjaan
function warnaType($t){
	if($t == 'Remote')    return 'tRemote';
	if($t == 'Internship') return 'tIntern';
	if($t == 'Part-time') return 'tPart';
	if($t == 'Freelance') return 'tFree';
	return 'tFull';
}

function warnaStatus($s){
	$map = ['diterima'=>'st-diterima','ditolak'=>'st-ditolak','review'=>'st-review','pending'=>'st-pending'];
	return $map[$s] ?? 'st-pending';
}

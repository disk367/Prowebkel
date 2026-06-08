<?php
session_start(); require_once '../config/db.php'; harusAdmin();
$id = (int)($_GET['id'] ?? 0);
if($id > 0){
  $st=$conn->prepare("SELECT title FROM jobs WHERE id=?");
  $st->bind_param('i',$id); $st->execute();
  $r=$st->get_result()->fetch_assoc();
  if($r){
    $del=$conn->prepare("DELETE FROM jobs WHERE id=?");
    $del->bind_param('i',$id); $del->execute();
    $_SESSION['flash'] = "Lowongan \"{$r['title']}\" berhasil dihapus.";
  }
}
redirect('index.php');

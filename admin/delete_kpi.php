<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  header('Location: /login.php');
  exit;
}
include('../config/db.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id) {
  $stmt = $conn->prepare('DELETE FROM kpi_scores WHERE id=?');
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $stmt->close();
}
header('Location: dashboard.php');
exit;
?>
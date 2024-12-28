<?php
session_start();

if (!isset($_SESSION["kullanici_id"])) {
    header("Location: ../index.php");  // Kullanıcı giriş yapmadıysa yönlendir
    exit();
}

$kullanici_id = $_SESSION["kullanici_id"];
$etkinlik_id = $_GET['etkinlik_id'];

// Veritabanına bağlan
$conn = new mysqli("localhost", "root", "", "dizifilm");

// Kullanıcıdan gelen etkinliği favorilerden çıkar
$sql = "DELETE FROM favoriler WHERE kullanici_id = ? AND etkinlik_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $kullanici_id, $etkinlik_id);
$stmt->execute();

// Favoriler sayfasına yönlendir
header("Location: favoriler.php");
exit();
?>

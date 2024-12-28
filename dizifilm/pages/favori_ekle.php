<?php
session_start();
if (!isset($_SESSION["kullanici_id"])) {
    header("Location: ../index.php");  // Kullanıcı giriş yapmadıysa yönlendir
    exit();
}

if (isset($_GET['etkinlik_id'])) {
    $etkinlik_id = $_GET['etkinlik_id'];
    $kullanici_id = $_SESSION["kullanici_id"];

    // Veritabanına bağlan
    $conn = new mysqli("localhost", "root", "", "dizifilm");

    // Kullanıcı bu etkinliği favorilerine ekledi mi kontrol et
    $sql = "SELECT * FROM favoriler WHERE kullanici_id = ? AND etkinlik_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $kullanici_id, $etkinlik_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Eğer bu etkinlik daha önce favorilere eklenmediyse, favorilere ekle
        $sql = "INSERT INTO favoriler (kullanici_id, etkinlik_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $kullanici_id, $etkinlik_id);
        $stmt->execute();
    }

    header("Location: etkinlikler.php");  // Favoriye ekledikten sonra etkinlikler sayfasına yönlendir
    exit();
}
?>

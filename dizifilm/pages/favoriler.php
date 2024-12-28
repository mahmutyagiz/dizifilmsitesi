<?php
session_start();
if (!isset($_SESSION["kullanici_id"])) {
    header("Location: ../index.php");  // Kullanıcı giriş yapmadıysa yönlendir
    exit();
}

$kullanici_id = $_SESSION["kullanici_id"];

// Veritabanına bağlan
$conn = new mysqli("localhost", "root", "", "dizifilm");

// Kullanıcının favori etkinliklerini al
$sql = "SELECT e.* FROM etkinlikler e 
        JOIN favoriler f ON e.id = f.etkinlik_id 
        WHERE f.kullanici_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $kullanici_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Favorilerim</title>
    <link rel="stylesheet" href="favori.css">
</head>
<body>
    <header>
        <h1>Favori Etkinliklerim</h1>
        <a href="etkinlikler.php" class="back-btn">Geri Dön</a>
    </header>

    <div class="container">
        <div class="section">
            <h2>Favoriler</h2>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <img src="<?php echo $row['resim_yolu']; ?>" alt="<?php echo $row['isim']; ?>">
                    <h3><?php echo $row['isim']; ?></h3>
                    <a href="koltuk_secimi.php?etkinlik_id=<?php echo $row['id']; ?>">Koltuk Seç</a>
                    <a href="yorum_ekle.php?etkinlik_id=<?php echo $row['id']; ?>" class="comment-btn">Yorum Yap</a>
                    <!-- Favorilerden Çıkar Butonu Eklendi -->
                    <a href="favori_sil.php?etkinlik_id=<?php echo $row['id']; ?>" class="remove-fav-btn">Favorilerden Çıkar</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>

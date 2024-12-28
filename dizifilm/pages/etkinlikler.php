<?php
session_start();
if (!isset($_SESSION["kullanici_id"])) {
    header("Location: ../index.php");
    exit();
}

// Çıkış yap işlemi
if (isset($_GET['logout'])) {
    session_destroy(); 
    header("Location: ../index.php");  
    exit();
}

$conn = new mysqli("localhost", "root", "", "dizifilm");
$sql = "SELECT * FROM etkinlikler";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vizyondaki Film ve Tiyatrolar</title>
    <link rel="stylesheet" href="etkinlikler.css">
</head>
<body>
    <header>
        <h1>Vizyondaki Film ve Tiyatrolar</h1>
        <!-- Çıkış ve favori butonları -->
        <div class="button-container">   
        <a href="favoriler.php" class="favorite-btnn">Favoriler</a>
        <a href="?logout" class="logout-btn">Çıkış Yap</a>

        </div>
    </header>

    <div class="container">
        <div class="section">
            <h2>Tiyatro</h2>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php if (isset($row['tur']) && $row['tur'] == 'tiyatro'): ?>
                    <div class="card">
                        <img src="<?php echo $row['resim_yolu']; ?>" alt="<?php echo $row['isim']; ?>">
                        <h3><?php echo $row['isim']; ?></h3>
                        <a href="koltuk_secimi.php?etkinlik_id=<?php echo $row['id']; ?>">Koltuk Seç</a>
                        <a href="yorum_ekle.php?etkinlik_id=<?php echo $row['id']; ?>" class="comment-btn">Yorum Yap</a>
                        <a href="favori_ekle.php?etkinlik_id=<?php echo $row['id']; ?>" class="favorite-btn">Favori Ekle</a>
                    </div>
                <?php endif; ?>
            <?php endwhile; ?>
        </div>

        <div class="section">
            <h2>Film</h2>
            <?php $result->data_seek(0); ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php if (isset($row['tur']) && $row['tur'] == 'film'): ?>
                    <div class="card">
                        <img src="<?php echo $row['resim_yolu']; ?>" alt="<?php echo $row['isim']; ?>">
                        <h3><?php echo $row['isim']; ?></h3>
                        <a href="koltuk_secimi.php?etkinlik_id=<?php echo $row['id']; ?>">Koltuk Seç</a>
                        <a href="yorum_ekle.php?etkinlik_id=<?php echo $row['id']; ?>" class="comment-btn">Yorum Yap</a>                      
                        <a href="favori_ekle.php?etkinlik_id=<?php echo $row['id']; ?>" class="favorite-btn">Favori Ekle</a>
                    </div>
                <?php endif; ?>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>


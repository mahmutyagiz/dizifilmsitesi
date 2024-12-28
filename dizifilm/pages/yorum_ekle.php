<?php
session_start();
if (!isset($_SESSION["kullanici_id"])) {
    header("Location: ../index.php");
    exit();
}

// Veritabanı bağlantısı
$conn = new mysqli("localhost", "root", "", "dizifilm");
if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}

// Kullanıcı bilgilerini oturumdan alın
$kullanici_id = $_SESSION["kullanici_id"];
$kullanici = $conn->query("SELECT * FROM kullanicilar WHERE id = $kullanici_id")->fetch_assoc();
$kullanici_ad = $kullanici['ad'];
$kullanici_soyad = $kullanici['soyad'];
$kullanici_eposta = $kullanici['eposta'];

// Yorum ekleme işlemi
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["etkinlik_id"]) && isset($_POST["yorum"])) {
    $etkinlik_id = intval($_POST["etkinlik_id"]);
    $yorum = $conn->real_escape_string($_POST["yorum"]);

    // Yorumları veritabanına kaydet
    $sql = "INSERT INTO yorumlar (etkinlik_id, kullanici_eposta, kullanici_ad, kullanici_soyad, yorum) 
            VALUES ('$etkinlik_id', '$kullanici_eposta', '$kullanici_ad', '$kullanici_soyad', '$yorum')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Yorum başarıyla kaydedildi.');</script>";
    } else {
        echo "<script>alert('Hata: " . $conn->error . "');</script>";
    }
}

// Etkinlik bilgisi al
$etkinlik_id = isset($_GET['etkinlik_id']) ? intval($_GET['etkinlik_id']) : 0;
$etkinlik = $conn->query("SELECT * FROM etkinlikler WHERE id = $etkinlik_id")->fetch_assoc();
if (!$etkinlik) {
    die("Etkinlik bulunamadı.");
}

// Mevcut yorumları al
$yorumlar = $conn->query("SELECT * FROM yorumlar WHERE etkinlik_id = $etkinlik_id ORDER BY tarih DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yorum Sayfası</title>
    <link rel="stylesheet" href="yorum.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="yorum-container">


        <!-- Yorum Ekleme Alanı -->
        <div class="yorum-ekle">
        <h1>Yorumlar</h1>
            <form method="POST" action="">
                <textarea name="yorum" placeholder="Yorumunuzu yazın..." required></textarea>
                <input type="hidden" name="etkinlik_id" value="<?php echo $etkinlik_id; ?>">
                <button type="submit">Gönder</button>
            </form>
        </div>

        <!-- Mevcut Yorumlar -->
        <div class="yorum-liste">
            <?php while ($yorum = $yorumlar->fetch_assoc()): ?>
                <div class="yorum">
                    <img src="https://via.placeholder.com/60" alt="Kullanıcı">
                    <div class="yorum-detay">
                        <h3><?php echo htmlspecialchars($yorum['kullanici_ad'] . ' ' . $yorum['kullanici_soyad']); ?></h3>
                        <p><?php echo htmlspecialchars($yorum['yorum']); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>

































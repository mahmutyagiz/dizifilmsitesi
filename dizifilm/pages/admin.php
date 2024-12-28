<?php
session_start(); // Oturumu başlat

// Eğer kullanıcı oturum açmamışsa veya admin değilse, giriş sayfasına yönlendir
if (!isset($_SESSION["kullanici_id"]) || $_SESSION["role"] !== 'admin') {
    header("Location: ../index.php"); // Admin değilse giriş sayfasına yönlendir
    exit();
}

$conn = new mysqli("localhost", "root", "", "dizifilm");

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Koltuk bilgilerini çekme (koltuklar, etkinlikler, kullanıcılar)
$sql = "SELECT k.id AS koltuk_id, k.koltuk_no, e.isim AS etkinlik_isim, u.ad, u.soyad
        FROM koltuklar k
        JOIN etkinlikler e ON k.etkinlik_id = e.id
        JOIN kullanicilar u ON k.kullanici_id = u.id";

$result = $conn->query($sql);

// Sorgunun çalışıp çalışmadığını kontrol et
if (!$result) {
    die("Sorgu hatası: " . $conn->error);
}

// Koltuk silme işlemi
if (isset($_GET['delete'])) {
    $koltuk_id = $_GET['delete'];
    
    // Silme işlemi için güvenli sorgu
    $sql_delete = "DELETE FROM koltuklar WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $koltuk_id);
    
    // Silme sorgusunu çalıştır
    if ($stmt_delete->execute()) {
        // Silme başarılıysa admin sayfasına yönlendir
        header("Location: admin.php");
        exit();
    } else {
        // Hata varsa ekrana yazdır
        echo "Silme işlemi başarısız: " . $conn->error;
    }
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <link rel="stylesheet" href="../source/admin.css">
    <!-- Link to Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body>
    <div class="container">
        <!-- Ana Sayfa Butonu -->
        <a href="../index.php" class="home-btn"><i class="fas fa-home"></i>Ana Sayfa</a>
        <a href="adminyorumlar.php" class="comments-btn"><i class="fas fa-comments"></i>Yorumlar Admin</a>
        <h2>Admin Paneli</h2>
        <table>
            <thead>
                <tr>
                    <th>Etkinlik İsmi</th>
                    <th>Kullanıcı Adı</th>
                    <th>Kullanıcı Soyadı</th>
                    <th>Koltuk No</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    // Her satır için veri çıktısını yazdır
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["etkinlik_isim"] . "</td>";
                        echo "<td>" . $row["ad"] . "</td>";
                        echo "<td>" . $row["soyad"] . "</td>";
                        echo "<td>" . $row["koltuk_no"] . "</td>";
                        echo "<td><a href='admin.php?delete=" . $row["koltuk_id"] . "' class='delete-btn'><i class='fas fa-trash'></i></a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Koltuk bulunamadı.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <td>
    <div class="img-container">
        
    </div>
</td>

    </div>
</body>
</html>

<?php
// Bağlantıyı kapat
$conn->close();
?>

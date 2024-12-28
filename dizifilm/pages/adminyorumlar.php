<?php
session_start(); // Oturum başlat

// Eğer admin değilse giriş sayfasına yönlendir
if (!isset($_SESSION["kullanici_id"]) || $_SESSION["role"] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Veritabanına bağlan
$conn = new mysqli("localhost", "root", "", "dizifilm");

// Bağlantı hatasını kontrol et
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Yorum silme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $yorum_id = $_POST['delete'];
    $sql_delete = "DELETE FROM yorumlar WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $yorum_id);

    if ($stmt_delete->execute()) {
        header("Location: yorumlar_admin.php");
        exit();
    } else {
        echo "Silme işlemi başarısız: " . $conn->error;
    }
}

// Yorumları veritabanından çek
$sql = "SELECT * FROM yorumlar";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yorumlar Admin</title>
    <link rel="stylesheet" href="../source/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        // Silme işlemi için onaylama
        function confirmDeletion(event, form) {
            if (!confirm("Bu yorumu silmek istediğinize emin misiniz?")) {
                event.preventDefault(); // İşlemi durdur
            } else {
                form.submit(); // Formu gönder
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <a href="admin.php" class="back-btn"><i class="fas fa-arrow-left"></i>Geri</a>
        <h2>Yorumlar Yönetimi</h2>
        <table>
            <thead>
                <tr>
                    <th>Kullanıcı Adı</th>
                    <th>Yorum</th>
                    <th>Tarih</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["kullanici_ad"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["yorum"]) . "</td>";
                        echo "<td>" . $row["tarih"] . "</td>";
                        echo "<td>";
                        echo "<form method='POST' style='display: inline;' onsubmit='confirmDeletion(event, this)'>";
                        echo "<input type='hidden' name='delete' value='" . $row["id"] . "'>";
                        echo "<button type='submit' class='delete-btn'><i class='fas fa-trash'></i></button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Yorum bulunamadı.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Veritabanı bağlantısını kapat
$conn->close();
?>

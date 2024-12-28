<?php
session_start(); // Oturum başlat

$conn = new mysqli("localhost", "root", "", "dizifilm");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["register"])) {
        $ad = $_POST["ad"];
        $soyad = $_POST["soyad"];
        $eposta = $_POST["eposta"];
        $sifre = $_POST["sifre"];
        $role = 'user'; // Varsayılan rol 'user' olarak belirleniyor

        // Eğer admin kaydı yapılıyorsa, rol 'admin' olarak ayarlanıyor
        if ($eposta === 'admin@example.com') { // Örneğin, admin@example.com e-posta adresi için admin rolü veriliyor
            $role = 'admin';
        }

        $sql = "INSERT INTO kullanicilar (ad, soyad, eposta, sifre, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $ad, $soyad, $eposta, $sifre, $role);
        $stmt->execute();

        echo "Kayıt başarılı! Giriş yapabilirsiniz.";
    } elseif (isset($_POST["login"])) {
        $eposta = $_POST["eposta"];
        $sifre = $_POST["sifre"];

        $sql = "SELECT * FROM kullanicilar WHERE eposta = ? AND sifre = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $eposta, $sifre);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $_SESSION["kullanici_id"] = $user["id"];
            $_SESSION["role"] = $user["role"]; // Kullanıcı rolünü de oturumda tutuyoruz

            // Eğer kullanıcı adminse, admin sayfasına yönlendiriyoruz
            if ($user["role"] === "admin") {
                header("Location: pages/admin.php");
                exit();
            } else {
                header("Location: pages/etkinlikler.php");
                exit();
            }
        } else {
            echo "Hatalı e-posta veya şifre.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Girişi</title>
    <link rel="stylesheet" href="source/style.css">
</head>
<body>
    <h2>Kayıt Ol</h2>
    <form method="post">
        <input type="text" name="ad" placeholder="Ad" required>
        <input type="text" name="soyad" placeholder="Soyad" required>
        <input type="email" name="eposta" placeholder="E-posta" required>
        <input type="password" name="sifre" placeholder="Şifre" required>
        <button type="submit" name="register">Kayıt Ol</button>
    </form>

    <h2>Giriş Yap</h2>
    <form method="post">
        <input type="email" name="eposta" placeholder="E-posta" required>
        <input type="password" name="sifre" placeholder="Şifre" required>
        <button type="submit" name="login">Giriş Yap</button>
    </form>
</body>
</html>

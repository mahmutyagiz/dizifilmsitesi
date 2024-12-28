<?php
session_start();
$conn = new mysqli("localhost", "root", "", "dizifilm");

$etkinlik_id = $_GET["etkinlik_id"];
$kullanici_id = $_SESSION["kullanici_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $koltuk_no = $_POST["koltuk_no"];

    // Kullanıcının zaten bu koltuğu alıp almadığını kontrol et
    $sql_check = "SELECT * FROM koltuklar WHERE etkinlik_id = ? AND koltuk_no = ? AND kullanici_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("iii", $etkinlik_id, $koltuk_no, $kullanici_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Kullanıcı bu koltuğu zaten almış, iptal et
        $sql_delete = "DELETE FROM koltuklar WHERE etkinlik_id = ? AND koltuk_no = ? AND kullanici_id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("iii", $etkinlik_id, $koltuk_no, $kullanici_id);
        $stmt_delete->execute();
        echo "Koltuk iptal edildi.";
    } else {
        // Kullanıcı yeni koltuk seçiyor
        $sql_insert = "INSERT INTO koltuklar (etkinlik_id, koltuk_no, kullanici_id) VALUES (?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iii", $etkinlik_id, $koltuk_no, $kullanici_id);
        $stmt_insert->execute();
        echo "Koltuk seçildi.";
    }
}

$sql = "SELECT koltuk_no FROM koltuklar WHERE etkinlik_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $etkinlik_id);
$stmt->execute();
$result = $stmt->get_result();

$dolu_koltuklar = [];
while ($row = $result->fetch_assoc()) {
    $dolu_koltuklar[] = $row["koltuk_no"];
}

$sql_user_seat = "SELECT koltuk_no FROM koltuklar WHERE etkinlik_id = ? AND kullanici_id = ?";
$stmt_user_seat = $conn->prepare($sql_user_seat);
$stmt_user_seat->bind_param("ii", $etkinlik_id, $kullanici_id);
$stmt_user_seat->execute();
$result_user_seat = $stmt_user_seat->get_result();
$user_seat = $result_user_seat->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koltuk Seçimi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" >
    <style>
        /* Genel Sayfa Stili */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }
        
        /* Başlık Stili */
        h1 {
            font-size: 2.5rem;
            color: #4CAF50;
            margin-bottom: 1.5rem;
            font-weight: 600;
            text-align: center;
        }

        /* Koltuk Seçim Konteyneri */
        .seats-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 1rem;
            justify-content: center;
            margin-bottom: 2rem;
            width: 100%;
            max-width: 600px;
        }

        /* Bireysel Koltuk Düğmesi */
        .button {
            font-size: 1.2rem;
            width: 70px;
            height: 70px;
            background-color: #f4f6f9;
            border: 2px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            color: #333;
            position: relative;
            font-weight: 600;
            user-select: none;
        }

        /* Üzerine gelindiğinde Renk Değişimi */
        .button:hover {
            background-color: #28a745;
            transform: scale(1.1);
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.15);
        }

        /* Engellenmiş Koltuk (Dolu Koltuk) */
        .button:disabled {
            background-color: #dc3545;
            cursor: not-allowed;
            transform: none;
        }

        /* Koltuk İptali için Tooltip */
        .button-tooltip {
            position: absolute;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #28a745;
            color: white;
            font-size: 0.9rem;
            border-radius: 4px;
            padding: 5px;
            display: none;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .button:hover .button-tooltip {
            display: block;
            opacity: 1;
        }

        /* Geri Dön Butonu */
        .back-btn {
            padding: 12px 25px;
            font-size: 1.1rem;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 8px;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-top: 20px;
        }

        .back-btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        
        /* Mobil Uyumluluk */
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }

            .seats-container {
                grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
            }

            .button {
                font-size: 1rem;
                width: 60px;
                height: 60px;
            }

            .back-btn {
                font-size: 1rem;
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>
    <h1>Koltuk Seçimi</h1>
    <form method="post">
        <div class="seats-container">
            <?php for ($i = 1; $i <= 20; $i++): ?>
                <?php if (in_array($i, $dolu_koltuklar)): ?>
                    <button class="button" disabled>
                        <i class="fas fa-chair"></i>
                        <span class="button-tooltip">Dolu</span>
                        <?php echo $i; ?>
                    </button>
                <?php elseif ($user_seat && $user_seat['koltuk_no'] == $i): ?>
                    <!-- Kullanıcı bu koltuğu seçti, iptal et seçeneği -->
                    <button class="button" name="koltuk_no" value="<?php echo $i; ?>" id="koltuk-<?php echo $i; ?>" class="cancel">
                        <i class="fas fa-chair"></i>
                        <span class="button-tooltip">İptal Et</span>
                        <?php echo $i; ?>
                    </button>
                <?php else: ?>
                    <button class="button" name="koltuk_no" value="<?php echo $i; ?>">
                        <i class="fas fa-chair"></i>
                        <?php echo $i; ?>
                    </button>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        <a href="etkinlikler.php" class="back-btn">Geri Dön</a>
    </form>

    <script>
        // Koltuk iptal işlemi için uyarı ekleyelim
        document.querySelectorAll('.cancel').forEach(button => {
            button.addEventListener('click', function(e) {
                const seatNumber = this.value;
                const confirmation = confirm(`Koltuk ${seatNumber} iptal edilsin mi?`);
                if (!confirmation) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>

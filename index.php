<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

define('DB_SERVER', 'localhost'); 
define('DB_USERNAME', 'dbusr22360859050'); 
define('DB_PASSWORD', 'gcXNPJ15MeAK');       
define('DB_NAME', 'dbstorage22360859050');   

try {
    $db = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USERNAME, DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Bağlantı hatası: " . $e->getMessage());
}

$error = "";
$success = "";
$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. KULLANICI KAYIT İŞLEMİ
    if(isset($_POST['register_user'])) {
        $ad_soyad = trim($_POST["ad_soyad"]);
        $eposta = trim($_POST["eposta"]);
        $sifre = trim($_POST["sifre"]);
        if(!empty($ad_soyad) && !empty($eposta) && !empty($sifre)){
            $stmt = $db->prepare("SELECT id FROM kullanicilar WHERE eposta = :eposta");
            $stmt->bindParam(":eposta", $eposta, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount() == 1){
                $error = "Bu e-posta zaten kayıtlı.";
            } else {
                $hashed_password = password_hash($sifre, PASSWORD_DEFAULT);
                $insert = $db->prepare("INSERT INTO kullanicilar (ad_soyad, eposta, sifre) VALUES (:ad_soyad, :eposta, :sifre)");
                $insert->execute([
                    ':ad_soyad' => $ad_soyad, 
                    ':eposta' => $eposta, 
                    ':sifre' => $hashed_password
                ]);
                $success = "Kayıt başarılı. Giriş yapabilirsiniz.";
                $action = 'login';
            }
        }
    }
    
    // 2. KULLANICI GİRİŞ İŞLEMİ
    if(isset($_POST['login_user'])) {
        $eposta = trim($_POST["eposta"]);
        $sifre = trim($_POST["sifre"]);
        if(!empty($eposta) && !empty($sifre)){
            $stmt = $db->prepare("SELECT id, ad_soyad, sifre FROM kullanicilar WHERE eposta = :eposta");
            $stmt->execute([':eposta' => $eposta]);
            if($row = $stmt->fetch()){
                if(password_verify($sifre, $row["sifre"])){
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $row["id"];
                    $_SESSION["username"] = $row["ad_soyad"];
                    header("location: index.php");
                    exit;
                } else { $error = "Hatalı şifre."; }
            } else { $error = "Hesap bulunamadı."; }
        }
    }

    // 3. YENİ GÖREV EKLEME (CREATE)
    if(isset($_POST['add_task']) && isset($_SESSION["loggedin"])) {
        $proje_adi = trim($_POST["proje_adi"]);
        $gorev_basligi = trim($_POST["gorev_basligi"]);
        $detaylar = trim($_POST["detaylar"]);
        $oncelik = $_POST["oncelik"];
        $durum = $_POST["durum"];
        if(!empty($proje_adi) && !empty($gorev_basligi)){
            $ins = $db->prepare("INSERT INTO gorevler (kullanici_id, proje_adi, gorev_basligi, detaylar, oncelik, durum) VALUES (:k_id, :p_adi, :g_baslik, :detay, :onc, :durum)");
            $ins->execute([
                ':k_id' => $_SESSION["id"], ':p_adi' => $proje_adi, ':g_baslik' => $gorev_basligi,
                ':detay' => $detaylar, ':onc' => $oncelik, ':durum' => $durum
            ]);
            header("location: index.php");
            exit;
        }
    }

    // 4. GÖREV DÜZENLEME (UPDATE)
    if(isset($_POST['edit_task']) && isset($_SESSION["loggedin"])) {
        $id = $_POST["task_id"];
        $proje_adi = trim($_POST["proje_adi"]);
        $gorev_basligi = trim($_POST["gorev_basligi"]);
        $detaylar = trim($_POST["detaylar"]);
        $oncelik = $_POST["oncelik"];
        $durum = $_POST["durum"];
        if(!empty($proje_adi) && !empty($gorev_basligi)){
            $up = $db->prepare("UPDATE gorevler SET proje_adi = :p_adi, gorev_basligi = :g_baslik, detaylar = :detay, oncelik = :onc, durum = :durum WHERE id = :id AND kullanici_id = :k_id");
            $up->execute([
                ':p_adi' => $proje_adi, ':g_baslik' => $gorev_basligi, ':detay' => $detaylar,
                ':onc' => $oncelik, ':durum' => $durum, ':id' => $id, ':k_id' => $_SESSION["id"]
            ]);
            header("location: index.php");
            exit;
        }
    }
}

// 5. GÖREV SİLME (DELETE)
if($action == 'delete' && isset($_GET['id']) && isset($_SESSION["loggedin"])) {
    $del = $db->prepare("DELETE FROM gorevler WHERE id = :id AND kullanici_id = :k_id");
    $del->execute([':id' => $_GET['id'], ':k_id' => $_SESSION["id"]]);
    header("location: index.php");
    exit;
}

// 6. GÜVENLİ ÇIKIŞ (LOGOUT)
if($action == 'logout') {
    $_SESSION = array();
    session_destroy();
    header("location: index.php");
    exit;
}

include 'view.php';
?>
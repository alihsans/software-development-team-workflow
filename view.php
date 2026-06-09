<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İş Akışı Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">DevWorkflow <span class="badge bg-primary fs-6">Ekip Havuzu</span></a>
        <div class="collapse navbar-collapse" id="navbarNav" style="display: flex !important; justify-content: flex-end;">
            <ul class="navbar-nav">
                <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                    <li class="nav-item"><span class="nav-link text-light">Aktif Geliştirici: <strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong></span></li>
                    <li class="nav-item"><a class="btn btn-outline-danger btn-sm ms-2 mt-1" href="index.php?action=logout">Çıkış Yap</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="index.php?action=login">Giriş Yap</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?action=register">Kayıt Ol</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <?php if(!empty($error)): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
    <?php if(!empty($success)): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

    <?php if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true): ?>
        <?php if($action == 'register'): ?>
            <div class="row justify-content-center"><div class="col-md-6"><div class="card shadow-sm"><div class="card-body p-4"><h3 class="text-center mb-4">Ekibe Katıl</h3><form action="index.php?action=register" method="POST"><div class="mb-3"><label class="form-label">Ad Soyad</label><input type="text" name="ad_soyad" class="form-control" required></div><div class="mb-3"><label class="form-label">E-posta</label><input type="email" name="eposta" class="form-control" required></div><div class="mb-3"><label class="form-label">Şifre</label><input type="password" name="sifre" class="form-control" required></div><button type="submit" name="register_user" class="btn btn-primary w-100">Kayıt Ol</button></form><div class="text-center mt-3"><a href="index.php?action=login">Giriş Yapın</a></div></div></div></div></div>
        <?php else: ?>
            <div class="row justify-content-center"><div class="col-md-6"><div class="card shadow-sm"><div class="card-body p-4"><h3 class="text-center mb-4">Giriş Yap</h3><form action="index.php?action=login" method="POST"><div class="mb-3"><label class="form-label">E-posta</label><input type="email" name="eposta" class="form-control" required></div><div class="mb-3"><label class="form-label">Şifre</label><input type="password" name="sifre" class="form-control" required></div><button type="submit" name="login_user" class="btn btn-success w-100">Giriş Yap</button></form><div class="text-center mt-3"><a href="index.php?action=register">Kayıt Olun</a></div></div></div></div></div>
        <?php endif; ?>

    <?php else: ?>
        <?php if($action == 'add'): ?>
            <div class="card shadow-sm"><div class="card-body p-4"><h3>Yeni Görev Ekle</h3><form action="index.php?action=add" method="POST"><div class="mb-3"><label class="form-label">Proje Adı</label><input type="text" name="proje_adi" class="form-control" required></div><div class="mb-3"><label class="form-label">Görev / İş Başlığı</label><input type="text" name="gorev_basligi" class="form-control" required></div><div class="mb-3"><label class="form-label">Detaylar</label><textarea name="detaylar" class="form-control" rows="4"></textarea></div><div class="row"><div class="col-md-6 mb-3"><label class="form-label">Öncelik</label><select name="oncelik" class="form-select"><option value="Düşük">Düşük</option><option value="Orta" selected>Orta</option><option value="Yüksek">Yüksek</option></select></div><div class="col-md-6 mb-3"><label class="form-label">Durum</label><select name="durum" class="form-select"><option value="Yapılacak">Yapılacak</option><option value="Geliştirme Aşamasında">Geliştirme Aşamasında</option><option value="Test Aşamasında">Test Aşamasında</option><option value="Tamamlandı">Tamamlandı</option></select></div></div><button type="submit" name="add_task" class="btn btn-primary">Kaydet</button> <a href="index.php" class="btn btn-secondary">İptal</a></form></div></div>

        <?php elseif($action == 'edit' && isset($_GET['id'])): ?>
            <?php 
            $st = $db->prepare("SELECT * FROM gorevler WHERE id = :id");
            $st->execute([':id' => $_GET['id']]);
            $g = $st->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class="card shadow-sm"><div class="card-body p-4"><h3>Görevi Düzenle</h3><form action="index.php?action=edit&id=<?php echo $g['id']; ?>" method="POST"><input type="hidden" name="task_id" value="<?php echo $g['id']; ?>"><div class="mb-3"><label class="form-label">Proje Adı</label><input type="text" name="proje_adi" class="form-control" value="<?php echo htmlspecialchars($g['proje_adi']); ?>" required></div><div class="mb-3"><label class="form-label">Görev Başlığı</label><input type="text" name="gorev_basligi" class="form-control" value="<?php echo htmlspecialchars($g['gorev_basligi']); ?>" required></div><div class="mb-3"><label class="form-label">Detaylar</label><textarea name="detaylar" class="form-control" rows="4"><?php echo htmlspecialchars($g['detaylar']); ?></textarea></div><div class="row"><div class="col-md-6 mb-3"><label class="form-label">Öncelik</label><select name="oncelik" class="form-select"><option value="Düşük" <?php if($g['oncelik']=='Düşük')echo'selected';?>>Düşük</option><option value="Orta" <?php if($g['oncelik']=='Orta')echo'selected';?>>Orta</option><option value="Yüksek" <?php if($g['oncelik']=='Yüksek')echo'selected';?>>Yüksek</option></select></div><div class="col-md-6 mb-3"><label class="form-label">Durum</label><select name="durum" class="form-select"><option value="Yapılacak" <?php if($g['durum']=='Yapılacak')echo'selected';?>>Yapılacak</option><option value="Geliştirme Aşamasında" <?php if($g['durum']=='Geliştirme Aşamasında')echo'selected';?>>Geliştirme Aşamasında</option><option value="Test Aşamasında" <?php if($g['durum']=='Test Aşamasında')echo'selected';?>>Test Aşamasında</option><option value="Tamamlandı" <?php if($g['durum']=='Tamamlandı')echo'selected';?>>Tamamlandı</option></select></div></div><button type="submit" name="edit_task" class="btn btn-warning">Değişiklikleri Kaydet</button> <a href="index.php" class="btn btn-secondary">Geri Dön</a></form></div></div>

        <?php else: ?>
            <div class="d-flex justify-content-between align-items-center mb-4"><h2>Tüm Ekip Görevleri</h2><a href="index.php?action=add" class="btn btn-primary">+ Yeni Görev Ekle</a></div>
            <?php
            // İLİŞKİSEL SORGULAMA: Görevler ile Kullanıcılar tablosunu birleştirip ekleyen kişiyi çekiyoruz
            $stmt = $db->prepare("SELECT g.*, k.ad_soyad FROM gorevler g INNER JOIN kullanicilar k ON g.kullanici_id = k.id ORDER BY g.id DESC");
            $stmt->execute();
            $gorevler = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <?php if(count($gorevler) > 0): ?>
                <div class="table-responsive bg-white p-3 rounded shadow-sm border"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Proje Adı</th><th>Görev Başlığı</th><th>Detaylar</th><th>Öncelik</th><th>Durum</th><th>Ekleyen Geliştirici</th><th class="text-center">İşlemler</th></tr></thead><tbody>
                    <?php foreach($gorevler as $g): ?>
                        <tr><td><strong><?php echo htmlspecialchars($g["proje_adi"]); ?></strong></td><td><?php echo htmlspecialchars($g["gorev_basligi"]); ?></td><td><?php echo htmlspecialchars($g["detaylar"]); ?></td><td><span class="badge <?php echo $g['oncelik']=='Yüksek'?'bg-danger':($g['oncelik']=='Orta'?'bg-warning text-dark':'bg-secondary'); ?>"><?php echo $g["oncelik"]; ?></span></td><td><span class="badge bg-info text-dark" style="<?php if($g['durum']=='Tamamlandı')echo 'background-color:#198754 !important;color:#fff !important;'; if($g['durum']=='Test Aşamasında')echo 'background-color:#6f42c1 !important;color:#fff !important;'; ?>"><?php echo $g["durum"]; ?></span></td><td><span class="text-primary font-monospace">@<?php echo htmlspecialchars($g["ad_soyad"]); ?></span></td><td class="text-center"><a href="index.php?action=edit&id=<?php echo $g["id"]; ?>" class="btn btn-sm btn-warning">Düzenle</a> <a href="index.php?action=delete&id=<?php echo $g["id"]; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Silinsin mi?');">Sil</a></td></tr>
                    <?php endforeach; ?>
                </tbody></table></div>
            <?php else: ?>
                <div class="alert alert-info text-center">Henüz eklenmiş bir görev bulunmuyor.</div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

<footer class="footer mt-5 py-3 bg-white text-center border-top"><div class="container"><span class="text-muted">© 2026 İş Akışı Paneli</span></div></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

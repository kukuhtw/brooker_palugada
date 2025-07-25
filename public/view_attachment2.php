<?php
// public/view_attachment.php
/*
 * 🤖 Aplikasi Brooker AI / Calo AI Palugada
 * (Apa elo mau, gw ada)
 * Dibuat oleh: Kukuh TW
 *
 * 📧 Email     : kukuhtw@gmail.com
 * 📱 WhatsApp  : 628129893706
 * 📷 Instagram : @kukuhtw
 * 🐦 X/Twitter : @kukuhtw
 * 👍 Facebook  : https://www.facebook.com/kukuhtw
 * 💼 LinkedIn  : https://id.linkedin.com/in/kukuhtw
*/
require __DIR__ . '/../bootstrap.php';
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// Ambil base domain dari settings
$hostDomain = '';
$sqlHost = "SELECT value FROM settings WHERE `key` = 'HOST_DOMAIN' LIMIT 1";
$stmtHost = $db->getConnection()->prepare($sqlHost);
$stmtHost->execute();
$setting = $stmtHost->fetch(PDO::FETCH_ASSOC);
if ($setting) {
    $hostDomain = rtrim($setting['value'], '/');
}

// Ambil data_id, ownerFilter, dan page untuk navigasi kembali
$dataId      = (int)($_GET['data_id'] ?? 0);
$ownerFilter = trim($_GET['ownerFilter'] ?? '');
$page        = max(1, (int)($_GET['page'] ?? 1));

if (!$dataId) {
    header('Location: view_data.php');
    exit;
}

// Ambil data inventory (owner, rawdata, phone, email)
$sqlInv = "SELECT owner, rawdata, phone, email FROM data_inventory WHERE id = :id";
$stmtInv = $db->getConnection()->prepare($sqlInv);
$stmtInv->execute([':id' => $dataId]);
$inv = $stmtInv->fetch(PDO::FETCH_ASSOC);

// Ambil attachments
$sqlAtt = "SELECT id, file_name, url_attachment, regdate
           FROM data_attachment
           WHERE data_id = :id
           ORDER BY regdate DESC";
$stmtAtt = $db->getConnection()->prepare($sqlAtt);
$stmtAtt->execute([':id' => $dataId]);
$attachments = $stmtAtt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Attachment Data #<?= $dataId ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex">
  <?php include 'nav_admin.php'; ?>
  <div id="content" class="flex-grow-1 p-4">
    <h1 class="mb-3">Lampiran untuk: <?= htmlspecialchars($inv['owner']) ?></h1>
    <ul class="list-unstyled mb-4">
      <li><strong>Nama Pemilik:</strong> <?= htmlspecialchars($inv['owner']) ?></li>
      <li><strong>Email:</strong> <?= htmlspecialchars($inv['email'] ?: '-') ?></li>
      <li><strong>Phone:</strong> <?= htmlspecialchars($inv['phone']) ?></li>
    </ul>
    <p><strong>Rawdata:</strong><br><?= nl2br(htmlspecialchars($inv['rawdata'])) ?></p>

    <?php if ($attachments): ?>
      <div class="row g-3">
        <?php foreach ($attachments as $att): ?>
          <?php
            $ext = strtolower(pathinfo($att['file_name'], PATHINFO_EXTENSION));
            $fullUrl = htmlspecialchars($hostDomain . '/' . ltrim($att['url_attachment'], '/'));
          ?>
          <div class="col-6 col-sm-4 col-md-3">
            <div class="card">
              <?php if (in_array($ext, ['pdf','doc','docx','txt'])): ?>
                <div class="card-body text-center p-3">
                  <p class="mb-2 small text-truncate"><?= htmlspecialchars($att['file_name']) ?></p>
              <?php elseif (in_array($ext, ['mp4','mov'])): ?>
                <video controls class="w-100">
                  <source src="<?= $fullUrl ?>" type="video/<?= $ext ?>">
                  Browser Anda tidak mendukung tag video.
                </video>
                <div class="card-body p-2 text-center">
                  <p class="mb-1 small text-truncate"><?= htmlspecialchars($att['file_name']) ?></p>
              <?php else: ?>
                <a href="<?= $fullUrl ?>" target="_blank">
                  <img src="<?= $fullUrl ?>" class="card-img-top img-fluid img-thumbnail" alt="<?= htmlspecialchars($att['file_name']) ?>">
                </a>
                <div class="card-body p-2 text-center">
                  <p class="mb-1 small text-truncate"><?= htmlspecialchars($att['file_name']) ?></p>
              <?php endif; ?>
                  <div class="btn-group mt-2 w-100" role="group">
                    <a href="<?= $fullUrl ?>" download class="btn btn-sm btn-light flex-grow-1">Download</a>
                    <form method="post" action="delete_attachment.php" onsubmit="return confirm('Yakin ingin menghapus lampiran ini?');" class="m-0 p-0">
                      <input type="hidden" name="id" value="<?= $att['id'] ?>">
                      <input type="hidden" name="data_id" value="<?= $dataId ?>">
                      <input type="hidden" name="ownerFilter" value="<?= htmlspecialchars($ownerFilter) ?>">
                      <input type="hidden" name="page" value="<?= $page ?>">
                      <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                  </div>
                </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="alert alert-info">Belum ada lampiran untuk data ini.</div>
    <?php endif; ?>

    <a href="view_data.php?owner=<?= urlencode($ownerFilter) ?>&page=<?= $page ?>" class="btn btn-secondary mt-4">
      Kembali ke Daftar Data
    </a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

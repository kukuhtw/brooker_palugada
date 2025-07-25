<?php
// public/add_data.php
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

$di = new DataInventory($db);
$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi
    $owner   = trim($_POST['owner']   ?? '');
    $phone   = trim($_POST['phone']   ?? '');
    $email   = trim($_POST['email']   ?? '');
    $rawdata = trim($_POST['rawdata'] ?? '');

    if ($owner === '') {
        $errors[] = 'Owner Name harus diisi.';
    }
    if ($phone === '') {
        $errors[] = 'Phone Owner harus diisi.';
    }
    if ($rawdata === '') {
        $errors[] = 'Rawdata harus diisi.';
    }


     if (empty($errors)) {
        $di = new DataInventory($db);
        if ($di->add($owner, $phone, $email ?: null, $rawdata)) {
            // kalau sukses, langsung redirect ke view_data.php
            header('Location: view_data.php');
            exit;
        } else {
            $errors[] = 'Gagal menyimpan data. Silakan coba lagi.';
        }
    }

    
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tambah Data Inventory</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="d-flex">
  <?php include 'nav_admin.php'; ?>

  <div id="content" class="flex-grow-1 p-4">
    <h1 class="mb-4">Tambah Data Inventory</h1>

    <?php if ($success): ?>
      <div class="alert alert-success">Data berhasil disimpan!</div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="">
      <div class="mb-3">
        <label for="owner" class="form-label">Owner Name <span class="text-danger">*</span></label>
        <input type="text" id="owner" name="owner" class="form-control" value="<?= htmlspecialchars($owner ?? '') ?>" required>
      </div>

      <div class="mb-3">
        <label for="phone" class="form-label">Phone Owner <span class="text-danger">*</span></label>
        <input type="text" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($phone ?? '') ?>" required>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email Owner</label>
        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email ?? '') ?>">
      </div>

      <div class="mb-3">
        <label for="rawdata" class="form-label">Rawdata <span class="text-danger">*</span></label>
        <textarea id="rawdata" name="rawdata" class="form-control" rows="6" required><?= htmlspecialchars($rawdata ?? '') ?></textarea>
      </div>

      <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// public/view_transaksi_query.php
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

// Tangani notifikasi
$info = $_SESSION['info'] ?? null;
unset($_SESSION['info']);

// Ambil filter & paging
$buyerFilter = trim($_GET['buyer'] ?? '');
$page        = max(1, (int)($_GET['page'] ?? 1));
$perPage     = 10;
$offset      = ($page - 1) * $perPage;

Logger::debug("view_transaksi_query.php - buyerFilter: '{$buyerFilter}', page: {$page}");

// Hitung total
$countSql = "SELECT COUNT(*) FROM transaksi_query" . ($buyerFilter ? " WHERE buyer_name LIKE :buyer" : "");
$countStmt = $db->getConnection()->prepare($countSql);
if ($buyerFilter) {
    $countStmt->execute([':buyer' => "%{$buyerFilter}%"]);
} else {
    $countStmt->execute();
}
$total      = (int)$countStmt->fetchColumn();
$totalPages = (int)ceil($total / $perPage);

// Ambil data
$sql = "SELECT id, buyer_name, buyer_email, buyer_wa, buyer_query, results, followup, trdate
        FROM transaksi_query"
     . ($buyerFilter ? " WHERE buyer_name LIKE :buyer" : "")
     . " ORDER BY trdate DESC
        LIMIT :limit OFFSET :offset";
$stmt = $db->getConnection()->prepare($sql);
if ($buyerFilter) {
    $stmt->bindValue(':buyer', "%{$buyerFilter}%", PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
Logger::debug('Fetched ' . count($rows) . ' rows from transaksi_query.');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Daftar Transaksi Query</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="d-flex">
  <?php include 'nav_admin.php'; ?>

  <div id="content" class="flex-grow-1 p-4">
    <h1 class="mb-4">Daftar Transaksi Query</h1>

    <?php if ($info): ?>
      <div class="alert alert-<?= $info['status'] ?>"><?= htmlspecialchars($info['message']) ?></div>
    <?php endif; ?>

    <form method="get" class="row g-2 mb-4">
      <div class="col-auto">
        <input type="text" name="buyer" class="form-control"
               placeholder="Cari Buyer Name" value="<?= htmlspecialchars($buyerFilter) ?>">
      </div>
      <div class="col-auto">
        <button class="btn btn-primary">Filter</button>
      </div>
      <?php if ($buyerFilter): ?>
      <div class="col-auto">
        <a href="view_transaksi_query.php" class="btn btn-secondary">Reset</a>
      </div>
      <?php endif; ?>
    </form>

    <div class="table-responsive">
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Buyer Name</th>
            <th>Email</th>
            <th>WA</th>
            <th>Query</th>
            <th>Results</th>
            <th>Followup</th>
            <th>Tanggal</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$rows): ?>
            <tr><td colspan="9" class="text-center">Tidak ada data.</td></tr>
          <?php else: foreach ($rows as $row): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['buyer_name']) ?></td>
              <td><?= htmlspecialchars($row['buyer_email']) ?></td>
              <td><?= htmlspecialchars($row['buyer_wa']) ?></td>
              <td>
                <?= nl2br(htmlspecialchars(substr($row['buyer_query'], 0, 100))) ?>
                <?= strlen($row['buyer_query']) > 100 ? '…' : '' ?>
              </td>
              <td>
                <button
                  class="btn btn-info btn-sm"
                  data-bs-toggle="modal"
                  data-bs-target="#resultsModal<?= $row['id'] ?>">
                  View
                </button>
              </td>
              <td>
                
  <?php
    // Split teks followup menjadi array kata
    $words = preg_split('/\s+/', trim($row['followup']));
    // Ambil 10 kata pertama
    $preview = implode(' ', array_slice($words, 0, 10));
    // Tampilkan preview, tambahkan ellipsis jika lebih dari 10 kata
    echo htmlspecialchars($preview) . (count($words) > 10 ? '…' : '');
  ?>
  

                <button
                  class="btn btn-warning btn-sm"
                  data-bs-toggle="modal"
                  data-bs-target="#followupModal<?= $row['id'] ?>">
                  View/Edit
                </button>
              </td>
              <td><?= $row['trdate'] ?></td>
              <td>
                <a
                  href="delete_transaksi_query.php?id=<?= $row['id'] ?>"
                  class="btn btn-danger btn-sm"
                  onclick="return confirm('Yakin ingin menghapus data ini?');">
                  Delete
                </a>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>


    <!-- Modals -->
    <?php foreach ($rows as $row): ?>
      <!-- Results Modal -->
      <div class="modal fade" id="resultsModal<?= $row['id'] ?>" tabindex="-1"
           aria-labelledby="resultsModalLabel<?= $row['id'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="resultsModalLabel<?= $row['id'] ?>">
                Results for ID <?= $row['id'] ?>
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"
                      aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <pre><?= htmlspecialchars($row['results']) ?></pre>
            </div>
          </div>
        </div>
      </div>


     <!-- Followup Modal -->
<div class="modal fade" id="followupModal<?= $row['id'] ?>" tabindex="-1"
     aria-labelledby="followupModalLabel<?= $row['id'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="update_followup.php" method="post">
        <div class="modal-header">
          <h5 class="modal-title" id="followupModalLabel<?= $row['id'] ?>">
            Follow‑up untuk ID <?= $row['id'] ?>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"
                  aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" value="<?= $row['id'] ?>">

          <!-- Informasi Buyer -->
          <dl class="row mb-3">
            <dt class="col-sm-3">Buyer Name</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($row['buyer_name']) ?></dd>

            <dt class="col-sm-3">Email</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($row['buyer_email']) ?></dd>

            <dt class="col-sm-3">WhatsApp</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($row['buyer_wa']) ?></dd>
          </dl>

          <!-- Query Asli -->
          <h6>Buyer Query</h6>
          <pre class="bg-light p-2"><?= htmlspecialchars($row['buyer_query']) ?></pre>

          <!-- Results -->
          <h6>Results</h6>
          <pre class="bg-light p-2"><?= htmlspecialchars($row['results']) ?></pre>

          <hr>

          <!-- Textarea Followup -->
          <div class="mb-3">
            <label for="followupTextarea<?= $row['id'] ?>" class="form-label">
              Followup
            </label>
            <textarea
              class="form-control"
              id="followupTextarea<?= $row['id'] ?>"
              name="followup"
              rows="5"
            ><?= htmlspecialchars($row['followup']) ?></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary"
                  data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endforeach; ?>


<?php if ($totalPages > 1): ?>
<nav>
  <ul class="pagination">
<?php if ($totalPages > 1): ?>
<nav>
  <ul class="pagination">

<?php
  $pagesPerBlock = 10;
  $blockIndex = (int) floor(($page - 1) / $pagesPerBlock);
  $startPage  = $blockIndex * $pagesPerBlock + 1;
  $endPage    = min($startPage + $pagesPerBlock - 1, $totalPages);
?>

    <!-- Tombol « -->
    <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
      <a class="page-link"
         href="?<?= http_build_query(['buyer' => $buyerFilter, 'page' => max(1, $page-1)]) ?>">&laquo;</a>
    </li>

    <!-- Link ke blok sebelumnya -->
    <?php if ($startPage > 1): ?>
      <li class="page-item">
        <a class="page-link"
           href="?<?= http_build_query(['buyer' => $buyerFilter, 'page' => $startPage-1]) ?>">…</a>
      </li>
    <?php endif; ?>

    <!-- Nomor halaman di blok aktif -->
    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
      <li class="page-item<?= $i === $page ? ' active' : '' ?>">
        <a class="page-link"
           href="?<?= http_build_query(['buyer' => $buyerFilter, 'page' => $i]) ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>

    <!-- Link ke blok berikutnya -->
    <?php if ($endPage < $totalPages): ?>
      <li class="page-item">
        <a class="page-link"
           href="?<?= http_build_query(['buyer' => $buyerFilter, 'page' => $endPage+1]) ?>">…</a>
      </li>
    <?php endif; ?>

    <!-- Tombol » -->
    <li class="page-item<?= $page >= $totalPages ? ' disabled' : '' ?>">
      <a class="page-link"
         href="?<?= http_build_query(['buyer' => $buyerFilter, 'page' => min($totalPages,$page+1)]) ?>">&raquo;</a>
    </li>
  </ul>
</nav>
<?php endif; ?>


    <!-- Tombol « -->
    <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
      <a class="page-link"
         href="?<?= http_build_query(['buyer' => $buyerFilter, 'page' => max(1, $page-1)]) ?>">&laquo;</a>
    </li>

    <!-- Link ke blok sebelumnya -->
    <?php if ($startPage > 1): ?>
      <li class="page-item">
        <a class="page-link"
           href="?<?= http_build_query(['buyer' => $buyerFilter, 'page' => $startPage-1]) ?>">…</a>
      </li>
    <?php endif; ?>

   

  </ul>
</nav>
<?php endif; ?>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

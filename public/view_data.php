<?php
// public/view_data.php
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

// Notifikasi
$info = $_SESSION['info'] ?? null;
unset($_SESSION['info']);

// 1. Ambil filter & paging
// Ambil filter & paging
$search  = trim($_GET['q'] ?? '');
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset  = ($page - 1) * $perPage;

// Siapkan WHERE + params
$where = '';
$params = [];
if ($search !== '') {
    $where = " WHERE (
        id = :exact_id
        OR owner       LIKE :kw_owner
        OR rawdata     LIKE :kw_rawdata
        OR description LIKE :kw_desc
    )";
    $params[':exact_id']   = ctype_digit($search) ? (int)$search : -1;
    $params[':kw_owner']   = "%{$search}%";
    $params[':kw_rawdata'] = "%{$search}%";
    $params[':kw_desc']    = "%{$search}%";
}

// Hitung total
$countSql  = "SELECT COUNT(*) FROM data_inventory" . $where;
$countStmt = $db->getConnection()->prepare($countSql);
foreach ($params as $k => $v) {
    $countStmt->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$countStmt->execute();
$total = (int)$countStmt->fetchColumn();
$totalPages = (int)ceil($total / $perPage);

// 4. Status Pinecone (opsional)
$isp1 = (int)$db->getConnection()
                ->query("SELECT COUNT(*) FROM data_inventory WHERE ispinecone = 1")
                ->fetchColumn();
$isp0 = (int)$db->getConnection()
                ->query("SELECT COUNT(*) FROM data_inventory WHERE ispinecone = 0")
                ->fetchColumn();

// 5. Ambil data dengan paging
$sql = "SELECT id, owner, phone, email, rawdata, description,
               metadata_pinecone, ispinecone, regdate
        FROM data_inventory"
        . $where .
        " ORDER BY lastupdatedate DESC
          LIMIT :limit OFFSET :offset";
$stmt = $db->getConnection()->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Daftar Data Inventory</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="d-flex">
  <?php include 'nav_admin.php'; ?>

  <div id="content" class="flex-grow-1 p-4">
    <h1 class="mb-3">Daftar Data Inventory</h1>

    <div class="mb-3">
      <span class="badge bg-primary">Total Data: <?= $total ?></span>
      <span class="badge bg-success">Processed (ispinecone=1): <?= $isp1 ?></span>
      <span class="badge bg-warning text-dark">Pending (ispinecone=0): <?= $isp0 ?></span>
    </div>

    <?php if($info): ?>
      <div class="alert alert-<?= $info['status'] ?>"><?= htmlspecialchars($info['message']) ?></div>
    <?php endif; ?>

    <!-- Filter -->
    <form method="get" class="mb-3">
  <div class="input-group">
    <input type="text" name="q" class="form-control" placeholder="Cari ID, Owner, Rawdata, Description"
           value="<?= htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES) ?>">
    <button class="btn btn-primary" type="submit">Cari</button>
  </div>
</form>


    <!-- Tabel Data -->
    <div class="table-responsive">
      <table class="table table-striped table-bordered">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Owner</th>
            <th>Rawdata</th>
            <th>Description</th>
            <th>Metadata Pinecone</th>
            <th>Pinecone</th>
            <th>Reg Date</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$rows): ?>
            <tr><td colspan="8" class="text-center">Tidak ada data.</td></tr>
          <?php else: foreach($rows as $row): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td>
                <?= htmlspecialchars($row['owner']) ?><br>
                <?= htmlspecialchars($row['phone']) ?><br>
                <?= htmlspecialchars($row['email']) ?>
              </td>
              <td><?= nl2br(htmlspecialchars(substr($row['rawdata'],0,100))) ?><?= strlen($row['rawdata'])>100?'…':'' ?></td>
              <td>
                <?php if (trim($row['description']) === ''): ?>
                  <a href="proses_info_desc.php?data_id=<?= $row['id'] ?>&ownerFilter=&page=<?= $page ?>"
                     class="btn btn-sm btn-warning">Proses Deskripsi</a>
                <?php else: ?>
                  <?= nl2br(htmlspecialchars(substr($row['description'], 0, 100))) ?><?= strlen($row['description'])>100 ? '…' : '' ?>
                  <?php if (strlen($row['description'])>100): ?>
                    <br><button type="button" class="btn btn-sm btn-info mt-1"
                            data-bs-toggle="modal" data-bs-target="#descModal"
                            data-desc-full="<?= htmlspecialchars($row['description'], ENT_QUOTES) ?>">
                      View
                    </button>
                  <?php endif; ?>
                <?php endif; ?>
                <?php if (trim($row['description']) !== '' && trim($row['metadata_pinecone'] ?? '') === ''): ?>
                  <a href="proses_metadata.php?data_id=<?= $row['id'] ?>&ownerFilter=page=<?= $page ?>"
                     class="btn btn-sm btn-warning mt-2">Proses Metadata Pinecone</a>
                <?php endif; ?>
              </td>
              <td>
                <?= nl2br(htmlspecialchars(substr($row['metadata_pinecone'],0,100))) ?><?= strlen($row['metadata_pinecone'])>100?'…':'' ?>
                <?php if(strlen($row['metadata_pinecone'])>100): ?>
                  <br><button type="button" class="btn btn-sm btn-info mt-1"
                          data-bs-toggle="modal" data-bs-target="#metaModal"
                          data-meta-full="<?= htmlspecialchars($row['metadata_pinecone'], ENT_QUOTES) ?>">
                    View
                  </button>
                <?php endif; ?>
              </td>
              <td>
                <?= $row['ispinecone'] ?>
                <?php
                  $meta = trim($row['metadata_pinecone'] ?? '');
                  $isValidJson = false;
                  if ($meta !== '') {
                      json_decode($meta);
                      $isValidJson = (json_last_error() === JSON_ERROR_NONE);
                  }
                  if (trim($row['ispinecone']) === '0' && $isValidJson): ?>
                    <a href="proses_store_pinecone.php?data_id=<?= $row['id'] ?>"
                       class="btn btn-sm btn-warning mt-2">Store to Pinecone</a>
                <?php endif; ?>
              </td>
              <td><?= $row['regdate'] ?></td>
              <td>
                <!-- Edit -->
                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal"
                        data-id="<?= $row['id'] ?>" data-owner="<?= htmlspecialchars($row['owner'], ENT_QUOTES) ?>"
                        data-phone="<?= htmlspecialchars($row['phone'], ENT_QUOTES) ?>" data-email="<?= htmlspecialchars($row['email'], ENT_QUOTES) ?>"
                        data-raw="<?= htmlspecialchars($row['rawdata'], ENT_QUOTES) ?>" data-desc="<?= htmlspecialchars($row['description'], ENT_QUOTES) ?>">
                  Edit
                </button>
              

 <!-- Tombol Add Attachment -->
  <a href="add_attachment.php?data_id=<?= $row['id'] ?>
           &owner=
           &page=<?= $page ?>"
     class="btn btn-sm btn-success">
    Add Attachment
  </a>




                <a href="view_attachment.php?data_id=<?= $row['id'] ?>&owner=page=<?= $page ?>" class="btn btn-sm btn-secondary">View Attachments</a>
                <!-- Delete -->
                <form method="post" action="delete_data_inventory.php" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                  <input type="hidden" name="id" value="<?= $row['id'] ?>">
                  <input type="hidden" name="ownerFilter" value="">
                  <input type="hidden" name="page" value="<?= $page ?>">
                  <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                </form>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if($totalPages > 1): ?>
    <nav><ul class="pagination">
      <li class="page-item<?= $page<=1?' disabled':'' ?>">
        <a class="page-link" href="?<?= http_build_query(['owner'=>'','page'=>$page-1]) ?>">&laquo;</a>
      </li>
      <?php for($i=1; $i<=$totalPages; $i++): ?>
      <li class="page-item<?= $i===$page?' active':'' ?>">
        <a class="page-link" href="?<?= http_build_query(['owner'=>'','page'=>$i]) ?>"><?= $i ?></a>
      </li>
      <?php endfor; ?>
      <li class="page-item<?= $page>=$totalPages?' disabled':'' ?>">
        <a class="page-link" href="?<?= http_build_query(['owner'=>'','page'=>$page+1]) ?>">&raquo;</a>
      </li>
    </ul></nav>
    <?php endif; ?>

  </div>
 
  <!-- Modal Edit Data -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form class="modal-content" method="post" action="edit_rawdata.php">
        <div class="modal-header">
          <h5 class="modal-title">Edit Data Inventory</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="edit-id">
          
          <input type="hidden" name="page" value="<?= $page ?>">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label" for="edit-owner">Owner</label>
              <input type="text" name="owner" id="edit-owner" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label" for="edit-phone">Phone</label>
              <input type="text" name="phone" id="edit-phone" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label" for="edit-email">Email</label>
              <input type="email" name="email" id="edit-email" class="form-control">
            </div>
          </div>
          <div class="mt-3">
            <label for="edit-rawdata" class="form-label">Rawdata</label>
            <textarea name="rawdata" id="edit-rawdata" class="form-control" rows="8"></textarea>
          </div>
          <div class="mt-3">
            <label for="edit-description" class="form-label">Description</label>
            <textarea name="description" id="edit-description" class="form-control" rows="4"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Description View -->
  <div class="modal fade" id="descModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Full Description</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <pre id="descContent" style="white-space: pre-wrap; word-wrap: break-word;"></pre>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Metadata Pinecone View -->
  <div class="modal fade" id="metaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Full Metadata Pinecone</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <pre id="metaContent" style="white-space: pre-wrap; word-wrap: break-word;"></pre>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

 
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   <script>
    // Populate Edit Modal
    var editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', function(e) {
      var btn = e.relatedTarget;
      editModal.querySelector('#edit-id').value      = btn.getAttribute('data-id');
      editModal.querySelector('#edit-owner').value   = btn.getAttribute('data-owner');
      editModal.querySelector('#edit-phone').value   = btn.getAttribute('data-phone');
      editModal.querySelector('#edit-email').value   = btn.getAttribute('data-email');
      editModal.querySelector('#edit-rawdata').value = btn.getAttribute('data-raw');
      editModal.querySelector('#edit-description').value = btn.getAttribute('data-desc');
    });

    // Populate Description View Modal
    var descModal = document.getElementById('descModal');
    descModal.addEventListener('show.bs.modal', function(e) {
      var btn = e.relatedTarget;
      var content = btn.getAttribute('data-desc-full');
      descModal.querySelector('#descContent').textContent = content;
    });

    // Populate Metadata View Modal
    var metaModal = document.getElementById('metaModal');
    metaModal.addEventListener('show.bs.modal', function(e) {
      var btn = e.relatedTarget;
      var content = btn.getAttribute('data-meta-full');
      metaModal.querySelector('#metaContent').textContent = content;
    });
  </script>
</body>
</html>

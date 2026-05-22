<?php
$active = 'admin_users';
require_once __DIR__ . '/../../components/admin_header.php';

$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

$where = [];
$params = [];

if ($q !== '') {
  $where[] = '(nama LIKE :q1 OR email LIKE :q2)';
  $params['q1'] = '%' . $q . '%';
  $params['q2'] = '%' . $q . '%';
}

$whereSql = $where ? (' WHERE ' . implode(' AND ', $where)) : '';

$countStmt = $pdo->prepare('SELECT COUNT(*) FROM users' . $whereSql);
foreach ($params as $key => $value) {
    $countStmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
}
$countStmt->execute();
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalRows / $perPage));

$listSql = 'SELECT id_user, nama, email, role, status_akun, created_at FROM users' . $whereSql . ' ORDER BY created_at DESC LIMIT :limit OFFSET :offset';
$listStmt = $pdo->prepare($listSql);
foreach ($params as $key => $value) {
    $listStmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
}
$listStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$listStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$listStmt->execute();
$users = $listStmt->fetchAll();

$flash = flash_get('message');
?>

<div class="card-glass">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
    <div>
      <h5 class="fw-semibold mb-1">Kelola User</h5>
      <p class="text-muted mb-0">Kelola akun admin, petugas, dan user.</p>
    </div>
    <a class="btn btn-primary" href="<?php echo base_url('admin/users/create.php'); ?>">
      <i class="bi bi-plus-lg"></i> Tambah Akun
    </a>
  </div>

  <?php if ($flash): ?>
    <div class="alert alert-warning border-0 mb-3" role="alert"><?php echo e($flash); ?></div>
  <?php endif; ?>

  <form class="row g-2 align-items-center mb-3" method="get">
    <div class="col-md-4 ms-auto">
      <input type="search" class="form-control form-control-sm" name="q" placeholder="Cari nama atau email" value="<?php echo e($q); ?>">
    </div>
  </form>

  <div class="table-modern">
    <table class="table table-borderless align-middle mb-0">
      <thead>
        <tr>
          <th>Nama</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Tanggal Dibuat</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$users): ?>
          <tr><td colspan="6" class="text-muted">Belum ada user.</td></tr>
        <?php endif; ?>
        <?php foreach ($users as $user): ?>
          <?php
            $roleClass = $user['role'] === 'admin' ? 'primary' : ($user['role'] === 'petugas' ? 'info' : 'secondary');
            $statusClass = $user['status_akun'] === 'aktif' ? 'success' : 'danger';
            $nextStatus = $user['status_akun'] === 'aktif' ? 'nonaktif' : 'aktif';
          ?>
          <tr>
            <td><?php echo e($user['nama']); ?></td>
            <td><?php echo e($user['email']); ?></td>
            <td><span class="badge bg-<?php echo $roleClass; ?>"><?php echo e($user['role']); ?></span></td>
            <td><span class="badge bg-<?php echo $statusClass; ?>"><?php echo e($user['status_akun']); ?></span></td>
            <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
            <td>
              <div class="d-flex gap-2">
                <a class="btn btn-sm btn-outline-primary" href="<?php echo base_url('admin/users/edit.php?id=' . $user['id_user']); ?>">Edit</a>
                <button
                  type="button"
                  class="btn btn-sm btn-outline-warning"
                  data-bs-toggle="modal"
                  data-bs-target="#statusModal"
                  data-user-id="<?php echo (int)$user['id_user']; ?>"
                  data-user-name="<?php echo e($user['nama']); ?>"
                  data-user-status="<?php echo $nextStatus; ?>"
                ><?php echo $user['status_akun'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan'; ?></button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="d-flex justify-content-between align-items-center mt-3">
    <small class="text-muted">Total: <?php echo $totalRows; ?> user</small>
    <nav>
      <ul class="pagination mb-0">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
            <a class="page-link" href="<?php echo base_url('admin/users/index.php?q=' . urlencode($q) . '&page=' . $i); ?>"><?php echo $i; ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  </div>
</div>

<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post" action="<?php echo base_url('admin/users/nonaktif.php'); ?>">
        <div class="modal-header">
          <h5 class="modal-title">Konfirmasi Status Akun</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="statusUserId">
          <input type="hidden" name="status" id="statusValue">
          <p class="mb-0">Ubah status akun <strong id="statusUserName"></strong>?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning">Ya, lanjutkan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  var statusModal = document.getElementById('statusModal');
  if (statusModal) {
    statusModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      document.getElementById('statusUserId').value = button.getAttribute('data-user-id');
      document.getElementById('statusValue').value = button.getAttribute('data-user-status');
      document.getElementById('statusUserName').textContent = button.getAttribute('data-user-name');
    });
  }
</script>

<?php require_once __DIR__ . '/../../components/admin_footer.php'; ?>

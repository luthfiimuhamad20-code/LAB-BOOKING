<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_login();

$user = current_user();
$isAdmin = $user['role'] === 'admin';

$pageTitle = $isAdmin ? 'Manajemen Lab' : 'Daftar Lab';
$active = 'labs';

$search = get('q');
$statusFilter = get('status');
if (!in_array($statusFilter, ['active', 'inactive'], true)) {
    $statusFilter = '';
}

$sql = "SELECT l.*,
               (SELECT COUNT(*) FROM bookings b WHERE b.lab_id = l.id) AS booking_count,
               (SELECT COUNT(*) FROM bookings b WHERE b.lab_id = l.id
                    AND b.booking_date >= CURDATE()
                    AND b.status IN ('pending', 'approved')) AS upcoming_count
        FROM labs l
        WHERE 1 = 1";
$parameters = [];

if ($search !== '') {
    $sql .= ' AND (l.name LIKE ? OR l.code LIKE ? OR l.location LIKE ?)';
    $like = '%' . $search . '%';
    $parameters[] = $like;
    $parameters[] = $like;
    $parameters[] = $like;
}

if ($statusFilter !== '') {
    $sql .= ' AND l.status = ?';
    $parameters[] = $statusFilter;
}

$sql .= " ORDER BY CASE WHEN l.status = 'active' THEN 0 ELSE 1 END, l.name ASC";
$labs = q($sql, $parameters)->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
    <div class="page-head-text">
        <span class="page-eyebrow"><?= icon('flask') ?>Laboratorium</span>
        <h1 class="page-title"><?= $isAdmin ? 'Manajemen Lab' : 'Daftar Lab' ?></h1>
        <p class="page-subtitle">
            <?= $isAdmin
                ? 'Kelola data, fasilitas, dan ketersediaan laboratorium LAB BOOKING.'
                : 'Jelajahi laboratorium yang tersedia dan ajukan jadwal pemakaian.' ?>
        </p>
    </div>
    <?php if ($isAdmin): ?>
        <div class="page-actions">
            <a class="btn btn-primary" href="<?= e(url('labs/create.php')) ?>"><?= icon('plus') ?>Tambah Lab</a>
        </div>
    <?php endif; ?>
</div>

<div class="filter-bar">
    <form class="filter-form" method="get" action="<?= e(url('labs/index.php')) ?>">
        <div class="filter-search">
            <?= icon('search') ?>
            <input class="input" type="search" name="q" value="<?= e($search) ?>" placeholder="Cari nama, kode, atau lokasi lab…" aria-label="Cari laboratorium">
        </div>
        <select class="select" name="status" aria-label="Filter status laboratorium">
            <option value="">Semua status</option>
            <option value="active"<?= selected($statusFilter === 'active') ?>>Tersedia</option>
            <option value="inactive"<?= selected($statusFilter === 'inactive') ?>>Nonaktif</option>
        </select>
        <button class="btn btn-secondary" type="submit"><?= icon('filter') ?>Terapkan</button>
        <?php if ($search !== '' || $statusFilter !== ''): ?>
            <a class="filter-reset" href="<?= e(url('labs/index.php')) ?>"><?= icon('x') ?>Reset</a>
        <?php endif; ?>
    </form>
</div>

<?php if ($labs === []): ?>
    <div class="card">
        <div class="empty-state">
            <div class="empty-icon"><?= icon('flask') ?></div>
            <h3>Laboratorium tidak ditemukan</h3>
            <p>Coba ubah kata kunci pencarian atau filter status laboratorium.</p>
            <a class="btn btn-secondary btn-sm" href="<?= e(url('labs/index.php')) ?>">Tampilkan semua lab</a>
        </div>
    </div>
<?php else: ?>
    <div class="lab-grid">
        <?php foreach ($labs as $lab): ?>
            <?php $facilities = facility_list($lab['facilities']); ?>
            <article class="lab-card">
                <div class="lab-head">
                    <span class="lab-mark"><?= icon(lab_icon_name($lab)) ?></span>
                    <?= lab_status_badge($lab['status']) ?>
                </div>

                <h2 class="lab-title"><?= e($lab['name']) ?></h2>
                <div class="lab-location"><?= icon('map-pin') ?><?= e($lab['location']) ?></div>
                <p class="lab-desc"><?= e($lab['description'] ?: 'Laboratorium siap mendukung kegiatan praktikum dan pengembangan mahasiswa.') ?></p>

                <div class="lab-facilities">
                    <?php if ($facilities !== []): ?>
                        <div class="chip-row">
                            <?php foreach (array_slice($facilities, 0, 3) as $facility): ?>
                                <span class="chip"><?= e($facility) ?></span>
                            <?php endforeach; ?>
                            <?php if (count($facilities) > 3): ?>
                                <span class="chip">+<?= count($facilities) - 3 ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="lab-meta">
                    <div>
                        <?= icon('users') ?>
                        <span><strong><?= (int) $lab['capacity'] ?> orang</strong>Kapasitas</span>
                    </div>
                    <div>
                        <?= icon('calendar-check') ?>
                        <span><strong><?= (int) $lab['upcoming_count'] ?></strong>Jadwal mendatang</span>
                    </div>
                </div>

                <div class="lab-footer">
                    <a class="link-arrow" href="<?= e(url('labs/view.php?id=' . (int) $lab['id'])) ?>">
                        Detail lab <?= icon('arrow-right') ?>
                    </a>
                    <div class="lab-footer-actions">
                        <?php if ($isAdmin): ?>
                            <a class="btn btn-icon btn-sm btn-secondary" href="<?= e(url('labs/edit.php?id=' . (int) $lab['id'])) ?>" title="Edit <?= e($lab['name']) ?>" aria-label="Edit <?= e($lab['name']) ?>">
                                <?= icon('edit') ?>
                            </a>
                            <form method="post" action="<?= e(url('labs/delete.php')) ?>" data-confirm="Hapus lab <?= e($lab['name']) ?>? Tindakan ini tidak dapat dibatalkan.">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $lab['id'] ?>">
                                <button class="btn btn-icon btn-sm btn-danger-soft" type="submit" title="Hapus <?= e($lab['name']) ?>" aria-label="Hapus <?= e($lab['name']) ?>">
                                    <?= icon('trash') ?>
                                </button>
                            </form>
                        <?php elseif ($lab['status'] === 'active'): ?>
                            <a class="btn btn-sm btn-primary" href="<?= e(url('bookings/create.php?lab_id=' . (int) $lab['id'])) ?>">
                                <?= icon('plus') ?>Booking
                            </a>
                        <?php else: ?>
                            <span class="btn btn-sm btn-secondary is-disabled">Nonaktif</span>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>

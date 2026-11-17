<?php
declare(strict_types=1);

/** @var array $lab */
/** @var array $errors */
/** @var bool $isEdit */
/** @var string $formAction */
?>
<form method="post" action="<?= e($formAction) ?>" data-submit-once>
    <?= csrf_field() ?>
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int) $lab['id'] ?>">
    <?php endif; ?>

    <div class="form-grid">
        <div class="field">
            <label class="field-label" for="name">Nama laboratorium <span class="req">*</span></label>
            <input
                class="input<?= $errors['name'] !== '' ? ' has-error' : '' ?>"
                type="text"
                id="name"
                name="name"
                value="<?= e($lab['name']) ?>"
                placeholder="Contoh: Lab Komputer 1"
                maxlength="120"
                required
            >
            <?php if ($errors['name'] !== ''): ?>
                <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['name']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-grid cols-2">
            <div class="field">
                <label class="field-label" for="code">Kode lab <span class="req">*</span></label>
                <input
                    class="input<?= $errors['code'] !== '' ? ' has-error' : '' ?>"
                    type="text"
                    id="code"
                    name="code"
                    value="<?= e($lab['code']) ?>"
                    placeholder="Contoh: LK-01"
                    maxlength="20"
                    required
                >
                <span class="field-hint">Gunakan huruf kapital, angka, atau tanda hubung.</span>
                <?php if ($errors['code'] !== ''): ?>
                    <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['code']) ?></span>
                <?php endif; ?>
            </div>

            <div class="field">
                <label class="field-label" for="capacity">Kapasitas <span class="req">*</span></label>
                <input
                    class="input<?= $errors['capacity'] !== '' ? ' has-error' : '' ?>"
                    type="number"
                    id="capacity"
                    name="capacity"
                    value="<?= e($lab['capacity']) ?>"
                    placeholder="Contoh: 30"
                    min="1"
                    max="500"
                    required
                >
                <span class="field-hint">Jumlah maksimal peserta dalam satu sesi.</span>
                <?php if ($errors['capacity'] !== ''): ?>
                    <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['capacity']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-grid cols-2">
            <div class="field">
                <label class="field-label" for="location">Lokasi <span class="req">*</span></label>
                <input
                    class="input<?= $errors['location'] !== '' ? ' has-error' : '' ?>"
                    type="text"
                    id="location"
                    name="location"
                    value="<?= e($lab['location']) ?>"
                    placeholder="Contoh: Gedung A, Lantai 2"
                    maxlength="150"
                    required
                >
                <?php if ($errors['location'] !== ''): ?>
                    <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['location']) ?></span>
                <?php endif; ?>
            </div>

            <div class="field">
                <label class="field-label" for="status">Status laboratorium <span class="req">*</span></label>
                <select class="select<?= $errors['status'] !== '' ? ' has-error' : '' ?>" id="status" name="status" required>
                    <option value="active"<?= selected($lab['status'] === 'active') ?>>Tersedia</option>
                    <option value="inactive"<?= selected($lab['status'] === 'inactive') ?>>Nonaktif</option>
                </select>
                <span class="field-hint">Pilih nonaktif jika lab sedang dalam perawatan.</span>
            </div>
        </div>

        <div class="field">
            <label class="field-label" for="description">Deskripsi</label>
            <textarea
                class="textarea<?= $errors['description'] !== '' ? ' has-error' : '' ?>"
                id="description"
                name="description"
                placeholder="Jelaskan peruntukan dan fasilitas utama laboratorium…"
                maxlength="2000"
            ><?= e($lab['description']) ?></textarea>
            <?php if ($errors['description'] !== ''): ?>
                <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['description']) ?></span>
            <?php endif; ?>
        </div>

        <div class="field">
            <label class="field-label" for="facilities">Fasilitas</label>
            <textarea
                class="textarea<?= $errors['facilities'] !== '' ? ' has-error' : '' ?>"
                id="facilities"
                name="facilities"
                placeholder="Contoh: 40 PC Workstation, Proyektor, Jaringan Gigabit, AC"
                maxlength="1000"
                style="min-height:88px"
            ><?= e($lab['facilities']) ?></textarea>
            <span class="field-hint">Pisahkan setiap fasilitas dengan koma.</span>
            <?php if ($errors['facilities'] !== ''): ?>
                <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['facilities']) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-actions">
        <a class="btn btn-secondary" href="<?= e($isEdit ? url('labs/view.php?id=' . (int) $lab['id']) : url('labs/index.php')) ?>">Batal</a>
        <button class="btn btn-primary" type="submit">
            <?= icon('check') ?><?= $isEdit ? 'Simpan Perubahan' : 'Tambah Laboratorium' ?>
        </button>
    </div>
</form>

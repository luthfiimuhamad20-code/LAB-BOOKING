<?php
declare(strict_types=1);

/** @var array $account */
/** @var array $errors */
/** @var bool $isEdit */
/** @var bool $isSelf */
/** @var string $formAction */
?>
<form method="post" action="<?= e($formAction) ?>" data-submit-once>
    <?= csrf_field() ?>
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int) $account['id'] ?>">
    <?php endif; ?>

    <div class="form-grid">
        <div class="field">
            <label class="field-label" for="name">Nama lengkap <span class="req">*</span></label>
            <input
                class="input<?= $errors['name'] !== '' ? ' has-error' : '' ?>"
                type="text"
                id="name"
                name="name"
                value="<?= e($account['name']) ?>"
                placeholder="Nama lengkap pengguna"
                maxlength="100"
                required
            >
            <?php if ($errors['name'] !== ''): ?>
                <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['name']) ?></span>
            <?php endif; ?>
        </div>

        <div class="field">
            <label class="field-label" for="email">Alamat email <span class="req">*</span></label>
            <input
                class="input<?= $errors['email'] !== '' ? ' has-error' : '' ?>"
                type="email"
                id="email"
                name="email"
                value="<?= e($account['email']) ?>"
                placeholder="nama@email.com"
                maxlength="150"
                required
            >
            <?php if ($errors['email'] !== ''): ?>
                <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['email']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-grid cols-2">
            <div class="field">
                <label class="field-label" for="role">Peran <span class="req">*</span></label>
                <select class="select<?= $errors['role'] !== '' ? ' has-error' : '' ?>" id="role" name="role" required>
                    <option value="user"<?= selected($account['role'] === 'user') ?>>Pengguna</option>
                    <option value="admin"<?= selected($account['role'] === 'admin') ?>>Admin</option>
                </select>
                <?php if ($errors['role'] !== ''): ?>
                    <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['role']) ?></span>
                <?php endif; ?>
            </div>

            <div class="field">
                <label class="field-label" for="status">Status akun <span class="req">*</span></label>
                <select class="select<?= $errors['status'] !== '' ? ' has-error' : '' ?>" id="status" name="status" required>
                    <option value="active"<?= selected($account['status'] === 'active') ?>>Aktif</option>
                    <option value="inactive"<?= selected($account['status'] === 'inactive') ?>>Nonaktif</option>
                </select>
                <?php if ($errors['status'] !== ''): ?>
                    <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['status']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-grid cols-2">
            <div class="field">
                <label class="field-label" for="phone">Nomor telepon</label>
                <input
                    class="input<?= $errors['phone'] !== '' ? ' has-error' : '' ?>"
                    type="text"
                    id="phone"
                    name="phone"
                    value="<?= e($account['phone']) ?>"
                    placeholder="08xxxxxxxxxx"
                    maxlength="30"
                    inputmode="tel"
                >
                <?php if ($errors['phone'] !== ''): ?>
                    <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['phone']) ?></span>
                <?php endif; ?>
            </div>

            <div class="field">
                <label class="field-label" for="department">Program studi / unit</label>
                <input
                    class="input<?= $errors['department'] !== '' ? ' has-error' : '' ?>"
                    type="text"
                    id="department"
                    name="department"
                    value="<?= e($account['department']) ?>"
                    placeholder="Contoh: Teknik Informatika"
                    maxlength="120"
                >
                <?php if ($errors['department'] !== ''): ?>
                    <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['department']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="field">
            <label class="field-label" for="password">
                Password <?= $isEdit ? '' : '<span class="req">*</span>' ?>
            </label>
            <div class="password-field">
                <input
                    class="input<?= $errors['password'] !== '' ? ' has-error' : '' ?>"
                    type="password"
                    id="password"
                    name="password"
                    placeholder="<?= $isEdit ? 'Kosongkan jika tidak ingin mengubah password' : 'Minimal 8 karakter' ?>"
                    autocomplete="new-password"
                    minlength="8"
                    <?= $isEdit ? '' : 'required' ?>
                >
                <button class="password-toggle" type="button" data-toggle-password="password" aria-label="Tampilkan password" aria-pressed="false">
                    <span class="icon-eye"><?= icon('eye') ?></span>
                    <span class="icon-eye-off"><?= icon('eye-off') ?></span>
                </button>
            </div>
            <span class="field-hint">
                <?= $isEdit ? 'Isi hanya apabila admin ingin mereset password pengguna.' : 'Gunakan minimal 8 karakter agar akun lebih aman.' ?>
            </span>
            <?php if ($errors['password'] !== ''): ?>
                <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['password']) ?></span>
            <?php endif; ?>
        </div>

        <?php if ($isSelf): ?>
            <div class="alert alert-info">
                <?= icon('info') ?>
                <span>Anda sedang mengedit akun sendiri. Peran admin dan status aktif tidak dapat diubah dari halaman ini.</span>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-actions">
        <a class="btn btn-secondary" href="<?= e(url('users/index.php')) ?>">Batal</a>
        <button class="btn btn-primary" type="submit">
            <?= icon('check') ?><?= $isEdit ? 'Simpan Perubahan' : 'Tambah Pengguna' ?>
        </button>
    </div>
</form>

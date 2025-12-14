<?php
$title = 'Админ-панель';
require __DIR__ . '/../layouts/header.php';
?>

<div class="admin-container">
    <div class="admin-header">
        <h1>Админ-панель</h1>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Всего пользователей</h3>
            <p class="stat-number"><?= $stats['total_users'] ?></p>
        </div>
        <div class="stat-card">
            <h3>Активных</h3>
            <p class="stat-number"><?= $stats['active_users'] ?></p>
        </div>
        <div class="stat-card">
            <h3>Подтвержденных</h3>
            <p class="stat-number"><?= $stats['verified_users'] ?></p>
        </div>
        <div class="stat-card">
            <h3>Администраторов</h3>
            <p class="stat-number"><?= $stats['admins'] ?></p>
        </div>
    </div>

    <div class="admin-card">
        <h2>Управление пользователями</h2>
        
        <div class="users-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Имя</th>
                        <th>Роли</th>
                        <th>Статус</th>
                        <th>Email подтвержден</th>
                        <th>Дата регистрации</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <?php 
                                $name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                                echo htmlspecialchars($name ?: '-');
                                ?>
                            </td>
                            <td>
                                <?php 
                                $currentRole = !empty($user['roles']) ? $user['roles'][0] : null;
                                if ($currentRole): ?>
                                    <span class="role-badge"><?= htmlspecialchars($currentRole) ?></span>
                                <?php else: ?>
                                    <span style="color: var(--gray);">Нет роли</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['is_active']): ?>
                                    <span class="status-active">Активен</span>
                                <?php else: ?>
                                    <span class="status-inactive">Заблокирован</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['email_verified_at']): ?>
                                    <span class="status-verified">Да</span>
                                <?php else: ?>
                                    <span class="status-unverified">Нет</span>
                                <?php endif; ?>
                            </td>
                            <td><?= formatDate($user['created_at']) ?></td>
                            <td class="actions">
                                <?php if ($user['id'] !== ($_SESSION['user_id'] ?? 0)): ?>
                                    <form method="POST" action="/admin/toggle-user" style="display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="submit" class="btn btn-small <?= $user['is_active'] ? 'btn-warning' : 'btn-success' ?>">
                                            <?= $user['is_active'] ? 'Заблокировать' : 'Разблокировать' ?>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: var(--gray); font-size: 0.85rem;">Вы</span>
                                <?php endif; ?>
                                
                                <form method="POST" action="/admin/assign-role" style="display: inline; margin-left: 5px;">
                                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <select name="role" onchange="this.form.submit()" style="padding: 0.25rem;">
                                        <option value="">Назначить роль</option>
                                        <option value="admin" <?= $currentRole === 'admin' ? 'selected' : '' ?>>Admin</option>
                                        <option value="user" <?= $currentRole === 'user' ? 'selected' : '' ?>>User</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


<?php
$title = 'Профиль';
require __DIR__ . '/../layouts/header.php';
?>

<div class="profile-container">
    <div class="profile-header">
        <h1>Мой профиль</h1>
        <a href="/profile/edit" class="btn btn-secondary">Редактировать</a>
    </div>
    
    <div class="profile-card">
        <div class="profile-info">
            <div class="info-row">
                <span class="label">Email:</span>
                <span class="value"><?= htmlspecialchars($user['email']) ?></span>
            </div>
            
            <?php if (!empty($profile['first_name']) || !empty($profile['last_name'])): ?>
                <div class="info-row">
                    <span class="label">Имя:</span>
                    <span class="value">
                        <?= htmlspecialchars(trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? ''))) ?>
                    </span>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($profile['phone'])): ?>
                <div class="info-row">
                    <span class="label">Телефон:</span>
                    <span class="value"><?= htmlspecialchars($profile['phone']) ?></span>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($profile['birth_date'])): ?>
                <div class="info-row">
                    <span class="label">Дата рождения:</span>
                    <span class="value"><?= formatDate($profile['birth_date']) ?></span>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($profile['country']) || !empty($profile['city'])): ?>
                <div class="info-row">
                    <span class="label">Местоположение:</span>
                    <span class="value">
                        <?= htmlspecialchars(trim(($profile['country'] ?? '') . ', ' . ($profile['city'] ?? ''))) ?>
                    </span>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($profile['bio'])): ?>
                <div class="info-row">
                    <span class="label">О себе:</span>
                    <span class="value"><?= nl2br(htmlspecialchars($profile['bio'])) ?></span>
                </div>
            <?php endif; ?>
            
            <div class="info-row">
                <span class="label">Роли:</span>
                <span class="value">
                    <?= implode(', ', array_map('htmlspecialchars', $user['roles'] ?? [])) ?>
                </span>
            </div>
            
            <div class="info-row">
                <span class="label">Дата регистрации:</span>
                <span class="value"><?= formatDate($user['created_at']) ?></span>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>



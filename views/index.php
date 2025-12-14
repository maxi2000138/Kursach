<?php
$title = 'Главная';
require __DIR__ . '/layouts/header.php';
?>

<div class="home-container">
    <h1>Добро пожаловать в систему управления пользователями!</h1>
    
    <?php if (isAuthenticated()): ?>
        <p>Вы вошли как: <strong><?= htmlspecialchars(currentUser()['email']) ?></strong></p>
        <a href="/profile" class="btn btn-primary">Перейти в профиль</a>
    <?php else: ?>
        <p>Пожалуйста, войдите или зарегистрируйтесь</p>
        <div class="home-actions">
            <a href="/login" class="btn btn-primary">Войти</a>
            <a href="/register" class="btn btn-secondary">Регистрироваться</a>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/layouts/footer.php'; ?>



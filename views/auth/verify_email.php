<?php
$title = 'Подтверждение email';
require __DIR__ . '/../layouts/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h1>Подтверждение email</h1>
        <p>Введите код, отправленный на <strong><?= htmlspecialchars($email ?? '') ?></strong></p>
        
        <form method="POST" action="/verify-email" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>">
            
            <div class="form-group">
                <label for="code">Код подтверждения</label>
                <input type="text" id="code" name="code" maxlength="6" pattern="[0-9]{6}" required autofocus>
                <small>6-значный код</small>
            </div>
            
            <button type="submit" class="btn btn-primary">Подтвердить</button>
        </form>
        
        <div class="auth-links">
            <a href="/login">Вернуться к входу</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>



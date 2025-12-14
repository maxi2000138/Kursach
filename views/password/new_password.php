<?php
$title = 'Новый пароль';
require __DIR__ . '/../layouts/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h1>Установить новый пароль</h1>
        
        <form method="POST" action="/new-password" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            
            <div class="form-group">
                <label for="password">Новый пароль</label>
                <input type="password" id="password" name="password" required autofocus>
                <small>Минимум 8 символов, буквы и цифры</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Подтвердите пароль</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Изменить пароль</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>



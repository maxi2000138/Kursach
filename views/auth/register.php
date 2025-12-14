<?php
$title = 'Регистрация';
require __DIR__ . '/../layouts/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h1>Регистрация</h1>
        
        <form method="POST" action="/register" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required>
                <small>Минимум 8 символов, буквы и цифры</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Подтвердите пароль</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
        </form>
        
        <div class="auth-links">
            <a href="/login">Уже есть аккаунт? Войти</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>



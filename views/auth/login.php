<?php
$title = 'Вход';
require __DIR__ . '/../layouts/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h1>Вход</h1>
        
        <form method="POST" action="/login" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Войти</button>
        </form>
        
        <div class="auth-links">
            <a href="/forgot-password">Забыли пароль?</a>
            <a href="/register">Регистрация</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>



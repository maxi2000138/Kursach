<?php
$title = 'Восстановление пароля';
require __DIR__ . '/../layouts/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h1>Восстановление пароля</h1>
        <p>Введите email, на который будет отправлен код для восстановления пароля</p>
        
        <form method="POST" action="/forgot-password" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autofocus>
            </div>
            
            <button type="submit" class="btn btn-primary">Отправить код</button>
        </form>
        
        <div class="auth-links">
            <a href="/login">Вернуться к входу</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>



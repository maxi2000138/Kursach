<?php
$title = 'Редактирование профиля';
require __DIR__ . '/../layouts/header.php';
?>

<div class="profile-container">
    <div class="profile-header">
        <h1>Редактирование профиля</h1>
        <a href="/profile" class="btn btn-secondary">Назад</a>
    </div>
    
    <div class="profile-card">
        <h2>Личная информация</h2>
        <form method="POST" action="/profile/update" class="profile-form">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            
            <div class="form-group">
                <label for="first_name">Имя</label>
                <input type="text" id="first_name" name="first_name" 
                       value="<?= htmlspecialchars($profile['first_name'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="last_name">Фамилия</label>
                <input type="text" id="last_name" name="last_name" 
                       value="<?= htmlspecialchars($profile['last_name'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="phone">Телефон</label>
                <input type="tel" id="phone" name="phone" 
                       value="<?= htmlspecialchars($profile['phone'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="birth_date">Дата рождения</label>
                <input type="date" id="birth_date" name="birth_date" 
                       value="<?= htmlspecialchars($profile['birth_date'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="country">Страна</label>
                <input type="text" id="country" name="country" 
                       value="<?= htmlspecialchars($profile['country'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="city">Город</label>
                <input type="text" id="city" name="city" 
                       value="<?= htmlspecialchars($profile['city'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="bio">О себе</label>
                <textarea id="bio" name="bio" rows="4" maxlength="1000"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
                <small>Максимум 1000 символов</small>
            </div>
            
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>
    
    <div class="profile-card">
        <h2>Смена пароля</h2>
        <form method="POST" action="/profile/change-password" class="profile-form">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            
            <div class="form-group">
                <label for="current_password">Текущий пароль</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            
            <div class="form-group">
                <label for="new_password">Новый пароль</label>
                <input type="password" id="new_password" name="new_password" required>
                <small>Минимум 8 символов, буквы и цифры</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Подтвердите новый пароль</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Изменить пароль</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>



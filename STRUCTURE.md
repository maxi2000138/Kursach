# Структура проекта

## Описание архитектуры

Проект использует классическую архитектуру MVC (Model-View-Controller).

### Модели (`models/`)
- `Database.php` - Singleton для работы с PDO
- `User.php` - Модель пользователя
- `UserProfile.php` - Модель профиля
- `Role.php` - Модель роли
- `PasswordReset.php` - Модель восстановления пароля
- `EmailVerification.php` - Модель верификации email

### Контроллеры (`controllers/`)
- `BaseController.php` - Базовый контроллер
- `AuthController.php` - Авторизация, регистрация, верификация
- `ProfileController.php` - Управление профилем
- `PasswordController.php` - Восстановление пароля

### Сервисы (`services/`)
- `EmailService.php` - Отправка email через PHPMailer
- `ValidationService.php` - Валидация данных

### Представления (`views/`)
- `layouts/` - Шаблоны (header, footer, navigation)
- `auth/` - Страницы авторизации
- `profile/` - Страницы профиля
- `password/` - Страницы восстановления пароля
- `index.php` - Главная страница

### Конфигурация (`config/`)
- `database.php` - Настройки БД
- `app.php` - Общие настройки приложения
- `mail.php` - Настройки почты

### Вспомогательные функции (`includes/`)
- `autoload.php` - Автозагрузка классов
- `functions.php` - Вспомогательные функции

## Поток работы

### Регистрация
1. Пользователь заполняет форму → `AuthController::register()`
2. Создается пользователь в БД
3. Генерируется код верификации
4. Отправляется email с кодом
5. Пользователь вводит код → `AuthController::verifyEmail()`
6. Email подтверждается, пользователь авторизуется

### Восстановление пароля
1. Пользователь вводит email → `PasswordController::forgotPassword()`
2. Генерируется код
3. Отправляется email с кодом
4. Пользователь вводит код → `PasswordController::verifyResetCode()`
5. Пользователь вводит новый пароль → `PasswordController::setNewPassword()`

### Управление профилем
1. Просмотр → `ProfileController::showProfile()`
2. Редактирование → `ProfileController::update()`
3. Смена пароля → `ProfileController::changePassword()`

## Безопасность

- Все пароли хешируются через `password_hash()`
- CSRF токены для всех форм
- Подготовленные SQL запросы (PDO)
- Валидация всех входных данных
- Санитизация вывода через `htmlspecialchars()`

## База данных

6 таблиц:
1. `users` - пользователи
2. `user_profiles` - профили
3. `roles` - роли
4. `user_roles` - связь пользователей и ролей
5. `password_resets` - восстановление пароля
6. `email_verifications` - верификация email



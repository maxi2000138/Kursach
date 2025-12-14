# Система управления пользователями

Система управления пользователями с функциями регистрации, входа и управления профилями на PHP.

## Функционал

- ✅ Регистрация пользователей
- ✅ Подтверждение email через код
- ✅ Вход в систему
- ✅ Восстановление пароля через email
- ✅ Управление профилем (редактирование данных)
- ✅ Смена пароля
- ✅ Система ролей (user, admin)

## Технологии

- PHP 8.1+
- MySQL 8.0
- PHPMailer для отправки писем
- Docker & Docker Compose
- Apache с mod_rewrite

## Структура проекта

```
/
├── config/          # Конфигурационные файлы
├── controllers/     # Контроллеры (MVC)
├── models/          # Модели данных
├── services/        # Сервисы (Email, Validation)
├── views/           # Представления (шаблоны)
├── includes/        # Вспомогательные функции
├── public/          # Публичные файлы (CSS, JS)
├── sql/             # SQL скрипты
└── index.php        # Точка входа
```

## Установка и запуск

### С Docker (рекомендуется)

1. Клонируй репозиторий или скопируй файлы проекта

2. Настрой переменные окружения в `docker-compose.yml`:
   - `SMTP_USERNAME` - email для отправки писем
   - `SMTP_PASSWORD` - пароль от email
   - `FROM_EMAIL` - отправитель писем

3. Запусти контейнеры:
```bash
docker-compose up -d
```

4. Установи зависимости Composer:
```bash
docker-compose exec web composer install
```

5. Открой в браузере:
   - Приложение: http://localhost:8080
   - phpMyAdmin: http://localhost:8081

### Без Docker

1. Установи PHP 8.1+, MySQL 8.0, Apache

2. Установи зависимости:
```bash
composer install
```

3. Создай базу данных и выполни SQL скрипт:
```bash
mysql -u root -p < sql/schema.sql
```

4. Настрой конфиги в `config/`:
   - `database.php` - настройки БД
   - `mail.php` - настройки SMTP

5. Настрой Apache для работы с `.htaccess`

## Структура БД

Проект использует 6 таблиц:
- `users` - пользователи
- `user_profiles` - профили пользователей
- `roles` - роли
- `user_roles` - связь пользователей и ролей
- `password_resets` - восстановление пароля
- `email_verifications` - верификация email

## Безопасность

- Хеширование паролей через `password_hash()`
- CSRF защита для форм
- Подготовленные SQL запросы (PDO)
- Валидация всех входных данных
- Санитизация вывода

## Разработка

Для разработки с Docker:

```bash
# Запуск
docker-compose up -d

# Просмотр логов
docker-compose logs -f web

# Остановка
docker-compose down

# Пересборка
docker-compose build --no-cache
```

## Лицензия

Проект для курсовой работы.


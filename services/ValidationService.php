<?php

class ValidationService
{
    public static function validateEmail(string $email): array
    {
        if (empty($email)) {
            return ['valid' => false, 'error' => 'Email обязателен для заполнения'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'error' => 'Некорректный формат email'];
        }

        if (strlen($email) > 255) {
            return ['valid' => false, 'error' => 'Email слишком длинный'];
        }

        return ['valid' => true];
    }

    public static function validatePassword(string $password, ?string $confirmPassword = null): array
    {
        if (empty($password)) {
            return ['valid' => false, 'error' => 'Пароль обязателен для заполнения'];
        }

        if (strlen($password) < 8) {
            return ['valid' => false, 'error' => 'Пароль должен содержать минимум 8 символов'];
        }

        if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            return ['valid' => false, 'error' => 'Пароль должен содержать буквы и цифры'];
        }

        if ($confirmPassword !== null && $password !== $confirmPassword) {
            return ['valid' => false, 'error' => 'Пароли не совпадают'];
        }

        return ['valid' => true];
    }

    public static function validateName(string $name, string $fieldName = 'Имя'): array
    {
        if (empty($name)) {
            return ['valid' => false, 'error' => "{$fieldName} обязательно для заполнения"];
        }

        if (strlen($name) < 2) {
            return ['valid' => false, 'error' => "{$fieldName} должно содержать минимум 2 символа"];
        }

        if (strlen($name) > 100) {
            return ['valid' => false, 'error' => "{$fieldName} слишком длинное"];
        }

        if (!preg_match('/^[а-яА-ЯёЁa-zA-Z\s\-]+$/u', $name)) {
            return ['valid' => false, 'error' => "{$fieldName} содержит недопустимые символы"];
        }

        return ['valid' => true];
    }

    public static function validatePhone(string $phone): array
    {
        if (empty($phone)) {
            return ['valid' => true]; // Телефон необязателен
        }

        $cleaned = preg_replace('/[^0-9+]/', '', $phone);
        if (strlen($cleaned) < 10) {
            return ['valid' => false, 'error' => 'Некорректный формат телефона'];
        }

        return ['valid' => true];
    }

    public static function validateVerificationCode(string $code): array
    {
        if (empty($code)) {
            return ['valid' => false, 'error' => 'Код обязателен для заполнения'];
        }

        if (!preg_match('/^\d{6}$/', $code)) {
            return ['valid' => false, 'error' => 'Код должен состоять из 6 цифр'];
        }

        return ['valid' => true];
    }

    public static function validateDate(string $date, string $fieldName = 'Дата'): array
    {
        if (empty($date)) {
            return ['valid' => true]; // Дата необязательна
        }

        $d = DateTime::createFromFormat('Y-m-d', $date);
        if (!$d || $d->format('Y-m-d') !== $date) {
            return ['valid' => false, 'error' => "Некорректный формат {$fieldName}"];
        }

        $now = new DateTime();
        if ($d > $now) {
            return ['valid' => false, 'error' => "{$fieldName} не может быть в будущем"];
        }

        return ['valid' => true];
    }
}



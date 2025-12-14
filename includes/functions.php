<?php

function config(string $file): array
{
    $path = __DIR__ . '/../config/' . $file . '.php';
    if (!file_exists($path)) {
        throw new Exception("Config file not found: {$file}");
    }
    return require $path;
}

function view(string $view, array $data = []): void
{
    extract($data);
    $viewPath = __DIR__ . '/../views/' . $view . '.php';
    if (!file_exists($viewPath)) {
        throw new Exception("View not found: {$view}");
    }
    require $viewPath;
}

function redirect(string $url): void
{
    header("Location: {$url}");
    exit;
}

function session(string $key, $default = null)
{
    return $_SESSION[$key] ?? $default;
}

function setSession(string $key, $value): void
{
    $_SESSION[$key] = $value;
}

function unsetSession(string $key): void
{
    unset($_SESSION[$key]);
}

function flash(string $key, $value = null)
{
    if ($value === null) {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    $_SESSION['flash'][$key] = $value;
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function generateToken(int $length = 32): string
{
    return bin2hex(random_bytes($length));
}

function generateVerificationCode(int $length = 6): string
{
    return str_pad((string)random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}

function isAuthenticated(): bool
{
    return isset($_SESSION['user_id']);
}

function currentUser(): ?array
{
    if (!isAuthenticated()) {
        return null;
    }
    return $_SESSION['user'] ?? null;
}

function requireAuth(): void
{
    if (!isAuthenticated()) {
        redirect('/login');
    }
}

function hasRole(string $roleName): bool
{
    $user = currentUser();
    if (!$user) {
        return false;
    }
    return in_array($roleName, $user['roles'] ?? []);
}

function requireRole(string $roleName): void
{
    requireAuth();
    if (!hasRole($roleName)) {
        redirect('/');
    }
}

function sanitize(string $input): string
{
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function formatDate(?string $date, string $format = 'd.m.Y'): string
{
    if (!$date) {
        return '';
    }
    return date($format, strtotime($date));
}



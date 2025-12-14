<?php

class BaseController
{
    protected function render(string $view, array $data = []): void
    {
        view($view, $data);
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $url): void
    {
        redirect($url);
    }

    protected function validateCsrf(string $token): bool
    {
        if (!verifyCsrfToken($token)) {
            flash('error', 'Ошибка безопасности. Попробуйте еще раз.');
            return false;
        }
        return true;
    }
}



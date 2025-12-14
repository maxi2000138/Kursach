<?php

class PasswordController extends BaseController
{
    public function showForgotPassword(): void
    {
        $this->render('password/forgot_password');
    }

    public function forgotPassword(): void
    {
        if (!isset($_POST['csrf_token']) || !$this->validateCsrf($_POST['csrf_token'])) {
            $this->redirect('/forgot-password');
            return;
        }

        $email = sanitize($_POST['email'] ?? '');

        $emailValidation = ValidationService::validateEmail($email);
        if (!$emailValidation['valid']) {
            flash('error', $emailValidation['error']);
            $this->redirect('/forgot-password');
            return;
        }

        $user = User::findByEmail($email);
        if (!$user) {
            flash('info', 'Если пользователь с таким email существует, письмо отправлено');
            $this->redirect('/forgot-password');
            return;
        }

        $code = generateVerificationCode();
        $token = generateToken();
        
        PasswordReset::invalidateUserTokens($user->getId());
        PasswordReset::create($user->getId(), $token, config('app')['password_reset_expiry']);
        
        $emailService = new EmailService();
        $emailService->sendPasswordResetCode($email, $code);

        setSession('reset_code', $code);
        setSession('reset_user_id', $user->getId());
        setSession('reset_token', $token);

        flash('success', 'Проверьте почту для получения кода восстановления');
        $this->redirect('/reset-password?email=' . urlencode($email));
    }

    public function showResetPassword(): void
    {
        $email = $_GET['email'] ?? '';
        $this->render('password/reset', ['email' => $email]);
    }

    public function verifyResetCode(): void
    {
        if (!isset($_POST['csrf_token']) || !$this->validateCsrf($_POST['csrf_token'])) {
            $this->redirect('/forgot-password');
            return;
        }

        $code = $_POST['code'] ?? '';
        $email = sanitize($_POST['email'] ?? '');

        $codeValidation = ValidationService::validateVerificationCode($code);
        if (!$codeValidation['valid']) {
            flash('error', $codeValidation['error']);
            $this->redirect('/reset-password?email=' . urlencode($email));
            return;
        }

        $storedCode = session('reset_code');
        $userId = session('reset_user_id');

        if (!$storedCode || !$userId || $storedCode !== $code) {
            flash('error', 'Неверный код');
            $this->redirect('/reset-password?email=' . urlencode($email));
            return;
        }

        $user = User::findById($userId);
        if (!$user || $user->getEmail() !== $email) {
            flash('error', 'Ошибка верификации');
            $this->redirect('/reset-password?email=' . urlencode($email));
            return;
        }

        $token = session('reset_token');
        $reset = PasswordReset::findByToken($token);
        
        if (!$reset || !$reset->isValid()) {
            flash('error', 'Код истек или уже использован');
            $this->redirect('/forgot-password');
            return;
        }

        setSession('reset_verified', true);
        setSession('reset_user_id', $userId);
        
        $this->redirect('/new-password');
    }

    public function showNewPassword(): void
    {
        if (!session('reset_verified')) {
            $this->redirect('/forgot-password');
            return;
        }
        $this->render('password/new_password');
    }

    public function setNewPassword(): void
    {
        if (!isset($_POST['csrf_token']) || !$this->validateCsrf($_POST['csrf_token'])) {
            $this->redirect('/forgot-password');
            return;
        }

        if (!session('reset_verified')) {
            $this->redirect('/forgot-password');
            return;
        }

        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $passwordValidation = ValidationService::validatePassword($password, $confirmPassword);
        if (!$passwordValidation['valid']) {
            flash('error', $passwordValidation['error']);
            $this->redirect('/new-password');
            return;
        }

        $userId = session('reset_user_id');
        $user = User::findById($userId);
        
        if (!$user) {
            flash('error', 'Пользователь не найден');
            $this->redirect('/forgot-password');
            return;
        }

        $token = session('reset_token');
        $reset = PasswordReset::findByToken($token);
        
        if (!$reset || !$reset->isValid()) {
            flash('error', 'Сессия истекла');
            $this->redirect('/forgot-password');
            return;
        }

        $user->updatePassword($password);
        $reset->markAsUsed();

        unsetSession('reset_code');
        unsetSession('reset_user_id');
        unsetSession('reset_token');
        unsetSession('reset_verified');

        flash('success', 'Пароль успешно изменен!');
        $this->redirect('/login');
    }
}



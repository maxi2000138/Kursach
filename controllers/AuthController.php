<?php

class AuthController extends BaseController
{
    public function showLogin(): void
    {
        if (isAuthenticated()) {
            $this->redirect('/profile');
            return;
        }
        $this->render('auth/login');
    }

    public function login(): void
    {
        if (!isset($_POST['csrf_token']) || !$this->validateCsrf($_POST['csrf_token'])) {
            $this->redirect('/login');
            return;
        }

        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $emailValidation = ValidationService::validateEmail($email);
        if (!$emailValidation['valid']) {
            flash('error', $emailValidation['error']);
            $this->redirect('/login');
            return;
        }

        $user = User::findByEmail($email);
        if (!$user || !$user->verifyPassword($password)) {
            flash('error', 'Неверный email или пароль');
            $this->redirect('/login');
            return;
        }

        if (!$user->isActive()) {
            flash('error', 'Ваш аккаунт заблокирован');
            $this->redirect('/login');
            return;
        }

        if (!$user->isEmailVerified()) {
            flash('warning', 'Подтвердите email для входа');
            $this->redirect('/verify-email?email=' . urlencode($email));
            return;
        }

        $this->_setUserSession($user);
        flash('success', 'Добро пожаловать!');
        $this->redirect('/profile');
    }

    public function showRegister(): void
    {
        if (isAuthenticated()) {
            $this->redirect('/profile');
            return;
        }
        $this->render('auth/register');
    }

    public function register(): void
    {
        if (!isset($_POST['csrf_token']) || !$this->validateCsrf($_POST['csrf_token'])) {
            $this->redirect('/register');
            return;
        }

        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $emailValidation = ValidationService::validateEmail($email);
        if (!$emailValidation['valid']) {
            flash('error', $emailValidation['error']);
            $this->redirect('/register');
            return;
        }

        if (User::findByEmail($email)) {
            flash('error', 'Пользователь с таким email уже существует');
            $this->redirect('/register');
            return;
        }

        $passwordValidation = ValidationService::validatePassword($password, $confirmPassword);
        if (!$passwordValidation['valid']) {
            flash('error', $passwordValidation['error']);
            $this->redirect('/register');
            return;
        }

        $user = User::create($email, $password);
        if (!$user) {
            flash('error', 'Ошибка при создании аккаунта');
            $this->redirect('/register');
            return;
        }

        UserProfile::create($user->getId());
        Role::assignToUser($user->getId(), 'user');

        $code = generateVerificationCode();
        $token = generateToken();
        
        EmailVerification::create($user->getId(), $token, config('app')['verification_code_expiry']);
        
        $emailService = new EmailService();
        $emailSent = $emailService->sendVerificationCode($email, $code);
        
        if (!$emailSent) {
            flash('error', 'Ошибка отправки письма. Попробуйте позже.');
            $this->redirect('/register');
            return;
        }

        setSession('verification_code', $code);
        setSession('verification_user_id', $user->getId());
        setSession('verification_token', $token);

        flash('success', 'Регистрация успешна! Проверьте почту для подтверждения email.');
        $this->redirect('/verify-email?email=' . urlencode($email));
    }

    public function showVerifyEmail(): void
    {
        $email = $_GET['email'] ?? '';
        
        // Если пользователь уже зарегистрирован, но не подтвердил email, отправляем код заново
        // Но только если код еще не был отправлен (нет в сессии)
        $user = User::findByEmail($email);
        if ($user && !$user->isEmailVerified()) {
            $existingCode = session('verification_code');
            $existingUserId = session('verification_user_id');
            
            // Отправляем код только если его еще нет в сессии или это другой пользователь
            if (!$existingCode || $existingUserId !== $user->getId()) {
                $code = generateVerificationCode();
                $token = generateToken();
                
                EmailVerification::invalidateUserTokens($user->getId());
                EmailVerification::create($user->getId(), $token, config('app')['verification_code_expiry']);
                
                $emailService = new EmailService();
                $emailSent = $emailService->sendVerificationCode($email, $code);
                
                if ($emailSent) {
                    setSession('verification_code', $code);
                    setSession('verification_user_id', $user->getId());
                    setSession('verification_token', $token);
                    flash('success', 'Код подтверждения отправлен на вашу почту!');
                } else {
                    flash('error', 'Ошибка отправки письма. Попробуйте позже.');
                }
            }
        }
        
        $this->render('auth/verify_email', ['email' => $email]);
    }

    public function verifyEmail(): void
    {
        if (!isset($_POST['csrf_token']) || !$this->validateCsrf($_POST['csrf_token'])) {
            $this->redirect('/verify-email');
            return;
        }

        $code = $_POST['code'] ?? '';
        $email = sanitize($_POST['email'] ?? '');

        $codeValidation = ValidationService::validateVerificationCode($code);
        if (!$codeValidation['valid']) {
            flash('error', $codeValidation['error']);
            $this->redirect('/verify-email?email=' . urlencode($email));
            return;
        }

        $storedCode = session('verification_code');
        $userId = session('verification_user_id');

        if (!$storedCode || !$userId || $storedCode !== $code) {
            flash('error', 'Неверный код подтверждения');
            $this->redirect('/verify-email?email=' . urlencode($email));
            return;
        }

        $user = User::findById($userId);
        if (!$user || $user->getEmail() !== $email) {
            flash('error', 'Ошибка верификации');
            $this->redirect('/verify-email?email=' . urlencode($email));
            return;
        }

        $user->markEmailAsVerified();
        EmailVerification::invalidateUserTokens($userId);

        unsetSession('verification_code');
        unsetSession('verification_user_id');
        unsetSession('verification_token');

        $this->_setUserSession($user);
        flash('success', 'Email успешно подтвержден!');
        $this->redirect('/profile');
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('/login');
    }

    private function _setUserSession(User $user): void
    {
        $userData = $user->toArray();
        $profile = UserProfile::findByUserId($user->getId());
        
        if ($profile) {
            $userData['profile'] = $profile->toArray();
        }

        setSession('user_id', $user->getId());
        setSession('user', $userData);
    }
}


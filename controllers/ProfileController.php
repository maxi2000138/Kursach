<?php

class ProfileController extends BaseController
{
    public function showProfile(): void
    {
        requireAuth();
        
        $user = User::findById($_SESSION['user_id']);
        if (!$user) {
            flash('error', 'Пользователь не найден');
            $this->redirect('/login');
            return;
        }

        $profile = UserProfile::findByUserId($user->getId());
        if (!$profile) {
            $profile = UserProfile::create($user->getId());
        }

        $this->render('profile/view', [
            'user' => $user->toArray(),
            'profile' => $profile->toArray()
        ]);
    }

    public function showEdit(): void
    {
        requireAuth();
        
        $user = User::findById($_SESSION['user_id']);
        if (!$user) {
            flash('error', 'Пользователь не найден');
            $this->redirect('/login');
            return;
        }

        $profile = UserProfile::findByUserId($user->getId());
        if (!$profile) {
            $profile = UserProfile::create($user->getId());
        }

        $this->render('profile/edit', [
            'user' => $user->toArray(),
            'profile' => $profile->toArray()
        ]);
    }

    public function update(): void
    {
        requireAuth();

        if (!isset($_POST['csrf_token']) || !$this->validateCsrf($_POST['csrf_token'])) {
            $this->redirect('/profile/edit');
            return;
        }

        $userId = $_SESSION['user_id'];
        $profile = UserProfile::findByUserId($userId);
        
        if (!$profile) {
            $profile = UserProfile::create($userId);
        }

        $data = [];
        $errors = [];

        if (isset($_POST['first_name'])) {
            $firstName = sanitize($_POST['first_name']);
            $validation = ValidationService::validateName($firstName, 'Имя');
            if ($validation['valid']) {
                $data['first_name'] = $firstName;
            } else {
                $errors[] = $validation['error'];
            }
        }

        if (isset($_POST['last_name'])) {
            $lastName = sanitize($_POST['last_name']);
            $validation = ValidationService::validateName($lastName, 'Фамилия');
            if ($validation['valid']) {
                $data['last_name'] = $lastName;
            } else {
                $errors[] = $validation['error'];
            }
        }

        if (isset($_POST['phone'])) {
            $phone = sanitize($_POST['phone']);
            $validation = ValidationService::validatePhone($phone);
            if ($validation['valid']) {
                $data['phone'] = $phone;
            } else {
                $errors[] = $validation['error'];
            }
        }

        if (isset($_POST['birth_date']) && !empty($_POST['birth_date'])) {
            $birthDate = sanitize($_POST['birth_date']);
            $validation = ValidationService::validateDate($birthDate, 'Дата рождения');
            if ($validation['valid']) {
                $data['birth_date'] = $birthDate;
            } else {
                $errors[] = $validation['error'];
            }
        }

        if (isset($_POST['bio'])) {
            $bio = sanitize($_POST['bio']);
            if (strlen($bio) <= 1000) {
                $data['bio'] = $bio;
            } else {
                $errors[] = 'Биография слишком длинная (максимум 1000 символов)';
            }
        }

        if (isset($_POST['country'])) {
            $data['country'] = sanitize($_POST['country']);
        }

        if (isset($_POST['city'])) {
            $data['city'] = sanitize($_POST['city']);
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                flash('error', $error);
            }
            $this->redirect('/profile/edit');
            return;
        }

        if (!empty($data)) {
            $profile->update($data);
            flash('success', 'Профиль успешно обновлен');
        }

        $this->redirect('/profile');
    }

    public function changePassword(): void
    {
        requireAuth();

        if (!isset($_POST['csrf_token']) || !$this->validateCsrf($_POST['csrf_token'])) {
            $this->redirect('/profile/edit');
            return;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $user = User::findById($_SESSION['user_id']);
        if (!$user) {
            flash('error', 'Пользователь не найден');
            $this->redirect('/profile/edit');
            return;
        }

        if (!$user->verifyPassword($currentPassword)) {
            flash('error', 'Текущий пароль неверен');
            $this->redirect('/profile/edit');
            return;
        }

        $passwordValidation = ValidationService::validatePassword($newPassword, $confirmPassword);
        if (!$passwordValidation['valid']) {
            flash('error', $passwordValidation['error']);
            $this->redirect('/profile/edit');
            return;
        }

        if ($user->updatePassword($newPassword)) {
            flash('success', 'Пароль успешно изменен');
        } else {
            flash('error', 'Ошибка при изменении пароля');
        }

        $this->redirect('/profile/edit');
    }
}



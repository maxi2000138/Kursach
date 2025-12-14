<?php

class AdminController extends BaseController
{
    public function index(): void
    {
        requireRole('admin');
        
        $users = $this->_getAllUsers();
        $stats = $this->_getStats();
        
        $this->render('admin/index', [
            'users' => $users,
            'stats' => $stats
        ]);
    }

    public function toggleUserStatus(): void
    {
        requireRole('admin');
        
        if (!isset($_POST['csrf_token']) || !$this->validateCsrf($_POST['csrf_token'])) {
            $this->redirect('/admin');
            return;
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        if (!$userId) {
            flash('error', 'Неверный ID пользователя');
            $this->redirect('/admin');
            return;
        }

        $user = User::findById($userId);
        if (!$user) {
            flash('error', 'Пользователь не найден');
            $this->redirect('/admin');
            return;
        }

        $newStatus = !$user->isActive();
        Database::execute(
            "UPDATE users SET is_active = ? WHERE id = ?",
            [$newStatus ? 1 : 0, $userId]
        );

        flash('success', $newStatus ? 'Пользователь разблокирован' : 'Пользователь заблокирован');
        $this->redirect('/admin');
    }

    public function assignRole(): void
    {
        requireRole('admin');
        
        if (!isset($_POST['csrf_token']) || !$this->validateCsrf($_POST['csrf_token'])) {
            $this->redirect('/admin');
            return;
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $roleName = sanitize($_POST['role'] ?? '');

        if (!$userId || empty($roleName)) {
            flash('error', 'Неверные данные');
            $this->redirect('/admin');
            return;
        }

        $result = Role::assignToUser($userId, $roleName);
        if ($result) {
            flash('success', "Роль {$roleName} назначена");
        } else {
            flash('error', 'Ошибка при назначении роли');
        }

        $this->redirect('/admin');
    }

    private function _getAllUsers(): array
    {
        $users = Database::fetchAll(
            "SELECT u.*, 
             GROUP_CONCAT(r.name) as roles,
             up.first_name, up.last_name
             FROM users u
             LEFT JOIN user_roles ur ON u.id = ur.user_id
             LEFT JOIN roles r ON ur.role_id = r.id
             LEFT JOIN user_profiles up ON u.id = up.user_id
             GROUP BY u.id
             ORDER BY u.created_at DESC"
        );

        foreach ($users as &$user) {
            $user['roles'] = $user['roles'] ? explode(',', $user['roles']) : [];
        }

        return $users;
    }

    private function _getStats(): array
    {
        $totalUsers = Database::fetchOne("SELECT COUNT(*) as count FROM users")['count'];
        $activeUsers = Database::fetchOne("SELECT COUNT(*) as count FROM users WHERE is_active = 1")['count'];
        $verifiedUsers = Database::fetchOne("SELECT COUNT(*) as count FROM users WHERE email_verified_at IS NOT NULL")['count'];
        $admins = Database::fetchOne(
            "SELECT COUNT(DISTINCT ur.user_id) as count 
             FROM user_roles ur 
             INNER JOIN roles r ON ur.role_id = r.id 
             WHERE r.name = 'admin'"
        )['count'];

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'verified_users' => $verifiedUsers,
            'admins' => $admins
        ];
    }
}



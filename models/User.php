<?php

class User
{
    private int $_id;
    private string $_email;
    private string $_passwordHash;
    private ?string $_emailVerifiedAt;
    private bool $_isActive;
    private string $_createdAt;
    private string $_updatedAt;

    public function __construct(array $data)
    {
        $this->_id = (int)$data['id'];
        $this->_email = $data['email'];
        $this->_passwordHash = $data['password_hash'];
        $this->_emailVerifiedAt = $data['email_verified_at'] ?? null;
        $this->_isActive = (bool)($data['is_active'] ?? true);
        $this->_createdAt = $data['created_at'];
        $this->_updatedAt = $data['updated_at'];
    }

    public function getId(): int
    {
        return $this->_id;
    }

    public function getEmail(): string
    {
        return $this->_email;
    }

    public function getPasswordHash(): string
    {
        return $this->_passwordHash;
    }

    public function isEmailVerified(): bool
    {
        return $this->_emailVerifiedAt !== null;
    }

    public function isActive(): bool
    {
        return $this->_isActive;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->_passwordHash);
    }

    public static function findByEmail(string $email): ?self
    {
        $data = Database::fetchOne(
            "SELECT * FROM users WHERE email = ?",
            [$email]
        );
        return $data ? new self($data) : null;
    }

    public static function findById(int $id): ?self
    {
        $data = Database::fetchOne(
            "SELECT * FROM users WHERE id = ?",
            [$id]
        );
        return $data ? new self($data) : null;
    }

    public static function create(string $email, string $password): ?self
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (email, password_hash) VALUES (?, ?)";
        Database::query($sql, [$email, $passwordHash]);
        
        $userId = (int)Database::lastInsertId();
        return self::findById($userId);
    }

    public function markEmailAsVerified(): bool
    {
        return Database::execute(
            "UPDATE users SET email_verified_at = NOW() WHERE id = ?",
            [$this->_id]
        );
    }

    public function updatePassword(string $newPassword): bool
    {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        return Database::execute(
            "UPDATE users SET password_hash = ? WHERE id = ?",
            [$passwordHash, $this->_id]
        );
    }

    public function getRoles(): array
    {
        $roles = Database::fetchAll(
            "SELECT r.name FROM roles r 
             INNER JOIN user_roles ur ON r.id = ur.role_id 
             WHERE ur.user_id = ?",
            [$this->_id]
        );
        return array_column($roles, 'name');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->_id,
            'email' => $this->_email,
            'email_verified_at' => $this->_emailVerifiedAt,
            'is_active' => $this->_isActive,
            'created_at' => $this->_createdAt,
            'roles' => $this->getRoles()
        ];
    }
}



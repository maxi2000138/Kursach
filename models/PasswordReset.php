<?php

class PasswordReset
{
    private int $_id;
    private int $_userId;
    private string $_token;
    private string $_expiresAt;
    private ?string $_usedAt;
    private string $_createdAt;

    public function __construct(array $data)
    {
        $this->_id = (int)$data['id'];
        $this->_userId = (int)$data['user_id'];
        $this->_token = $data['token'];
        $this->_expiresAt = $data['expires_at'];
        $this->_usedAt = $data['used_at'] ?? null;
        $this->_createdAt = $data['created_at'];
    }

    public function getToken(): string
    {
        return $this->_token;
    }

    public function getUserId(): int
    {
        return $this->_userId;
    }

    public function isExpired(): bool
    {
        return strtotime($this->_expiresAt) < time();
    }

    public function isUsed(): bool
    {
        return $this->_usedAt !== null;
    }

    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isUsed();
    }

    public static function findByToken(string $token): ?self
    {
        $data = Database::fetchOne(
            "SELECT * FROM password_resets WHERE token = ?",
            [$token]
        );
        return $data ? new self($data) : null;
    }

    public static function create(int $userId, string $token, int $expirySeconds = 3600): ?self
    {
        $expiresAt = date('Y-m-d H:i:s', time() + $expirySeconds);
        
        $sql = "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)";
        Database::query($sql, [$userId, $token, $expiresAt]);
        
        $id = (int)Database::lastInsertId();
        $data = Database::fetchOne("SELECT * FROM password_resets WHERE id = ?", [$id]);
        return $data ? new self($data) : null;
    }

    public function markAsUsed(): bool
    {
        return Database::execute(
            "UPDATE password_resets SET used_at = NOW() WHERE id = ?",
            [$this->_id]
        );
    }

    public static function invalidateUserTokens(int $userId): void
    {
        Database::execute(
            "UPDATE password_resets SET used_at = NOW() 
             WHERE user_id = ? AND used_at IS NULL AND expires_at > NOW()",
            [$userId]
        );
    }
}



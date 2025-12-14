<?php

class EmailVerification
{
    private int $_id;
    private int $_userId;
    private string $_token;
    private string $_expiresAt;
    private ?string $_verifiedAt;
    private string $_createdAt;

    public function __construct(array $data)
    {
        $this->_id = (int)$data['id'];
        $this->_userId = (int)$data['user_id'];
        $this->_token = $data['token'];
        $this->_expiresAt = $data['expires_at'];
        $this->_verifiedAt = $data['verified_at'] ?? null;
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

    public function isVerified(): bool
    {
        return $this->_verifiedAt !== null;
    }

    public static function findByToken(string $token): ?self
    {
        $data = Database::fetchOne(
            "SELECT * FROM email_verifications WHERE token = ?",
            [$token]
        );
        return $data ? new self($data) : null;
    }

    public static function findByUserId(int $userId): ?self
    {
        $data = Database::fetchOne(
            "SELECT * FROM email_verifications 
             WHERE user_id = ? AND verified_at IS NULL AND expires_at > NOW()
             ORDER BY created_at DESC LIMIT 1",
            [$userId]
        );
        return $data ? new self($data) : null;
    }

    public static function create(int $userId, string $token, int $expirySeconds = 3600): ?self
    {
        $expiresAt = date('Y-m-d H:i:s', time() + $expirySeconds);
        
        $sql = "INSERT INTO email_verifications (user_id, token, expires_at) VALUES (?, ?, ?)";
        Database::query($sql, [$userId, $token, $expiresAt]);
        
        $id = (int)Database::lastInsertId();
        $data = Database::fetchOne("SELECT * FROM email_verifications WHERE id = ?", [$id]);
        return $data ? new self($data) : null;
    }

    public function markAsVerified(): bool
    {
        return Database::execute(
            "UPDATE email_verifications SET verified_at = NOW() WHERE id = ?",
            [$this->_id]
        );
    }

    public static function invalidateUserTokens(int $userId): void
    {
        Database::execute(
            "UPDATE email_verifications SET verified_at = NOW() 
             WHERE user_id = ? AND verified_at IS NULL",
            [$userId]
        );
    }
}



<?php

class Role
{
    private int $_id;
    private string $_name;
    private ?string $_description;
    private string $_createdAt;

    public function __construct(array $data)
    {
        $this->_id = (int)$data['id'];
        $this->_name = $data['name'];
        $this->_description = $data['description'] ?? null;
        $this->_createdAt = $data['created_at'];
    }

    public function getName(): string
    {
        return $this->_name;
    }

    public static function findByName(string $name): ?self
    {
        $data = Database::fetchOne(
            "SELECT * FROM roles WHERE name = ?",
            [$name]
        );
        return $data ? new self($data) : null;
    }

    public static function assignToUser(int $userId, string $roleName): bool
    {
        $role = self::findByName($roleName);
        if (!$role) {
            return false;
        }

        $existing = Database::fetchOne(
            "SELECT id FROM user_roles WHERE user_id = ? AND role_id = ?",
            [$userId, $role->_id]
        );

        if ($existing) {
            return true;
        }

        return Database::execute(
            "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)",
            [$userId, $role->_id]
        );
    }
}



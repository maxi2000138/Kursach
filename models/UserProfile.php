<?php

class UserProfile
{
    private int $_id;
    private int $_userId;
    private ?string $_firstName;
    private ?string $_lastName;
    private ?string $_phone;
    private ?string $_avatarPath;
    private ?string $_birthDate;
    private ?string $_bio;
    private ?string $_country;
    private ?string $_city;
    private string $_createdAt;
    private string $_updatedAt;

    public function __construct(array $data)
    {
        $this->_id = (int)$data['id'];
        $this->_userId = (int)$data['user_id'];
        $this->_firstName = $data['first_name'] ?? null;
        $this->_lastName = $data['last_name'] ?? null;
        $this->_phone = $data['phone'] ?? null;
        $this->_avatarPath = $data['avatar_path'] ?? null;
        $this->_birthDate = $data['birth_date'] ?? null;
        $this->_bio = $data['bio'] ?? null;
        $this->_country = $data['country'] ?? null;
        $this->_city = $data['city'] ?? null;
        $this->_createdAt = $data['created_at'];
        $this->_updatedAt = $data['updated_at'];
    }

    public static function findByUserId(int $userId): ?self
    {
        $data = Database::fetchOne(
            "SELECT * FROM user_profiles WHERE user_id = ?",
            [$userId]
        );
        return $data ? new self($data) : null;
    }

    public static function create(int $userId): ?self
    {
        $sql = "INSERT INTO user_profiles (user_id) VALUES (?)";
        Database::query($sql, [$userId]);
        
        $profileId = (int)Database::lastInsertId();
        $data = Database::fetchOne("SELECT * FROM user_profiles WHERE id = ?", [$profileId]);
        return $data ? new self($data) : null;
    }

    public function update(array $data): bool
    {
        $allowedFields = ['first_name', 'last_name', 'phone', 'birth_date', 'bio', 'country', 'city'];
        $updates = [];
        $params = [];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updates[] = "{$field} = ?";
                $params[] = $data[$field] ?: null;
            }
        }

        if (empty($updates)) {
            return false;
        }

        $params[] = $this->_userId;
        $sql = "UPDATE user_profiles SET " . implode(', ', $updates) . " WHERE user_id = ?";
        return Database::execute($sql, $params);
    }

    public function updateAvatar(string $avatarPath): bool
    {
        return Database::execute(
            "UPDATE user_profiles SET avatar_path = ? WHERE user_id = ?",
            [$avatarPath, $this->_userId]
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->_id,
            'user_id' => $this->_userId,
            'first_name' => $this->_firstName,
            'last_name' => $this->_lastName,
            'phone' => $this->_phone,
            'avatar_path' => $this->_avatarPath,
            'birth_date' => $this->_birthDate,
            'bio' => $this->_bio,
            'country' => $this->_country,
            'city' => $this->_city,
            'created_at' => $this->_createdAt,
            'updated_at' => $this->_updatedAt
        ];
    }

    public function getFirstName(): ?string
    {
        return $this->_firstName;
    }

    public function getLastName(): ?string
    {
        return $this->_lastName;
    }

    public function getFullName(): string
    {
        $parts = array_filter([$this->_firstName, $this->_lastName]);
        return implode(' ', $parts) ?: 'Пользователь';
    }
}


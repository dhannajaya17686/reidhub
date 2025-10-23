<?php
class Admin extends Model
{
    protected $table = 'admins';

    /**
     * Find an admin by email.
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Verify admin credentials.
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function verify(string $email, string $password): bool
    {
        $admin = $this->findByEmail($email);
        return $admin ? password_verify($password, $admin['password']) : false;
    }

    /**
     * Get admin by primary key
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}

<?php
/**
 * User Model
 */

require_once CONFIG_PATH . '/database.php';

class User {
    protected $table = 'users';
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get all users
     */
    public function all($active = true) {
        $query = "SELECT * FROM {$this->table}";
        $params = [];

        if ($active) {
            $query .= " WHERE is_active = 1 AND deleted_at IS NULL";
        } else {
            $query .= " WHERE deleted_at IS NULL";
        }

        $query .= " ORDER BY created_at DESC";

        return $this->db->fetchAll($query, $params);
    }

    /**
     * Get user by ID
     */
    public function findById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ? AND deleted_at IS NULL";
        return $this->db->fetchOne($query, [$id]);
    }

    /**
     * Get user by username
     */
    public function findByUsername($username) {
        $query = "SELECT * FROM {$this->table} WHERE username = ? AND deleted_at IS NULL";
        return $this->db->fetchOne($query, [$username]);
    }

    /**
     * Get user by email
     */
    public function findByEmail($email) {
        $query = "SELECT * FROM {$this->table} WHERE email = ? AND deleted_at IS NULL";
        return $this->db->fetchOne($query, [$email]);
    }

    /**
     * Create user
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} (username, email, password, full_name, phone, address, role, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        return $this->db->insert($query, [
            $data['username'],
            $data['email'],
            $data['password'],
            $data['full_name'],
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $data['role'] ?? ROLE_CASHIER,
            $data['is_active'] ?? 1
        ]);
    }

    /**
     * Update user
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} SET ";
        $updates = [];
        $params = [];

        $allowedFields = ['username', 'email', 'password', 'full_name', 'phone', 'address', 'role', 'is_active'];

        foreach ($data as $field => $value) {
            if (in_array($field, $allowedFields)) {
                $updates[] = "$field = ?";
                $params[] = $value;
            }
        }

        if (empty($updates)) {
            return false;
        }

        $query .= implode(', ', $updates) . " WHERE id = ?";
        $params[] = $id;

        return $this->db->rowCount($query, $params) > 0;
    }

    /**
     * Delete user (soft delete)
     */
    public function delete($id) {
        $query = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        return $this->db->rowCount($query, [$id]) > 0;
    }

    /**
     * Get user count
     */
    public function count() {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE deleted_at IS NULL";
        $result = $this->db->fetchOne($query);
        return $result['count'] ?? 0;
    }

    /**
     * Check if username exists
     */
    public function usernameExists($username, $exceptId = null) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE username = ? AND deleted_at IS NULL";
        $params = [$username];

        if ($exceptId) {
            $query .= " AND id != ?";
            $params[] = $exceptId;
        }

        $result = $this->db->fetchOne($query, $params);
        return $result['count'] > 0;
    }

    /**
     * Check if email exists
     */
    public function emailExists($email, $exceptId = null) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = ? AND deleted_at IS NULL";
        $params = [$email];

        if ($exceptId) {
            $query .= " AND id != ?";
            $params[] = $exceptId;
        }

        $result = $this->db->fetchOne($query, $params);
        return $result['count'] > 0;
    }
}

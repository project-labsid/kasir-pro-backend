<?php
/**
 * Category Model
 */

require_once CONFIG_PATH . '/database.php';

class Category {
    protected $table = 'categories';
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get all categories
     */
    public function all($active = true) {
        $query = "SELECT * FROM {$this->table}";
        $params = [];

        if ($active) {
            $query .= " WHERE is_active = 1 AND deleted_at IS NULL";
        } else {
            $query .= " WHERE deleted_at IS NULL";
        }

        $query .= " ORDER BY name ASC";

        return $this->db->fetchAll($query, $params);
    }

    /**
     * Get category by ID
     */
    public function findById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ? AND deleted_at IS NULL";
        return $this->db->fetchOne($query, [$id]);
    }

    /**
     * Create category
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} (name, description, image_url, is_active) VALUES (?, ?, ?, ?)";
        
        return $this->db->insert($query, [
            $data['name'],
            $data['description'] ?? null,
            $data['image_url'] ?? null,
            $data['is_active'] ?? 1
        ]);
    }

    /**
     * Update category
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} SET ";
        $updates = [];
        $params = [];

        $allowedFields = ['name', 'description', 'image_url', 'is_active'];

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
     * Delete category (soft delete)
     */
    public function delete($id) {
        $query = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        return $this->db->rowCount($query, [$id]) > 0;
    }
}

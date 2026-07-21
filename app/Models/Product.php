<?php
/**
 * Product Model
 */

require_once CONFIG_PATH . '/database.php';

class Product {
    protected $table = 'products';
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get all products
     */
    public function all($active = true, $limit = null, $offset = 0) {
        $query = "SELECT p.*, c.name as category_name FROM {$this->table} p LEFT JOIN categories c ON p.category_id = c.id";
        $params = [];

        if ($active) {
            $query .= " WHERE p.is_active = 1 AND p.deleted_at IS NULL";
        } else {
            $query .= " WHERE p.deleted_at IS NULL";
        }

        $query .= " ORDER BY p.created_at DESC";

        if ($limit) {
            $query .= " LIMIT $limit OFFSET $offset";
        }

        return $this->db->fetchAll($query, $params);
    }

    /**
     * Get product by ID
     */
    public function findById($id) {
        $query = "SELECT p.*, c.name as category_name FROM {$this->table} p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ? AND p.deleted_at IS NULL";
        return $this->db->fetchOne($query, [$id]);
    }

    /**
     * Get product by barcode
     */
    public function findByBarcode($barcode) {
        $query = "SELECT p.*, c.name as category_name FROM {$this->table} p LEFT JOIN categories c ON p.category_id = c.id WHERE p.barcode = ? AND p.deleted_at IS NULL";
        return $this->db->fetchOne($query, [$barcode]);
    }

    /**
     * Get products by category
     */
    public function getByCategory($categoryId, $active = true) {
        $query = "SELECT * FROM {$this->table} WHERE category_id = ?";
        $params = [$categoryId];

        if ($active) {
            $query .= " AND is_active = 1 AND deleted_at IS NULL";
        } else {
            $query .= " AND deleted_at IS NULL";
        }

        $query .= " ORDER BY name ASC";

        return $this->db->fetchAll($query, $params);
    }

    /**
     * Search products
     */
    public function search($keyword, $active = true) {
        $query = "SELECT * FROM {$this->table} WHERE (name LIKE ? OR barcode LIKE ? OR description LIKE ?)";
        $params = ["%$keyword%", "%$keyword%", "%$keyword%"];

        if ($active) {
            $query .= " AND is_active = 1 AND deleted_at IS NULL";
        } else {
            $query .= " AND deleted_at IS NULL";
        }

        $query .= " ORDER BY name ASC";

        return $this->db->fetchAll($query, $params);
    }

    /**
     * Create product
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} (category_id, barcode, name, description, image_url, purchase_price, selling_price, current_stock, minimum_stock, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        return $this->db->insert($query, [
            $data['category_id'],
            $data['barcode'],
            $data['name'],
            $data['description'] ?? null,
            $data['image_url'] ?? null,
            $data['purchase_price'],
            $data['selling_price'],
            $data['current_stock'] ?? 0,
            $data['minimum_stock'] ?? 10,
            $data['is_active'] ?? 1
        ]);
    }

    /**
     * Update product
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} SET ";
        $updates = [];
        $params = [];

        $allowedFields = ['category_id', 'barcode', 'name', 'description', 'image_url', 'purchase_price', 'selling_price', 'current_stock', 'minimum_stock', 'is_active'];

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
     * Delete product (soft delete)
     */
    public function delete($id) {
        $query = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        return $this->db->rowCount($query, [$id]) > 0;
    }

    /**
     * Get low stock products
     */
    public function getLowStock() {
        $query = "SELECT * FROM {$this->table} WHERE current_stock <= minimum_stock AND is_active = 1 AND deleted_at IS NULL ORDER BY current_stock ASC";
        return $this->db->fetchAll($query);
    }

    /**
     * Get best selling products
     */
    public function getBestSelling($limit = 10) {
        $query = "SELECT p.*, COALESCE(SUM(ti.quantity), 0) as total_sold FROM {$this->table} p LEFT JOIN transaction_items ti ON p.id = ti.product_id LEFT JOIN transactions t ON ti.transaction_id = t.id WHERE p.is_active = 1 AND p.deleted_at IS NULL AND t.status = 'completed' GROUP BY p.id ORDER BY total_sold DESC LIMIT $limit";
        return $this->db->fetchAll($query);
    }

    /**
     * Update stock
     */
    public function updateStock($id, $quantity) {
        $query = "UPDATE {$this->table} SET current_stock = ? WHERE id = ?";
        return $this->db->rowCount($query, [$quantity, $id]) > 0;
    }

    /**
     * Adjust stock
     */
    public function adjustStock($id, $adjustment) {
        $query = "UPDATE {$this->table} SET current_stock = current_stock + ? WHERE id = ?";
        return $this->db->rowCount($query, [$adjustment, $id]) > 0;
    }
}

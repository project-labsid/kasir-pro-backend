<?php
/**
 * Input Validation Helper
 */

require_once CONFIG_PATH . '/constants.php';

class Validation {
    private $errors = [];
    private $data = [];

    /**
     * Constructor
     */
    public function __construct($data = []) {
        $this->data = $data;
    }

    /**
     * Validate required field
     */
    public function required($field, $label = null) {
        $label = $label ?? ucfirst(str_replace('_', ' ', $field));
        if (empty($this->data[$field] ?? null)) {
            $this->errors[$field] = "$label is required";
        }
        return $this;
    }

    /**
     * Validate email
     */
    public function email($field, $label = null) {
        $label = $label ?? ucfirst(str_replace('_', ' ', $field));
        if (!empty($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "$label must be a valid email";
        }
        return $this;
    }

    /**
     * Validate numeric
     */
    public function numeric($field, $label = null) {
        $label = $label ?? ucfirst(str_replace('_', ' ', $field));
        if (!empty($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field] = "$label must be numeric";
        }
        return $this;
    }

    /**
     * Validate min length
     */
    public function minLength($field, $length, $label = null) {
        $label = $label ?? ucfirst(str_replace('_', ' ', $field));
        if (!empty($this->data[$field]) && strlen($this->data[$field]) < $length) {
            $this->errors[$field] = "$label must be at least $length characters";
        }
        return $this;
    }

    /**
     * Validate max length
     */
    public function maxLength($field, $length, $label = null) {
        $label = $label ?? ucfirst(str_replace('_', ' ', $field));
        if (!empty($this->data[$field]) && strlen($this->data[$field]) > $length) {
            $this->errors[$field] = "$label must not exceed $length characters";
        }
        return $this;
    }

    /**
     * Validate matches
     */
    public function matches($field, $matchField, $label = null) {
        $label = $label ?? ucfirst(str_replace('_', ' ', $field));
        if ($this->data[$field] !== ($this->data[$matchField] ?? null)) {
            $this->errors[$field] = "$label does not match";
        }
        return $this;
    }

    /**
     * Validate unique in database
     */
    public function unique($field, $table, $column = null, $exceptId = null) {
        $column = $column ?? $field;
        $value = $this->data[$field] ?? null;
        
        if (empty($value)) {
            return $this;
        }

        $query = "SELECT COUNT(*) as count FROM $table WHERE $column = ?";
        $params = [$value];

        if ($exceptId) {
            $query .= " AND id != ?";
            $params[] = $exceptId;
        }

        $result = Database::getInstance()->fetchOne($query, $params);
        if ($result['count'] > 0) {
            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " already exists";
        }
        return $this;
    }

    /**
     * Validate file upload
     */
    public function file($field, $allowedTypes = [], $maxSize = MAX_UPLOAD_SIZE) {
        if (empty($_FILES[$field] ?? null) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
            return $this;
        }

        if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
            $this->errors[$field] = 'File upload error';
            return $this;
        }

        if ($_FILES[$field]['size'] > $maxSize) {
            $this->errors[$field] = 'File size exceeds limit';
            return $this;
        }

        if (!empty($allowedTypes)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES[$field]['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) {
                $this->errors[$field] = 'File type not allowed';
            }
        }

        return $this;
    }

    /**
     * Check if validation passes
     */
    public function passes() {
        return empty($this->errors);
    }

    /**
     * Check if validation fails
     */
    public function fails() {
        return !$this->passes();
    }

    /**
     * Get errors
     */
    public function errors() {
        return $this->errors;
    }

    /**
     * Get first error
     */
    public function getError($field) {
        return $this->errors[$field] ?? null;
    }

    /**
     * Get all errors as array
     */
    public function getErrors() {
        return $this->errors;
    }
}

require_once CONFIG_PATH . '/database.php';

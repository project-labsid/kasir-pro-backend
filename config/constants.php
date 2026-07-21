<?php
/**
 * Application Constants
 */

// Application
define('APP_NAME', 'Kasir Pro POS');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'production');

// Paths
define('ROOT_PATH', dirname(dirname(__FILE__)));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('HELPER_PATH', ROOT_PATH . '/helpers');
define('UPLOAD_PATH', ROOT_PATH . '/assets/uploads');
define('LOG_PATH', ROOT_PATH . '/logs');
define('CACHE_PATH', ROOT_PATH . '/cache');
define('DATABASE_PATH', ROOT_PATH . '/database');

// Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'kasir_pro');
define('DB_PORT', 3306);
define('DB_CHARSET', 'utf8mb4');

// Session
define('SESSION_TIMEOUT', 3600); // 1 hour
define('SESSION_NAME', 'kasir_pro_session');
define('CSRF_TOKEN_NAME', 'csrf_token');

// Security
define('ENCRYPTION_KEY', 'your-secret-encryption-key-change-this');
define('PASSWORD_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_COST', 12);

// File Upload
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpeg', 'jpg', 'png', 'gif']);
define('ALLOWED_FILE_TYPES', ['csv', 'xlsx', 'xls', 'pdf']);

// Pagination
define('ITEMS_PER_PAGE', 20);

// API Response Codes
define('API_SUCCESS', 200);
define('API_CREATED', 201);
define('API_BAD_REQUEST', 400);
define('API_UNAUTHORIZED', 401);
define('API_FORBIDDEN', 403);
define('API_NOT_FOUND', 404);
define('API_CONFLICT', 409);
define('API_INTERNAL_ERROR', 500);

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_CASHIER', 'cashier');

// Transaction Status
define('TRANSACTION_PENDING', 'pending');
define('TRANSACTION_COMPLETED', 'completed');
define('TRANSACTION_CANCELLED', 'cancelled');
define('TRANSACTION_HELD', 'held');

// Payment Methods
define('PAYMENT_CASH', 'cash');
define('PAYMENT_QRIS', 'qris');
define('PAYMENT_BANK_TRANSFER', 'bank_transfer');
define('PAYMENT_DEBIT_CARD', 'debit_card');

// Shift Status
define('SHIFT_OPENED', 'opened');
define('SHIFT_CLOSED', 'closed');

// Audit Actions
define('ACTION_LOGIN', 'login');
define('ACTION_LOGOUT', 'logout');
define('ACTION_CREATE', 'create');
define('ACTION_UPDATE', 'update');
define('ACTION_DELETE', 'delete');
define('ACTION_BACKUP', 'backup');
define('ACTION_RESTORE', 'restore');

// Time Zone
define('TIMEZONE', 'Asia/Jakarta');
date_default_timezone_set(TIMEZONE);

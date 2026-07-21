<?php
/**
 * Utility Helper Functions
 */

require_once CONFIG_PATH . '/constants.php';

class Utils {
    /**
     * Format currency
     */
    public static function formatCurrency($amount, $currency = 'IDR') {
        return $currency . ' ' . number_format($amount, 2, ',', '.');
    }

    /**
     * Format date
     */
    public static function formatDate($date, $format = 'd/m/Y') {
        return date($format, strtotime($date));
    }

    /**
     * Format datetime
     */
    public static function formatDateTime($datetime, $format = 'd/m/Y H:i:s') {
        return date($format, strtotime($datetime));
    }

    /**
     * Generate barcode
     */
    public static function generateBarcode($length = 12) {
        return str_pad(rand(0, 999999999999), $length, '0', STR_PAD_LEFT);
    }

    /**
     * Generate unique ID
     */
    public static function generateUniqueId($prefix = '') {
        return $prefix . time() . random_int(1000, 9999);
    }

    /**
     * Truncate text
     */
    public static function truncate($text, $length = 100, $end = '...') {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length - strlen($end)) . $end;
    }

    /**
     * Format file size
     */
    public static function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Generate slug
     */
    public static function generateSlug($text) {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^\w\s-]/', '', $text);
        $text = preg_replace('/[\s_-]+/', '-', $text);
        $text = preg_replace('/^-+|-+$/', '', $text);
        return $text;
    }

    /**
     * Get file extension
     */
    public static function getFileExtension($filename) {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * Generate random color
     */
    public static function generateRandomColor() {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Convert array to CSV
     */
    public static function arrayToCSV($data) {
        $output = fopen('php://temp', 'r+');
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        return stream_get_contents($output);
    }

    /**
     * Get months array
     */
    public static function getMonths() {
        return [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
    }

    /**
     * Get current month name
     */
    public static function getCurrentMonthName() {
        $months = self::getMonths();
        return $months[date('n')];
    }

    /**
     * Calculate percentage
     */
    public static function calculatePercentage($value, $total) {
        return $total > 0 ? ($value / $total) * 100 : 0;
    }

    /**
     * Generate JSON response
     */
    public static function jsonResponse($success = true, $data = [], $message = '', $code = 200) {
        header('Content-Type: application/json');
        http_response_code($code);
        return json_encode([
            'success' => $success,
            'data' => $data,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Redirect
     */
    public static function redirect($url, $statusCode = 302) {
        header('Location: ' . $url, true, $statusCode);
        exit();
    }

    /**
     * Get query parameter
     */
    public static function getQuery($key, $default = null) {
        return $_GET[$key] ?? $default;
    }

    /**
     * Get post parameter
     */
    public static function getPost($key, $default = null) {
        return $_POST[$key] ?? $default;
    }
}

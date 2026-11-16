<?php
namespace App\Core;

/**
 * Class Helper
 * Utility serbaguna untuk formatting, URL, flash messages, dan sanitasi
 */
class Helper {
    /**
     * Menghasilkan URL sistem (Semester 3 Friendly query string: index.php?page=xxx&action=yyy)
     */
    public static function url(string $page = 'dashboard', array $params = []): string {
        $query = ['page' => $page];
        if (!empty($_GET['dev_role'])) {
            $query['dev_role'] = $_GET['dev_role'];
        }
        if (!empty($params)) {
            $query = array_merge($query, $params);
        }
        return 'index.php?' . http_build_query($query);
    }

    /**
     * Menghasilkan path aset statis (CSS, JS, Gambar)
     */
    public static function asset(string $path): string {
        $cleanPath = ltrim($path, '/');
        return 'assets/' . $cleanPath;
    }

    /**
     * Format angka ke Rupiah Indonesia (Contoh: Rp 25.000)
     */
    public static function rupiah($angka, bool $withRp = true): string {
        $num = (float)$angka;
        $formatted = number_format($num, 0, ',', '.');
        return $withRp ? 'Rp ' . $formatted : $formatted;
    }

    /**
     * Format tanggal ke Bahasa Indonesia lengkap dengan hari
     */
    public static function tanggalIndonesia($datetime = null, bool $withDay = true): string {
        $ts = $datetime ? (is_numeric($datetime) ? (int)$datetime : strtotime($datetime)) : time();
        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $dayName = $hari[date('w', $ts)];
        $d = date('d', $ts);
        $m = (int)date('m', $ts);
        $y = date('Y', $ts);
        return ($withDay ? "{$dayName}, " : "") . "{$d} {$bulan[$m]} {$y}";
    }

    /**
     * Format tanggal ke Bahasa Indonesia
     */
    public static function formatDate($datetime, bool $withTime = false): string {
        if (empty($datetime)) {
            return '-';
        }

        $timestamp = is_numeric($datetime) ? (int)$datetime : strtotime($datetime);
        if (!$timestamp) {
            return '-';
        }

        $bulanIndo = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $d = date('d', $timestamp);
        $m = (int)date('m', $timestamp);
        $y = date('Y', $timestamp);

        $result = "{$d} {$bulanIndo[$m]} {$y}";

        if ($withTime) {
            $result .= ' ' . date('H:i', $timestamp) . ' WIB';
        }

        return $result;
    }

    /**
     * Sanitasi input string untuk mencegah XSS
     */
    public static function sanitize(?string $str): string {
        if ($str === null) {
            return '';
        }
        return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Mengatur pesan notifikasi (Flash Message)
     */
    public static function setFlash(string $type, string $message): void {
        Auth::init();
        $_SESSION['flash'][$type] = $message;
    }

    /**
     * Mengambil dan menghapus pesan notifikasi (Flash Message)
     */
    public static function getFlash(string $type): ?string {
        Auth::init();
        if (isset($_SESSION['flash'][$type])) {
            $msg = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $msg;
        }
        return null;
    }

    /**
     * Cek apakah ada flash message
     */
    public static function hasFlash(string $type): bool {
        Auth::init();
        return isset($_SESSION['flash'][$type]);
    }

    /**
     * Redirect ke halaman tertentu
     */
    public static function redirect(string $url): void {
        header("Location: {$url}");
        exit;
    }

    /**
     * Mengembalikan response JSON (untuk request AJAX / API)
     */
    public static function jsonResponse($data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Generate Invoice Nomor Pesanan (Format: LG-YYMMDD-XXX)
     */
    public static function generateInvoice(): string {
        $prefix = 'LG-' . date('ymd') . '-';
        $sql = "SELECT id_pesanan FROM pesanan WHERE id_pesanan LIKE :prefix ORDER BY id_pesanan DESC LIMIT 1";
        $lastOrder = Database::fetch($sql, ['prefix' => $prefix . '%']);

        if ($lastOrder) {
            $lastNumber = (int)substr($lastOrder['id_pesanan'], -3);
            $newNumber = str_pad((string)($lastNumber + 1), 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return $prefix . $newNumber;
    }

    /**
     * Generate Sequential ID untuk Master Data (contoh: PEL-001, EXP-001)
     */
    public static function generateId(string $prefix, string $table, string $column, int $padLength = 3): string {
        // Gunakan ORDER BY LENGTH({$column}) DESC, {$column} DESC untuk menghindari string sorting collision (e.g. '099' vs '1000')
        $sql = "SELECT {$column} FROM {$table} WHERE {$column} LIKE :prefix ORDER BY LENGTH({$column}) DESC, {$column} DESC LIMIT 1";
        $lastRow = Database::fetch($sql, ['prefix' => $prefix . '%']);

        if ($lastRow) {
            $lastStr = $lastRow[$column];
            $numericPart = preg_replace('/[^0-9]/', '', substr($lastStr, strlen($prefix)));
            $nextNumber = ((int)$numericPart) + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad((string)$nextNumber, $padLength, '0', STR_PAD_LEFT);
    }

    /**
     * Hitung sisa waktu SLA (Countdown)
     * Mengembalikan array ['hours' => X, 'minutes' => Y, 'is_overdue' => bool, 'badge_class' => string, 'text' => string]
     */
    public static function calculateSla(?string $deadline): array {
        if (!$deadline) {
            return [
                'hours' => 0, 'minutes' => 0, 'is_overdue' => false,
                'badge_class' => 'badge-neutral', 'text' => 'Tanpa Deadline'
            ];
        }

        $now = time();
        $target = strtotime($deadline);
        $diff = $target - $now;

        if ($diff < 0) {
            $absDiff = abs($diff);
            $h = floor($absDiff / 3600);
            $m = floor(($absDiff % 3600) / 60);
            return [
                'hours' => $h, 'minutes' => $m, 'is_overdue' => true,
                'badge_class' => 'badge-danger',
                'text' => "Terlewat {$h}j {$m}m"
            ];
        }

        $h = floor($diff / 3600);
        $m = floor(($diff % 3600) / 60);

        if ($h < 2) {
            $badge = 'badge-danger';
        } elseif ($h < 6) {
            $badge = 'badge-warning';
        } else {
            $badge = 'badge-success';
        }

        return [
            'hours' => $h, 'minutes' => $m, 'is_overdue' => false,
            'badge_class' => $badge,
            'text' => "Sisa {$h}j {$m}m"
        ];
    }
}

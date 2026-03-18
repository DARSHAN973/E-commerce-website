<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/db.php';

function ensureAdminTables(PDO $conn): void
{
    $conn->exec("CREATE TABLE IF NOT EXISTS admin_users (
        id BIGSERIAL PRIMARY KEY,
        username VARCHAR(100) UNIQUE NOT NULL,
        password_hash TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT NOW(),
        updated_at TIMESTAMP DEFAULT NOW()
    )");

    $conn->exec("CREATE TABLE IF NOT EXISTS home_banners (
        id BIGSERIAL PRIMARY KEY,
        title VARCHAR(200) NOT NULL,
        subtitle TEXT,
        button_text VARCHAR(100),
        button_link VARCHAR(255),
        image_url TEXT NOT NULL,
        sort_order INT NOT NULL DEFAULT 0,
        is_active BOOLEAN NOT NULL DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT NOW(),
        updated_at TIMESTAMP DEFAULT NOW()
    )");

    $exists = (int) $conn->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
    if ($exists === 0) {
        $stmt = $conn->prepare('INSERT INTO admin_users (username, password_hash) VALUES (?, ?)');
        $stmt->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT)]);
    }
}

function currentAdmin(): ?array
{
    return $_SESSION['admin_user_row'] ?? null;
}

function isAdminLoggedIn(): bool
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function adminLogin(PDO $conn, string $username, string $password): bool
{
    $stmt = $conn->prepare('SELECT * FROM admin_users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $row = $stmt->fetch();
    if (!$row) {
        return false;
    }

    if (!password_verify($password, (string) $row['password_hash'])) {
        return false;
    }

    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_user_row'] = $row;
    return true;
}

function adminLogout(): void
{
    unset($_SESSION['admin_logged_in'], $_SESSION['admin_user_row']);
}

function dbScalar(PDO $conn, string $sql, array $params = [], $default = 0)
{
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $val = $stmt->fetchColumn();
        return $val === false ? $default : $val;
    } catch (Throwable $e) {
        return $default;
    }
}

function dbRows(PDO $conn, string $sql, array $params = []): array
{
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
}

function cloudinaryConfig(): ?array
{
    $raw = getenv('CLOUDINARY_URL') ?: 'cloudinary://116518186394451:YQMeTHhwwYVs0D9sP_da5QZXpNM@dhzk2xuvt';
    $parts = parse_url($raw);
    if ($parts === false || empty($parts['host']) || empty($parts['user']) || empty($parts['pass'])) {
        return null;
    }

    return [
        'cloud_name' => $parts['host'],
        'api_key' => $parts['user'],
        'api_secret' => $parts['pass'],
    ];
}

function cloudinaryUploadFile(array $file, string $publicIdPrefix = 'admin'): ?array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    $cfg = cloudinaryConfig();
    if ($cfg === null) {
        return null;
    }

    $tmp = (string) $file['tmp_name'];
    if (!is_uploaded_file($tmp) && !is_file($tmp)) {
        return null;
    }

    $base = pathinfo((string) ($file['name'] ?? 'upload'), PATHINFO_FILENAME);
    $base = preg_replace('/[^A-Za-z0-9_-]+/', '-', strtolower((string) $base));
    $publicId = $publicIdPrefix . '-' . $base . '-' . time();

    $timestamp = time();
    $folder = 'stylique';
    $params = [
        'folder' => $folder,
        'public_id' => $publicId,
        'timestamp' => $timestamp,
    ];
    ksort($params);

    $chunks = [];
    foreach ($params as $k => $v) {
        $chunks[] = $k . '=' . $v;
    }
    $signature = sha1(implode('&', $chunks) . $cfg['api_secret']);

    $endpoint = 'https://api.cloudinary.com/v1_1/' . rawurlencode((string) $cfg['cloud_name']) . '/image/upload';
    $post = [
        'file' => new CURLFile($tmp),
        'api_key' => (string) $cfg['api_key'],
        'timestamp' => (string) $timestamp,
        'folder' => $folder,
        'public_id' => $publicId,
        'signature' => $signature,
    ];

    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $post,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 60,
    ]);

    $raw = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($raw === false || $code >= 400) {
        return null;
    }

    $json = json_decode((string) $raw, true);
    if (!is_array($json) || isset($json['error'])) {
        return null;
    }

    return $json;
}

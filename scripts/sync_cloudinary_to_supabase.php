<?php
/**
 * One-shot setup script:
 * 1) Creates required Supabase tables
 * 2) Uploads all files in /images to Cloudinary
 * 3) Stores all uploaded assets in media_assets
 * 4) Upserts product rows and sets products.image to Cloudinary URL
 *
 * Usage:
 *   CLOUDINARY_URL="cloudinary://<api_key>:<api_secret>@<cloud_name>" php scripts/sync_cloudinary_to_supabase.php
 */

declare(strict_types=1);

require __DIR__ . '/../includes/db.php';

$cloudinaryUrl = getenv('CLOUDINARY_URL') ?: '';
if ($cloudinaryUrl === '') {
    fwrite(STDERR, "CLOUDINARY_URL is missing.\n");
    exit(1);
}

$parts = parse_url($cloudinaryUrl);
if ($parts === false || empty($parts['host']) || empty($parts['user']) || empty($parts['pass'])) {
    fwrite(STDERR, "Invalid CLOUDINARY_URL format.\n");
    exit(1);
}

$cloudName = $parts['host'];
$apiKey = $parts['user'];
$apiSecret = $parts['pass'];
$imagesDir = realpath(__DIR__ . '/../images');

if ($imagesDir === false || !is_dir($imagesDir)) {
    fwrite(STDERR, "Images directory not found.\n");
    exit(1);
}

function logLine(string $message): void
{
    echo $message . PHP_EOL;
}

function cloudinaryUpload(string $filePath, string $cloudName, string $apiKey, string $apiSecret, string $publicId): array
{
    $timestamp = time();
    $folder = 'stylique';

    $paramsToSign = [
        'folder' => $folder,
        'public_id' => $publicId,
        'timestamp' => $timestamp,
    ];
    ksort($paramsToSign);

    $toSignParts = [];
    foreach ($paramsToSign as $k => $v) {
        $toSignParts[] = $k . '=' . $v;
    }
    $signature = sha1(implode('&', $toSignParts) . $apiSecret);

    $endpoint = 'https://api.cloudinary.com/v1_1/' . rawurlencode($cloudName) . '/image/upload';

    $ch = curl_init($endpoint);
    $payload = [
        'file' => new CURLFile($filePath),
        'api_key' => $apiKey,
        'timestamp' => (string) $timestamp,
        'folder' => $folder,
        'public_id' => $publicId,
        'signature' => $signature,
    ];

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 60,
    ]);

    $raw = curl_exec($ch);
    $curlErr = curl_error($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($raw === false) {
        throw new RuntimeException('Cloudinary curl failure: ' . $curlErr);
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        throw new RuntimeException('Cloudinary invalid JSON response (HTTP ' . $status . ')');
    }

    if ($status >= 400 || isset($decoded['error'])) {
        $msg = $decoded['error']['message'] ?? ('HTTP ' . $status);
        throw new RuntimeException('Cloudinary upload failed: ' . $msg);
    }

    return $decoded;
}

function productCategoryByNumber(int $n): string
{
    if ($n >= 1 && $n <= 12) {
        return 'special';
    }
    if ($n >= 13 && $n <= 24) {
        return 'men';
    }
    if ($n >= 25 && $n <= 36) {
        return 'women';
    }
    return 'collection';
}

function productNameByNumber(int $n): string
{
    return 'Stylique Product ' . $n;
}

function seedSchema(PDO $conn): void
{
    $schemaSql = file_get_contents(__DIR__ . '/../supabase_schema.sql');
    if ($schemaSql === false) {
        throw new RuntimeException('Unable to read supabase_schema.sql');
    }
    $conn->exec($schemaSql);

    $conn->exec(
        "CREATE TABLE IF NOT EXISTS media_assets (
            id BIGSERIAL PRIMARY KEY,
            filename VARCHAR(255) UNIQUE NOT NULL,
            public_id VARCHAR(255) NOT NULL,
            secure_url TEXT NOT NULL,
            width INT,
            height INT,
            bytes BIGINT,
            format VARCHAR(50),
            created_at TIMESTAMP DEFAULT NOW(),
            updated_at TIMESTAMP DEFAULT NOW()
        );"
    );

    $conn->exec(
        "CREATE OR REPLACE FUNCTION set_media_assets_updated_at()
         RETURNS TRIGGER AS $$
         BEGIN
             NEW.updated_at = NOW();
             RETURN NEW;
         END;
         $$ LANGUAGE plpgsql;"
    );

    $conn->exec("DROP TRIGGER IF EXISTS trg_media_assets_updated_at ON media_assets;");
    $conn->exec(
        "CREATE TRIGGER trg_media_assets_updated_at
         BEFORE UPDATE ON media_assets
         FOR EACH ROW EXECUTE FUNCTION set_media_assets_updated_at();"
    );
}

try {
    seedSchema($conn);
    logLine('Schema ready.');

    $files = array_values(array_filter(scandir($imagesDir) ?: [], static function ($f): bool {
        return $f !== '.' && $f !== '..';
    }));

    if (count($files) === 0) {
        logLine('No images found in images/.');
        exit(0);
    }

    $assetUpsert = $conn->prepare(
        "INSERT INTO media_assets (filename, public_id, secure_url, width, height, bytes, format)
         VALUES (?, ?, ?, ?, ?, ?, ?)
         ON CONFLICT (filename) DO UPDATE SET
             public_id = EXCLUDED.public_id,
             secure_url = EXCLUDED.secure_url,
             width = EXCLUDED.width,
             height = EXCLUDED.height,
             bytes = EXCLUDED.bytes,
             format = EXCLUDED.format,
             updated_at = NOW()"
    );

    $productUpsert = $conn->prepare(
        "INSERT INTO products (id, name, description, category, price, discount, stock, image)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)
         ON CONFLICT (id) DO UPDATE SET
             name = EXCLUDED.name,
             description = EXCLUDED.description,
             category = EXCLUDED.category,
             price = EXCLUDED.price,
             discount = EXCLUDED.discount,
             stock = EXCLUDED.stock,
             image = EXCLUDED.image"
    );

    $uploaded = 0;
    $productRows = 0;

    foreach ($files as $filename) {
        $fullPath = $imagesDir . DIRECTORY_SEPARATOR . $filename;
        if (!is_file($fullPath)) {
            continue;
        }

        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        $safePublic = preg_replace('/[^A-Za-z0-9_-]+/', '-', strtolower($baseName)) ?: 'asset';
        $publicId = 'stylique-' . $safePublic;

        $result = cloudinaryUpload($fullPath, $cloudName, $apiKey, $apiSecret, $publicId);

        $assetUpsert->execute([
            $filename,
            (string) ($result['public_id'] ?? $publicId),
            (string) ($result['secure_url'] ?? ''),
            isset($result['width']) ? (int) $result['width'] : null,
            isset($result['height']) ? (int) $result['height'] : null,
            isset($result['bytes']) ? (int) $result['bytes'] : null,
            isset($result['format']) ? (string) $result['format'] : null,
        ]);

        $uploaded++;

        if (preg_match('/^product(\d+)$/i', $baseName, $m)) {
            $n = (int) $m[1];
            $category = productCategoryByNumber($n);
            $name = productNameByNumber($n);
            $price = 899 + ($n * 50);
            $discount = ($n % 3 === 0) ? 120 : 0;
            $stock = 10 + ($n % 15);
            $description = 'Premium fashion pick from Stylique collection.';
            $imageUrl = (string) ($result['secure_url'] ?? '');

            $productUpsert->execute([
                $n,
                $name,
                $description,
                $category,
                $price,
                $discount,
                $stock,
                $imageUrl,
            ]);
            $productRows++;
        }

        logLine('Uploaded: ' . $filename);
    }

    $countProducts = (int) $conn->query('SELECT COUNT(*) FROM products')->fetchColumn();
    $countAssets = (int) $conn->query('SELECT COUNT(*) FROM media_assets')->fetchColumn();

    logLine('Done. Uploaded assets: ' . $uploaded);
    logLine('Product rows upserted from product*.webp: ' . $productRows);
    logLine('Total products in DB: ' . $countProducts);
    logLine('Total media_assets in DB: ' . $countAssets);
} catch (Throwable $e) {
    fwrite(STDERR, 'Error: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}

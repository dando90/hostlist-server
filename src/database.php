<?php
declare(strict_types=1);

function loadEnvironmentFile(string $path): void
{
    if (!is_readable($path)) return;

    foreach (file($path, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (str_starts_with($line, 'export ')) $line = substr($line, 7);

        $separator = strpos($line, '=');
        if ($separator === false) continue;
        $key = trim(substr($line, 0, $separator));
        if (!preg_match('/^[A-Z_][A-Z0-9_]*$/', $key) || getenv($key) !== false) continue;

        $value = trim(substr($line, $separator + 1));
        if (strlen($value) >= 2 && $value[0] === '"' && $value[-1] === '"') {
            $decoded = json_decode($value, true);
            if (is_string($decoded)) $value = $decoded;
        } elseif (strlen($value) >= 2 && $value[0] === "'" && $value[-1] === "'") {
            $value = substr($value, 1, -1);
        } else {
            $value = preg_replace('/\s+#.*$/', '', $value) ?? $value;
        }

        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

loadEnvironmentFile(dirname(__DIR__) . '/.env');

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    // Keep shared database credentials outside the public web root and load
    // them relative to this file.
    $privateConfigPath = getenv('HOSTLIST_PRIVATE_CONFIG')
        ?: dirname(__DIR__, 3) . '/private/config.php';

    if (is_file($privateConfigPath) && is_readable($privateConfigPath)) {
        $appConfig = require $privateConfigPath;
        $databaseConfig = is_array($appConfig) ? ($appConfig['database'] ?? null) : null;
        if (!is_array($databaseConfig)
            || !is_string($databaseConfig['dsn'] ?? null)
            || !is_string($databaseConfig['username'] ?? null)
            || !is_string($databaseConfig['password'] ?? null)) {
            throw new RuntimeException('The private config file does not contain valid database settings.');
        }

        $dsn = $databaseConfig['dsn'];
        $user = $databaseConfig['username'];
        $password = $databaseConfig['password'];
    } else {
        // Local Docker development falls back to credentials loaded from .env.
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $name = getenv('DB_NAME') ?: 'hostlist_demo';
        $user = getenv('DB_USER') ?: 'hostlist';
        $password = getenv('DB_PASSWORD') ?: 'hostlist-local';
        $dsn = "mysql:host=$host;dbname=$name;charset=utf8mb4";
    }

    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

<?php
declare(strict_types=1);
require __DIR__ . '/dataset.php';

$host = getenv('DB_HOST') ?: '127.0.0.1';
$name = getenv('DB_NAME') ?: 'hostlist_demo';
$user = getenv('DB_USER') ?: 'hostlist';
$password = getenv('DB_PASSWORD') ?: 'hostlist-local';
$dsn = "mysql:host=$host;dbname=$name;charset=utf8mb4";

for ($attempt = 1; ; $attempt++) {
    try {
        $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        break;
    } catch (PDOException $error) {
        if ($attempt >= 40) throw $error;
        sleep(2);
    }
}

foreach (explode(';', (string)file_get_contents(__DIR__ . '/../schema.sql')) as $statement) {
    if (trim($statement) !== '') $pdo->exec($statement);
}
$count = (int)$pdo->query('SELECT COUNT(*) FROM hostlist_hosts')->fetchColumn();
if ($count > 0) {
    echo "Seed skipped: hostlist_hosts already contains $count rows.\n";
    exit(0);
}

$insertHost = $pdo->prepare('INSERT INTO hostlist_hosts
    (id,fullname,company,country,city,primary_domain,secondary_domain,primary_country,secondary_country,languages)
    VALUES (:id,:fullname,:company,:country,:city,:primary_domain,:secondary_domain,:primary_country,:secondary_country,:languages)');
$insertDetail = $pdo->prepare('INSERT INTO hostlist_host_details
    (host_id,host_experience_years,team_size,motivation,specific_skills,offer_extra_info,business_impact)
    VALUES (:host_id,:host_experience_years,:team_size,:motivation,:specific_skills,:offer_extra_info,:business_impact)');

$pdo->beginTransaction();
try {
    foreach (makeProfiles() as $profile) {
        $hostRow = $profile['host'];
        $insertHost->execute($hostRow);
        $detail = $profile['detail'];
        $insertDetail->execute([
            'host_id' => $hostRow['id'],
            ...$detail,
            'business_impact' => json_encode($detail['business_impact'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
        ]);
    }
    $pdo->commit();
} catch (Throwable $error) {
    $pdo->rollBack();
    throw $error;
}
echo 'Loaded ' . DATASET_SIZE . " synthetic hosts.\n";

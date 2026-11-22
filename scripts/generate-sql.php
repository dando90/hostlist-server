<?php
declare(strict_types=1);
require __DIR__ . '/dataset.php';

$output = $argv[1] ?? (__DIR__ . '/../output/hostlist-seed.sql');
$directory = dirname($output);
if (!is_dir($directory)) mkdir($directory, 0775, true);
$quote = static fn(string $value): string => "'" . str_replace("'", "''", $value) . "'";
$sql = ["SET NAMES utf8mb4;", "START TRANSACTION;", file_get_contents(__DIR__ . '/../schema.sql')];
$sql[] = 'DELETE FROM hostlist_host_details;';
$sql[] = 'DELETE FROM hostlist_hosts;';
foreach (makeProfiles() as $profile) {
    $h = $profile['host'];
    $sql[] = 'INSERT INTO hostlist_hosts (id,fullname,company,country,city,primary_domain,secondary_domain,primary_country,secondary_country,languages) VALUES (' . implode(',', [
        (string)$h['id'], ...array_map($quote, array_values(array_intersect_key($h, array_flip(['fullname','company','country','city','primary_domain','secondary_domain','primary_country','secondary_country','languages'])))),
    ]) . ');';
    $d = $profile['detail'];
    $sql[] = 'INSERT INTO hostlist_host_details (host_id,host_experience_years,team_size,motivation,specific_skills,offer_extra_info,business_impact) VALUES (' . implode(',', [
        (string)$h['id'], $d['host_experience_years'], (string)$d['team_size'], $quote($d['motivation']), $quote($d['specific_skills']), $quote($d['offer_extra_info']), $quote(json_encode($d['business_impact'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)),
    ]) . ');';
}
$sql[] = 'COMMIT;';
file_put_contents($output, implode("\n\n", $sql) . "\n");
echo "Wrote " . DATASET_SIZE . " profiles to $output\n";

<?php
declare(strict_types=1);

require __DIR__ . '/src/database.php';

$configuredBasePath = getenv('APP_BASE_PATH');
$basePath = rtrim((string)($configuredBasePath === false ? '/projects/hostlist-server' : $configuredBasePath), '/');
if ($basePath === '.' || $basePath === '/') $basePath = '';
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if ($basePath !== '' && ($requestPath === $basePath || str_starts_with($requestPath, $basePath . '/'))) {
    $requestPath = substr($requestPath, strlen($basePath)) ?: '/';
}
$requestPath = '/' . trim($requestPath, '/');
if ($requestPath === '//') $requestPath = '/';

function sendJson(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if (preg_match('#^/demo-api/hosts(?:/([0-9]+))?/?$#', $requestPath, $match)) {
    try {
        if (!empty($match[1])) {
            $statement = db()->prepare('SELECT h.id,h.fullname,h.company,h.country,h.city,h.primary_domain,h.secondary_domain,
                h.primary_country,h.secondary_country,h.languages,d.host_experience_years,d.team_size,d.motivation,
                d.specific_skills,d.offer_extra_info,d.business_impact
                FROM hostlist_hosts h JOIN hostlist_host_details d ON d.host_id=h.id WHERE h.id=:id');
            $statement->execute(['id' => (int)$match[1]]);
            $record = $statement->fetch();
            if (!$record) sendJson(['error' => 'Host not found'], 404);
            $record['id'] = (int)$record['id'];
            $record['team_size'] = (int)$record['team_size'];
            $record['host_experience_years'] = (float)$record['host_experience_years'];
            $record['business_impact'] = json_decode((string)$record['business_impact'], true, 512, JSON_THROW_ON_ERROR);
            sendJson($record);
        }

        $totalRows = (int)db()->query('SELECT COUNT(*) FROM hostlist_hosts')->fetchColumn();
        $pageStart = max(1, filter_input(INPUT_GET, 'pageStart', FILTER_VALIDATE_INT) ?: 1);
        $pageCount = min(100, max(1, filter_input(INPUT_GET, 'pageCount', FILTER_VALIDATE_INT) ?: 20));
        $offset = $pageStart - 1;
        $statement = db()->prepare('SELECT id,fullname,company,country,city,primary_domain,secondary_domain,
            primary_country,secondary_country,languages FROM hostlist_hosts ORDER BY id LIMIT :limit OFFSET :offset');
        $statement->bindValue(':limit', $pageCount, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        $records = $statement->fetchAll();
        foreach ($records as &$record) $record['id'] = (int)$record['id'];
        unset($record);
        sendJson(['records' => $records, 'totalRows' => $totalRows]);
    } catch (Throwable $error) {
        error_log($error->getMessage());
        sendJson(['error' => 'Demo data is temporarily unavailable'], 500);
    }
}

if ($requestPath !== '/' && !preg_match('#^/hosts/[0-9]+/?$#', $requestPath)) {
    http_response_code(404);
    $title = 'Page not found';
    $body = '<section class="empty"><p class="eyebrow">404</p><h1>That page wandered off.</h1><p>Return to the directory to browse the fictional host profiles.</p><a class="button" href="' . htmlspecialchars($basePath . '/', ENT_QUOTES) . '">Back to directory</a></section>';
    renderPage($title, $body, $basePath);
    exit;
}

if (preg_match('#^/hosts/([0-9]+)/?$#', $requestPath, $match)) {
    $hostId = (int)$match[1];
    $body = '<a class="back-link" href="' . htmlspecialchars($basePath . '/', ENT_QUOTES) . '">← All hosts</a>
      <section id="host-detail" class="detail-card" data-host-id="' . $hostId . '"><p class="eyebrow">Fictional IT host</p><h1>Loading host profile…</h1></section>';
    $script = 'loadHostDetail(' . $hostId . ')';
    renderPage('Host profile', $body, $basePath, $script);
    exit;
}

$body = '<section class="hero"><div><p class="eyebrow">HostList · European IT demo directory</p><h1>Find a place to build something useful.</h1><p class="hero-copy">Explore fictional technology teams, their projects and the ways they support new collaborators.</p></div><div class="hero-stat"><strong>500</strong><span>synthetic profiles</span></div></section>
  <section class="directory"><div class="section-heading"><div><p class="eyebrow">The directory</p><h2>Hosts across Europe</h2></div><p id="result-count" class="muted">Loading the directory…</p></div>
  <div class="table-wrap"><table><thead><tr><th>Host</th><th>Company</th><th>Location</th><th>Primary domain</th><th>Focus</th><th></th></tr></thead><tbody id="host-rows"><tr><td colspan="6" class="loading">Preparing the fictional directory…</td></tr></tbody></table></div>
  <nav class="pagination" aria-label="Directory pages"><button id="previous-page" class="page-button" type="button">← Previous</button><span id="page-label">Page 1</span><button id="next-page" class="page-button" type="button">Next →</button></nav>
  <p class="demo-note">All people, companies and opportunities on this page are fictional and generated for demonstration.</p></section>';
renderPage('European IT host directory', $body, $basePath, 'loadDirectory()');

function renderPage(string $title, string $body, string $basePath, string $scriptCall = ''): void
{
    $safeTitle = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $safeBase = json_encode($basePath, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    $init = $scriptCall !== '' ? 'window.addEventListener("DOMContentLoaded", () => ' . $scriptCall . ');' : '';
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="theme-color" content="#f5f4ef"><title>' . $safeTitle . ' · HostList demo</title><link rel="stylesheet" href="' . htmlspecialchars($basePath . '/styles.css', ENT_QUOTES) . '"></head><body>
      <header class="topbar"><a class="brand" href="' . htmlspecialchars($basePath . '/', ENT_QUOTES) . '"><span class="brand-mark">H</span><span>HostList <small>DEMO DIRECTORY</small></span></a><span class="topbar-note">Synthetic data · IT sector</span></header>
      <main>' . $body . '</main><footer><span>HostList Server</span><span>Built for a local-first demo</span></footer>
      <script>const APP_BASE=' . $safeBase . ';</script><script src="' . htmlspecialchars($basePath . '/app.js', ENT_QUOTES) . '" defer></script><script>' . $init . '</script></body></html>';
}

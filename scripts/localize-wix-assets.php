<?php

$root = dirname(__DIR__);
$storageDir = $root . '/public/assets/wix-assets';
$publicPrefix = '/assets/wix-assets/';
$databasePath = $root . '/database/database.sqlite';
$sourceFiles = [
    $root . '/resources/views/public/page.blade.php',
    $root . '/database/seeders/DatabaseSeeder.php',
    $root . '/database/migrations/2026_07_01_000001_add_faq_page.php',
];
$urlPattern = '#https://(?:static|video)\.wixstatic\.com/[^\s"\'<]+#';

if (! is_dir($storageDir) && ! mkdir($storageDir, 0755, true) && ! is_dir($storageDir)) {
    fwrite(STDERR, "Could not create {$storageDir}\n");
    exit(1);
}

$urls = [];

foreach ($sourceFiles as $file) {
    if (! is_file($file)) {
        continue;
    }

    preg_match_all($urlPattern, file_get_contents($file), $matches);

    foreach ($matches[0] as $url) {
        $urls[$url] = true;
    }
}

$pdo = new PDO('sqlite:' . $databasePath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$tables = $pdo->query("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'")
    ->fetchAll(PDO::FETCH_COLUMN);
$textColumnsByTable = [];

foreach ($tables as $table) {
    $columns = $pdo->query('PRAGMA table_info("' . str_replace('"', '""', $table) . '")')
        ->fetchAll(PDO::FETCH_ASSOC);

    foreach ($columns as $column) {
        $type = strtoupper((string) $column['type']);

        if (str_contains($type, 'CHAR') || str_contains($type, 'CLOB') || str_contains($type, 'TEXT') || $type === '') {
            $textColumnsByTable[$table][] = $column['name'];
        }
    }
}

foreach ($textColumnsByTable as $table => $columns) {
    foreach ($columns as $column) {
        $statement = $pdo->query(
            'SELECT rowid, "' . str_replace('"', '""', $column) . '" AS value FROM "' . str_replace('"', '""', $table) . '" WHERE "' . str_replace('"', '""', $column) . '" LIKE "%wixstatic.com%"'
        );

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            preg_match_all($urlPattern, (string) $row['value'], $matches);

            foreach ($matches[0] as $url) {
                $urls[$url] = true;
            }
        }
    }
}

$replacements = [];

foreach (array_keys($urls) as $index => $url) {
    $path = parse_url($url, PHP_URL_PATH) ?: '';
    $basename = basename(rawurldecode($path)) ?: 'asset';
    $extension = strtolower(pathinfo($basename, PATHINFO_EXTENSION));
    $extension = preg_match('/^[a-z0-9]{2,5}$/', $extension) ? $extension : 'bin';
    $name = pathinfo($basename, PATHINFO_FILENAME);
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
    $slug = trim($slug, '-') ?: 'asset';
    $filename = substr(hash('sha256', $url), 0, 16) . '-' . substr($slug, 0, 48) . '.' . $extension;
    $target = $storageDir . '/' . $filename;
    $localUrl = $publicPrefix . $filename;

    if (! is_file($target)) {
        $command = sprintf(
            'curl -L --fail --silent --show-error --connect-timeout 20 --max-time 120 --output %s %s',
            escapeshellarg($target),
            escapeshellarg($url)
        );
        exec($command, $output, $status);

        if ($status !== 0 || ! is_file($target) || filesize($target) === 0) {
            @unlink($target);
            fwrite(STDERR, "Failed to download {$url}\n");
            continue;
        }
    }

    $replacements[$url] = $localUrl;
    echo '[' . ($index + 1) . '/' . count($urls) . "] {$localUrl}\n";
}

foreach ($sourceFiles as $file) {
    if (! is_file($file)) {
        continue;
    }

    $contents = file_get_contents($file);
    $updated = str_replace(array_keys($replacements), array_values($replacements), $contents);

    if ($updated !== $contents) {
        file_put_contents($file, $updated);
    }
}

foreach ($textColumnsByTable as $table => $columns) {
    foreach ($columns as $column) {
        $select = $pdo->query(
            'SELECT rowid, "' . str_replace('"', '""', $column) . '" AS value FROM "' . str_replace('"', '""', $table) . '" WHERE "' . str_replace('"', '""', $column) . '" LIKE "%wixstatic.com%"'
        );
        $update = $pdo->prepare(
            'UPDATE "' . str_replace('"', '""', $table) . '" SET "' . str_replace('"', '""', $column) . '" = :value WHERE rowid = :rowid'
        );

        while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
            $value = (string) $row['value'];
            $updated = str_replace(array_keys($replacements), array_values($replacements), $value);

            if ($updated !== $value) {
                $update->execute([
                    'value' => $updated,
                    'rowid' => $row['rowid'],
                ]);
            }
        }
    }
}

echo 'Localized ' . count($replacements) . ' Wix assets.' . PHP_EOL;

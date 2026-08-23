<?php

$root = dirname(__DIR__);
$databasePath = $root . '/database/database.sqlite';
$files = [
    $root . '/resources/views/public/page.blade.php',
    $root . '/database/seeders/DatabaseSeeder.php',
    $root . '/database/migrations/2026_07_01_000001_add_faq_page.php',
];

foreach ($files as $file) {
    if (! is_file($file)) {
        continue;
    }

    $contents = file_get_contents($file);
    $updated = str_replace('/storage/wix-assets/', '/assets/wix-assets/', $contents);

    if ($updated !== $contents) {
        file_put_contents($file, $updated);
    }
}

$pdo = new PDO('sqlite:' . $databasePath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$tables = $pdo->query("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'")
    ->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    $quotedTable = '"' . str_replace('"', '""', $table) . '"';
    $columns = $pdo->query('PRAGMA table_info(' . $quotedTable . ')')
        ->fetchAll(PDO::FETCH_ASSOC);

    foreach ($columns as $column) {
        $type = strtoupper((string) $column['type']);

        if (! str_contains($type, 'CHAR') && ! str_contains($type, 'CLOB') && ! str_contains($type, 'TEXT') && $type !== '') {
            continue;
        }

        $quotedColumn = '"' . str_replace('"', '""', $column['name']) . '"';
        $pdo->exec(
            'UPDATE ' . $quotedTable
            . ' SET ' . $quotedColumn . " = replace({$quotedColumn}, '/storage/wix-assets/', '/assets/wix-assets/')"
            . ' WHERE ' . $quotedColumn . " LIKE '%/storage/wix-assets/%'"
        );
    }
}

echo "Switched Wix asset URLs to /assets/wix-assets.\n";

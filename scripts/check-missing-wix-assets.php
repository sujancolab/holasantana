<?php

use App\Models\HolidayHome;
use App\Models\Page;
use App\Support\WixAssetUrl;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pattern = '#https://(?:static|video)\.wixstatic\.com/[^\s"\'<]+#';
$missing = [];

foreach (Page::all() as $page) {
    $json = json_encode($page->content_blocks, JSON_UNESCAPED_SLASHES) ?: '';
    preg_match_all($pattern, $json, $matches);

    foreach (array_unique($matches[0]) as $url) {
        $file = WixAssetUrl::filenameForUrl($url);

        if (! is_file(public_path('assets/wix-assets/' . $file))) {
            $missing[] = "{$page->slug}: {$file} <= {$url}";
        }
    }
}

if (class_exists(HolidayHome::class) && \Illuminate\Support\Facades\Schema::hasTable('holiday_homes')) {
    foreach (HolidayHome::all() as $holidayHome) {
        preg_match_all($pattern, (string) $holidayHome->image_url, $matches);

        foreach (array_unique($matches[0]) as $url) {
            $file = WixAssetUrl::filenameForUrl($url);

            if (! is_file(public_path('assets/wix-assets/' . $file))) {
                $missing[] = "holiday-home: {$file} <= {$url}";
            }
        }
    }
}

if ($missing) {
    echo implode(PHP_EOL, array_unique($missing)) . PHP_EOL;
    exit(1);
}

echo "No missing localized Wix assets.\n";

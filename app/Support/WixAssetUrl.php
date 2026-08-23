<?php

namespace App\Support;

class WixAssetUrl
{
    private const URL_PATTERN = '#https://(?:static|video)\.wixstatic\.com/[^\s"\'<]+#';

    public static function localize(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn ($item) => self::localize($item), $value);
        }

        if (! is_string($value)) {
            return $value;
        }

        return preg_replace_callback(self::URL_PATTERN, function (array $match) {
            return '/assets/wix-assets/' . self::filenameForUrl($match[0]);
        }, $value) ?? $value;
    }

    public static function filenameForUrl(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $basename = basename(rawurldecode($path)) ?: 'asset';
        $extension = strtolower(pathinfo($basename, PATHINFO_EXTENSION));
        $extension = preg_match('/^[a-z0-9]{2,5}$/', $extension) ? $extension : 'bin';
        $name = pathinfo($basename, PATHINFO_FILENAME);
        $slug = strtolower((string) preg_replace('/[^a-z0-9]+/i', '-', $name));
        $slug = trim($slug, '-') ?: 'asset';

        return substr(hash('sha256', $url), 0, 16) . '-' . substr($slug, 0, 48) . '.' . $extension;
    }
}

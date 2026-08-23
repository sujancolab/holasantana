<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const URL_PATTERN = '#https://(?:static|video)\.wixstatic\.com/[^\s"\'<]+#';

    public function up(): void
    {
        foreach ($this->textColumnsByTable() as $table => $columns) {
            foreach ($columns as $column) {
                $rows = DB::table($table)
                    ->select($column)
                    ->where($column, 'like', '%wixstatic.com%')
                    ->get();

                foreach ($rows as $row) {
                    $value = (string) $row->{$column};
                    $updated = $this->localizeUrls($value);

                    if ($updated !== $value) {
                        DB::table($table)
                            ->where($column, $value)
                            ->update([$column => $updated]);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        //
    }

    private function localizeUrls(string $value): string
    {
        return preg_replace_callback(self::URL_PATTERN, function (array $match) {
            return '/assets/wix-assets/' . $this->filenameForUrl($match[0]);
        }, $value) ?? $value;
    }

    private function filenameForUrl(string $url): string
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

    private function textColumnsByTable(): array
    {
        $tables = collect(Schema::getTables())
            ->pluck('name')
            ->filter(fn ($table) => ! str_starts_with($table, 'sqlite_'));
        $columnsByTable = [];

        foreach ($tables as $table) {
            $columns = Schema::getColumns($table);

            foreach ($columns as $column) {
                $name = $column['name'];
                $type = strtoupper((string) ($column['type_name'] ?? $column['type'] ?? ''));

                if (str_contains($type, 'CHAR') || str_contains($type, 'CLOB') || str_contains($type, 'TEXT') || $type === '') {
                    $columnsByTable[$table][] = $name;
                }
            }
        }

        return $columnsByTable;
    }
};

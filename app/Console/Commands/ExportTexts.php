<?php

namespace App\Console\Commands;

use App\Models\Translation;
use App\Translation\TextCatalog;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Writes texts edited in the admin back into lang/{locale}/{group}.php,
 * so production edits can be committed to the repo.
 */
class ExportTexts extends Command
{
    protected $signature = 'texts:export {--clear : Delete the exported overrides afterwards, so the files are the only source}';

    protected $description = 'Write texts edited in the admin back into the lang files';

    public function handle(): int
    {
        $overrides = Translation::query()->orderBy('key')->get()->groupBy(fn (Translation $t) => "{$t->locale}/{$t->group}");

        if ($overrides->isEmpty()) {
            $this->info('No edited texts to export.');

            return self::SUCCESS;
        }

        foreach ($overrides as $file => $rows) {
            [$locale, $group] = explode('/', $file);
            $path = lang_path("{$file}.php");

            if (! in_array($group, TextCatalog::GROUPS, true) || ! is_file($path)) {
                $this->warn("Skipped {$file}: no such lang file.");

                continue;
            }

            $lines = require $path;
            foreach ($rows as $row) {
                Arr::set($lines, $row->key, $row->value);
            }

            file_put_contents($path, $this->header($path)."return {$this->export($lines)};\n");
            $this->line("Wrote {$rows->count()} text(s) to lang/{$file}.php");

            if ($this->option('clear')) {
                $rows->each->delete();
            }
        }

        $this->comment('Comments inside the rewritten arrays are not kept. Check the diff before committing.');

        return self::SUCCESS;
    }

    /** Everything before "return [", i.e. the opening tag and the file's doc comment. */
    private function header(string $path): string
    {
        return Str::before(file_get_contents($path), 'return [');
    }

    /** Short-array PHP in the same style as the hand-written files. */
    private function export(mixed $value, int $depth = 0): string
    {
        if (! is_array($value)) {
            return match (true) {
                is_string($value) => "'".str_replace(['\\', "'"], ['\\\\', "\\'"], $value)."'",
                $value === null => 'null',
                default => var_export($value, true),
            };
        }

        $indent = str_repeat('    ', $depth + 1);
        $isList = array_is_list($value);
        $items = [];
        foreach ($value as $key => $item) {
            $prefix = $isList ? '' : $this->export($key).' => ';
            $items[] = $indent.$prefix.$this->export($item, $depth + 1).',';
        }

        return "[\n".implode("\n", $items)."\n".str_repeat('    ', $depth).']';
    }
}

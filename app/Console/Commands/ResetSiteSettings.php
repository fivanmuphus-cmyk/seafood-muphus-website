<?php

namespace App\Console\Commands;

use App\Models\SiteSetting;
use Illuminate\Console\Command;

class ResetSiteSettings extends Command
{
    protected $signature = 'site:reset {group : general, home, ...}';

    protected $description = 'Kembalikan satu kelompok pengaturan situs ke teks bawaan';

    public function handle(): int
    {
        $group = (string) $this->argument('group');

        if (! array_key_exists($group, SiteSetting::GROUPS)) {
            $this->error('Kelompok tidak dikenal. Pilihan: ' . implode(', ', array_keys(SiteSetting::GROUPS)));

            return self::FAILURE;
        }

        $row = SiteSetting::query()->where('group', $group)->first();
        $row?->update(['data' => null]);

        $this->info("Pengaturan \"{$group}\" dikembalikan ke teks bawaan.");

        return self::SUCCESS;
    }
}
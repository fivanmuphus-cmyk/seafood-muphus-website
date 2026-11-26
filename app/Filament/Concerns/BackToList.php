<?php

namespace App\Filament\Concerns;

/**
 * Untuk halaman Tambah dan Ubah: setelah disimpan kembali ke daftar,
 * dengan pesan berbahasa Indonesia, mis. "Artikel berhasil disimpan".
 */
trait BackToList
{
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return static::getResource()::getModelLabel() . ' berhasil ditambahkan';
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return static::getResource()::getModelLabel() . ' berhasil disimpan';
    }
}

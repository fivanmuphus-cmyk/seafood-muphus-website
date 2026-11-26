<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nama')->required()->maxLength(100),
            TextInput::make('email')->label('Email')->email()->required()->maxLength(150)->unique(ignoreRecord: true),
            Select::make('role')->label('Peran')->options(User::ROLES)->default('admin')->required()->native(false)
                ->disabled(fn (?User $record) => $record && $record->getKey() === auth()->id())
                ->helperText('Superadmin dapat mengubah semua pengaturan. Admin hanya konten harian. Peran akun sendiri tidak bisa diubah.'),
            TextInput::make('password')->label('Kata sandi')->password()->revealable()
                ->required(fn (?User $record) => $record === null)
                ->minLength(12)
                ->dehydrated(fn (?string $state) => filled($state))
                ->helperText('Minimal 12 karakter. Saat mengubah pengguna, kosongkan bila tidak ingin mengganti.'),
        ])->columns(1);
    }
}

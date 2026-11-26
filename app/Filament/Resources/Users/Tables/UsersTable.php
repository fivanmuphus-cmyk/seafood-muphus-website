<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('role')->label('Peran')->badge()
                    ->formatStateUsing(fn (?string $state) => User::ROLES[$state] ?? $state)
                    ->color(fn (?string $state) => $state === 'superadmin' ? 'success' : 'gray'),
                TextColumn::make('created_at')->label('Dibuat')->date('j M Y')->sortable(),
            ])
            ->recordActions([
                EditAction::make()->label('Ubah'),
                DeleteAction::make()->label('Hapus'),
            ])
            ->defaultSort('id');
    }
}

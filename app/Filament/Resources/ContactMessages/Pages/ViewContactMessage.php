<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    /** Membuka pesan otomatis menandainya sudah dibaca. */
    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->record->read_at === null) {
            $this->record->update(['read_at' => now()]);
        }
    }

    public function getTitle(): string
    {
        return 'Pesan dari ' . $this->record->name;
    }

    public function getBreadcrumb(): string
    {
        return 'Detail';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('whatsapp')
                ->label('Balas via WhatsApp')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('success')
                ->visible(fn () => filled($this->record->phone))
                ->url(function () {
                    $digits = preg_replace('/\D+/', '', (string) $this->record->phone);
                    $digits = str_starts_with($digits, '0') ? '62' . substr($digits, 1) : $digits;

                    return 'https://wa.me/' . $digits;
                })
                ->openUrlInNewTab(),
            Action::make('email')
                ->label('Balas via email')
                ->icon('heroicon-o-envelope')
                ->color('gray')
                ->visible(fn () => filled($this->record->email))
                ->url(fn () => 'mailto:' . $this->record->email),
            DeleteAction::make()
                ->label('Hapus')
                ->modalHeading('Hapus pesan ini?')
                ->modalDescription('Pesan yang dihapus tidak bisa dikembalikan.')
                ->successRedirectUrl(fn () => ContactMessageResource::getUrl('index')),
        ];
    }
}
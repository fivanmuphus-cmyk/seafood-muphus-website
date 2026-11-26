<?php

namespace App\Filament\Resources\Faqs\Pages;

use App\Filament\Concerns\BackToList;
use App\Filament\Resources\Faqs\FaqResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFaq extends CreateRecord
{
    use BackToList;

    protected static string $resource = FaqResource::class;
}

<?php

namespace App\Filament\Resources\Articles\Pages;

use App\Filament\Concerns\BackToList;
use App\Filament\Resources\Articles\ArticleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArticle extends CreateRecord
{
    use BackToList;

    protected static string $resource = ArticleResource::class;
}

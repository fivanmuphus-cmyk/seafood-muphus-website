<?php

namespace App\Filament\Resources\Branches\Pages;

use App\Filament\Concerns\BackToList;
use App\Filament\Resources\Branches\BranchResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBranch extends CreateRecord
{
    use BackToList;

    protected static string $resource = BranchResource::class;
}

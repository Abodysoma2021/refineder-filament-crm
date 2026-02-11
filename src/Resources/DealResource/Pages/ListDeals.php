<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\DealResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Refineder\FilamentCrm\Resources\DealResource\DealResource;

class ListDeals extends ListRecords
{
    protected static string $resource = DealResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

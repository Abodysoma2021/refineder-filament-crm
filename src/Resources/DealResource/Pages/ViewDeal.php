<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\DealResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Refineder\FilamentCrm\Resources\DealResource\DealResource;

class ViewDeal extends ViewRecord
{
    protected static string $resource = DealResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

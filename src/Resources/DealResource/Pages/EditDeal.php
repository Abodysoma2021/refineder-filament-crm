<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\DealResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Refineder\FilamentCrm\Resources\DealResource\DealResource;

class EditDeal extends EditRecord
{
    protected static string $resource = DealResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\DealResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Refineder\FilamentCrm\Resources\DealResource\DealResource;

class CreateDeal extends CreateRecord
{
    protected static string $resource = DealResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}

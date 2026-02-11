<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\ContactResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Refineder\FilamentCrm\Resources\ContactResource\ContactResource;

class CreateContact extends CreateRecord
{
    protected static string $resource = ContactResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}

<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\ContactResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Refineder\FilamentCrm\Resources\ContactResource\ContactResource;

class ListContacts extends ListRecords
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

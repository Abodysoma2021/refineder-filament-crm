<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\ContactResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Refineder\FilamentCrm\Resources\ContactResource\ContactResource;

class EditContact extends EditRecord
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

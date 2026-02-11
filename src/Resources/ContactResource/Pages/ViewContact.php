<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\ContactResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Refineder\FilamentCrm\Resources\ContactResource\ContactResource;

class ViewContact extends ViewRecord
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

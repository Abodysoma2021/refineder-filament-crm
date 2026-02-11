<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\WhatsappSessionResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Refineder\FilamentCrm\Resources\WhatsappSessionResource\WhatsappSessionResource;

class ListWhatsappSessions extends ListRecords
{
    protected static string $resource = WhatsappSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

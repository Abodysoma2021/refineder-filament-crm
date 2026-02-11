<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\WhatsappSessionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Refineder\FilamentCrm\Resources\WhatsappSessionResource\WhatsappSessionResource;

class CreateWhatsappSession extends CreateRecord
{
    protected static string $resource = WhatsappSessionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}

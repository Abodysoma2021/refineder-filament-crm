<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\ConversationResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Refineder\FilamentCrm\Resources\ConversationResource\ConversationResource;

class ListConversations extends ListRecords
{
    protected static string $resource = ConversationResource::class;
}

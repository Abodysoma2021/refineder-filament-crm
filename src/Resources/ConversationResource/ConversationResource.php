<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\ConversationResource;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Refineder\FilamentCrm\Models\CrmConversation;
use Refineder\FilamentCrm\Resources\ConversationResource\Pages\ListConversations;
use Refineder\FilamentCrm\Resources\ConversationResource\Pages\ViewConversation;
use Refineder\FilamentCrm\Resources\ConversationResource\Tables\ConversationsTable;

class ConversationResource extends Resource
{
    protected static ?string $model = CrmConversation::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return config('refineder-crm.navigation.group', 'CRM');
    }

    public static function getNavigationLabel(): string
    {
        return __('refineder-crm::conversations.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('refineder-crm::conversations.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('refineder-crm::conversations.plural_model_label');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::unread()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string | array | null
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return ConversationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConversations::route('/'),
            'view' => ViewConversation::route('/{record}'),
        ];
    }
}

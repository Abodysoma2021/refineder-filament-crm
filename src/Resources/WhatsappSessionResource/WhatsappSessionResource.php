<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\WhatsappSessionResource;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Refineder\FilamentCrm\Models\WhatsappSession;
use Refineder\FilamentCrm\Resources\WhatsappSessionResource\Pages\CreateWhatsappSession;
use Refineder\FilamentCrm\Resources\WhatsappSessionResource\Pages\EditWhatsappSession;
use Refineder\FilamentCrm\Resources\WhatsappSessionResource\Pages\ListWhatsappSessions;
use Refineder\FilamentCrm\Resources\WhatsappSessionResource\Schemas\WhatsappSessionForm;
use Refineder\FilamentCrm\Resources\WhatsappSessionResource\Tables\WhatsappSessionsTable;

class WhatsappSessionResource extends Resource
{
    protected static ?string $model = WhatsappSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return config('refineder-crm.navigation.group', 'CRM');
    }

    public static function getNavigationLabel(): string
    {
        return __('refineder-crm::sessions.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('refineder-crm::sessions.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('refineder-crm::sessions.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return WhatsappSessionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WhatsappSessionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWhatsappSessions::route('/'),
            'create' => CreateWhatsappSession::route('/create'),
            'edit' => EditWhatsappSession::route('/{record}/edit'),
        ];
    }
}

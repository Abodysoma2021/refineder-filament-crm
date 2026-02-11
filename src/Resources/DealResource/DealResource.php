<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\DealResource;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Refineder\FilamentCrm\Enums\DealStage;
use Refineder\FilamentCrm\Models\CrmDeal;
use Refineder\FilamentCrm\Resources\DealResource\Pages\CreateDeal;
use Refineder\FilamentCrm\Resources\DealResource\Pages\EditDeal;
use Refineder\FilamentCrm\Resources\DealResource\Pages\ListDeals;
use Refineder\FilamentCrm\Resources\DealResource\Pages\ViewDeal;
use Refineder\FilamentCrm\Resources\DealResource\Schemas\DealForm;
use Refineder\FilamentCrm\Resources\DealResource\Tables\DealsTable;

class DealResource extends Resource
{
    protected static ?string $model = CrmDeal::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationGroup(): ?string
    {
        return config('refineder-crm.navigation.group', 'CRM');
    }

    public static function getNavigationLabel(): string
    {
        return __('refineder-crm::deals.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('refineder-crm::deals.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('refineder-crm::deals.plural_model_label');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::open()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::open()->count();

        return $count > 10 ? 'warning' : 'primary';
    }

    public static function form(Schema $schema): Schema
    {
        return DealForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DealsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDeals::route('/'),
            'create' => CreateDeal::route('/create'),
            'view' => ViewDeal::route('/{record}'),
            'edit' => EditDeal::route('/{record}/edit'),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\ContactResource;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Refineder\FilamentCrm\Models\CrmContact;
use Refineder\FilamentCrm\Resources\ContactResource\Pages\CreateContact;
use Refineder\FilamentCrm\Resources\ContactResource\Pages\EditContact;
use Refineder\FilamentCrm\Resources\ContactResource\Pages\ListContacts;
use Refineder\FilamentCrm\Resources\ContactResource\Pages\ViewContact;
use Refineder\FilamentCrm\Resources\ContactResource\Schemas\ContactForm;
use Refineder\FilamentCrm\Resources\ContactResource\Tables\ContactsTable;

class ContactResource extends Resource
{
    protected static ?string $model = CrmContact::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return config('refineder-crm.navigation.group', 'CRM');
    }

    public static function getNavigationLabel(): string
    {
        return __('refineder-crm::contacts.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('refineder-crm::contacts.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('refineder-crm::contacts.plural_model_label');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return ContactForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContacts::route('/'),
            'create' => CreateContact::route('/create'),
            'view' => ViewContact::route('/{record}'),
            'edit' => EditContact::route('/{record}/edit'),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\ContactResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('refineder-crm::contacts.table.name'))
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->company),

                TextColumn::make('phone')
                    ->label(__('refineder-crm::contacts.table.phone'))
                    ->searchable()
                    ->copyable(),

                TextColumn::make('email')
                    ->label(__('refineder-crm::contacts.table.email'))
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('company')
                    ->label(__('refineder-crm::contacts.table.company'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('whatsappSession.name')
                    ->label(__('refineder-crm::contacts.table.session'))
                    ->toggleable(),

                TextColumn::make('conversations_count')
                    ->label(__('refineder-crm::contacts.table.conversations'))
                    ->counts('conversations')
                    ->sortable(),

                TextColumn::make('deals_count')
                    ->label(__('refineder-crm::contacts.table.deals'))
                    ->counts('deals')
                    ->sortable(),

                TextColumn::make('last_message_at')
                    ->label(__('refineder-crm::contacts.table.last_message'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('last_message_at', 'desc')
            ->filters([
                SelectFilter::make('whatsapp_session_id')
                    ->label(__('refineder-crm::contacts.filters.session'))
                    ->relationship('whatsappSession', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

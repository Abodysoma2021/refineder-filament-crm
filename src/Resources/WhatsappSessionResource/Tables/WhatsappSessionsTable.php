<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\WhatsappSessionResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WhatsappSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('refineder-crm::sessions.table.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone_number')
                    ->label(__('refineder-crm::sessions.table.phone_number'))
                    ->searchable(),

                TextColumn::make('status')
                    ->label(__('refineder-crm::sessions.table.status'))
                    ->badge()
                    ->color(fn ($state) => $state->color()),

                IconColumn::make('is_default')
                    ->label(__('refineder-crm::sessions.table.is_default'))
                    ->boolean(),

                TextColumn::make('conversations_count')
                    ->label(__('refineder-crm::sessions.table.conversations'))
                    ->counts('conversations')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('refineder-crm::sessions.table.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

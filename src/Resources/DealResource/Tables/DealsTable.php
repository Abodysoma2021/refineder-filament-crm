<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\DealResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Refineder\FilamentCrm\Enums\DealPriority;
use Refineder\FilamentCrm\Enums\DealStage;

class DealsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('refineder-crm::deals.table.title'))
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->contact?->name),

                TextColumn::make('contact.name')
                    ->label(__('refineder-crm::deals.table.contact'))
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('value')
                    ->label(__('refineder-crm::deals.table.value'))
                    ->money(fn ($record) => $record->currency ?? 'USD')
                    ->sortable(),

                TextColumn::make('stage')
                    ->label(__('refineder-crm::deals.table.stage'))
                    ->badge()
                    ->color(fn (DealStage $state) => $state->color()),

                TextColumn::make('priority')
                    ->label(__('refineder-crm::deals.table.priority'))
                    ->badge()
                    ->color(fn (DealPriority $state) => $state->color()),

                TextColumn::make('expected_close_date')
                    ->label(__('refineder-crm::deals.table.expected_close'))
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->isOverdue() ? 'danger' : null),

                TextColumn::make('created_at')
                    ->label(__('refineder-crm::deals.table.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('stage')
                    ->label(__('refineder-crm::deals.filters.stage'))
                    ->options(collect(DealStage::cases())->mapWithKeys(
                        fn (DealStage $s) => [$s->value => $s->label()]
                    )),

                SelectFilter::make('priority')
                    ->label(__('refineder-crm::deals.filters.priority'))
                    ->options(collect(DealPriority::cases())->mapWithKeys(
                        fn (DealPriority $p) => [$p->value => $p->label()]
                    )),
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

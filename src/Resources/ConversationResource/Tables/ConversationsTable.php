<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\ConversationResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Refineder\FilamentCrm\Enums\ConversationStatus;

class ConversationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contact.name')
                    ->label(__('refineder-crm::conversations.table.contact'))
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->contact?->phone),

                TextColumn::make('last_message')
                    ->label(__('refineder-crm::conversations.table.last_message'))
                    ->limit(50)
                    ->wrap(),

                TextColumn::make('unread_count')
                    ->label(__('refineder-crm::conversations.table.unread'))
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'gray'),

                TextColumn::make('status')
                    ->label(__('refineder-crm::conversations.table.status'))
                    ->badge()
                    ->color(fn (ConversationStatus $state) => $state->color()),

                TextColumn::make('whatsappSession.name')
                    ->label(__('refineder-crm::conversations.table.session'))
                    ->toggleable(),

                TextColumn::make('last_message_at')
                    ->label(__('refineder-crm::conversations.table.last_activity'))
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('last_message_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('refineder-crm::conversations.filters.status'))
                    ->options(collect(ConversationStatus::cases())->mapWithKeys(
                        fn (ConversationStatus $s) => [$s->value => $s->label()]
                    )),

                TernaryFilter::make('unread')
                    ->label(__('refineder-crm::conversations.filters.unread'))
                    ->queries(
                        true: fn ($query) => $query->where('unread_count', '>', 0),
                        false: fn ($query) => $query->where('unread_count', 0),
                    ),

                TernaryFilter::make('is_archived')
                    ->label(__('refineder-crm::conversations.filters.archived')),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

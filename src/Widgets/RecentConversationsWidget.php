<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Refineder\FilamentCrm\Models\CrmConversation;

class RecentConversationsWidget extends BaseWidget
{
    protected static ?int $sort = -2;

    protected static ?string $pollingInterval = '10s';

    protected int | string | array $columnSpan = 'full';

    public function getTableHeading(): string
    {
        return __('refineder-crm::widgets.recent.heading');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CrmConversation::query()
                    ->with(['contact', 'whatsappSession'])
                    ->notArchived()
                    ->orderByDesc('last_message_at')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('contact.name')
                    ->label(__('refineder-crm::widgets.recent.contact'))
                    ->searchable()
                    ->description(fn ($record) => $record->contact?->phone),

                TextColumn::make('last_message')
                    ->label(__('refineder-crm::widgets.recent.message'))
                    ->limit(40)
                    ->wrap(),

                TextColumn::make('unread_count')
                    ->label(__('refineder-crm::widgets.recent.unread'))
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'gray'),

                TextColumn::make('status')
                    ->label(__('refineder-crm::widgets.recent.status'))
                    ->badge()
                    ->color(fn ($state) => $state->color()),

                TextColumn::make('last_message_at')
                    ->label(__('refineder-crm::widgets.recent.time'))
                    ->since(),
            ])
            ->paginated(false);
    }
}

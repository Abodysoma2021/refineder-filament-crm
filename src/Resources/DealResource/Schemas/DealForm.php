<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\DealResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Refineder\FilamentCrm\Enums\DealPriority;
use Refineder\FilamentCrm\Enums\DealStage;
use Refineder\FilamentCrm\Models\CrmContact;

class DealForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('refineder-crm::deals.form.deal_info'))
                ->schema([
                    TextInput::make('title')
                        ->label(__('refineder-crm::deals.form.title'))
                        ->required()
                        ->maxLength(255),

                    Select::make('contact_id')
                        ->label(__('refineder-crm::deals.form.contact'))
                        ->options(fn () => CrmContact::pluck('name', 'id'))
                        ->searchable()
                        ->preload(),

                    TextInput::make('value')
                        ->label(__('refineder-crm::deals.form.value'))
                        ->numeric()
                        ->prefix(config('refineder-crm.currency', 'USD'))
                        ->default(0),

                    Select::make('currency')
                        ->label(__('refineder-crm::deals.form.currency'))
                        ->options([
                            'USD' => 'USD',
                            'EUR' => 'EUR',
                            'GBP' => 'GBP',
                            'SAR' => 'SAR',
                            'AED' => 'AED',
                            'EGP' => 'EGP',
                        ])
                        ->default(config('refineder-crm.currency', 'USD')),
                ])
                ->columns(2),

            Section::make(__('refineder-crm::deals.form.status_info'))
                ->schema([
                    Select::make('stage')
                        ->label(__('refineder-crm::deals.form.stage'))
                        ->options(collect(DealStage::cases())->mapWithKeys(
                            fn (DealStage $stage) => [$stage->value => $stage->label()]
                        ))
                        ->default(DealStage::Lead->value)
                        ->required(),

                    Select::make('priority')
                        ->label(__('refineder-crm::deals.form.priority'))
                        ->options(collect(DealPriority::cases())->mapWithKeys(
                            fn (DealPriority $p) => [$p->value => $p->label()]
                        ))
                        ->default(DealPriority::Medium->value),

                    DatePicker::make('expected_close_date')
                        ->label(__('refineder-crm::deals.form.expected_close_date')),

                    Textarea::make('notes')
                        ->label(__('refineder-crm::deals.form.notes'))
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}

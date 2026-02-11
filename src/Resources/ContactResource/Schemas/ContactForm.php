<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\ContactResource\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Refineder\FilamentCrm\Models\WhatsappSession;

class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('refineder-crm::contacts.form.contact_info'))
                ->schema([
                    TextInput::make('name')
                        ->label(__('refineder-crm::contacts.form.name'))
                        ->required()
                        ->maxLength(255),

                    TextInput::make('phone')
                        ->label(__('refineder-crm::contacts.form.phone'))
                        ->required()
                        ->tel()
                        ->maxLength(20),

                    TextInput::make('email')
                        ->label(__('refineder-crm::contacts.form.email'))
                        ->email()
                        ->maxLength(255),

                    TextInput::make('company')
                        ->label(__('refineder-crm::contacts.form.company'))
                        ->maxLength(255),
                ])
                ->columns(2),

            Section::make(__('refineder-crm::contacts.form.additional_info'))
                ->schema([
                    Select::make('whatsapp_session_id')
                        ->label(__('refineder-crm::contacts.form.whatsapp_session'))
                        ->options(fn () => WhatsappSession::pluck('name', 'id'))
                        ->searchable(),

                    Textarea::make('notes')
                        ->label(__('refineder-crm::contacts.form.notes'))
                        ->rows(3),
                ]),
        ]);
    }
}

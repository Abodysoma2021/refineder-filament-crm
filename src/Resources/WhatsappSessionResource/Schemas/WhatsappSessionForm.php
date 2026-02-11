<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\WhatsappSessionResource\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Refineder\FilamentCrm\Enums\SessionStatus;

class WhatsappSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('refineder-crm::sessions.form.session_details'))
                ->description(__('refineder-crm::sessions.form.session_details_description'))
                ->schema([
                    TextInput::make('name')
                        ->label(__('refineder-crm::sessions.form.name'))
                        ->required()
                        ->maxLength(255),

                    TextInput::make('phone_number')
                        ->label(__('refineder-crm::sessions.form.phone_number'))
                        ->tel()
                        ->maxLength(20),

                    Toggle::make('is_default')
                        ->label(__('refineder-crm::sessions.form.is_default'))
                        ->helperText(__('refineder-crm::sessions.form.is_default_help')),
                ]),

            Section::make(__('refineder-crm::sessions.form.api_configuration'))
                ->description(__('refineder-crm::sessions.form.api_configuration_description'))
                ->schema([
                    TextInput::make('session_id')
                        ->label(__('refineder-crm::sessions.form.session_id'))
                        ->numeric()
                        ->helperText(__('refineder-crm::sessions.form.session_id_help')),

                    TextInput::make('api_key')
                        ->label(__('refineder-crm::sessions.form.api_key'))
                        ->password()
                        ->revealable()
                        ->helperText(__('refineder-crm::sessions.form.api_key_help')),

                    TextInput::make('personal_access_token')
                        ->label(__('refineder-crm::sessions.form.personal_access_token'))
                        ->password()
                        ->revealable()
                        ->required()
                        ->helperText(__('refineder-crm::sessions.form.personal_access_token_help')),
                ]),

            Section::make(__('refineder-crm::sessions.form.webhook_configuration'))
                ->description(__('refineder-crm::sessions.form.webhook_configuration_description'))
                ->schema([
                    Placeholder::make('webhook_url_display')
                        ->label(__('refineder-crm::sessions.form.webhook_url'))
                        ->content(fn ($record) => $record?->getWebhookUrl() ?? __('refineder-crm::sessions.form.webhook_url_after_save')),

                    TextInput::make('webhook_secret')
                        ->label(__('refineder-crm::sessions.form.webhook_secret'))
                        ->password()
                        ->revealable()
                        ->helperText(__('refineder-crm::sessions.form.webhook_secret_help')),
                ]),
        ]);
    }
}

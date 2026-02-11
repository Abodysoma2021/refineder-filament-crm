# Changelog

All notable changes to `refineder/filament-crm` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## v1.0.0 - 2026-02-11

### Added

- **WhatsApp Session Management** -- create, connect, disconnect, and manage multiple WhatsApp sessions via WasenderAPI
- **Real-Time Chat** -- Livewire-powered chat interface with polling, message types, and delivery receipts
- **Contact Management** -- auto-creation from incoming messages, rich profiles, search and filters
- **Deal Pipeline** -- customizable stages (Lead, Qualified, Proposal, Negotiation, Won, Lost), priorities, value tracking
- **Webhook Controller** -- handles `messages.received`, `messages.upsert`, `message.sent`, `messages.update`, `messages.delete`, `session.status`
- **Dashboard Widgets** -- CRM stats overview and recent conversations table
- **Laravel Events** -- `MessageReceived`, `MessageSent`, `SessionStatusChanged`
- **WasenderService** -- multi-session wrapper around WasenderClient with logging and error handling
- **Full Translations** -- English and Arabic out of the box
- **Encrypted Storage** -- API keys and webhook secrets stored with Laravel encryption
- **Configurable Architecture** -- toggle features, override models, adjust polling intervals
- **Filament v5 Native** -- follows the latest Schemas/Tables/Pages resource pattern

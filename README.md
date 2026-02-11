<p align="center">
    <img src="https://raw.githubusercontent.com/Abodysoma2021/refineder-filament-crm/main/.github/banner.png" alt="Refineder Filament CRM" width="100%" style="border-radius: 12px;" />
</p>

<h1 align="center">Refineder Filament CRM</h1>

<p align="center">
    A real-time CRM plugin for <strong>Filament v5</strong> powered by WhatsApp messaging via <strong>WasenderAPI</strong>.
</p>

<p align="center">
    <a href="https://packagist.org/packages/refineder/filament-crm"><img src="https://img.shields.io/packagist/v/refineder/filament-crm.svg?style=flat-square" alt="Latest Version on Packagist"></a>
    <a href="https://packagist.org/packages/refineder/filament-crm"><img src="https://img.shields.io/packagist/dt/refineder/filament-crm.svg?style=flat-square" alt="Total Downloads"></a>
    <a href="https://github.com/Abodysoma2021/refineder-filament-crm/blob/main/LICENSE.md"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="License"></a>
    <a href="https://php.net"><img src="https://img.shields.io/badge/php-8.2%2B-777BB4.svg?style=flat-square&logo=php&logoColor=white" alt="PHP 8.2+"></a>
    <a href="https://laravel.com"><img src="https://img.shields.io/badge/laravel-11.28%2B-FF2D20.svg?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 11.28+"></a>
    <a href="https://filamentphp.com"><img src="https://img.shields.io/badge/filament-5.x-FBBF24.svg?style=flat-square" alt="Filament 5.x"></a>
</p>

<p align="center">
    <a href="#features">Features</a> &bull;
    <a href="#requirements">Requirements</a> &bull;
    <a href="#installation">Installation</a> &bull;
    <a href="#configuration">Configuration</a> &bull;
    <a href="#usage">Usage</a> &bull;
    <a href="#webhook-setup">Webhook Setup</a> &bull;
    <a href="#customization">Customization</a> &bull;
    <a href="#events">Events</a> &bull;
    <a href="#translations">Translations</a> &bull;
    <a href="#roadmap">Roadmap</a> &bull;
    <a href="#license">License</a>
</p>

---

## Overview

**Refineder Filament CRM** transforms your Filament admin panel into a full-featured customer relationship management system with real-time WhatsApp messaging. Connect one or more WhatsApp accounts via [WasenderAPI](https://wasenderapi.com), receive customer messages instantly, reply directly from your dashboard, and manage deals through a visual pipeline -- all without leaving Filament.

---

## Features

### WhatsApp Integration
- **Multi-Session Support** -- connect and manage multiple WhatsApp accounts simultaneously
- **Real-Time Chat** -- WhatsApp-style chat interface with Livewire polling
- **All Message Types** -- text, images, video, audio, documents, locations, contacts, stickers
- **Delivery Receipts** -- sent, delivered, and read status indicators
- **Media Preview** -- inline image previews and media type badges
- **Webhook-Driven** -- automatic message ingestion from WasenderAPI webhooks

### Contact Management
- **Auto-Creation** -- contacts are created automatically from incoming messages
- **Rich Profiles** -- name, phone, email, company, notes, custom metadata
- **Conversation History** -- full message history per contact
- **Search & Filter** -- find contacts by name, phone, email, or company

### Deal Pipeline
- **Customizable Stages** -- Lead, Qualified, Proposal, Negotiation, Won, Lost (configurable)
- **Priority Levels** -- Low, Medium, High, Urgent with color-coded badges
- **Value Tracking** -- monetary values with multi-currency support (USD, EUR, GBP, SAR, AED, EGP)
- **Overdue Detection** -- visual indicators for deals past their expected close date
- **Contact Linking** -- associate deals with contacts and conversations

### Dashboard Widgets
- **CRM Stats** -- contacts, unread conversations, open deals, revenue, today's messages
- **Recent Conversations** -- live-updating table of latest conversations with unread badges

### Developer Experience
- **Filament v5 Native** -- follows the latest resource architecture (Schemas/, Tables/, Pages/)
- **Fully Translatable** -- ships with English and Arabic; add any language
- **Configurable** -- toggle features on/off, override models, adjust polling intervals
- **Event-Driven** -- Laravel events dispatched for messages and session changes
- **Encrypted Storage** -- all API keys and secrets stored with Laravel's encryption

---

## Requirements

| Dependency | Version |
|------------|---------|
| PHP | 8.2+ |
| Laravel | 11.28+ |
| Filament | 5.x |
| Livewire | 4.0+ |
| WasenderAPI Account | [Sign up](https://wasenderapi.com/register) |

---

## Installation

### 1. Install the package

```bash
composer require refineder/filament-crm
```

### 2. Publish and run migrations

```bash
php artisan vendor:publish --tag="refineder-crm-migrations"
php artisan migrate
```

### 3. Register the plugin in your Panel Provider

```php
use Refineder\FilamentCrm\RefinederCrmPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(RefinederCrmPlugin::make());
}
```

### 4. (Optional) Publish the configuration file

```bash
php artisan vendor:publish --tag="refineder-crm-config"
```

---

## Configuration

After publishing, the configuration file is located at `config/refineder-crm.php`.

### Feature Toggles

Enable or disable individual CRM modules:

```php
'features' => [
    'contacts'          => true,
    'conversations'     => true,
    'deals'             => true,
    'whatsapp_sessions' => true,
    'widgets'           => true,
],
```

### Plugin Fluent API

You can also configure features directly on the plugin instance:

```php
RefinederCrmPlugin::make()
    ->contacts(true)
    ->conversations(true)
    ->deals(true)
    ->whatsappSessions(true)
    ->widgets(true)
    ->navigationGroup('CRM')
    ->navigationSort(1)
```

### Chat Polling Interval

Control how frequently the chat checks for new messages (in seconds):

```php
'chat_poll_interval' => 5,
```

### Currency

Set the default currency for deal values:

```php
'currency' => 'USD',
```

### Overriding Models

Replace any model with your own implementation:

```php
'models' => [
    'contact'          => \App\Models\MyContact::class,
    'conversation'     => \App\Models\MyConversation::class,
    'message'          => \App\Models\MyMessage::class,
    'deal'             => \App\Models\MyDeal::class,
    'deal_stage'       => \App\Models\MyDealStage::class,
    'whatsapp_session' => \App\Models\MyWhatsappSession::class,
],
```

---

## Usage

### Setting Up a WhatsApp Session

1. Navigate to **CRM > WhatsApp Sessions** in your Filament panel
2. Click **Create** and fill in your session details:
   - **Session Name** -- a friendly label (e.g. "Sales Team")
   - **WasenderAPI Session ID** -- from your [WasenderAPI dashboard](https://wasenderapi.com)
   - **API Key** -- the unique key for this session
   - **Webhook Secret** -- for verifying incoming webhooks
3. Save the session -- a **Webhook URL** will be generated automatically
4. Copy the Webhook URL into your WasenderAPI session settings
5. Use the **Connect** button to establish the WhatsApp connection

### Sending Messages

Once a session is connected, navigate to any conversation and type your reply in the chat box. Press **Enter** or click the send button. The message is sent via WasenderAPI and stored in your database.

### Managing Deals

Navigate to **CRM > Deals** to create and track deals. Each deal can be linked to a contact and moves through configurable pipeline stages.

---

## Webhook Setup

The plugin registers a webhook endpoint at:

```
POST {your-domain}/refineder-crm/webhook/{session_id}
```

### WasenderAPI Configuration

1. Open your session in the [WasenderAPI dashboard](https://wasenderapi.com)
2. Set the **Webhook URL** to the URL shown in your session's edit page
3. Set the **Webhook Secret** to match the secret stored in the plugin
4. Enable the following events:
   - `messages.received`
   - `messages.upsert`
   - `message.sent`
   - `messages.update`
   - `messages.delete`
   - `session.status`

### Webhook Verification

Every incoming request is verified by comparing the `X-Webhook-Signature` header against the stored webhook secret. If no secret is configured, all requests are accepted.

### Supported Events

| Event | Description |
|-------|-------------|
| `messages.received` | Incoming message -- creates contact, conversation, stores message |
| `messages.upsert` | All messages (incoming + outgoing) |
| `message.sent` | Outgoing message confirmation |
| `messages.update` | Delivery / read status updates |
| `messages.delete` | Message deletion |
| `session.status` | Session connection status change |

---

## Customization

### Extending Resources

All Filament resources follow the v5 pattern with separate `Schemas/`, `Tables/`, and `Pages/` directories. You can extend any resource by publishing and modifying the files.

### Custom Navigation Group

```php
RefinederCrmPlugin::make()
    ->navigationGroup('My Custom CRM')
```

### Disabling Specific Features

```php
RefinederCrmPlugin::make()
    ->deals(false)         // hide the Deals resource
    ->widgets(false)       // remove dashboard widgets
```

---

## Events

The plugin dispatches Laravel events that you can listen for in your application:

| Event | Payload | When |
|-------|---------|------|
| `Refineder\FilamentCrm\Events\MessageReceived` | `CrmMessage $message`, `array $rawPayload` | New incoming message processed |
| `Refineder\FilamentCrm\Events\MessageSent` | `CrmMessage $message` | Outgoing message sent successfully |
| `Refineder\FilamentCrm\Events\SessionStatusChanged` | `WhatsappSession $session`, `SessionStatus $previous`, `SessionStatus $new` | Session connection status changed |

### Example Listener

```php
use Refineder\FilamentCrm\Events\MessageReceived;

class NotifyTeamOnNewMessage
{
    public function handle(MessageReceived $event): void
    {
        $message = $event->message;
        $contact = $message->conversation->contact;

        // Send a notification to your team
        Notification::route('slack', config('services.slack.webhook'))
            ->notify(new NewWhatsAppMessage($contact, $message));
    }
}
```

---

## Translations

The plugin ships with full **English** and **Arabic** translations. To add a new language, publish the translations and create a new directory:

```bash
php artisan vendor:publish --tag="refineder-crm-translations"
```

Translation files are located in `resources/lang/vendor/refineder-crm/`:

```
lang/vendor/refineder-crm/
├── en/
│   ├── chat.php
│   ├── contacts.php
│   ├── conversations.php
│   ├── deals.php
│   ├── messages.php
│   ├── sessions.php
│   └── widgets.php
└── ar/
    └── ...
```

---

## Database Schema

The plugin creates the following tables (all prefixed with `crm_`):

| Table | Purpose |
|-------|---------|
| `crm_whatsapp_sessions` | WhatsApp session credentials and status |
| `crm_contacts` | Customer contact profiles |
| `crm_conversations` | Chat threads with contacts |
| `crm_messages` | Individual messages with type and status |
| `crm_deals` | Sales deals with pipeline stages |
| `crm_deal_stages` | Customizable pipeline stage definitions |

---

## Security

- **Encrypted at Rest** -- API keys, personal access tokens, and webhook secrets are stored using Laravel's `encrypted` cast
- **Webhook Signature Verification** -- every webhook request is validated via `X-Webhook-Signature`
- **CSRF Protection** -- webhook routes are exempt from CSRF (required for external POSTs) but protected by signature verification
- **Tenant Isolation** -- all queries are scoped by `user_id` to prevent cross-user data access

---

## Roadmap

Planned features for future releases:

- [ ] **Broadcasting** -- real-time updates via Laravel Reverb / Echo
- [ ] **AI Auto-Replies** -- GPT-powered automatic responses
- [ ] **Chatbot Builder** -- visual flow-based conversation automation
- [ ] **Message Templates** -- WhatsApp business template management
- [ ] **Bulk Messaging** -- campaign broadcasting to contact lists
- [ ] **Kanban Board** -- drag-and-drop deal pipeline view
- [ ] **Team Assignment** -- assign conversations to team members
- [ ] **SLA Tracking** -- response time monitoring and alerts
- [ ] **Advanced Analytics** -- charts, reports, and conversion metrics
- [ ] **Custom Fields** -- dynamic fields for contacts and deals
- [ ] **REST API** -- headless API for external integrations

---

## Testing

```bash
composer test
```

---

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

---

## Contributing

Contributions are welcome! Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

---

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---

<p align="center">
    Built with care by <a href="https://github.com/Abodysoma2021">Refineder</a> &bull; Powered by <a href="https://filamentphp.com">Filament</a> &bull; WhatsApp via <a href="https://wasenderapi.com">WasenderAPI</a>
</p>

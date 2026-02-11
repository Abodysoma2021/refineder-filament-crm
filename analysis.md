# Refineder Filament CRM - Technical Analysis

## 1. Project Overview

**Refineder Filament CRM** is a Filament v5 panel plugin that provides a real-time CRM system powered by WhatsApp messaging through WasenderAPI. It enables Filament dashboard users to:

- Receive and reply to WhatsApp messages in real-time
- Manage customer contacts automatically
- Create and track deals through customizable pipelines
- Manage multiple WhatsApp sessions/accounts
- View CRM analytics and statistics

---

## 2. Technology Stack

| Component | Technology | Version |
|-----------|-----------|---------|
| Framework | Laravel | 11.28+ |
| Admin Panel | Filament | 5.x |
| Real-time | Livewire | 4.0+ |
| CSS | Tailwind CSS | 4.0+ |
| WhatsApp API | WasenderAPI | Latest |
| WhatsApp SDK | wasenderapi/wasenderapi-laravel | ^1.0 |
| Package Tools | spatie/laravel-package-tools | ^1.15 |
| PHP | PHP | 8.2+ |

---

## 3. Architecture Design

### 3.1 Plugin Architecture

```
┌──────────────────────────────────────────────────┐
│                 Filament Panel                    │
│  ┌────────────┐  ┌──────────┐  ┌──────────────┐ │
│  │  Resources  │  │ Widgets  │  │  Pages       │ │
│  │ - Contacts  │  │ - Stats  │  │ - Chat       │ │
│  │ - Deals     │  │ - Recent │  │ - Pipeline   │ │
│  │ - Sessions  │  │ - Deals  │  │              │ │
│  │ - Convos    │  │          │  │              │ │
│  └──────┬─────┘  └────┬─────┘  └──────┬───────┘ │
│         │              │               │         │
│  ┌──────▼──────────────▼───────────────▼───────┐ │
│  │           RefinederCrmPlugin                 │ │
│  │    (Filament\Contracts\Plugin)               │ │
│  └──────────────────┬──────────────────────────┘ │
└─────────────────────┼────────────────────────────┘
                      │
┌─────────────────────▼────────────────────────────┐
│           RefinederCrmServiceProvider             │
│  ┌─────────────┐  ┌──────────┐  ┌────────────┐  │
│  │   Models     │  │ Services │  │  Events    │  │
│  │   Migrations │  │ Wasender │  │  Listeners │  │
│  │   Config     │  │ Service  │  │            │  │
│  └─────────────┘  └──────────┘  └────────────┘  │
│  ┌─────────────┐  ┌──────────────────────────┐   │
│  │  Routes      │  │  Webhook Controller     │   │
│  │  (webhook)   │  │  (receives WasenderAPI) │   │
│  └─────────────┘  └──────────────────────────┘   │
└──────────────────────────────────────────────────┘
                      │
                      ▼
┌──────────────────────────────────────────────────┐
│              WasenderAPI Cloud                    │
│  ┌─────────────┐  ┌──────────────────────────┐   │
│  │  Sessions    │  │  WhatsApp Messages       │   │
│  │  Management  │  │  Send / Receive          │   │
│  └─────────────┘  └──────────────────────────┘   │
└──────────────────────────────────────────────────┘
```

### 3.2 Data Flow

```
Incoming Message Flow:
WhatsApp User → WasenderAPI → Webhook POST → WebhookController
  → Create/Update Contact → Create/Update Conversation → Store Message
  → Dispatch Event → Livewire Chat Component Updates (Real-time)

Outgoing Message Flow:
Dashboard User → ChatBox Component → WasenderService
  → WasenderClient::sendText() → WasenderAPI → WhatsApp User
  → Store outgoing message in DB
```

---

## 4. Database Schema

### 4.1 Entity Relationship Diagram

```
crm_whatsapp_sessions
├── id
├── user_id (tenant)
├── name
├── phone_number
├── session_id (wasender session id)
├── api_key (encrypted)
├── personal_access_token (encrypted)
├── webhook_secret (encrypted)
├── webhook_url
├── status (enum: connected, disconnected, connecting, qr_pending)
├── is_default
└── timestamps

crm_contacts
├── id
├── user_id (tenant)
├── whatsapp_session_id
├── name
├── phone
├── remote_jid
├── avatar_url
├── email (nullable)
├── company (nullable)
├── notes (nullable)
├── metadata (json)
├── last_message_at
└── timestamps

crm_conversations
├── id
├── user_id (tenant)
├── contact_id → crm_contacts
├── whatsapp_session_id → crm_whatsapp_sessions
├── remote_jid
├── last_message
├── last_message_at
├── unread_count
├── is_archived
├── status (enum: open, closed, pending)
└── timestamps

crm_messages
├── id
├── conversation_id → crm_conversations
├── whatsapp_message_id (wasender msg id)
├── type (enum: text, image, video, audio, document, location, contact, sticker)
├── body
├── media_url (nullable)
├── media_mime_type (nullable)
├── is_from_me
├── status (enum: pending, sent, delivered, read, failed)
├── metadata (json)
├── replied_to_id (nullable, self-reference)
└── timestamps

crm_deals
├── id
├── user_id (tenant)
├── contact_id → crm_contacts
├── conversation_id → crm_conversations (nullable)
├── title
├── value (decimal)
├── currency (default: USD)
├── stage (enum: lead, qualified, proposal, negotiation, won, lost)
├── priority (enum: low, medium, high, urgent)
├── expected_close_date (nullable)
├── closed_at (nullable)
├── notes (nullable)
├── metadata (json)
└── timestamps

crm_deal_stages (customizable pipeline)
├── id
├── user_id (tenant)
├── name
├── color
├── order
├── is_won
├── is_lost
└── timestamps
```

---

## 5. Feature Breakdown

### 5.1 WhatsApp Session Management
- **Create Session**: Admin creates a new WhatsApp session via WasenderAPI
- **QR Code Scanning**: Display QR code for WhatsApp Web linking
- **Connect/Disconnect**: Manage session lifecycle
- **Auto-Webhook Setup**: Automatically configure webhook URL when connecting
- **Multi-Session**: Support multiple WhatsApp numbers per user
- **Status Monitoring**: Real-time session status display
- **API Key Management**: Securely store and manage session API keys

### 5.2 Real-time Chat (Inbox)
- **Conversation List**: All active conversations with last message preview
- **Chat Interface**: WhatsApp-like chat bubbles with timestamps
- **Message Types**: Text, image, video, audio, document, location, contact
- **Reply**: Reply to customer messages directly from dashboard
- **Media Support**: View/download received media, send images/documents
- **Read Receipts**: Show message delivery/read status
- **Unread Counter**: Badge showing unread messages
- **Livewire Polling**: Auto-refresh for new messages (configurable interval)

### 5.3 Contact Management
- **Auto-Creation**: Contacts created automatically from incoming messages
- **Contact Profiles**: Name, phone, email, company, notes
- **Conversation History**: View all conversations per contact
- **Deal Association**: Link contacts to deals
- **Search & Filter**: Search by name, phone, company

### 5.4 Deal Management
- **Deal Pipeline**: Visual pipeline with customizable stages
- **Deal CRUD**: Create, edit, view, delete deals
- **Stage Transitions**: Drag or select to move between stages
- **Contact Linking**: Associate deals with contacts/conversations
- **Priority System**: Low, Medium, High, Urgent
- **Value Tracking**: Deal monetary value with currency
- **Close Dates**: Expected close date with overdue indicators
- **Deal Stats**: Total value, conversion rates, pipeline overview

### 5.5 Dashboard Widgets
- **CRM Stats**: Total contacts, conversations, deals, revenue
- **Recent Conversations**: Latest messages requiring attention
- **Deal Pipeline Overview**: Visual pipeline stage distribution
- **Unread Messages Counter**: Quick glance at pending items

---

## 6. WasenderAPI Integration Details

### 6.1 Webhook Route
```
POST /refineder-crm/webhook/{session}
```
- Protected by signature verification (`X-Webhook-Signature`)
- Handles all event types
- Returns 200 immediately, processes async

### 6.2 Supported Webhook Events
| Event | Handler | Action |
|-------|---------|--------|
| `messages.received` | `handleMessageReceived()` | Create contact, conversation, store message |
| `messages.upsert` | `handleMessageUpsert()` | Update existing messages |
| `message.sent` | `handleMessageSent()` | Update outgoing message status |
| `messages.update` | `handleMessageUpdate()` | Update delivery/read status |
| `messages.delete` | `handleMessageDelete()` | Soft-delete message |
| `session.status` | `handleSessionStatus()` | Update session status |
| `qr.updated` | `handleQrUpdated()` | Store new QR code |

### 6.3 WasenderService
A wrapper around `WasenderClient` that:
- Resolves the correct API key per session
- Handles retries and rate limiting
- Logs all API interactions
- Dispatches Laravel events for outgoing messages
- Supports multi-session message routing

---

## 7. Configuration

```php
// config/refineder-crm.php
return [
    // Default tenant model
    'tenant_model' => null,

    // Enable/disable features
    'features' => [
        'deals' => true,
        'chat' => true,
        'contacts' => true,
        'sessions' => true,
        'widgets' => true,
    ],

    // Chat polling interval (seconds)
    'chat_poll_interval' => 5,

    // Navigation group
    'navigation_group' => 'CRM',

    // Navigation icon
    'navigation_icon' => 'heroicon-o-chat-bubble-left-right',

    // Database table prefix
    'table_prefix' => 'crm_',

    // Default deal stages
    'deal_stages' => [
        'lead' => ['name' => 'Lead', 'color' => 'gray'],
        'qualified' => ['name' => 'Qualified', 'color' => 'info'],
        'proposal' => ['name' => 'Proposal', 'color' => 'warning'],
        'negotiation' => ['name' => 'Negotiation', 'color' => 'primary'],
        'won' => ['name' => 'Won', 'color' => 'success'],
        'lost' => ['name' => 'Lost', 'color' => 'danger'],
    ],

    // Default currency
    'currency' => 'USD',

    // Webhook path prefix
    'webhook_prefix' => 'refineder-crm/webhook',

    // Max media file size (MB)
    'max_media_size' => 16,

    // Models (overridable)
    'models' => [
        'contact' => \Refineder\FilamentCrm\Models\CrmContact::class,
        'conversation' => \Refineder\FilamentCrm\Models\CrmConversation::class,
        'message' => \Refineder\FilamentCrm\Models\CrmMessage::class,
        'deal' => \Refineder\FilamentCrm\Models\CrmDeal::class,
        'deal_stage' => \Refineder\FilamentCrm\Models\CrmDealStage::class,
        'whatsapp_session' => \Refineder\FilamentCrm\Models\WhatsappSession::class,
    ],
];
```

---

## 8. Security Considerations

1. **API Key Encryption**: All WasenderAPI keys stored encrypted in database
2. **Webhook Verification**: `X-Webhook-Signature` header validation on every request
3. **CSRF Exemption**: Webhook routes exempt from CSRF (POST from external service)
4. **Rate Limiting**: Configurable rate limits on webhook endpoint
5. **Authorization**: Filament policies for all resources
6. **Input Sanitization**: Sanitize all webhook payloads before storage
7. **Tenant Isolation**: All queries scoped by tenant/user

---

## 9. Plugin Installation Flow

```bash
# 1. Install package
composer require refineder/filament-crm

# 2. Publish and run migrations
php artisan vendor:publish --tag="refineder-crm-migrations"
php artisan migrate

# 3. Publish config (optional)
php artisan vendor:publish --tag="refineder-crm-config"

# 4. Register plugin in panel provider
->plugin(RefinederCrmPlugin::make())

# 5. Configure WhatsApp sessions via dashboard
```

---

## 10. Future Enhancements (v2+)

- **Broadcasting**: Real-time updates via Laravel Echo/Reverb
- **AI Auto-Replies**: GPT-powered automatic responses
- **Chatbots**: Flow-based automated conversation builder
- **Templates**: WhatsApp message templates management
- **Bulk Messaging**: Send campaigns to contact lists
- **Analytics**: Advanced reporting and charts
- **Teams**: Assign conversations to team members
- **SLA Tracking**: Response time tracking and alerts
- **Custom Fields**: Dynamic fields for contacts and deals
- **Kanban Board**: Drag-and-drop deal pipeline
- **API**: REST API for external integrations

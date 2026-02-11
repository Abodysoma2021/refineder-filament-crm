<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Refineder\FilamentCrm\Enums\ConversationStatus;
use Refineder\FilamentCrm\Enums\MessageStatus;
use Refineder\FilamentCrm\Enums\MessageType;
use Refineder\FilamentCrm\Enums\SessionStatus;
use Refineder\FilamentCrm\Events\MessageReceived;
use Refineder\FilamentCrm\Events\SessionStatusChanged;
use Refineder\FilamentCrm\Models\CrmContact;
use Refineder\FilamentCrm\Models\CrmConversation;
use Refineder\FilamentCrm\Models\CrmMessage;
use Refineder\FilamentCrm\Models\WhatsappSession;
use Refineder\FilamentCrm\Services\DealService;

class WebhookController extends Controller
{
    public function handle(Request $request, int $sessionId): JsonResponse
    {
        $session = WhatsappSession::find($sessionId);

        if (! $session) {
            Log::warning('Refineder CRM: Webhook received for unknown session', [
                'session_id' => $sessionId,
            ]);

            return response()->json(['status' => 'error', 'message' => 'Session not found'], 404);
        }

        // Verify webhook signature
        if (! $this->verifySignature($request, $session)) {
            Log::warning('Refineder CRM: Webhook signature verification failed', [
                'session_id' => $sessionId,
            ]);

            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 403);
        }

        $payload = $request->all();
        $event = $payload['event'] ?? null;

        Log::debug('Refineder CRM: Webhook received', [
            'session_id' => $sessionId,
            'event' => $event,
        ]);

        try {
            match ($event) {
                'messages.upsert' => $this->handleMessageUpsert($session, $payload),
                'messages.received',
                'messages-personal.received' => $this->handleMessageReceived($session, $payload),
                'message.sent' => $this->handleMessageSent($session, $payload),
                'messages.update' => $this->handleMessageUpdate($session, $payload),
                'messages.delete' => $this->handleMessageDelete($session, $payload),
                'session.status' => $this->handleSessionStatus($session, $payload),
                default => Log::debug("Refineder CRM: Unhandled webhook event: {$event}"),
            };
        } catch (\Exception $e) {
            Log::error('Refineder CRM: Webhook processing error', [
                'session_id' => $sessionId,
                'event' => $event,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    protected function verifySignature(Request $request, WhatsappSession $session): bool
    {
        if (! $session->webhook_secret) {
            return true; // No secret configured, allow all
        }

        $signature = $request->header('X-Webhook-Signature');

        return $signature === $session->webhook_secret;
    }

    protected function handleMessageReceived(WhatsappSession $session, array $payload): void
    {
        $messageData = $payload['data']['messages'] ?? null;

        if (! $messageData) {
            return;
        }

        $key = $messageData['key'] ?? [];

        // Skip messages from ourselves
        if ($key['fromMe'] ?? false) {
            return;
        }

        $remoteJid = $key['remoteJid'] ?? null;
        $senderPhone = $key['cleanedParticipantPn'] ?? $key['cleanedSenderPn'] ?? null;
        $messageBody = $messageData['messageBody'] ?? '';
        $messageId = $key['id'] ?? null;
        $rawMessage = $messageData['message'] ?? [];

        if (! $remoteJid) {
            return;
        }

        // ---- Deduplication: skip if this message already exists ----
        if ($messageId && CrmMessage::where('whatsapp_message_id', $messageId)->exists()) {
            Log::debug('Refineder CRM: Skipping duplicate message', ['wa_id' => $messageId]);

            return;
        }

        // Extract phone number from sender or JID
        $resolvedPhone = $this->resolvePhoneNumber($senderPhone, $remoteJid);

        // Find or create contact (using phone number for matching, not LID JID)
        $contact = $this->findOrCreateContact($session, $resolvedPhone, $remoteJid);

        // Find or create conversation (resolves LID JID to existing conversations)
        $conversation = $this->findOrCreateConversation($session, $contact, $remoteJid);

        // Determine message type
        $type = $this->detectMessageType($rawMessage);

        // Extract media URL if present
        $mediaUrl = $this->extractMediaUrl($rawMessage, $type);
        $mediaMime = $this->extractMediaMime($rawMessage, $type);

        // Store the message
        $message = $conversation->messages()->create([
            'whatsapp_message_id' => $messageId,
            'type' => $type,
            'body' => $messageBody,
            'media_url' => $mediaUrl,
            'media_mime_type' => $mediaMime,
            'is_from_me' => false,
            'status' => MessageStatus::Delivered,
            'metadata' => $rawMessage,
        ]);

        // Update conversation
        $conversation->update([
            'last_message' => $messageBody ?: $type->label(),
            'last_message_at' => now(),
            'status' => ConversationStatus::Open,
        ]);
        $conversation->incrementUnread();

        // Update contact
        $contact->update(['last_message_at' => now()]);

        // Auto-create deal if needed (new customer or all deals closed)
        try {
            $dealService = app(DealService::class);
            $dealService->autoCreateDealIfNeeded($session, $contact, $conversation, $messageBody);
        } catch (\Exception $e) {
            Log::error('Refineder CRM: Failed to auto-create deal', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Dispatch event
        event(new MessageReceived($message, $payload));
    }

    protected function handleMessageUpsert(WhatsappSession $session, array $payload): void
    {
        $messageData = $payload['data']['messages'] ?? null;

        if (! $messageData) {
            return;
        }

        $key = $messageData['key'] ?? [];

        // Only process incoming messages via upsert
        if (! ($key['fromMe'] ?? false)) {
            // Deduplication is handled inside handleMessageReceived
            $this->handleMessageReceived($session, $payload);
        }
    }

    protected function handleMessageSent(WhatsappSession $session, array $payload): void
    {
        $messageData = $payload['data']['messages'] ?? null;

        if (! $messageData) {
            return;
        }

        $key = $messageData['key'] ?? [];
        $messageId = $key['id'] ?? null;

        if ($messageId) {
            CrmMessage::where('whatsapp_message_id', $messageId)
                ->update(['status' => MessageStatus::Sent]);
        }
    }

    protected function handleMessageUpdate(WhatsappSession $session, array $payload): void
    {
        $updates = $payload['data'] ?? [];
        $messageId = $updates['key']['id'] ?? null;
        $status = $updates['update']['status'] ?? null;

        if (! $messageId || ! $status) {
            return;
        }

        $newStatus = match ($status) {
            2, 'SERVER_ACK' => MessageStatus::Sent,
            3, 'DELIVERY_ACK' => MessageStatus::Delivered,
            4, 'READ' => MessageStatus::Read,
            5, 'PLAYED' => MessageStatus::Read,
            default => null,
        };

        if ($newStatus) {
            CrmMessage::where('whatsapp_message_id', $messageId)
                ->update(['status' => $newStatus]);
        }
    }

    protected function handleMessageDelete(WhatsappSession $session, array $payload): void
    {
        $key = $payload['data']['key'] ?? [];
        $messageId = $key['id'] ?? null;

        if ($messageId) {
            CrmMessage::where('whatsapp_message_id', $messageId)->delete();
        }
    }

    protected function handleSessionStatus(WhatsappSession $session, array $payload): void
    {
        $status = $payload['data']['status'] ?? null;
        $previousStatus = $session->status;

        $newStatus = match ($status) {
            'open', 'connected' => SessionStatus::Connected,
            'close', 'disconnected' => SessionStatus::Disconnected,
            'connecting' => SessionStatus::Connecting,
            default => $session->status,
        };

        $session->update(['status' => $newStatus]);

        event(new SessionStatusChanged($session, $previousStatus, $newStatus));
    }

    // --- Helper Methods ---

    /**
     * Resolve phone number from sender info and JID.
     * Handles LID JIDs (e.g., 73770492547136@lid) by preferring the sender phone.
     */
    protected function resolvePhoneNumber(?string $senderPhone, string $remoteJid): string
    {
        // If we have a clean sender phone number, use it
        if ($senderPhone) {
            // Normalize: add + if not present
            return str_starts_with($senderPhone, '+') ? $senderPhone : "+{$senderPhone}";
        }

        // Extract phone from standard JID (e.g., 96551162231@s.whatsapp.net)
        if (str_contains($remoteJid, '@s.whatsapp.net')) {
            $number = explode('@', $remoteJid)[0];

            return "+{$number}";
        }

        // For LID JIDs or other formats, return the JID as-is
        return $remoteJid;
    }

    /**
     * Find or create contact. Tries to match by phone number first,
     * regardless of JID format (handles LID JID → phone resolution).
     */
    protected function findOrCreateContact(
        WhatsappSession $session,
        string $phone,
        string $remoteJid,
    ): CrmContact {
        $contactModel = config('refineder-crm.models.contact');

        // First try to find by phone number (most reliable match)
        $contact = $contactModel::where('user_id', $session->user_id)
            ->where('whatsapp_session_id', $session->id)
            ->where('phone', $phone)
            ->first();

        if ($contact) {
            return $contact;
        }

        // Also try with/without + prefix
        $altPhone = str_starts_with($phone, '+') ? ltrim($phone, '+') : "+{$phone}";
        $contact = $contactModel::where('user_id', $session->user_id)
            ->where('whatsapp_session_id', $session->id)
            ->where('phone', $altPhone)
            ->first();

        if ($contact) {
            return $contact;
        }

        // Create new contact
        return $contactModel::create([
            'user_id' => $session->user_id,
            'whatsapp_session_id' => $session->id,
            'phone' => $phone,
            'name' => $phone,
            'remote_jid' => $remoteJid,
        ]);
    }

    /**
     * Find or create conversation. Resolves LID JIDs to existing conversations
     * by matching through the contact's existing conversations.
     */
    protected function findOrCreateConversation(
        WhatsappSession $session,
        CrmContact $contact,
        string $remoteJid,
    ): CrmConversation {
        $conversationModel = config('refineder-crm.models.conversation');

        // First: try to find an existing conversation for this contact on this session
        // This handles the LID JID case - the contact already has a conversation
        $conversation = $conversationModel::where('user_id', $session->user_id)
            ->where('contact_id', $contact->id)
            ->where('whatsapp_session_id', $session->id)
            ->first();

        if ($conversation) {
            // Update remote_jid if it changed (e.g., was phone-based, now LID-based)
            // Keep the original JID but store the LID mapping
            return $conversation;
        }

        // Try exact JID match
        $conversation = $conversationModel::where('user_id', $session->user_id)
            ->where('whatsapp_session_id', $session->id)
            ->where('remote_jid', $remoteJid)
            ->first();

        if ($conversation) {
            return $conversation;
        }

        // Create new conversation
        return $conversationModel::create([
            'user_id' => $session->user_id,
            'contact_id' => $contact->id,
            'whatsapp_session_id' => $session->id,
            'remote_jid' => $remoteJid,
            'status' => ConversationStatus::Open,
            'last_message_at' => now(),
        ]);
    }

    protected function detectMessageType(array $rawMessage): MessageType
    {
        if (isset($rawMessage['imageMessage'])) {
            return MessageType::Image;
        }
        if (isset($rawMessage['videoMessage'])) {
            return MessageType::Video;
        }
        if (isset($rawMessage['audioMessage'])) {
            return MessageType::Audio;
        }
        if (isset($rawMessage['documentMessage'])) {
            return MessageType::Document;
        }
        if (isset($rawMessage['stickerMessage'])) {
            return MessageType::Sticker;
        }
        if (isset($rawMessage['locationMessage'])) {
            return MessageType::Location;
        }
        if (isset($rawMessage['contactMessage'])) {
            return MessageType::Contact;
        }
        if (isset($rawMessage['pollCreationMessage'])) {
            return MessageType::Poll;
        }
        if (isset($rawMessage['reactionMessage'])) {
            return MessageType::Reaction;
        }

        return MessageType::Text;
    }

    protected function extractMediaUrl(array $rawMessage, MessageType $type): ?string
    {
        $mediaKey = match ($type) {
            MessageType::Image => 'imageMessage',
            MessageType::Video => 'videoMessage',
            MessageType::Audio => 'audioMessage',
            MessageType::Document => 'documentMessage',
            MessageType::Sticker => 'stickerMessage',
            default => null,
        };

        if ($mediaKey && isset($rawMessage[$mediaKey]['url'])) {
            return $rawMessage[$mediaKey]['url'];
        }

        return null;
    }

    protected function extractMediaMime(array $rawMessage, MessageType $type): ?string
    {
        $mediaKey = match ($type) {
            MessageType::Image => 'imageMessage',
            MessageType::Video => 'videoMessage',
            MessageType::Audio => 'audioMessage',
            MessageType::Document => 'documentMessage',
            MessageType::Sticker => 'stickerMessage',
            default => null,
        };

        if ($mediaKey && isset($rawMessage[$mediaKey]['mimetype'])) {
            return $rawMessage[$mediaKey]['mimetype'];
        }

        return null;
    }
}

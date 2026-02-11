<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Services;

use Illuminate\Support\Facades\Log;
use Refineder\FilamentCrm\Enums\MessageStatus;
use Refineder\FilamentCrm\Enums\MessageType;
use Refineder\FilamentCrm\Enums\SessionStatus;
use Refineder\FilamentCrm\Events\MessageSent;
use Refineder\FilamentCrm\Models\CrmConversation;
use Refineder\FilamentCrm\Models\CrmMessage;
use Refineder\FilamentCrm\Models\WhatsappSession;
use WasenderApi\WasenderClient;

class WasenderService
{
    /**
     * Get a WasenderClient instance for a specific session (uses session API key for messaging).
     */
    public function getClient(WhatsappSession $session): WasenderClient
    {
        return new WasenderClient($session->api_key);
    }

    /**
     * Get a WasenderClient instance configured with personal access token (for session management).
     */
    public function getManagementClient(WhatsappSession $session): WasenderClient
    {
        $client = new WasenderClient($session->api_key);

        // Set personal access token via config for session management operations
        if ($session->personal_access_token) {
            config(['wasenderapi.personal_access_token' => $session->personal_access_token]);
            $client = new WasenderClient($session->api_key);
        }

        return $client;
    }

    /**
     * Send a text message through a session.
     */
    public function sendText(
        WhatsappSession $session,
        CrmConversation $conversation,
        string $text,
    ): CrmMessage {
        $client = $this->getClient($session);

        $to = $conversation->remote_jid;

        try {
            $response = $client->sendText($to, $text);

            $message = $conversation->messages()->create([
                'type' => MessageType::Text,
                'body' => $text,
                'is_from_me' => true,
                'status' => MessageStatus::Sent,
                'whatsapp_message_id' => $response['data']['key']['id'] ?? null,
                'metadata' => $response,
            ]);

            // Update conversation
            $conversation->update([
                'last_message' => $text,
                'last_message_at' => now(),
            ]);

            event(new MessageSent($message));

            return $message;
        } catch (\Exception $e) {
            Log::error('Refineder CRM: Failed to send text message', [
                'session_id' => $session->id,
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
            ]);

            // Store failed message
            $message = $conversation->messages()->create([
                'type' => MessageType::Text,
                'body' => $text,
                'is_from_me' => true,
                'status' => MessageStatus::Failed,
                'metadata' => ['error' => $e->getMessage()],
            ]);

            throw $e;
        }
    }

    /**
     * Send an image message.
     */
    public function sendImage(
        WhatsappSession $session,
        CrmConversation $conversation,
        string $imageUrl,
        ?string $caption = null,
    ): CrmMessage {
        $client = $this->getClient($session);
        $to = $conversation->remote_jid;

        try {
            $response = $client->sendImage($to, $imageUrl, $caption);

            $message = $conversation->messages()->create([
                'type' => MessageType::Image,
                'body' => $caption,
                'media_url' => $imageUrl,
                'media_mime_type' => 'image/jpeg',
                'is_from_me' => true,
                'status' => MessageStatus::Sent,
                'whatsapp_message_id' => $response['data']['key']['id'] ?? null,
                'metadata' => $response,
            ]);

            $conversation->update([
                'last_message' => $caption ?: '📷 Image',
                'last_message_at' => now(),
            ]);

            event(new MessageSent($message));

            return $message;
        } catch (\Exception $e) {
            Log::error('Refineder CRM: Failed to send image', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Send a document message.
     */
    public function sendDocument(
        WhatsappSession $session,
        CrmConversation $conversation,
        string $documentUrl,
        string $fileName,
        ?string $caption = null,
    ): CrmMessage {
        $client = $this->getClient($session);
        $to = $conversation->remote_jid;

        try {
            $response = $client->sendDocument($to, $documentUrl, $fileName, $caption);

            $message = $conversation->messages()->create([
                'type' => MessageType::Document,
                'body' => $caption ?: $fileName,
                'media_url' => $documentUrl,
                'is_from_me' => true,
                'status' => MessageStatus::Sent,
                'whatsapp_message_id' => $response['data']['key']['id'] ?? null,
                'metadata' => $response,
            ]);

            $conversation->update([
                'last_message' => "📄 {$fileName}",
                'last_message_at' => now(),
            ]);

            event(new MessageSent($message));

            return $message;
        } catch (\Exception $e) {
            Log::error('Refineder CRM: Failed to send document', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Connect a WhatsApp session.
     */
    public function connectSession(WhatsappSession $session): array
    {
        $client = $this->getManagementClient($session);

        $session->update(['status' => SessionStatus::Connecting]);

        try {
            $response = $client->connectWhatsAppSession((int) $session->session_id);

            // Check response to determine actual status
            $this->syncSessionStatus($session);

            return $response;
        } catch (\Exception $e) {
            // Handle "already connected" gracefully
            if (str_contains($e->getMessage(), 'already connected')) {
                $session->update(['status' => SessionStatus::Connected]);

                return ['success' => true, 'message' => 'Session is already connected'];
            }

            $session->update(['status' => SessionStatus::Disconnected]);

            throw $e;
        }
    }

    /**
     * Disconnect a WhatsApp session.
     */
    public function disconnectSession(WhatsappSession $session): array
    {
        $client = $this->getManagementClient($session);

        try {
            $response = $client->disconnectWhatsAppSession((int) $session->session_id);

            $session->update(['status' => SessionStatus::Disconnected]);

            return $response;
        } catch (\Exception $e) {
            // Handle "already disconnected" gracefully
            if (str_contains($e->getMessage(), 'not connected') || str_contains($e->getMessage(), 'already disconnected')) {
                $session->update(['status' => SessionStatus::Disconnected]);

                return ['success' => true, 'message' => 'Session is already disconnected'];
            }

            throw $e;
        }
    }

    /**
     * Sync local session status with WasenderAPI remote status.
     */
    public function syncSessionStatus(WhatsappSession $session): SessionStatus
    {
        $client = $this->getManagementClient($session);

        try {
            $details = $client->getWhatsAppSessionDetails((int) $session->session_id);

            $remoteStatus = $details['data']['status'] ?? ($details['status'] ?? null);

            $newStatus = match (strtolower((string) $remoteStatus)) {
                'connected', 'open', 'active' => SessionStatus::Connected,
                'connecting', 'loading' => SessionStatus::Connecting,
                'qr', 'qr_pending', 'scan_qr' => SessionStatus::QrPending,
                default => SessionStatus::Disconnected,
            };

            $session->update(['status' => $newStatus]);

            return $newStatus;
        } catch (\Exception $e) {
            Log::error('Refineder CRM: Failed to sync session status', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);

            return $session->status;
        }
    }

    /**
     * Get QR code for a session.
     */
    public function getQrCode(WhatsappSession $session): ?string
    {
        $client = $this->getManagementClient($session);

        try {
            $response = $client->getWhatsAppSessionQrCode((int) $session->session_id);

            return $response['data']['qr'] ?? null;
        } catch (\Exception $e) {
            Log::error('Refineder CRM: Failed to get QR code', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get session status from WasenderAPI.
     */
    public function getSessionStatus(WhatsappSession $session): ?string
    {
        $client = $this->getManagementClient($session);

        try {
            $response = $client->getSessionStatus((string) $session->session_id);

            return $response['data']['status'] ?? null;
        } catch (\Exception $e) {
            Log::error('Refineder CRM: Failed to get session status', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Fetch all WhatsApp sessions from WasenderAPI using personal access token.
     */
    public function fetchRemoteSessions(WhatsappSession $session): array
    {
        $client = $this->getManagementClient($session);

        try {
            return $client->getAllWhatsAppSessions();
        } catch (\Exception $e) {
            Log::error('Refineder CRM: Failed to fetch remote sessions', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Get details for a specific session from WasenderAPI.
     */
    public function getSessionDetails(WhatsappSession $session): array
    {
        $client = $this->getManagementClient($session);

        try {
            return $client->getWhatsAppSessionDetails((int) $session->session_id);
        } catch (\Exception $e) {
            Log::error('Refineder CRM: Failed to get session details', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}

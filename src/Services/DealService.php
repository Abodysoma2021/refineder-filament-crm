<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Services;

use Illuminate\Support\Facades\Log;
use Refineder\FilamentCrm\Enums\DealPriority;
use Refineder\FilamentCrm\Enums\DealStage;
use Refineder\FilamentCrm\Models\CrmContact;
use Refineder\FilamentCrm\Models\CrmConversation;
use Refineder\FilamentCrm\Models\CrmDeal;
use Refineder\FilamentCrm\Models\WhatsappSession;

class DealService
{
    /**
     * Auto-create a deal when a new customer sends the first message,
     * or when a customer with all closed deals sends again.
     *
     * Returns the deal if one was created, null otherwise.
     */
    public function autoCreateDealIfNeeded(
        WhatsappSession $session,
        CrmContact $contact,
        CrmConversation $conversation,
        string $messageBody = '',
    ): ?CrmDeal {
        $dealModel = config('refineder-crm.models.deal', CrmDeal::class);

        // Get all deals for this contact
        $existingDeals = $dealModel::where('contact_id', $contact->id)->get();

        // Case 1: No deals at all (new customer) → create first deal
        if ($existingDeals->isEmpty()) {
            return $this->createDeal($session, $contact, $conversation, $messageBody, 'New Inquiry');
        }

        // Case 2: All existing deals are closed (won/lost) → create new deal
        $hasOpenDeal = $existingDeals->contains(fn (CrmDeal $deal) => ! $deal->isClosed());

        if (! $hasOpenDeal) {
            $dealNumber = $existingDeals->count() + 1;

            return $this->createDeal(
                $session,
                $contact,
                $conversation,
                $messageBody,
                "Follow-up #{$dealNumber}",
            );
        }

        // Case 3: There's an open deal → no need to create a new one
        return null;
    }

    /**
     * Create a new deal for a contact.
     */
    protected function createDeal(
        WhatsappSession $session,
        CrmContact $contact,
        CrmConversation $conversation,
        string $messageBody,
        string $titleSuffix,
    ): CrmDeal {
        $dealModel = config('refineder-crm.models.deal', CrmDeal::class);

        $contactName = $contact->getDisplayName();
        $title = "{$contactName} - {$titleSuffix}";

        $deal = $dealModel::create([
            'user_id' => $session->user_id,
            'contact_id' => $contact->id,
            'conversation_id' => $conversation->id,
            'title' => $title,
            'stage' => DealStage::Lead,
            'priority' => DealPriority::Medium,
            'value' => 0,
            'currency' => config('refineder-crm.currency', 'USD'),
            'notes' => $messageBody ? "First message: {$messageBody}" : null,
            'metadata' => [
                'auto_created' => true,
                'source' => 'whatsapp_webhook',
                'created_from_message' => $messageBody,
            ],
        ]);

        Log::info('Refineder CRM: Auto-created deal', [
            'deal_id' => $deal->id,
            'contact_id' => $contact->id,
            'title' => $title,
        ]);

        return $deal;
    }
}

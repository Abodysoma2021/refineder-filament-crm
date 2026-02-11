<?php

use Illuminate\Support\Facades\Route;
use Refineder\FilamentCrm\Http\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| Refineder CRM Webhook Routes
|--------------------------------------------------------------------------
|
| These routes handle incoming webhooks from WasenderAPI.
| They are exempt from CSRF verification.
|
*/

$prefix = config('refineder-crm.webhook.prefix', 'refineder-crm/webhook');
$middleware = array_merge(
    ['api'],
    config('refineder-crm.webhook.middleware', [])
);

Route::post("{$prefix}/{session}", [WebhookController::class, 'handle'])
    ->name('refineder-crm.webhook')
    ->middleware($middleware)
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

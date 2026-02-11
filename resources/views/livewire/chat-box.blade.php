<div
    wire:poll.{{ config('refineder-crm.chat_poll_interval', 15) }}s="pollMessages"
    class="flex flex-col h-[600px] bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
    x-data
    x-on:message-received.window="$nextTick(() => { document.getElementById('chat-messages').scrollTop = document.getElementById('chat-messages').scrollHeight })"
>
    {{-- Chat Header --}}
    @if($conversation && $conversation->contact)
        <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center text-white font-bold text-sm">
                {{ $conversation->contact->getInitials() }}
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                    {{ $conversation->contact->getDisplayName() }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $conversation->contact->phone }}
                    @if($conversation->whatsappSession)
                        &middot; {{ $conversation->whatsappSession->name }}
                    @endif
                </p>
            </div>
            <div>
                <span @class([
                    'inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full',
                    'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' => $conversation->status->value === 'open',
                    'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300' => $conversation->status->value === 'pending',
                    'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' => $conversation->status->value === 'closed',
                ])>
                    {{ $conversation->status->label() }}
                </span>
            </div>
        </div>
    @endif

    {{-- Messages Area --}}
    <div
        id="chat-messages"
        class="flex-1 overflow-y-auto p-4 space-y-3"
        x-data
        x-init="$nextTick(() => { $el.scrollTop = $el.scrollHeight })"
        x-effect="$wire.messages; $nextTick(() => { $el.scrollTop = $el.scrollHeight })"
    >
        @forelse($messages as $index => $message)
            {{-- Date separator --}}
            @if($index === 0 || ($messages[$index - 1]['date'] ?? '') !== $message['date'])
                <div class="flex items-center justify-center my-2">
                    <span class="px-3 py-1 text-xs bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 rounded-full">
                        {{ \Carbon\Carbon::parse($message['date'])->format('M d, Y') }}
                    </span>
                </div>
            @endif

            {{-- Message Bubble --}}
            <div @class([
                'flex',
                'justify-end' => $message['is_from_me'],
                'justify-start' => !$message['is_from_me'],
            ])>
                <div @class([
                    'max-w-[75%] rounded-2xl px-4 py-2 shadow-sm',
                    'bg-primary-500 text-white rounded-br-md' => $message['is_from_me'],
                    'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white rounded-bl-md' => !$message['is_from_me'],
                ])>
                    {{-- Media preview --}}
                    @if($message['is_media'] && $message['media_url'])
                        <div class="mb-2">
                            @if(str_starts_with($message['type'], 'image'))
                                <img src="{{ $message['media_url'] }}" alt="Image" class="rounded-lg max-w-full max-h-64 object-cover">
                            @elseif($message['type'] === 'audio')
                                <div class="flex items-center gap-2">
                                    <x-heroicon-o-microphone class="w-5 h-5" />
                                    <span class="text-sm">{{ __('refineder-crm::chat.audio_message') }}</span>
                                </div>
                            @elseif($message['type'] === 'video')
                                <div class="flex items-center gap-2">
                                    <x-heroicon-o-video-camera class="w-5 h-5" />
                                    <span class="text-sm">{{ __('refineder-crm::chat.video_message') }}</span>
                                </div>
                            @elseif($message['type'] === 'document')
                                <div class="flex items-center gap-2">
                                    <x-heroicon-o-document class="w-5 h-5" />
                                    <span class="text-sm">{{ __('refineder-crm::chat.document_message') }}</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="text-sm">{{ $message['type_label'] }}</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Text content --}}
                    @if($message['body'])
                        <p class="text-sm whitespace-pre-wrap break-words">{{ $message['body'] }}</p>
                    @endif

                    {{-- Time & Status --}}
                    <div @class([
                        'flex items-center gap-1 mt-1',
                        'justify-end' => $message['is_from_me'],
                        'justify-start' => !$message['is_from_me'],
                    ])>
                        <span @class([
                            'text-[10px]',
                            'text-primary-200' => $message['is_from_me'],
                            'text-gray-400 dark:text-gray-500' => !$message['is_from_me'],
                        ])>
                            {{ $message['time'] }}
                        </span>

                        @if($message['is_from_me'])
                            <span @class([
                                'text-[10px]',
                                'text-primary-200' => in_array($message['status'], ['pending', 'sent']),
                                'text-blue-200' => $message['status'] === 'delivered',
                                'text-green-200' => $message['status'] === 'read',
                                'text-red-300' => $message['status'] === 'failed',
                            ])>
                                @if($message['status'] === 'pending')
                                    &#x231B;
                                @elseif($message['status'] === 'sent')
                                    &#x2713;
                                @elseif($message['status'] === 'delivered')
                                    &#x2713;&#x2713;
                                @elseif($message['status'] === 'read')
                                    &#x2713;&#x2713;
                                @elseif($message['status'] === 'failed')
                                    &#x2717;
                                @endif
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="flex items-center justify-center h-full text-gray-400 dark:text-gray-500">
                <div class="text-center">
                    <x-heroicon-o-chat-bubble-left-right class="w-12 h-12 mx-auto mb-2 opacity-50" />
                    <p class="text-sm">{{ __('refineder-crm::chat.no_messages') }}</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Message Input --}}
    <div class="border-t border-gray-200 dark:border-gray-700 p-3 bg-gray-50 dark:bg-gray-800">
        <form wire:submit="sendMessage" class="flex items-end gap-2">
            <div class="flex-1">
                <textarea
                    wire:model="messageText"
                    placeholder="{{ __('refineder-crm::chat.type_message') }}"
                    rows="1"
                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm resize-none focus:ring-primary-500 focus:border-primary-500 placeholder:text-gray-400"
                    x-data
                    x-on:keydown.enter.prevent="if (!$event.shiftKey) { $wire.sendMessage() }"
                    x-on:input="$el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 120) + 'px'"
                ></textarea>
            </div>
            <button
                type="submit"
                class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-full bg-primary-500 hover:bg-primary-600 text-white transition-colors duration-150"
                wire:loading.attr="disabled"
            >
                <svg wire:loading.remove class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                <svg wire:loading class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </button>
        </form>
    </div>
</div>

<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Chat Area --}}
        <div class="lg:col-span-2">
            @livewire('refineder-crm-chat-box', ['conversationId' => $record->id])
        </div>

        {{-- Sidebar: Contact Info, Deals & Quick Actions --}}
        <div class="space-y-4">
            {{-- Contact Card --}}
            <x-filament::section>
                <x-slot name="heading">
                    {{ __('refineder-crm::conversations.sidebar.contact_info') }}
                </x-slot>

                @if($record->contact)
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-primary-500 flex items-center justify-center text-white font-bold">
                                {{ $record->contact->getInitials() }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">
                                    {{ $record->contact->getDisplayName() }}
                                </p>
                                <p class="text-sm text-gray-500">{{ $record->contact->phone }}</p>
                            </div>
                        </div>

                        @if($record->contact->email)
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <x-heroicon-o-envelope class="w-4 h-4" />
                                <span>{{ $record->contact->email }}</span>
                            </div>
                        @endif

                        @if($record->contact->company)
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <x-heroicon-o-building-office class="w-4 h-4" />
                                <span>{{ $record->contact->company }}</span>
                            </div>
                        @endif

                        @if($record->contact->notes)
                            <div class="mt-2 p-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $record->contact->notes }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </x-filament::section>

            {{-- Active Deal --}}
            @if($record->contact)
                @php
                    $activeDeal = $record->contact->deals()
                        ->whereNotIn('stage', ['won', 'lost'])
                        ->latest()
                        ->first();
                @endphp

                @if($activeDeal)
                    <x-filament::section>
                        <x-slot name="heading">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-bolt class="w-4 h-4 text-primary-500" />
                                {{ __('refineder-crm::conversations.sidebar.active_deal') }}
                            </div>
                        </x-slot>

                        <div class="space-y-3">
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $activeDeal->title }}</p>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">{{ __('refineder-crm::deals.form.stage') }}</span>
                                <span @class([
                                    'inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full',
                                    'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' => $activeDeal->stage->value === 'lead',
                                    'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' => $activeDeal->stage->value === 'qualified',
                                    'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300' => $activeDeal->stage->value === 'proposal',
                                    'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300' => $activeDeal->stage->value === 'negotiation',
                                ])>
                                    {{ $activeDeal->stage->label() }}
                                </span>
                            </div>

                            @if($activeDeal->value > 0)
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500">{{ __('refineder-crm::deals.form.value') }}</span>
                                    <span class="font-semibold text-gray-900 dark:text-white text-sm">
                                        {{ number_format($activeDeal->value, 2) }} {{ $activeDeal->currency }}
                                    </span>
                                </div>
                            @endif

                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">{{ __('refineder-crm::deals.form.priority') }}</span>
                                <span @class([
                                    'inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full',
                                    'bg-gray-100 text-gray-600' => $activeDeal->priority->value === 'low',
                                    'bg-blue-100 text-blue-600' => $activeDeal->priority->value === 'medium',
                                    'bg-yellow-100 text-yellow-600' => $activeDeal->priority->value === 'high',
                                    'bg-red-100 text-red-600' => $activeDeal->priority->value === 'urgent',
                                ])>
                                    {{ $activeDeal->priority->label() }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">{{ __('refineder-crm::conversations.sidebar.deal_created') }}</span>
                                <span class="text-xs text-gray-700 dark:text-gray-300">{{ $activeDeal->created_at->diffForHumans() }}</span>
                            </div>

                            @if($activeDeal->notes)
                                <div class="p-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($activeDeal->notes, 100) }}</p>
                                </div>
                            @endif
                        </div>
                    </x-filament::section>
                @endif

                {{-- Deal History --}}
                @php
                    $allDeals = $record->contact->deals()->latest()->get();
                    $closedDeals = $allDeals->filter(fn($d) => $d->isClosed());
                @endphp

                @if($allDeals->count() > 0)
                    <x-filament::section :collapsible="true" :collapsed="$closedDeals->count() > 0 && $activeDeal">
                        <x-slot name="heading">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-clipboard-document-list class="w-4 h-4" />
                                {{ __('refineder-crm::conversations.sidebar.deal_history') }}
                                <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-primary-500 rounded-full">
                                    {{ $allDeals->count() }}
                                </span>
                            </div>
                        </x-slot>

                        <div class="space-y-3">
                            @foreach($allDeals as $deal)
                                <div @class([
                                    'p-3 rounded-lg border transition-colors',
                                    'border-primary-200 bg-primary-50 dark:border-primary-800 dark:bg-primary-950' => !$deal->isClosed(),
                                    'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-950' => $deal->stage->value === 'won',
                                    'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950' => $deal->stage->value === 'lost',
                                ])>
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $deal->title }}
                                            </p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span @class([
                                                    'inline-flex items-center px-1.5 py-0.5 text-[10px] font-medium rounded',
                                                    'bg-gray-200 text-gray-700' => $deal->stage->value === 'lead',
                                                    'bg-blue-200 text-blue-700' => $deal->stage->value === 'qualified',
                                                    'bg-yellow-200 text-yellow-700' => $deal->stage->value === 'proposal',
                                                    'bg-primary-200 text-primary-700' => $deal->stage->value === 'negotiation',
                                                    'bg-green-200 text-green-700' => $deal->stage->value === 'won',
                                                    'bg-red-200 text-red-700' => $deal->stage->value === 'lost',
                                                ])>
                                                    {{ $deal->stage->label() }}
                                                </span>
                                                @if($deal->value > 0)
                                                    <span class="text-[10px] text-gray-500">
                                                        {{ number_format($deal->value, 2) }} {{ $deal->currency }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="text-[10px] text-gray-400 whitespace-nowrap">
                                            {{ $deal->created_at->format('M d') }}
                                        </span>
                                    </div>

                                    @if($deal->closed_at)
                                        <p class="text-[10px] text-gray-400 mt-1">
                                            {{ __('refineder-crm::conversations.sidebar.deal_closed_at') }}: {{ $deal->closed_at->diffForHumans() }}
                                        </p>
                                    @endif

                                    {{-- Link to conversation if it's a different one --}}
                                    @if($deal->conversation_id && $deal->conversation_id !== $record->id)
                                        <a href="{{ \Refineder\FilamentCrm\Resources\ConversationResource\ConversationResource::getUrl('view', ['record' => $deal->conversation_id]) }}"
                                           class="inline-flex items-center gap-1 mt-2 text-[10px] text-primary-600 hover:text-primary-700 dark:text-primary-400 transition-colors">
                                            <x-heroicon-o-chat-bubble-left-right class="w-3 h-3" />
                                            {{ __('refineder-crm::conversations.sidebar.view_conversation') }}
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </x-filament::section>
                @endif
            @endif

            {{-- Conversation Stats --}}
            <x-filament::section>
                <x-slot name="heading">
                    {{ __('refineder-crm::conversations.sidebar.stats') }}
                </x-slot>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">{{ __('refineder-crm::conversations.sidebar.total_messages') }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $record->messages()->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">{{ __('refineder-crm::conversations.sidebar.started') }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $record->created_at->diffForHumans() }}</span>
                    </div>
                    @if($record->contact && $record->contact->deals()->count() > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">{{ __('refineder-crm::conversations.sidebar.deals') }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $record->contact->deals()->count() }}</span>
                        </div>
                    @endif
                </div>
            </x-filament::section>

            {{-- Session Info --}}
            @if($record->whatsappSession)
                <x-filament::section>
                    <x-slot name="heading">
                        {{ __('refineder-crm::conversations.sidebar.session') }}
                    </x-slot>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">{{ __('refineder-crm::conversations.sidebar.session_name') }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $record->whatsappSession->name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 dark:text-gray-400">{{ __('refineder-crm::conversations.sidebar.session_status') }}</span>
                            <span @class([
                                'inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full',
                                'bg-green-100 text-green-700' => $record->whatsappSession->status->value === 'connected',
                                'bg-red-100 text-red-700' => $record->whatsappSession->status->value === 'disconnected',
                                'bg-yellow-100 text-yellow-700' => $record->whatsappSession->status->value === 'connecting',
                            ])>
                                {{ $record->whatsappSession->status->label() }}
                            </span>
                        </div>
                    </div>
                </x-filament::section>
            @endif
        </div>
    </div>
</x-filament-panels::page>

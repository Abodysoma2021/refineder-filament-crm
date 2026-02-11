<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Chat Area (Main) --}}
        <div class="lg:col-span-2">
            @if($record->hasConversation())
                @livewire('refineder-crm-chat-box', ['conversationId' => $record->conversation_id])
            @else
                <div class="flex items-center justify-center h-[600px] bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="text-center text-gray-400 dark:text-gray-500">
                        <x-heroicon-o-chat-bubble-left-right class="w-16 h-16 mx-auto mb-3 opacity-40" />
                        <p class="text-sm font-medium">{{ __('refineder-crm::deals.sidebar.no_conversation') }}</p>
                        <p class="text-xs mt-1">{{ __('refineder-crm::deals.sidebar.no_conversation_hint') }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar: Deal Management --}}
        <div class="space-y-4">
            {{-- Deal Info Card --}}
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-briefcase class="w-4 h-4 text-primary-500" />
                        {{ __('refineder-crm::deals.sidebar.deal_info') }}
                    </div>
                </x-slot>

                <div class="space-y-3">
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $record->title }}</p>
                    </div>

                    {{-- Stage --}}
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">{{ __('refineder-crm::deals.form.stage') }}</span>
                        <span @class([
                            'inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full',
                            'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' => $record->stage->value === 'lead',
                            'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' => $record->stage->value === 'qualified',
                            'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300' => $record->stage->value === 'proposal',
                            'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300' => $record->stage->value === 'negotiation',
                            'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' => $record->stage->value === 'won',
                            'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' => $record->stage->value === 'lost',
                        ])>
                            {{ $record->stage->label() }}
                        </span>
                    </div>

                    {{-- Priority --}}
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">{{ __('refineder-crm::deals.form.priority') }}</span>
                        <span @class([
                            'inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full',
                            'bg-gray-100 text-gray-600' => $record->priority->value === 'low',
                            'bg-blue-100 text-blue-600' => $record->priority->value === 'medium',
                            'bg-yellow-100 text-yellow-600' => $record->priority->value === 'high',
                            'bg-red-100 text-red-600' => $record->priority->value === 'urgent',
                        ])>
                            {{ $record->priority->label() }}
                        </span>
                    </div>

                    {{-- Value --}}
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">{{ __('refineder-crm::deals.form.value') }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white text-sm">
                            {{ number_format($record->value, 2) }} {{ $record->currency }}
                        </span>
                    </div>

                    {{-- Expected Close --}}
                    @if($record->expected_close_date)
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">{{ __('refineder-crm::deals.form.expected_close_date') }}</span>
                            <span @class([
                                'text-xs font-medium',
                                'text-red-600' => $record->isOverdue(),
                                'text-gray-700 dark:text-gray-300' => !$record->isOverdue(),
                            ])>
                                {{ $record->expected_close_date->format('M d, Y') }}
                                @if($record->isOverdue())
                                    ({{ __('refineder-crm::deals.sidebar.overdue') }})
                                @endif
                            </span>
                        </div>
                    @endif

                    {{-- Closed At --}}
                    @if($record->closed_at)
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">{{ __('refineder-crm::deals.sidebar.closed_at') }}</span>
                            <span class="text-xs text-gray-700 dark:text-gray-300">{{ $record->closed_at->diffForHumans() }}</span>
                        </div>
                    @endif

                    {{-- Created --}}
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">{{ __('refineder-crm::deals.table.created_at') }}</span>
                        <span class="text-xs text-gray-700 dark:text-gray-300">{{ $record->created_at->diffForHumans() }}</span>
                    </div>

                    {{-- Notes --}}
                    @if($record->notes)
                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800">
                            <p class="text-xs text-gray-500 mb-1">{{ __('refineder-crm::deals.form.notes') }}</p>
                            <p class="text-xs text-gray-700 dark:text-gray-300">{{ $record->notes }}</p>
                        </div>
                    @endif
                </div>
            </x-filament::section>

            {{-- Contact Card --}}
            @if($record->contact)
                <x-filament::section>
                    <x-slot name="heading">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-user class="w-4 h-4" />
                            {{ __('refineder-crm::deals.sidebar.contact_info') }}
                        </div>
                    </x-slot>

                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center text-white font-bold text-sm">
                                {{ $record->contact->getInitials() }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white text-sm">
                                    {{ $record->contact->getDisplayName() }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $record->contact->phone }}</p>
                            </div>
                        </div>

                        @if($record->contact->email)
                            <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                                <x-heroicon-o-envelope class="w-3.5 h-3.5" />
                                <span>{{ $record->contact->email }}</span>
                            </div>
                        @endif

                        @if($record->contact->company)
                            <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                                <x-heroicon-o-building-office class="w-3.5 h-3.5" />
                                <span>{{ $record->contact->company }}</span>
                            </div>
                        @endif
                    </div>
                </x-filament::section>
            @endif

            {{-- Deal History --}}
            @if($record->contact)
                @php
                    $allDeals = $record->contact->deals()->latest()->get();
                @endphp

                @if($allDeals->count() > 1)
                    <x-filament::section :collapsible="true" :collapsed="true">
                        <x-slot name="heading">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-clipboard-document-list class="w-4 h-4" />
                                {{ __('refineder-crm::deals.sidebar.deal_history') }}
                                <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-primary-500 rounded-full">
                                    {{ $allDeals->count() }}
                                </span>
                            </div>
                        </x-slot>

                        <div class="space-y-2">
                            @foreach($allDeals as $deal)
                                <div @class([
                                    'p-2.5 rounded-lg border transition-colors',
                                    'border-primary-300 bg-primary-50 dark:border-primary-700 dark:bg-primary-950 ring-1 ring-primary-200' => $deal->id === $record->id,
                                    'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-950' => $deal->id !== $record->id && $deal->stage->value === 'won',
                                    'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950' => $deal->id !== $record->id && $deal->stage->value === 'lost',
                                    'border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900' => $deal->id !== $record->id && !$deal->isClosed(),
                                ])>
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-gray-900 dark:text-white truncate">
                                                @if($deal->id === $record->id)
                                                    {{ $deal->title }} ({{ __('refineder-crm::deals.sidebar.current') }})
                                                @else
                                                    {{ $deal->title }}
                                                @endif
                                            </p>
                                            <div class="flex items-center gap-1.5 mt-1">
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

                                    @if($deal->id !== $record->id && $deal->conversation_id)
                                        <a href="{{ \Refineder\FilamentCrm\Resources\DealResource\DealResource::getUrl('view', ['record' => $deal->id]) }}"
                                           class="inline-flex items-center gap-1 mt-1.5 text-[10px] text-primary-600 hover:text-primary-700 dark:text-primary-400 transition-colors">
                                            <x-heroicon-o-arrow-top-right-on-square class="w-3 h-3" />
                                            {{ __('refineder-crm::deals.sidebar.view_deal') }}
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </x-filament::section>
                @endif
            @endif

            {{-- Session Info --}}
            @if($record->conversation && $record->conversation->whatsappSession)
                @php $session = $record->conversation->whatsappSession; @endphp
                <x-filament::section>
                    <x-slot name="heading">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-signal class="w-4 h-4" />
                            {{ __('refineder-crm::deals.sidebar.session') }}
                        </div>
                    </x-slot>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400 text-xs">{{ __('refineder-crm::sessions.table.name') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white text-xs">{{ $session->name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 dark:text-gray-400 text-xs">{{ __('refineder-crm::sessions.table.status') }}</span>
                            <span @class([
                                'inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-medium rounded-full',
                                'bg-green-100 text-green-700' => $session->status->value === 'connected',
                                'bg-red-100 text-red-700' => $session->status->value === 'disconnected',
                                'bg-yellow-100 text-yellow-700' => $session->status->value === 'connecting',
                            ])>
                                {{ $session->status->label() }}
                            </span>
                        </div>
                    </div>
                </x-filament::section>
            @endif
        </div>
    </div>
</x-filament-panels::page>

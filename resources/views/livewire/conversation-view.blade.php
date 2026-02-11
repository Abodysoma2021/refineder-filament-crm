<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Chat Area --}}
        <div class="lg:col-span-2">
            @livewire('refineder-crm-chat-box', ['conversationId' => $record->id])
        </div>

        {{-- Sidebar: Contact Info & Quick Actions --}}
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

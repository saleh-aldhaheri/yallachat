<div class="relative">
{{--  Models List  --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
        @foreach($models as $currentModel)
            <x-list-card>
                <x-slot:header>
                    <div class="space-x-5 flex items-center">
                        <div class="h-5 w-5 rounded-full {{$currentModel->is_available ? 'bg-them' : 'bg-danger'}}"></div>
                        <div class="space-y-2">
                            <h2 class="text-ink text-lg">{{ucfirst($currentModel->name->labels() )}}</h2>
                            <div class="flex  items-center space-x-1">
                                @if(!$currentModel->is_available)
                                    <span wire:click="connect({{$currentModel->id}})" class="inline-flex cursor-pointer">
                                        <x-gravityui-arrows-rotate-right wire:loading.remove wire:target="connect({{$currentModel->id}})"
                                                                          class="w-4 h-4 text-ink-muted hover:text-them transition-colors" />
                                        <x-gravityui-arrows-rotate-right wire:loading wire:target="connect({{$currentModel->id}})"
                                                                          class="w-4 h-4 text-them animate-spin" />
                                    </span>
                                @endif
                                <p class="text-ink-muted text-xs"> {{$currentModel->is_available ? "connected" : "disconnected"}}</p>
                            </div>
                        </div>
                    </div>
                </x-slot>

                <x-slot:badges>
                    <div class="flex gap-2">
                    <x-badge tooltip="your agent can send message schedule message with different languages"
                             dot-class="{{ $currentModel->is_multi_language ? 'bg-them' : 'border-ink-muted border-2' }}"
                             class="{{ $currentModel->is_multi_language ? 'w-fitbg-them/10 border-them text-them' : 'bg-ink-muted/10 border-ink-mutd text-ink-muted' }} flex-1 justify-center text-[11px]">
                        Multi-language
                    </x-badge>

                    <x-badge tooltip="the agent will replay automatically with the language of the sender for better communications"
                             dot-class="{{ $currentModel->is_auto_language ? 'bg-them' : 'border-ink-muted border-2' }}"
                             class="{{ $currentModel->is_auto_language ? 'bg-them/10 border-them text-them' : 'bg-ink-muted/10 border-ink-mutd text-ink-muted' }} flex-1 justify-center text-[11px]">
                        Auto-Language
                    </x-badge>
                    </div>
                </x-slot>

                <x-slot:actions>
                    <x-toggle :active="$currentModel->is_active" wire:click="toggleActive({{ $currentModel->id }})"
                              tooltip="to select which ai model the agent will use - only one model available at a time"/>
                </x-slot:actions>

                <x-slot:menu>
                    <x-btn-primary wire:click="update({{ $currentModel->id }})">update</x-btn-primary>
                    <x-btn-primary wire:click="view({{ $currentModel->id }})">view</x-btn-primary>
                    <x-btn-primary wire:click="showDelete({{ $currentModel->id }})">delete</x-btn-primary>
                </x-slot:menu>
            </x-list-card>
        @endforeach
    </div>

    {{-- View overlay --}}
    @if($isViewShow && $model)
        <x-view-modal title="{{ $model->name->labels() }}">
            <x-detail-list :rows="[
                ['label' => 'name', 'value' => $model->name->labels()],
                ['label' => 'api_key', 'value' => $model->api_key, 'class' => 'max-w-[250px] truncate'],
                ['label' => 'persona', 'value' => $model->persona ?: '—', 'class' => 'text-right max-w-[250px]'],
                ['label' => 'tone', 'value' => $model->tone ?: '—'],
                ['label' => 'multi_language', 'value' => $model->is_multi_language ? 'Yes' : 'No'],
                ['label' => 'auto_language', 'value' => $model->is_auto_language ? 'Yes' : 'No'],
                ['label' => 'active', 'value' => $model->is_active ? 'Yes' : 'No'],
                ['label' => 'available', 'value' => $model->is_available ? 'Yes' : 'No'],
            ]"/>
        </x-view-modal>
    @endif

    {{-- Delete confirmation overlay --}}
    @if($isDeleteShow && $model)
        <x-delete-confirmation title="delete model" entity="{{ $model->name->labels() }}"/>
    @endif

</div>

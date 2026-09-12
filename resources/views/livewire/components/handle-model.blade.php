<div class="h-full min-h-0 flex flex-col">
    <div class="flex-1 min-h-0 overflow-y-auto no-scrollbar">
        <div class="max-w-3xl mx-auto pb-6">
            <div class="bg-panel border border-border rounded-xl overflow-hidden">
                @php
                    $headSubtitle = $aiModel
                        ? 'Connected · <span class="text-them">'.($aiModel->is_active ? 'Handling chats' : 'Inactive').'</span>'
                        : 'This model becomes selectable once saved. It won\'t be active until you enable it from the list.';
                @endphp
                <x-form-head :title="$aiModel ? $aiModel->name->labels() : 'Add an AI model'"
                             :subtitle="$headSubtitle"
                             :status="$aiModel ? 'connected' : 'draft'"
                             :status-class="$aiModel ? 'border-them/50 text-them bg-them/10' : 'border-you/30 text-you bg-you/10'"/>

                {{-- Form body --}}
                <form wire:submit.prevent="submit">
                    <div class="px-4 sm:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-0">

                        <x-section-divider title="Identity &amp; access"/>

                        <x-form-field label="model" name="name">
                            <x-form-select wire:model="name">
                                <option value="" class="bg-panel">-- select model --</option>
                                @foreach($models as $model)
                                    <option value="{{ $model->value }}" class="bg-panel">{{ $model->labels() }}</option>
                                @endforeach
                            </x-form-select>
                        </x-form-field>

                        <x-form-field label="api_key" name="apiKey">
                            <div class="flex items-center gap-2 bg-[var(--color-panel)] border border-border rounded-md px-3 py-2.5">
                                <input type="text" wire:model="apiKey" placeholder="sk-..."
                                       class="flex-1 bg-transparent border-none outline-none text-ink font-mono text-[13px] placeholder:text-ink-muted/40 p-0">
                            </div>
                        </x-form-field>

                        <x-section-divider title="Behavior"/>

                        <x-form-field label="persona" name="persona" class="col-span-full">
                            <x-form-textarea resizable wire:model="persona" rows="3" placeholder="e.g. A concise, technical assistant for support agents..."/>
                        </x-form-field>

                        <x-form-field label="tone" name="tone" class="col-span-full">
                            <x-form-textarea resizable wire:model="tone" rows="3" placeholder="e.g. Friendly, plain language, no jargon..."/>
                        </x-form-field>

                        <x-section-divider title="Language"/>

                        <div class="col-span-full mb-4 space-y-3">
                            <x-toggle-row title="Multi-language" description="Model understands and can converse in more than one language.">
                                <x-toggle :active="$isMultiLanguage" wire:click="$toggle('isMultiLanguage')"/>
                            </x-toggle-row>
                            <x-toggle-row title="Auto-language" description="Reply in the same language the sender used, detected automatically.">
                                <x-toggle :active="$isAutoLanguage" wire:click="$toggle('isAutoLanguage')"/>
                            </x-toggle-row>
                        </div>

                        <x-form-error name="unsupported model type" class="col-span-full text-center mt-2"/>
                    </div>

                    <x-form-footer cancel-action="$dispatch('set-page', 'list_model')">
                        <x-submit-button>{{ $aiModel ? 'Save changes' : 'Run ./register-model' }}</x-submit-button>
                    </x-form-footer>
                </form>
            </div>
        </div>
    </div>
</div>

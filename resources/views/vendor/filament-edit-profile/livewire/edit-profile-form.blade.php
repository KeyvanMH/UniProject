<x-filament-panels::form id="profileForm" wire:submit.prevent="updateProfile">
    {{ $this->form }}
    <div class="fi-form-actions">
        <div class="flex flex-row-reverse flex-wrap items-center gap-3 fi-ac">
            <x-filament::button type="submit" >
                {{ __('filament-edit-profile::default.save') }}
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::form>

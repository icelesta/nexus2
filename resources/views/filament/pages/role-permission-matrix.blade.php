<x-filament::page>

    <div class="space-y-6">

        {{ $this->form }}

        <div>
            <x-filament::button
                wire:click="save"
                color="success"
            >
                Save Permissions
            </x-filament::button>
        </div>

    </div>

</x-filament::page>
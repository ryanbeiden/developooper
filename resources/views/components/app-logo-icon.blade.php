<flux:brand :href="route('home')" :name="config('app.name')" wire:navigate>
  <x-slot name="logo" class="size-6 rounded-sm bg-green-500 text-zinc-800 text-xs font-bold">
    <flux:icon name="rocket-launch" variant="micro" />
  </x-slot>
  <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
</flux:brand>

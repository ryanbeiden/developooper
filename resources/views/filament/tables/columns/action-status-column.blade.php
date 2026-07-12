@php
  use App\Models\Artifacts\Character;
  /** @var Character $record */
@endphp

<div {{ $getExtraAttributeBag()->class(['w-full mr-2']) }}>
  @if ($record->isInCooldown())
    <div class="flex items-center justify-end gap-2 w-full">
      <x-filament::badge color="warning">
        <span class="flex items-center gap-1">
          <x-filament::loading-indicator class="h-4 w-4" />
          <span>Gathering</span>
        </span>
      </x-filament::badge>
    </div>
  @else
    <div class="flex items-center justify-end gap-2 w-full">
      <x-filament::badge color="success">
        Idle
      </x-filament::badge>
    </div>
  @endif
</div>

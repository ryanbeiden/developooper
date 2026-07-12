<?php

namespace App\Filament\Widgets;

use App\Filament\Tables\Columns\ActionStatusColumn;
use App\Jobs\Artifacts\GenerateCharactersJob;
use App\Models\Artifacts\Character;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Bus;

class MyCharactersTableWidget extends TableWidget
{
    protected static ?string $heading = '';

    public ?string $characterBatchId = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Character::query())
            ->poll(fn (): ?string => $this->characterBatchId ? '2s' : null)
            ->columns([
                ImageColumn::make('skin')
                    ->label('')
                    ->width('1%')
                    ->visibility('public')
                    ->state(fn (Character $character) => $character->skinUrl()),

                TextColumn::make('name')
                    ->label('My Characters'),

                TextColumn::make('level')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                TextColumn::make('mining_level')
                    ->label('Mining')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                TextColumn::make('woodcutting_level')
                    ->label('Woodcutting')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                TextColumn::make('fishing_level')
                    ->label('Fishing')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                TextColumn::make('alchemy_level')
                    ->label('Alchemy')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                TextColumn::make('xp')
                    ->label('Experience')
                    ->formatStateUsing(fn (Character $character) => $character->currentXpOutOfTotal()),

                TextColumn::make('gold')
                    ->numeric()
                    ->icon('phosphor-currency-circle-dollar-duotone')
                    ->iconColor('warning'),

                TextColumn::make('layer')
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->badge()
                    ->color('info'),

                ActionStatusColumn::make('cooldown')->label('Status')->alignEnd(),
            ])
            ->deferLoading()
            ->paginated(false)
            ->emptyStateHeading('No Characters')
            ->emptyStateDescription('Create your characters, then they will appear here.')
            ->emptyStateIcon('phosphor-user-circle-dashed-duotone')
            ->emptyStateActions([
                Action::make('generateCharacters')
                    ->label('Generate Characters')
                    ->icon('phosphor-plus-circle-duotone')
                    ->schema([
                        Repeater::make('characters')
                            ->schema([
                                TextInput::make('name')->required(),
                                TextInput::make('skin')->required(),
                            ])
                            ->default([
                                ['name' => 'chunt', 'skin' => 'founder'],
                                ['name' => 'usidore', 'skin' => 'women1'],
                                ['name' => 'arnie', 'skin' => 'men1'],
                                ['name' => 'spintax', 'skin' => 'women2'],
                                ['name' => 'jynleeviah', 'skin' => 'women3'],
                            ])
                            ->minItems(5)
                            ->maxItems(5)
                            ->reorderable(false)
                            ->addable(false)
                            ->deletable(false),
                    ])
                    ->action(function (array $data): void {
                        $jobs = [];

                        foreach ($data['characters'] as $character) {
                            $jobs[] = new GenerateCharactersJob([
                                'name' => (string) $character['name'],
                                'skin' => (string) $character['skin'],
                            ]);
                        }

                        $batch = Bus::batch($jobs)->name('Create Artifacts Characters')->dispatch();

                        $this->characterBatchId = $batch->id;
                    }),
            ]);
    }

    public function hydrate(): void
    {
        $this->checkCharacterBatch();
    }

    public function checkCharacterBatch(): void
    {
        if (! $this->characterBatchId) {
            return;
        }

        $batch = Bus::findBatch($this->characterBatchId);

        if (! $batch) {
            return;
        }

        if ($batch->finished()) {
            $this->characterBatchId = null;
            $this->resetTable();

            Notification::make()
                ->success()
                ->title('Characters created.')
                ->send();
        }

        if ($batch->cancelled()) {
            $this->characterBatchId = null;

            Notification::make()
                ->danger()
                ->title('Character creation failed.')
                ->send();
        }
    }
}

<?php

namespace App\Models\Artifacts;

use ArtifactsMmo\Api\CharactersApi;
use ArtifactsMmo\Api\MyCharactersApi;
use ArtifactsMmo\Model\AddCharacterSchema;
use ArtifactsMmo\Model\CharacterSchema;
use ArtifactsMmo\Model\ErrorResponseSchema;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $xp
 * @property int $max_xp
 * @property string $skin
 */
class Character extends ArtifactsModel
{
    protected function performInsert(Builder $query): bool
    {
        $request = new AddCharacterSchema([
            'name' => $this->getAttribute('name'),
            'skin' => $this->getAttribute('skin'),
        ]);

        try {
            $response = app(CharactersApi::class)
                ->createCharacterCharactersCreatePost($request);

            if ($response instanceof ErrorResponseSchema) {
                throw new \RuntimeException($response->getError()->getMessage());
            }

            $createdCharacter = $response->getData();

            $this->forceFill(static::attributesFromSchema($createdCharacter, CharacterSchema::class));

            $this->exists = true;
            $this->wasRecentlyCreated = true;
            $this->syncOriginal();

            $this->fireModelEvent('created', false);

            return true;
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Could not create character')
                ->body($e->getMessage())
                ->danger()
                ->duration(10000)
                ->send();

            return false;
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getRows(): array
    {
        try {
            $characters = collect(app(MyCharactersApi::class)
                ->getMyCharactersMyCharactersGet()
                ->getData());

            return $characters
                ->map(fn (CharacterSchema $character) => static::attributesFromSchema($character, CharacterSchema::class))
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Could not get characters')
                ->body($e->getMessage())
                ->danger()
                ->duration(10000)
                ->send();

            return [];
        }
    }

    public function currentXpOutOfTotal(): string
    {
        return number_format($this->xp).' / '.number_format($this->max_xp);
    }

    public function skinUrl(): string
    {
        return url('/images/skins/'.$this->skin.'.png');
    }
}

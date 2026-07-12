<?php

namespace App\Jobs\Artifacts;

use App\Models\Artifacts\Character;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateCharactersJob implements ShouldQueue
{
    use Batchable, Queueable;

    /**
     * Create a new job instance.
     *
     * @param  array{name: string, skin: string}  $character
     */
    public function __construct(public array $character) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Character::create($this->character);
    }
}

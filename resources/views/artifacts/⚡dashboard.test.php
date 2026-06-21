<?php

use App\Models\User;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test('artifacts::dashboard')
        ->assertStatus(200);
});

it('has the component on the page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get('/artifacts')
        ->assertSeeLivewire('artifacts::dashboard');
});

it('can see dashboard', function () {
    Livewire::visit('artifacts::dashboard')
        ->assertSee('Artifacts Dashboard');
});

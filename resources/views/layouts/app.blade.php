<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  @include('partials.head')

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  @livewireStyles
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800 text-zinc-800 dark:text-white antialiased">
<flux:header container class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 mx-0">
  <x-app-logo-icon />

  <flux:navbar class="-mb-px max-lg:hidden">
    <flux:navbar.item icon="home" :href="route('home')" wire:navigate>Home</flux:navbar.item>
    <flux:navbar.item icon="beaker" :href="route('projects')" wire:navigate>Projects</flux:navbar.item>
    <flux:navbar.item icon="heart" :href="route('about')" wire:navigate>About</flux:navbar.item>
  </flux:navbar>

  <flux:spacer />

  <flux:navbar class="-mb-px max-lg:hidden">
    <flux:button :href="route('login')">Login</flux:button>
  </flux:navbar>
</flux:header>

<flux:main>
  {{ $slot }}
</flux:main>

@livewireScripts
</body>
</html>

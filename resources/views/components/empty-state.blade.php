@props(['icon' => 'inbox', 'title'])

<div class="empty-state">
    <x-ico :name="$icon" class="empty-state-icon" aria-hidden="true" />
    <p class="empty-state-text">{{ $title }}</p>
    @if (isset($slot) && ! $slot->isEmpty())
        <div class="mt-4">
            {{ $slot }}
        </div>
    @endif
</div>
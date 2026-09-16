@props(['event', 'class' => ''])

@php
    $display = $event->displayTitle();
    $canEdit = auth()->check() && auth()->user()->role === 'super_admin';
@endphp

@if($canEdit)
    <span {{ $attributes->merge(['class' => 'js-editable-title inline-flex items-start gap-1 max-w-full '.$class]) }}
          data-event-id="{{ $event->id }}"
          data-title="{{ e($display) }}"
          title="Click to edit title"
          role="button"
          tabindex="0">
        <span class="js-title-text break-words">{{ $display }}</span>
        <svg class="js-title-edit-icon w-3.5 h-3.5 mt-0.5 text-gray-400 shrink-0 opacity-60 hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
    </span>
@else
    <span {{ $attributes->merge(['class' => $class]) }}>{{ $display }}</span>
@endif

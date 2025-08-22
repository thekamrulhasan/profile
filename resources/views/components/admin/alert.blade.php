@props(['type' => 'info', 'dismissible' => true])

@php
    $classes = [
        'success' => 'alert-success',
        'error' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info',
    ];
    
    $icons = [
        'success' => 'fas fa-check',
        'error' => 'fas fa-ban',
        'warning' => 'fas fa-exclamation-triangle',
        'info' => 'fas fa-info',
    ];
@endphp

<div class="alert {{ $classes[$type] ?? $classes['info'] }} {{ $dismissible ? 'alert-dismissible' : '' }}">
    @if($dismissible)
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    @endif
    <i class="icon {{ $icons[$type] ?? $icons['info'] }}"></i>
    {{ $slot }}
</div>

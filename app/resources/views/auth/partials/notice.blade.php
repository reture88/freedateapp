@props([
    'kind' => 'info',
    'title',
    'message',
])

<div class="mtam-auth-notice mtam-auth-notice--{{ $kind }}" role="{{ 'error' === $kind ? 'alert' : 'status' }}">
    <span class="mtam-auth-notice__icon" aria-hidden="true">{{ 'error' === $kind ? '!' : ('success' === $kind ? 'ok' : 'i') }}</span>
    <span>
        <strong>{{ $title }}</strong>
        <small>{{ $message }}</small>
    </span>
</div>

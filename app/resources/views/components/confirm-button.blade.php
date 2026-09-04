<button type="submit" class="{{ $class ?? 'button button--danger' }}" onclick="return confirm(@js($message))">
    {{ $label }}
</button>

<button type="submit" class="{{ $class ?? 'button button--danger' }}" onclick="return confirm('{{ $message }}')">
    {{ $label }}
</button>

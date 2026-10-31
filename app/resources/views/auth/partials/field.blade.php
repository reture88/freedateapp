@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'placeholder' => '',
    'autocomplete' => null,
    'autofocus' => false,
    'required' => true,
    'help' => null,
])

@php($hasError = $errors->has($name))
@php($inputValue = 'password' === $type ? null : ($value ?? old($name)))

<div class="mtam-auth-field{{ $hasError ? ' mtam-auth-field--error' : '' }}">
    <label for="{{ $name }}">{{ $label }}</label>
    <span class="mtam-auth-field__control">
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            @if (null !== $inputValue) value="{{ $inputValue }}" @endif
            placeholder="{{ $placeholder }}"
            @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            @if ($autofocus) autofocus @endif
            @if ($required) required @endif
        >
        @if ('password' === $type)
            <button
                class="mtam-auth-password-toggle"
                type="button"
                aria-label="Show password"
                onclick="const input = this.parentElement.querySelector('input'); const showing = input.type === 'text'; input.type = showing ? 'password' : 'text'; this.setAttribute('aria-label', showing ? 'Show password' : 'Hide password'); this.textContent = showing ? 'show' : 'hide';"
            >show</button>
        @endif
    </span>
    @error($name)
        <p class="mtam-auth-field__error">{{ $message }}</p>
    @else
        @if ($help)
            <p class="mtam-auth-field__help">{{ $help }}</p>
        @endif
    @enderror
</div>

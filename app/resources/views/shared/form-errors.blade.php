@php($field = $field ?? null)

@if ($field && $errors->has($field))
    <p class="field-error">{{ $errors->first($field) }}</p>
@elseif (!$field && $errors->any())
    <div class="alert alert--error">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

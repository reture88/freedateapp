<section class="panel">
    <h2>{{ $title }}</h2>

    <form method="POST" action="{{ route('profile.address.save', ['type' => $type]) }}">
        @csrf

        <label for="{{ $type }}_name">Nev</label>
        <input id="{{ $type }}_name" name="name" type="text" value="{{ old('name', $address?->name ?? auth()->user()->name) }}" required>

        <label for="{{ $type }}_country">Orszagkod</label>
        <input id="{{ $type }}_country" name="country" type="text" value="{{ old('country', $address?->country ?? 'HU') }}" maxlength="2" required>

        <label for="{{ $type }}_postal_code">Iranyitoszam</label>
        <input id="{{ $type }}_postal_code" name="postal_code" type="text" value="{{ old('postal_code', $address?->postal_code) }}" required>

        <label for="{{ $type }}_city">Varos</label>
        <input id="{{ $type }}_city" name="city" type="text" value="{{ old('city', $address?->city) }}" required>

        <label for="{{ $type }}_street">Utca, hazszam</label>
        <input id="{{ $type }}_street" name="street" type="text" value="{{ old('street', $address?->street) }}" required>

        <label for="{{ $type }}_phone">Telefonszam</label>
        <input id="{{ $type }}_phone" name="phone" type="text" value="{{ old('phone', $address?->phone) }}">

        <button type="submit" class="button button--primary button--inline">Cim mentese</button>
    </form>
</section>

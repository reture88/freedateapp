@props([
    'title',
    'intro',
    'headline',
    'copy',
    'noteKind' => 'purple',
    'noteLabel' => null,
    'noteTitle' => null,
    'noteCopy' => null,
    'panelClass' => '',
])

<section class="mtam-auth-flow">
    <header class="mtam-auth-flow__top">
        @include('auth.partials.brand')
        {{ $topAction ?? '' }}
    </header>

    <div class="mtam-auth-flow__grid">
        <aside class="mtam-auth-flow__editorial">
            <h1>{{ $headline }}</h1>
            <p>{{ $copy }}</p>

            @if ($noteTitle)
                <div class="mtam-auth-editorial-note mtam-auth-editorial-note--{{ $noteKind }}">
                    @if ($noteLabel)
                        <span>{{ $noteLabel }}</span>
                    @endif
                    <strong>{{ $noteTitle }}</strong>
                    @if ($noteCopy)
                        <small>{{ $noteCopy }}</small>
                    @endif
                </div>
            @endif

            {{ $editorialExtra ?? '' }}
        </aside>

        <section class="mtam-auth-panel {{ $panelClass }}" aria-labelledby="mtam-auth-title">
            <div class="mtam-auth-panel__header">
                <h2 id="mtam-auth-title">{{ $title }}</h2>
                <p>{{ $intro }}</p>
            </div>

            @include('shared.flash')

            @if ($errors->any())
                @include('auth.partials.notice', [
                    'kind' => 'error',
                    'title' => 'Check the details',
                    'message' => 'Some information needs attention before you can continue.',
                ])
            @endif

            {{ $slot }}
        </section>
    </div>
</section>

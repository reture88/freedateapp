@php($active = $active ?? 'discover')

<nav class="mobile-bottom-nav" aria-label="Mobile discovery navigation">
    <a class="mobile-bottom-nav__item{{ 'discover' === $active ? ' mobile-bottom-nav__item--active' : '' }}" href="{{ route('discovery.show') }}" @if ('discover' === $active) aria-current="page" @endif>
        <span class="mobile-bottom-nav__icon" aria-hidden="true">&#9678;</span>
        <span>Discover</span>
    </a>
    <a class="mobile-bottom-nav__item{{ 'matches' === $active ? ' mobile-bottom-nav__item--active' : '' }}" href="{{ route('matches.index') }}" @if ('matches' === $active) aria-current="page" @endif>
        <span class="mobile-bottom-nav__icon" aria-hidden="true">&#9825;</span>
        <span>Matches</span>
    </a>
    <span class="mobile-bottom-nav__item mobile-bottom-nav__item--disabled" aria-disabled="true">
        <span class="mobile-bottom-nav__icon" aria-hidden="true">&#9645;</span>
        <span>Messages</span>
    </span>
    <a class="mobile-bottom-nav__item{{ 'profile' === $active ? ' mobile-bottom-nav__item--active' : '' }}" href="{{ route('profile.show') }}" @if ('profile' === $active) aria-current="page" @endif>
        <span class="mobile-bottom-nav__icon" aria-hidden="true">&#9817;</span>
        <span>Profile</span>
    </a>
</nav>

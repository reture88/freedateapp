<?php

declare(strict_types=1);

namespace App\Livewire\Preference;

use App\Models\ProfilePreference;
use App\Models\User;
use App\Preference\RequestHandler\SavePreference\SavePreferenceCommand;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use App\Shared\Infrastructure\Validation\ValidationException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

final class DiscoveryFilterSheet extends Component
{
    private const DEFAULT_AGE_MIN = 18;

    private const DEFAULT_AGE_MAX = 80;

    private const DEFAULT_DISTANCE_KM = 50;

    /** @var list<string> */
    private const DEFAULT_GENDERS = ['woman', 'man', 'non_binary', 'other'];

    /** @var list<string> */
    private const DEFAULT_ORIENTATIONS = ['straight', 'gay', 'lesbian', 'bisexual', 'pansexual', 'queer', 'other'];

    /** @var list<string> */
    private const DEFAULT_RELATIONSHIP_INTENTS = ['long_term', 'short_term', 'friendship', 'unsure'];

    public int $preferredAgeMin = self::DEFAULT_AGE_MIN;

    public int $preferredAgeMax = self::DEFAULT_AGE_MAX;

    public int $preferredDistanceKm = self::DEFAULT_DISTANCE_KM;

    /** @var list<string> */
    public array $preferredGenders = self::DEFAULT_GENDERS;

    /** @var list<string> */
    public array $preferredOrientations = self::DEFAULT_ORIENTATIONS;

    /** @var list<string> */
    public array $relationshipIntents = self::DEFAULT_RELATIONSHIP_INTENTS;

    public bool $excludeAiWingmanEnabled = false;

    /** @var list<string> */
    public array $dealbreakers = [];

    /** @var list<string> */
    public array $softPreferences = [];

    /** @var array<int, string> */
    public array $distanceOptions = [
        2 => '2 km',
        5 => '5 km',
        10 => '10 km',
        25 => '25 km',
        50 => '50 km',
    ];

    /** @var array<string, string> */
    public array $intentOptions = [
        'long_term' => 'Relationship',
        'short_term' => 'Dating',
        'friendship' => 'Friendship',
        'unsure' => 'Open to see',
    ];

    public function mount(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        $preference = $user?->datingProfile()->first()?->preference()->first();

        if (!$preference instanceof ProfilePreference) {
            return;
        }

        $this->preferredAgeMin = $preference->preferred_age_min;
        $this->preferredAgeMax = $preference->preferred_age_max;
        $this->preferredDistanceKm = $preference->preferred_distance_km;
        $this->includeCurrentDistanceOption();
        $this->preferredGenders = $preference->preferred_genders;
        $this->preferredOrientations = $preference->preferred_orientations;
        $this->relationshipIntents = $preference->relationship_intents;
        $this->excludeAiWingmanEnabled = $preference->exclude_ai_wingman_enabled;
        $this->dealbreakers = $preference->dealbreakers ?? [];
        $this->softPreferences = $preference->soft_preferences ?? [];
    }

    public function updatedPreferredAgeMin(): void
    {
        $this->normalizeAgeRange();
    }

    public function updatedPreferredAgeMax(): void
    {
        $this->normalizeAgeRange();
    }

    public function resetFilters(CommandBusInterface $commandBus): void
    {
        $this->preferredAgeMin = self::DEFAULT_AGE_MIN;
        $this->preferredAgeMax = self::DEFAULT_AGE_MAX;
        $this->preferredDistanceKm = self::DEFAULT_DISTANCE_KM;
        $this->preferredGenders = self::DEFAULT_GENDERS;
        $this->preferredOrientations = self::DEFAULT_ORIENTATIONS;
        $this->relationshipIntents = self::DEFAULT_RELATIONSHIP_INTENTS;
        $this->excludeAiWingmanEnabled = false;
        $this->dealbreakers = [];
        $this->softPreferences = [];

        if ($this->persist($commandBus)) {
            $this->redirectRoute('discovery.show');
        }
    }

    public function showMatches(CommandBusInterface $commandBus): void
    {
        if ($this->persist($commandBus)) {
            $this->redirectRoute('discovery.show');
        }
    }

    public function render(): View
    {
        return view('livewire.preference.discovery-filter-sheet');
    }

    private function persist(CommandBusInterface $commandBus): bool
    {
        $this->resetErrorBag();
        $this->normalizeAgeRange();

        /** @var User|null $user */
        $user = Auth::user();

        if (null === $user) {
            $this->addError('auth', 'You must be logged in to update filters.');

            return false;
        }

        try {
            $commandBus->dispatchPayload(SavePreferenceCommand::class, [
                'user_id' => $user->getKey(),
                'preferred_age_min' => $this->preferredAgeMin,
                'preferred_age_max' => $this->preferredAgeMax,
                'preferred_distance_km' => $this->preferredDistanceKm,
                'preferred_genders' => $this->preferredGenders,
                'preferred_orientations' => $this->preferredOrientations,
                'relationship_intents' => $this->relationshipIntents,
                'exclude_ai_wingman_enabled' => $this->excludeAiWingmanEnabled,
                'dealbreakers' => $this->dealbreakers,
                'soft_preferences' => $this->softPreferences,
            ], new SystemContext((string) $user->getKey(), request()->ip()));
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }

            return false;
        }

        return true;
    }

    private function normalizeAgeRange(): void
    {
        $this->preferredAgeMin = max(18, min(120, (int) $this->preferredAgeMin));
        $this->preferredAgeMax = max(18, min(120, (int) $this->preferredAgeMax));

        if ($this->preferredAgeMin > $this->preferredAgeMax) {
            $this->preferredAgeMax = $this->preferredAgeMin;
        }
    }

    private function includeCurrentDistanceOption(): void
    {
        if (array_key_exists($this->preferredDistanceKm, $this->distanceOptions)) {
            return;
        }

        $this->distanceOptions[$this->preferredDistanceKm] = sprintf('%d km', $this->preferredDistanceKm);
        ksort($this->distanceOptions);
    }
}

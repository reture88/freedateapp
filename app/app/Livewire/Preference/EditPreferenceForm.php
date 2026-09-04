<?php

declare(strict_types=1);

namespace App\Livewire\Preference;

use App\Models\ProfilePreference;
use App\Models\User;
use App\Preference\Dto\PreferenceSummary;
use App\Preference\RequestHandler\SavePreference\SavePreferenceCommand;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use App\Shared\Infrastructure\Validation\ValidationException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

final class EditPreferenceForm extends Component
{
    public int $preferredAgeMin;

    public int $preferredAgeMax;

    public int $preferredDistanceKm;

    /** @var list<string> */
    public array $preferredGenders = [];

    /** @var list<string> */
    public array $preferredOrientations = [];

    /** @var list<string> */
    public array $relationshipIntents = [];

    public bool $excludeAiWingmanEnabled = false;

    /** @var list<string> */
    public array $dealbreakers = [];

    /** @var list<string> */
    public array $softPreferences = [];

    public string $summaryDisplayText = '';

    /** @var array<string, string> */
    public array $genderOptions = [
        'woman' => 'Woman',
        'man' => 'Man',
        'non_binary' => 'Non-binary',
        'other' => 'Other',
    ];

    /** @var array<string, string> */
    public array $orientationOptions = [
        'straight' => 'Straight',
        'gay' => 'Gay',
        'lesbian' => 'Lesbian',
        'bisexual' => 'Bisexual',
        'pansexual' => 'Pansexual',
        'queer' => 'Queer',
        'other' => 'Other',
    ];

    /** @var array<string, string> */
    public array $intentOptions = [
        'long_term' => 'Long-term relationship',
        'short_term' => 'Short-term dating',
        'friendship' => 'Friendship first',
        'unsure' => 'Not sure yet',
    ];

    /** @var array<string, string> */
    public array $dealbreakerOptions = [
        'smoker' => 'Smoker',
        'no_kids' => 'Does not want kids',
        'far_away' => 'Too far away',
    ];

    /** @var array<string, string> */
    public array $softPreferenceOptions = [
        'pets' => 'Pets',
        'weekend_trips' => 'Weekend trips',
        'fitness' => 'Fitness',
    ];

    public function mount(ProfilePreference $preference, PreferenceSummary $summary): void
    {
        $this->preferredAgeMin = $preference->preferred_age_min;
        $this->preferredAgeMax = $preference->preferred_age_max;
        $this->preferredDistanceKm = $preference->preferred_distance_km;
        $this->preferredGenders = $preference->preferred_genders;
        $this->preferredOrientations = $preference->preferred_orientations;
        $this->relationshipIntents = $preference->relationship_intents;
        $this->excludeAiWingmanEnabled = $preference->exclude_ai_wingman_enabled;
        $this->dealbreakers = $preference->dealbreakers ?? [];
        $this->softPreferences = $preference->soft_preferences ?? [];
        $this->summaryDisplayText = $summary->displayText;
    }

    public function save(CommandBusInterface $commandBus): void
    {
        $this->resetErrorBag();

        /** @var User|null $user */
        $user = Auth::user();

        if (null === $user) {
            $this->addError('auth', 'You must be logged in to save preferences.');

            return;
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

            return;
        }

        session()->flash('success', 'Dating preferences saved.');
    }

    public function render(): View
    {
        return view('livewire.preference.edit-preference-form');
    }
}

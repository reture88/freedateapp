<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Models\DatingProfile;
use App\Models\ProfileTagCategory;
use App\Models\User;
use App\Profile\RequestHandler\ChoosePrimaryProfilePhoto\ChoosePrimaryProfilePhotoCommand;
use App\Profile\RequestHandler\SaveDatingProfile\SaveDatingProfileCommand;
use App\Profile\RequestHandler\UploadProfilePhoto\UploadProfilePhotoCommand;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use App\Shared\Infrastructure\Validation\ValidationException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

final class DatingProfileForm extends Component
{
    use WithFileUploads;

    public ?int $profileId = null;

    public string $displayName = '';

    public string $birthdate = '';

    public string $gender = 'woman';

    public string $orientation = 'straight';

    public string $city = '';

    public string $countryCode = 'HU';

    public ?string $approximateLatitude = null;

    public ?string $approximateLongitude = null;

    public string $relationshipIntent = 'long_term';

    public ?string $bio = null;

    public bool $aiWingmanEnabled = false;

    public bool $privacyConsent = false;

    /** @var list<int> */
    public array $selectedProfileTags = [];

    /** @var array<int, string> */
    public array $profileTagVisibility = [];

    public ?TemporaryUploadedFile $photo = null;

    /** @var list<array{id: int, label: string, tags: list<array{id: int, label: string}>}> */
    public array $tagCategories = [];

    /** @var list<array{id: int, url: string, is_primary: bool}> */
    public array $photos = [];

    /** @var array<string, string> */
    public array $genderOptions = [
        'man' => 'Man',
        'woman' => 'Woman',
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

    public function mount(): void
    {
        $this->loadProfile();
        $this->loadTagCategories();
    }

    public function save(CommandBusInterface $commandBus): mixed
    {
        $this->resetErrorBag();

        $user = $this->authenticatedUser();

        if (null === $user) {
            $this->addError('auth', 'You must be logged in to save your dating profile.');

            return null;
        }

        try {
            $commandBus->dispatchPayload(SaveDatingProfileCommand::class, [
                'user_id' => $user->getKey(),
                'display_name' => $this->displayName,
                'birthdate' => $this->birthdate,
                'gender' => $this->gender,
                'orientation' => $this->orientation,
                'city' => $this->city,
                'country_code' => $this->countryCode,
                'approximate_latitude' => $this->approximateLatitude,
                'approximate_longitude' => $this->approximateLongitude,
                'relationship_intent' => $this->relationshipIntent,
                'bio' => $this->bio,
                'ai_wingman_enabled' => $this->aiWingmanEnabled,
                'privacy_consent' => $this->privacyConsent,
                'selected_profile_tags' => $this->selectedProfileTags,
                'profile_tag_visibility' => $this->profileTagVisibility,
            ], $this->context($user));
        } catch (ValidationException $exception) {
            $this->addValidationErrors($exception);

            return null;
        } catch (\InvalidArgumentException $exception) {
            $this->addError('birthdate', $exception->getMessage());

            return null;
        }

        return redirect('/dating-profile/preview')->with('success', 'Dating profile saved.');
    }

    public function uploadPhoto(CommandBusInterface $commandBus): void
    {
        $this->resetErrorBag('photo');

        $user = $this->authenticatedUser();

        if (null === $user || null === $this->photo) {
            $this->addError('photo', 'Choose a photo to upload.');

            return;
        }

        try {
            $commandBus->dispatchPayload(UploadProfilePhotoCommand::class, [
                'user_id' => $user->getKey(),
                'photo' => $this->photo,
            ], $this->context($user));
        } catch (ValidationException $exception) {
            $this->addValidationErrors($exception);

            return;
        }

        $this->photo = null;
        $this->loadProfile();
        session()->flash('success', 'Profile photo uploaded.');
    }

    public function choosePrimary(int $photoId, CommandBusInterface $commandBus): void
    {
        $user = $this->authenticatedUser();

        if (null === $user) {
            return;
        }

        $commandBus->dispatchPayload(ChoosePrimaryProfilePhotoCommand::class, [
            'user_id' => $user->getKey(),
            'photo_id' => $photoId,
        ], $this->context($user));

        $this->loadProfile();
        session()->flash('success', 'Primary profile photo updated.');
    }

    public function render(): View
    {
        return view('livewire.profile.dating-profile-form');
    }

    private function loadProfile(): void
    {
        $profile = $this->authenticatedUser()?->datingProfile()
            ->with(['photos', 'tags'])
            ->first();

        if (!$profile instanceof DatingProfile) {
            return;
        }

        $this->profileId = (int) $profile->getKey();
        $this->displayName = $profile->display_name;
        $this->birthdate = $profile->birthdate->toDateString();
        $this->gender = $profile->gender;
        $this->orientation = $profile->orientation;
        $this->city = $profile->city;
        $this->countryCode = $profile->country_code;
        $this->approximateLatitude = null === $profile->approximate_latitude ? null : (string) $profile->approximate_latitude;
        $this->approximateLongitude = null === $profile->approximate_longitude ? null : (string) $profile->approximate_longitude;
        $this->relationshipIntent = $profile->relationship_intent;
        $this->bio = $profile->bio;
        $this->aiWingmanEnabled = $profile->ai_wingman_enabled;
        $this->privacyConsent = true;
        $this->selectedProfileTags = $profile->tags()
            ->pluck('profile_tags.id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->values()
            ->all();
        $this->profileTagVisibility = $profile->tags()
            ->pluck('dating_profile_tags.visibility', 'profile_tags.id')
            ->mapWithKeys(static fn (mixed $visibility, mixed $tagId): array => [(int) $tagId => (string) $visibility])
            ->all();
        $this->photos = $profile->photos
            ->sortBy('sort_order')
            ->map(static fn ($photo): array => [
                'id' => (int) $photo->getKey(),
                'url' => $photo->publicUrl(),
                'is_primary' => (bool) $photo->is_primary,
            ])
            ->values()
            ->all();
    }

    private function loadTagCategories(): void
    {
        $this->tagCategories = ProfileTagCategory::query()
            ->with(['tags' => fn ($query) => $query->orderBy('label')])
            ->orderBy('sort_order')
            ->get()
            ->map(static fn (ProfileTagCategory $category): array => [
                'id' => (int) $category->getKey(),
                'label' => $category->label,
                'tags' => $category->tags
                    ->map(static fn ($tag): array => [
                        'id' => (int) $tag->getKey(),
                        'label' => $tag->label,
                    ])
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }

    private function authenticatedUser(): ?User
    {
        $user = Auth::user();

        return $user instanceof User ? $user : null;
    }

    private function context(User $user): SystemContext
    {
        return new SystemContext((string) $user->getKey(), request()->ip());
    }

    private function addValidationErrors(ValidationException $exception): void
    {
        foreach ($exception->errors() as $field => $messages) {
            foreach ($messages as $message) {
                $this->addError($field, $message);
            }
        }
    }
}

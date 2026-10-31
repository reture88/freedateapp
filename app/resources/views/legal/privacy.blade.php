@extends('layouts.public')

@section('title', 'Privacy Policy')
@section('brand_label', 'MTAM')
@section('brand_detail', 'More Than A Match')

@section('content')
    {{-- Legal review required before production launch. --}}
    <section class="legal-page">
        <p class="eyebrow">MTAM legal</p>
        <h1>Privacy Policy</h1>
        <p class="legal-page__updated">Draft foundation. Requires legal review before production use.</p>

        <div class="legal-page__body">
            <h2>1. Information you provide</h2>
            <p>MTAM collects account information and dating profile information that you submit, such as email, display name, date of birth, interests, photos, preferences, reports, blocks, matches, and messages.</p>

            <h2>2. How information is used</h2>
            <p>Information is used to operate the product, support discovery, matches and messaging, protect safety, respond to reports, and maintain account access.</p>

            <h2>3. Profile visibility</h2>
            <p>Dating profile information may be shown to other users according to the product's visibility and matching rules.</p>

            <h2>4. Safety information</h2>
            <p>Reports, blocks, moderation actions, and related audit records may be used to investigate safety concerns and operate account restrictions.</p>

            <h2>5. Account lifecycle</h2>
            <p>Account deactivation and deletion-request flows may affect profile visibility and product access. Deletion handling requires a final reviewed retention policy.</p>

            <h2>6. Contact and legal details</h2>
            <p>Final contact, controller, jurisdiction, retention, and user-rights details must be supplied through legal review before production launch.</p>
        </div>
    </section>
@endsection

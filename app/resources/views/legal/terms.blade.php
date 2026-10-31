@extends('layouts.public')

@section('title', 'Terms of Service')
@section('brand_label', 'MTAM')
@section('brand_detail', 'More Than A Match')

@section('content')
    {{-- Legal review required before production launch. --}}
    <section class="legal-page">
        <p class="eyebrow">MTAM legal</p>
        <h1>Terms of Service</h1>
        <p class="legal-page__updated">Draft foundation. Requires legal review before production use.</p>

        <div class="legal-page__body">
            <h2>1. Adults-only service</h2>
            <p>MTAM is intended only for people who are at least 18 years old. You may not create or use a dating profile if you are under 18.</p>

            <h2>2. Account responsibility</h2>
            <p>You are responsible for keeping your account credentials secure and for the activity that happens through your account.</p>

            <h2>3. Dating profile information</h2>
            <p>You agree to provide accurate profile information, including your date of birth, and to keep your profile respectful and lawful.</p>

            <h2>4. Safety and moderation</h2>
            <p>MTAM may restrict accounts or profiles that appear to violate product rules, safety expectations, or applicable law.</p>

            <h2>5. No guarantee of matches</h2>
            <p>MTAM helps people discover and communicate with potential matches, but it does not guarantee compatibility, matches, dates, or outcomes.</p>

            <h2>6. Changes to these terms</h2>
            <p>These terms may change as the product and legal review process mature. Updated terms should be published on this page.</p>
        </div>
    </section>
@endsection

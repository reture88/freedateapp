@foreach (['success', 'status', 'error'] as $messageType)
    @if (session($messageType))
        <div class="alert alert--{{ $messageType }}" role="status">
            {{ session($messageType) }}
        </div>
    @endif
@endforeach

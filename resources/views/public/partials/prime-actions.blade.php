@if (! empty($actions))
    <div class="prime-button-row">
        @foreach ($actions as $action)
            @php
                $label = data_get($action, "label.$locale", data_get($action, 'label.en', data_get($action, 'label', 'Learn more')));
            @endphp
            <a class="prime-button {{ data_get($action, 'variant') ? 'is-' . data_get($action, 'variant') : '' }}" href="{{ data_get($action, 'url', '#') }}">{{ $label }}</a>
        @endforeach
    </div>
@endif

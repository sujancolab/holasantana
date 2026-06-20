@if (! empty($actions))
    <div class="prime-button-row">
        @foreach ($actions as $action)
            <a class="prime-button {{ data_get($action, 'variant') ? 'is-' . data_get($action, 'variant') : '' }}" href="{{ data_get($action, 'url', '#') }}">{{ data_get($action, 'label', 'Learn more') }}</a>
        @endforeach
    </div>
@endif

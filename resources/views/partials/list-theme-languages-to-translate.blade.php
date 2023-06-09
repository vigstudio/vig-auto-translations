@if (count($groups) > 1)
    <span class="admin-list-language-chooser">
        <span>{{ trans('plugins/translation::translation.translations') }}: </span>
        @foreach ($groups as $language)
            @if ($language['locale'] !== $group['locale'])
                <span>
                    <a href="{{ route('vig-auto-translations.theme', $language['locale'] == 'en' ? [] : ['ref_lang' => $language['locale']]) }}">{{ $language['name'] }}</a>
                </span>&nbsp;
            @endif
        @endforeach
        <input type="hidden" name="ref_lang" value="{{ BaseHelper::stringify(request()->input('ref_lang')) }}">
    </span>
@endif

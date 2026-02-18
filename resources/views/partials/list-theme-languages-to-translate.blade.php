@php
    $languages ??= $groups;
    $currentGroup ??= $group;
    $routeName ??= 'vig-auto-translations.theme';
@endphp

@if (count($languages) > 1)
    <div class="text-end d-flex gap-2 justify-content-start justify-content-lg-end align-items-center list-languages-chooser">
        <h4 class="mb-0 me-2">{{ trans('plugins/translation::translation.translations') }}:</h4>
        @if (count($languages) <= 3)
            <div class="d-flex gap-3 align-items-center">
                @foreach ($languages as $language)
                    @if ($language['locale'] !== $currentGroup['locale'])
                        <a
                            href="{{ route($routeName, $language['locale'] == 'en' ? [] : ['ref_lang' => $language['locale']]) }}"
                            class="text-decoration-none small"
                        >
                            @if (isset($language['flag']))
                                <span class="me-1">{{ $language['flag'] }}</span>
                            @endif
                            {{ $language['name'] }}
                        </a>
                    @endif
                @endforeach
            </div>
        @else
            <x-core::dropdown>
                <x-slot:trigger>
                    <a
                        class="d-flex align-items-center gap-2 dropdown-toggle text-muted text-decoration-none"
                        href="#"
                        data-bs-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        @if (isset($currentGroup['flag']))
                            <span>{{ $currentGroup['flag'] }}</span>
                        @endif
                        {{ $currentGroup['name'] }}
                    </a>
                </x-slot:trigger>

                @foreach ($languages as $language)
                    @if ($language['locale'] !== $currentGroup['locale'])
                        <x-core::dropdown.item
                            :href="route($routeName, $language['locale'] == 'en' ? [] : ['ref_lang' => $language['locale']])"
                            class="d-flex gap-2 align-items-center"
                        >
                            @if (isset($language['flag']))
                                <span>{{ $language['flag'] }}</span>
                            @endif
                            {{ $language['name'] }}
                        </x-core::dropdown.item>
                    @endif
                @endforeach
            </x-core::dropdown>
        @endif
        <input type="hidden" name="ref_lang" value="{{ BaseHelper::stringify(request()->input('ref_lang')) }}">
    </div>
@endif

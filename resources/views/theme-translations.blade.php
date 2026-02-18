@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core::alert type="info" :dismissible="false">
        <div class="d-flex gap-2">
            <x-core::icon name="ti ti-info-circle" class="alert-icon flex-shrink-0" />
            <div>
                <strong>{{ trans('plugins/vig-auto-translations::vig-auto-translations.help_text') }}</strong>
                <p class="mb-0 mt-1 text-muted small">{{ trans('plugins/vig-auto-translations::vig-auto-translations.theme_translations_help') }}</p>
            </div>
        </div>
    </x-core::alert>

    @if (count(\Botble\Base\Supports\Language::getAvailableLocales()) > 0)
        <!-- Language Tabs -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex gap-2 language-tabs" role="tablist">
                    @foreach ($groups as $lang)
                        @php
                            $isActive = ($lang['locale'] === $group['locale']);
                            $flag = $lang['flag'] ?? '';
                        @endphp
                        <a href="{{ route('vig-auto-translations.theme', $lang['locale'] == 'en' ? [] : ['ref_lang' => $lang['locale']]) }}"
                           class="language-tab @if($isActive) active @endif"
                           role="tab">
                            @if($flag)
                                <span class="me-1">{{ $flag }}</span>
                            @endif
                            <span class="language-name">{{ $lang['name'] }}</span>
                            @if($isActive)
                                <x-core::icon name="ti ti-check" class="ms-1 tab-check" />
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Translation Content -->
        <x-core::card>
            <x-slot:header>
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <x-core::icon name="ti ti-language" class="me-2" />
                        {{ trans('plugins/vig-auto-translations::vig-auto-translations.name_theme') }}
                        <span class="badge bg-primary ms-2">{{ $group['name'] }}</span>
                    </h3>

                    @if ($group['locale'] != 'en' && !empty($translations))
                        <form id="translate-all-form" action="{{ route('vig-auto-translations.theme.post-all') }}" method="POST">
                            @csrf
                            <input type="hidden" name="locale" value="{{ $group['locale'] }}">
                            <button type="button" class="btn btn-primary btn-translate-all">
                                <x-core::icon name="ti ti-rocket" class="me-1" />
                                {{ trans('plugins/vig-auto-translations::vig-auto-translations.translate_all', ['language' => $group['name']]) }}
                                <span class="badge bg-white text-primary ms-2">{{ count($translations) }}</span>
                            </button>
                        </form>
                    @endif
                </div>
            </x-slot:header>

            <x-core::card.body>
                @if ($group['locale'] == 'en')
                    <x-core::empty-state
                        :title="trans('plugins/vig-auto-translations::vig-auto-translations.cannot_translate_to_english')"
                        :subtitle="trans('plugins/vig-auto-translations::vig-auto-translations.select_another_language')"
                        icon="ti ti-language-off"
                    />
                @elseif (!empty($translations))
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 40%;">
                                        <div class="d-flex align-items-center gap-2">
                                            <x-core::icon name="ti ti-align-left" />
                                            {{ $defaultLanguage ? $defaultLanguage['name'] : 'en' }}
                                            <small class="text-muted">(Source)</small>
                                        </div>
                                    </th>
                                    <th style="width: 40%;">
                                        <div class="d-flex align-items-center gap-2">
                                            @if($group['flag'])
                                                <span class="me-1">{{ $group['flag'] }}</span>
                                            @endif
                                            {{ $group['name'] }}
                                            <small class="text-muted">(Target)</small>
                                        </div>
                                    </th>
                                    <th style="width: 20%;">
                                        <div class="d-flex align-items-center gap-2">
                                            <x-core::icon name="ti ti-settings" />
                                            {{ trans('plugins/vig-auto-translations::vig-auto-translations.actions') }}
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($translations as $key => $translation)
                                    <tr>
                                        <td>
                                            <code class="text-muted word-break">{{ $key }}</code>
                                        </td>
                                        <td>
                                            <a href="#" class="editable translation-value"
                                               data-name="{{ $key }}"
                                               data-type="textarea"
                                               data-pk="{{ $group['locale'] }}"
                                               data-url="{{ route('vig-auto-translations.theme') }}"
                                               data-title="{{ trans('plugins/translation::translation.edit_title') }}">
                                                {!! htmlentities($translation, ENT_QUOTES, 'UTF-8', false) !!}
                                            </a>
                                        </td>
                                        <td>
                                            @if ($key === $translation)
                                                <button class="btn btn-sm btn-primary btn-translate-row" data-name="{{ $key }}" data-reset="0" title="{{ trans('plugins/vig-auto-translations::vig-auto-translations.translate') }}">
                                                    <x-core::icon name="ti ti-rocket" />
                                                    <span class="ms-1">{{ trans('plugins/vig-auto-translations::vig-auto-translations.translate') }}</span>
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-warning btn-translate-row" data-name="{{ $key }}" data-reset="1" title="{{ trans('plugins/vig-auto-translations::vig-auto-translations.reset') }}">
                                                    <x-core::icon name="ti ti-refresh" />
                                                    <span class="ms-1">{{ trans('plugins/vig-auto-translations::vig-auto-translations.reset') }}</span>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <x-core::empty-state
                        :title="trans('plugins/vig-auto-translations::vig-auto-translations.empty_translations')"
                        :subtitle="trans('plugins/vig-auto-translations::vig-auto-translations.empty_translations_hint')"
                        icon="ti ti-file-off"
                    />
                @endif
            </x-core::card.body>
        </x-core::card>
    @else
        <x-core::card>
            <x-core::card.body>
                <x-core::empty-state
                    :title="trans('plugins/translation::translation.no_other_languages')"
                    :subtitle="trans('plugins/vig-auto-translations::vig-auto-translations.no_languages_hint')"
                    icon="ti ti-world"
                />
            </x-core::card.body>
        </x-core::card>
    @endif
@stop

@push('footer')
    <style>
        /* Language Tabs Styles */
        .language-tabs {
            flex-wrap: wrap;
        }

        .language-tab {
            display: flex;
            align-items: center;
            padding: 0.625rem 1rem;
            background: var(--tblr-card-bg, #fff);
            border: 1px solid var(--tblr-border-color, #e6e7e9);
            border-radius: 0.5rem;
            color: var(--tblr-body-color, #6c767d);
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            font-weight: 500;
        }

        .language-tab:hover {
            background: var(--tblr-primary-bg-subtle, #e7f5ff);
            border-color: var(--tblr-primary, #4c6ef5);
            color: var(--tblr-primary, #4c6ef5);
            transform: translateY(-1px);
        }

        .language-tab.active {
            background: var(--tblr-primary, #4c6ef5);
            border-color: var(--tblr-primary, #4c6ef5);
            color: #fff;
            box-shadow: 0 2px 8px rgba(76, 110, 245, 0.3);
        }

        .language-tab .tab-check {
            opacity: 0;
            transform: scale(0.5);
            transition: all 0.2s ease;
        }

        .language-tab.active .tab-check {
            opacity: 1;
            transform: scale(1);
        }

        /* Translation Value Styles */
        .translation-value {
            display: inline-block;
            padding: 0.375rem 0.5rem;
            background: var(--tblr-bg-surface-tertiary, #f6f7f8);
            border-radius: 0.375rem;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .translation-value:hover {
            background: var(--tblr-bg-surface-secondary, #e6e7e9);
            color: var(--tblr-primary, #4c6ef5);
        }

        .word-break {
            word-break: break-word;
            white-space: pre-wrap;
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            .language-tab:not(.active) {
                background: #1e293b;
                border-color: #334155;
            }

            .language-tab:hover {
                background: #1e3a5f;
            }

            .translation-value {
                background: #334155;
            }

            .translation-value:hover {
                background: #475569;
            }
        }
    </style>

    <script>
        $(document).on('click', '.btn-translate-row', function(event) {
            event.preventDefault();
            event.stopPropagation();
            var $btn = $(this);
            $btn.prop('disabled', true).addClass('button-loading');
            var name = $btn.data('name');
            var reset = $btn.data('reset');

            $.ajax({
                type: 'POST',
                url: "{{ route('vig-auto-translations.theme') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'pk': "{{ $group['locale'] }}",
                    'auto': !reset,
                    'value': name,
                    'name': name
                },
                success: res => {
                    if (!res.error) {
                        Botble.showSuccess(res.message);
                    } else {
                        Botble.showError(res.message);
                    }
                    location.reload();
                },
                error: res => {
                    $btn.prop('disabled', false).removeClass('button-loading');
                    Botble.handleError(res);
                }
            });
        });

        $(document).on('click', '.btn-translate-all', function(event) {
            event.preventDefault();
            event.stopPropagation();
            var $btn = $(this);
            $btn.prop('disabled', true).addClass('button-loading');

            $.ajax({
                type: 'POST',
                cache: false,
                url: $('#translate-all-form').prop('action'),
                data: new FormData($('#translate-all-form')[0]),
                contentType: false,
                processData: false,
                success: res => {
                    if (!res.error) {
                        Botble.showSuccess(res.message);
                    } else {
                        Botble.showError(res.message);
                    }
                    location.reload();
                },
                error: res => {
                    $btn.prop('disabled', false).removeClass('button-loading');
                    Botble.handleError(res);
                }
            });
        });

        // Initialize editable
        $('.editable').editable({
            mode: 'inline',
            emptytext: '',
            send: 'always',
            ajaxOptions: {
                type: 'POST',
                dataType: 'json'
            },
            success: function(response, newValue) {
                if (response.error) {
                    Botble.showError(response.message || 'Error saving translation');
                    return false;
                }
                Botble.showSuccess(response.message || '{{ trans('plugins/vig-auto-translations::vig-auto-translations.translated_successfully') }}');
            },
            error: function(xhr) {
                var errorMsg = 'Error: ' + (xhr.responseJSON?.message || xhr.statusText || 'Unknown error');
                Botble.showError(errorMsg);
            }
        });
    </script>
@endpush

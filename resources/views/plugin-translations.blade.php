@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core::alert type="info" :dismissible="false">
        <div class="d-flex gap-2">
            <x-core::icon name="ti ti-info-circle" class="alert-icon flex-shrink-0" />
            <div>
                <strong>{{ trans('plugins/vig-auto-translations::vig-auto-translations.help_text') }}</strong>
                <p class="mb-0 mt-1 text-muted small">{{ trans('plugins/vig-auto-translations::vig-auto-translations.plugin_translations_help') }}</p>
            </div>
        </div>
    </x-core::alert>

    <x-core::card>
        <x-slot:header>
            <h3 class="card-title mb-0">
                <x-core::icon name="ti ti-language" class="me-2" />
                {{ trans('plugins/vig-auto-translations::vig-auto-translations.name_plugin') }}
            </h3>
        </x-slot:header>

        <x-core::card.body>
            {!! Form::open(['role' => 'form', 'id' => 'translation-form']) !!}
            <div class="row">
                <div class="form-group col-md-6">
                    <label class="form-label fw-bold">
                        <x-core::icon name="ti ti-folder" class="me-1" />
                        {{ trans('plugins/vig-auto-translations::vig-auto-translations.group') }}
                    </label>
                    <select name="group" id="group" data-value="group" class="form-select select-search-full">
                        <option value="">{{ trans('plugins/vig-auto-translations::vig-auto-translations.please_select_group') }}</option>
                        @foreach ($groups as $key => $value)
                            <option value="{{ $key }}"{{ $key == $group ? ' selected' : '' }}>{{ $key }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label class="form-label fw-bold">
                        <x-core::icon name="ti ti-world" class="me-1" />
                        {{ trans('plugins/vig-auto-translations::vig-auto-translations.lang') }}
                    </label>
                    <select name="ref_lang" id="ref_lang" data-value="ref_lang" class="form-select select-search-full" @if(empty($group)) disabled @endif>
                        <option value="">{{ trans('plugins/vig-auto-translations::vig-auto-translations.choose_language') }}</option>
                        @foreach ($locales as $key => $locale)
                            <option value="{{ $key }}"{{ $key == $ref_lang ? ' selected' : '' }}>{{ $locale['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            {!! Form::close() !!}

            <div id="translations-container" class="mt-4">
                @if (!empty($group) && !empty($ref_lang))
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <span class="badge bg-light text-primary me-2">{{ $group }}</span>
                            <x-core::icon name="ti ti-arrow-right" class="mx-2 text-muted" />
                            <span class="badge bg-primary">{{ $locales[$ref_lang]['name'] ?? $ref_lang }}</span>
                        </h5>

                        <button class="btn btn-primary btn-translate-all">
                            <x-core::icon name="ti ti-rocket" class="me-1" />
                            {{ trans('plugins/vig-auto-translations::vig-auto-translations.translate_all', ['language' => $locales[$ref_lang]['name'] ?? $ref_lang]) }}
                            @if(!empty($sourceTranslations))
                                <span class="badge bg-white text-primary ms-2">{{ count($sourceTranslations) }}</span>
                            @endif
                        </button>
                    </div>

                    <hr>

                    @if (!empty($sourceTranslations))
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 30%;">
                                            <div class="d-flex align-items-center gap-2">
                                                <x-core::icon name="ti ti-key" />
                                                {{ trans('plugins/vig-auto-translations::vig-auto-translations.key') }}
                                            </div>
                                        </th>
                                        <th style="width: 30%;">
                                            <div class="d-flex align-items-center gap-2">
                                                <x-core::icon name="ti ti-align-left" />
                                                English (Source)
                                            </div>
                                        </th>
                                        <th style="width: 30%;">
                                            <div class="d-flex align-items-center gap-2">
                                                <x-core::icon name="ti ti-language" />
                                                {{ $locales[$ref_lang]['name'] ?? $ref_lang }} (Target)
                                            </div>
                                        </th>
                                        <th style="width: 10%;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sourceTranslations as $key => $sourceValue)
                                        <tr id="{{ str_replace('.', '-', $key) }}">
                                            <td>
                                                <code class="text-muted word-break small">{{ $key }}</code>
                                            </td>
                                            <td>
                                                <span class="translation-source">{{ $sourceValue }}</span>
                                            </td>
                                            <td>
                                                <a href="#edit" class="editable translation-value"
                                                   data-locale="{{ $ref_lang }}"
                                                   data-name="{{ $ref_lang . '|' . $key }}"
                                                   data-type="text"
                                                   data-pk="{{ $ref_lang }}"
                                                   data-url="{{ route('vig-auto-translations.plugin') }}"
                                                   data-title="{{ trans('plugins/translation::translation.edit_title') }}">
                                                    {{ $targetTranslations[$key] ?? '' }}
                                                </a>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary btn-translate-row"
                                                        data-name="{{ $ref_lang . '|' . $key }}"
                                                        data-value="{{ $sourceValue }}"
                                                        data-reset="0"
                                                        type="button"
                                                        title="{{ trans('plugins/vig-auto-translations::vig-auto-translations.translate') }}">
                                                    <x-core::icon name="ti ti-rocket" />
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <x-core::empty-state
                            :title="trans('plugins/vig-auto-translations::vig-auto-translations.empty_translations')"
                            :subtitle="trans('plugins/vig-auto-translations::vig-auto-translations.empty_group_hint')"
                            icon="ti ti-file-off"
                        />
                    @endif
                @else
                    <div class="py-5">
                        <x-core::empty-state
                            :title="trans('plugins/vig-auto-translations::vig-auto-translations.get_started')"
                            :subtitle="trans('plugins/vig-auto-translations::vig-auto-translations.select_group_language_hint')"
                            icon="ti ti-selector"
                        />
                    </div>
                @endif
            </div>
        </x-core::card.body>
    </x-core::card>
@stop


@push('footer')
    <style>
        .translation-value {
            display: inline-block;
            padding: 0.375rem 0.5rem;
            background: var(--tblr-bg-surface-tertiary, #f6f7f8);
            border-radius: 0.375rem;
            text-decoration: none;
            transition: all 0.15s ease;
            min-width: 100px;
            min-height: 24px;
        }

        .translation-value:hover {
            background: var(--tblr-bg-surface-secondary, #e6e7e9);
            color: var(--tblr-primary, #4c6ef5);
        }

        .translation-value.empty {
            color: var(--tblr-muted, #9ca3af);
            font-style: italic;
        }

        .translation-source {
            display: inline-block;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .word-break {
            word-break: break-word;
            white-space: pre-wrap;
        }

        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            .translation-value {
                background: #334155;
            }

            .translation-value:hover {
                background: #475569;
            }
        }
    </style>

    <script>
        $(document).ready(function() {
            // Handle group change
            $('#group').on('change', function() {
                var group = $(this).val();
                var refLang = $('#ref_lang').val();

                // Enable/disable language dropdown
                if (group) {
                    $('#ref_lang').prop('disabled', false);
                } else {
                    $('#ref_lang').prop('disabled', true);
                    $('#ref_lang').val('');
                }

                if (!group) {
                    $('#translations-container').html('<div class="py-5"><div class="empty d-flex flex-column align-items-center justify-content-center"><div class="empty-icon"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0" /><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0" /><path d="M3 6l0 13" /><path d="M12 6l0 13" /><path d="M21 6l0 13" /></svg></div><p class="empty-title">{{ $group ? '' : trans('plugins/vig-auto-translations::vig-auto-translations.get_started') }}</p></div></div>');
                    return;
                }

                // Reload page with selected group
                var url = new URL(window.location.href);
                url.searchParams.set('group', group);
                if (refLang) {
                    url.searchParams.set('ref_lang', refLang);
                } else {
                    url.searchParams.delete('ref_lang');
                }
                window.location.href = url.toString();
            });

            // Handle language change
            $('#ref_lang').on('change', function() {
                var group = $('#group').val();
                var refLang = $(this).val();

                if (!group) {
                    return;
                }

                // Reload page with selected language
                var url = new URL(window.location.href);
                url.searchParams.set('group', group);
                if (refLang) {
                    url.searchParams.set('ref_lang', refLang);
                } else {
                    url.searchParams.delete('ref_lang');
                }
                window.location.href = url.toString();
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
            params: function(params) {
                var groupValue = $('#group').val();
                var refLangValue = $('#ref_lang').val();

                if (!groupValue) {
                    Botble.showError('{{ trans('plugins/vig-auto-translations::vig-auto-translations.please_select_group') }}');
                    return { abort: true };
                }

                if (!refLangValue) {
                    Botble.showError('{{ trans('plugins/vig-auto-translations::vig-auto-translations.choose_language') }}');
                    return { abort: true };
                }

                return {
                    _token: "{{ csrf_token() }}",
                    name: $(this).data('name'),
                    value: params.value,
                    group: groupValue,
                    ref_lang: refLangValue
                };
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

        // Translate all button
        $(document).on('click', '.btn-translate-all', function(event) {
            event.preventDefault();
            event.stopPropagation();
            var $btn = $(this);
            $btn.prop('disabled', true).addClass('button-loading');

            var groupValue = $('#group').val();
            var localeValue = $('#ref_lang').val();

            if (!groupValue) {
                Botble.showError('{{ trans('plugins/vig-auto-translations::vig-auto-translations.please_select_group') }}');
                $btn.prop('disabled', false).removeClass('button-loading');
                return;
            }

            if (!localeValue) {
                Botble.showError('{{ trans('plugins/vig-auto-translations::vig-auto-translations.choose_language') }}');
                $btn.prop('disabled', false).removeClass('button-loading');
                return;
            }

            $.ajax({
                type: 'POST',
                cache: false,
                url: "{{ route('vig-auto-translations.plugin.all') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'group': groupValue,
                    'locale': localeValue
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

        // Translate single row button
        $(document).on('click', '.btn-translate-row', function(event) {
            event.preventDefault();
            event.stopPropagation();
            var $btn = $(this);
            $btn.prop('disabled', true).addClass('button-loading');
            var name = $btn.data('name');
            var value = $btn.data('value');
            var reset = $btn.data('reset');

            var groupValue = $('#group').val();
            var refLangValue = $('#ref_lang').val();

            if (!groupValue) {
                Botble.showError('{{ trans('plugins/vig-auto-translations::vig-auto-translations.please_select_group') }}');
                $btn.prop('disabled', false).removeClass('button-loading');
                return;
            }

            if (!refLangValue) {
                Botble.showError('{{ trans('plugins/vig-auto-translations::vig-auto-translations.choose_language') }}');
                $btn.prop('disabled', false).removeClass('button-loading');
                return;
            }

            $.ajax({
                type: 'POST',
                url: "{{ route('vig-auto-translations.plugin') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'group': groupValue,
                    'ref_lang': refLangValue,
                    'auto': !reset,
                    'value': value,
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
    </script>
@endpush

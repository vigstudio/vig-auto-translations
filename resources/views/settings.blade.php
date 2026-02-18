@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $form->getFormOption('section_title') }}</h3>
        </div>
        <div class="card-body">
            {!! $form->renderForm() !!}
        </div>
    </div>
@stop

@push('footer')
    <script>
        $(document).ready(function () {
            function toggleApiFields() {
                var selectedDriver = $('input[name="vig_translate_driver"]:checked').val();
                console.log('Selected driver:', selectedDriver);

                // Hide all API fields first - use more specific selectors
                $('.vig-translate-aws-field').each(function() {
                    $(this).closest('.mb-3').hide();
                });
                $('.vig-translate-chatgpt-field').each(function() {
                    $(this).closest('.mb-3').hide();
                });
                $('.vig-translate-gemini-field').each(function() {
                    $(this).closest('.mb-3').hide();
                });
                $('.vig-translate-openroute-field').each(function() {
                    $(this).closest('.mb-3').hide();
                });

                // Show fields based on selected driver
                if (selectedDriver === 'aws') {
                    $('.vig-translate-aws-field').each(function() {
                        $(this).closest('.mb-3').show();
                    });
                } else if (selectedDriver === 'chatgpt') {
                    $('.vig-translate-chatgpt-field').each(function() {
                        $(this).closest('.mb-3').show();
                    });
                } else if (selectedDriver === 'gemini') {
                    $('.vig-translate-gemini-field').each(function() {
                        $(this).closest('.mb-3').show();
                    });
                } else if (selectedDriver === 'openroute') {
                    $('.vig-translate-openroute-field').each(function() {
                        $(this).closest('.mb-3').show();
                    });
                }
            }

            // Run on load
            setTimeout(function() {
                toggleApiFields();
            }, 200);

            // Run on change
            $('input[name="vig_translate_driver"]').on('change', function() {
                toggleApiFields();
            });
        });
    </script>
@endpush

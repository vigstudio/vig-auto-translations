$(document).ready(function () {
    function toggleApiFields() {
        var selectedDriver = $('input[name="vig_translate_driver"]:checked').val();

        console.log('Selected driver:', selectedDriver);

        // Hide all API fields first - try multiple selectors
        $('input[name="vig_translate_aws_key"]').closest('.mb-3').hide();
        $('input[name="vig_translate_aws_secret"]').closest('.mb-3').hide();
        $('input[name="vig_translate_aws_region"]').closest('.mb-3').hide();
        $('input[name="vig_translate_chatgpt_key"]').closest('.mb-3').hide();

        $('input[name="vig_translate_aws_key"]').closest('.form-group').hide();
        $('input[name="vig_translate_aws_secret"]').closest('.form-group').hide();
        $('input[name="vig_translate_aws_region"]').closest('.form-group').hide();
        $('input[name="vig_translate_chatgpt_key"]').closest('.form-group').hide();

        $('[data-field-name="vig_translate_aws_key"]').closest('.mb-3').hide();
        $('[data-field-name="vig_translate_aws_secret"]').closest('.mb-3').hide();
        $('[data-field-name="vig_translate_aws_region"]').closest('.mb-3').hide();
        $('[data-field-name="vig_translate_chatgpt_key"]').closest('.mb-3').hide();

        $('.vig-translate-aws-field').closest('.mb-3').hide();
        $('.vig-translate-chatgpt-field').closest('.mb-3').hide();

        // Show fields based on selected driver
        if (selectedDriver === 'aws') {
            $('input[name="vig_translate_aws_key"]').closest('.mb-3').show();
            $('input[name="vig_translate_aws_secret"]').closest('.mb-3').show();
            $('input[name="vig_translate_aws_region"]').closest('.mb-3').show();
            $('input[name="vig_translate_aws_key"]').closest('.form-group').show();
            $('input[name="vig_translate_aws_secret"]').closest('.form-group').show();
            $('input[name="vig_translate_aws_region"]').closest('.form-group').show();
            $('[data-field-name="vig_translate_aws_key"]').closest('.mb-3').show();
            $('[data-field-name="vig_translate_aws_secret"]').closest('.mb-3').show();
            $('[data-field-name="vig_translate_aws_region"]').closest('.mb-3').show();
            $('.vig-translate-aws-field').closest('.mb-3').show();
        } else if (selectedDriver === 'chatgpt') {
            $('input[name="vig_translate_chatgpt_key"]').closest('.mb-3').show();
            $('input[name="vig_translate_chatgpt_key"]').closest('.form-group').show();
            $('[data-field-name="vig_translate_chatgpt_key"]').closest('.mb-3').show();
            $('.vig-translate-chatgpt-field').closest('.mb-3').show();
        }
        // Google driver doesn't need any API fields
    }

    // Initial toggle on page load
    toggleApiFields();

    // Toggle when radio button changes
    $('input[name="vig_translate_driver"]').on('change', function () {
        toggleApiFields();
    });
});

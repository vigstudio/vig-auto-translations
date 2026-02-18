$(document).ready(function () {
    function toggleApiFields() {
        var selectedDriver = $('input[name="vig_translate_driver"]:checked').val();

        console.log('Selected driver:', selectedDriver);

        // Get the form container
        var $form = $('#vig-auto-translations-settings, form[data-form="vig-auto-translations-settings"], .setting-form');

        // Hide all API fields first
        $form.find('.vig-translate-aws-field').each(function () {
            $(this).parents('.mb-3, .form-group, .form-field').hide();
        });
        $form.find('.vig-translate-chatgpt-field').each(function () {
            $(this).parents('.mb-3, .form-group, .form-field').hide();
        });
        $form.find('.vig-translate-gemini-field').each(function () {
            $(this).parents('.mb-3, .form-group, .form-field').hide();
        });
        $form.find('.vig-translate-openroute-field').each(function () {
            $(this).parents('.mb-3, .form-group, .form-field').hide();
        });

        // Show fields based on selected driver
        if (selectedDriver === 'aws') {
            console.log('Showing AWS fields');
            $form.find('.vig-translate-aws-field').each(function () {
                $(this).parents('.mb-3, .form-group, .form-field').show();
            });
        } else if (selectedDriver === 'chatgpt') {
            console.log('Showing ChatGPT fields');
            $form.find('.vig-translate-chatgpt-field').each(function () {
                $(this).parents('.mb-3, .form-group, .form-field').show();
            });
        } else if (selectedDriver === 'gemini') {
            console.log('Showing Gemini fields');
            $form.find('.vig-translate-gemini-field').each(function () {
                $(this).parents('.mb-3, .form-group, .form-field').show();
            });
        } else if (selectedDriver === 'openroute') {
            console.log('Showing OpenRoute fields');
            $form.find('.vig-translate-openroute-field').each(function () {
                $(this).parents('.mb-3, .form-group, .form-field').show();
            });
        }
        // Google driver doesn't need any API fields
    }

    // Wait for DOM to be fully ready and form to be rendered
    setTimeout(function () {
        toggleApiFields();
    }, 100);

    // Toggle when radio button changes
    $(document).on('change', 'input[name="vig_translate_driver"]', function () {
        toggleApiFields();
    });
});

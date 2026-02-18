/**
 * Theme Translations JavaScript for vig-auto-translations plugin
 */

$(document).ready(function () {
    // Initialize inline editing
    $('.editable').editable({
        mode: 'inline',
        emptytext: '{{ trans('plugins/translation::translation.empty') }}',
    });

    // Handle group dropdown change
    $('#group').on('change', function () {
        var url = new URL(window.location.href);
        url.searchParams.set('group', $(this).val());
        window.location.href = url.toString();
    });

    // Handle language dropdown change
    $('#ref_lang').on('change', function () {
        var url = new URL(window.location.href);
        url.searchParams.set('ref_lang', $(this).val());
        window.location.href = url.toString();
    });

    // Auto-focus on editable when using keyboard navigation
    $(document).on('shown', '.editable', function (e, editable) {
        var $input = editable.getInput();
        if ($input && $input.length > 0) {
            $input[0].focus();
        }
    });
});

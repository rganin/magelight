function bindFormsValidation()
{
    $('form[data-front-validate]').each(function(){
        var rules = jQuery.parseJSON($(this).attr('data-validator-rules')) || {};
        var messages = jQuery.parseJSON($(this).attr('data-validator-messages')) || {};
        var controlContainerSelector = $(this).attr('data-control-container-selector') || 'div';
        $(this).validate({
            rules: rules,
            messages: messages,
            errorClass: 'help-inline text-danger',
            ignore: false,
            highlight: function(element) {
                $(element).closest(controlContainerSelector).removeClass('has-success').addClass('has-error');
            },
            success: function(element) {
                $(element).addClass('valid').closest(controlContainerSelector).removeClass('has-error').addClass('has-success');
            },
            errorPlacement: function(error, element) {
                console.log(element.closest(controlContainerSelector).attr('class'));
                var errorContainer = element.closest(controlContainerSelector).find('.error-container');
                if (errorContainer.length) {
                    error.appendTo(errorContainer);
                } else {
                    error.appendTo(element.closest('.form-group'));
                }
            }
        });
    });

}

$(document).ready(function(){
    bindFormsValidation();
});

function bindFormsValidation()
{
    $('form[data-front-validate]').each(function(){
        var rules = jQuery.parseJSON($(this).attr('data-validator-rules')) || {};
        var messages = jQuery.parseJSON($(this).attr('data-validator-messages')) || {};
        $(this).validate({
            rules: rules,
            messages: messages,
            errorClass: 'help-inline text-danger',
            highlight: function(label) {
                $(label).closest('.form-group').removeClass('has-success').addClass('has-error');
            },
            success: function(label) {
                label.addClass('valid').closest('.form-group').removeClass('has-error').addClass('has-success');
            },
            errorPlacement: function(error, element) {
                error.appendTo(element.closest('.form-group'));
            }
        });
    });

}

$(document).ready(function(){
    bindFormsValidation();
});
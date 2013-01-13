function bindFormsValidation()
{
    $('form[data-front-validate]').each(function(){
        var rules = jQuery.parseJSON($(this).attr('data-validator-rules')) || {};
        var messages = jQuery.parseJSON($(this).attr('data-validator-messages')) || {};
        $(this).validate({
            rules: rules,
            messages: messages,
            errorClass: 'help-inline',
            highlight: function(label) {
                $(label).closest('.control-group').removeClass('success').addClass('error');
            },
            success: function(label) {
                label.addClass('valid').closest('.control-group').addClass('success');
            },
            errorPlacement: function(error, element) {
                error.appendTo(element.closest('.controls'));
            }
        });
    });

}

$(document).ready(function(){
    bindFormsValidation();
});
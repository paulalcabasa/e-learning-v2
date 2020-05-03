var crud = null;

$(function() {
    crud = {
        reset_form(form_id) {
            $('#' + form_id)[0].reset();
        },
        reset_error_feedback() {
            $('.invalid-feedback').text('');
        },
        close_modal(modal_id) {
            $('#' + modal_id).modal('hide');
        },
        set_method(method) {
            $('input[name="_method"]').val(method);
        },
        get_method() {
            return $('input[name="_method"]').val();
        }
    }
});
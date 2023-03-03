jQuery(document).ready(function () {

// multi select user role
    jQuery('#wpfp_access_user_role').multiselect({
        columns: 1,
        placeholder: 'Select User Roles',
        selectAll: true
    });

// Jquery form validate
    jQuery('#wp-force-password-form').validate({
        rules: {
            'reset_days': {
                required: true
            },
            'wpfp_access_user_role[]': {
                required: true,
            }
        },
        ignore: ':hidden:not("#wpfp_access_user_role")',
        errorClass: 'wpfp-invalid', 
        messages: {
            'reset_days':
                    {
                        required: "Please Enter Number of Password Reset Days",
                    },
            'wpfp_access_user_role[]':
                    {
                        required: "Please Select User Roles",
                    },
        },
        errorElement: 'div',
        errorLabelContainer: '.wpfperrorTxt'    
    });

// 
jQuery('input[name="wpfp_shut_off_email"]').change(function() {
    if(jQuery(this).is(':checked')){
        jQuery('#col_wpfp_reset_days').hide();
    }else{
        jQuery('#col_wpfp_reset_days').show();
    }
});

});

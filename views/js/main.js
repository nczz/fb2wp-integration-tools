(function($) {
    function openSection() {
        var x, tablinks;
        var SectionName = $(this).data('id');
        x = $(".Section");
        for (var i = 0; i < x.length; i++) {
            $(x[i]).hide();
        }
        tablinks = $(".tablink");
        for (var i = 0; i < tablinks.length; i++) {
            $(tablinks[i]).removeClass('opening');
        }
        $('#' + SectionName).show();
        $(this).addClass('opening');
        $('#mxp_fb2wp_active_tab').val(SectionName);
    }
    
    $(document).ready(function() {
        $('.tablink').click(openSection);
        $(".activebtn").trigger('click');
        $('#import_ratings').click(function() {
            var self = this;
            $(this).val(MXP_FB2WP.importRat);
            var data = {
                'action': 'mxp_import_fb_ratings',
                'nonce': MXP_FB2WP.nonce,
            };
            $.post(ajaxurl, data, function(res) {
                console.log(res);
                if (res.success) {
                    $(self).val(MXP_FB2WP.successMsg);
                    $(self).prop('disabled', true);
                } else {
                    //Error? That's my problem...
                }
            });
        });
    });
})(jQuery)

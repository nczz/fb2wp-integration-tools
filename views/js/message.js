function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
(function($) {
    function loading(flag) {
        if (flag) {
            $('body').waitMe({
                'effect': 'facebook',
                'text': MXP_FB2WP.waitMe,
                'bg': 'rgba(255,255,255,0.7)',
                'color': '#000',
                'maxSize': '',
                'textPos': 'vertical',
                'fontSize': '',
                'source': ''
            });
        } else {
            $('body').waitMe("hide");
        }
    }

    function add_item(prefix, id, req, resp) {
        return '<p class="item" id="' + prefix + '_' + id + '">' + id + '. ' + MXP_FB2WP.inputMatch + '<input class="' + prefix + '_' + id + ' item" type="text" value="' + htmlEntities(decodeURIComponent(req)) + '" size="20"/>' + MXP_FB2WP.matchReply + '<textarea class="' + prefix + '_' + id + ' item" rows="3" cols="30">' + decodeURIComponent(resp) + '</textarea><button data-id="' + prefix + '_' + id + '" class="button delete_item">' + MXP_FB2WP.removeItem + '</button></p>';
    }

    function delete_item() {
        var id = $(this).data().id;
        $('#' + id).remove();
    }

    function save_items() {
        loading(true);
        var m = [];
        var f = [];
        var m_items = $('#match>p.item');
        var f_items = $('#fuzzy>p.item');
        for (var i = 0; i < m_items.length; ++i) {
            var m_input = $('input', m_items[i]).val();
            var m_textarea = $('textarea', m_items[i]).val();
            if (m_input != "" && m_textarea != "") {
                m.push({
                    key: encodeURIComponent(m_input),
                    value: encodeURIComponent(m_textarea)
                });
            }
        }
        for (var i = 0; i < f_items.length; ++i) {
            var f_input = $('input', f_items[i]).val();
            var f_textarea = $('textarea', f_items[i]).val();
            if (f_input != "" && f_textarea != "") {
                f.push({
                    key: encodeURIComponent(f_input),
                    value: encodeURIComponent(f_textarea)
                });
            }
        }
        var items = {
            match: m,
            fuzzy: f
        };
        var data = {
            'action': 'mxp_messenger_settings_save',
            'nonce': MXP_FB2WP.nonce,
            'data': items,
            'method': 'set',
        };
        $.post(ajaxurl, data, function(res) {
            if (res.success) {
                alert(MXP_FB2WP.successMsg);
                loading(false);
            } else {
                alert(MXP_FB2WP.errorMsg);
                loading(false);
            }
        });
    }
    $(document).ready(function() {
        loading(true);
        var data = {
            'action': 'mxp_messenger_settings_save',
            'nonce': MXP_FB2WP.nonce,
            'method': 'get'
        };
        $.post(ajaxurl, data, function(res) {
            if (res.success) {
                console.log(res);
                var obj = res.data;
                var m = obj.match === undefined ? [] : obj.match;
                var f = obj.fuzzy === undefined ? [] : obj.fuzzy;
                for (var i = 0; i < m.length; ++i) {
                    $('#match').append(add_item('m', i + 1, m[i].key, m[i].value));
                }
                for (var i = 0; i < f.length; ++i) {
                    $('#fuzzy').append(add_item('f', i + 1, f[i].key, f[i].value));
                }
                $('.delete_item').click(delete_item);
                loading(false);
            } else {
                alert(MXP_FB2WP.errorMsg);
                loading(false);
            }
        });
        $('#add_match').click(function() {
            var count = $('#match>p').length;
            $('#match').append(add_item('m', count + 1, "", ""));
            $('.delete_item').click(delete_item);
        });
        $('#add_fuzzy').click(function() {
            var count = $('#fuzzy>p').length;
            $('#fuzzy').append(add_item('f', count + 1, "", ""));
            $('.delete_item').click(delete_item);
        });
        $('#save_match').click(save_items);
        $('#save_fuzzy').click(save_items);
    });
})(jQuery)
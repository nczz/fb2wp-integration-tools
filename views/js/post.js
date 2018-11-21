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
    //v1.4.3 修正後台輸出因html標籤，導致顯示錯誤，避免被自己XSS
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function timeConverter(timestamp) {
        var a = new Date(timestamp * 1000);
        var months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
        var year = a.getFullYear();
        var month = months[a.getMonth()];
        var date = a.getDate() < 10 ? '0' + a.getDate() : a.getDate();
        var hour = a.getHours() < 10 ? '0' + a.getHours() : a.getHours();
        var min = a.getMinutes() < 10 ? '0' + a.getMinutes() : a.getMinutes();
        var sec = a.getSeconds() < 10 ? '0' + a.getSeconds() : a.getSeconds();
        var time = year + '/' + month + '/' + date + ' ' + hour + ':' + min + ':' + sec;
        return time;
    }

    function build_table(data) {
        var items = data.data;
        var pages = data.total_pages;
        var search_box = '<div class="search_box"><input id="keyword" type="text" size="20" value="" placeholder="' + MXP_FB2WP.searchTerm + '"/><button class="button action" id="search">' + MXP_FB2WP.searchBtn + '</button></div>';
        var sel = '<select id="page_select">';
        for (var i = 0; i < pages; ++i) {
            var now = i == data.page ? 'selected' : '';
            sel += '<option value="' + i + '" ' + now + '>第 ' + (i + 1) + ' 頁</option>';
        }
        sel += '</select>';
        var table = '<table id="main"><tr><th><button class="button action delete_all">' + MXP_FB2WP.removeBtn + '</button> |' + sel + '</th><th>' + MXP_FB2WP.action + '</th><th>' + MXP_FB2WP.time + '</th><th>' + MXP_FB2WP.object + '</th><th>' + MXP_FB2WP.sender + '</th><th>' + MXP_FB2WP.msg +'</th></tr>';
        for (var i = 0; i < items.length; ++i) {
            var item = items[i];
            var disabled = item.action != 'add' ? 'disabled' : '';
            if (item.item == 'album') {
                disabled = 'disabled';
            }
            var post = '<tr id="item_' + item.sid + '""><td><button data-id="' + item.sid + '"" class="button action post" ' + disabled + '>'+MXP_FB2WP.postBtn+'</button> | <button data-id="' + item.sid + '"" class="button action delete">' + MXP_FB2WP.remove +'</button></td>';
            var action = '<td>' + item.action + '</td>';
            var created_time = '<td><a href="https://facebook.com/' + item.post_id + '" target="_blank" >' + timeConverter(item.created_time) + '</a></td>';
            var obj = '<td>' + item.item + '</td>';
            var sender = '<td><a href="https://facebook.com/' + item.sender + '" target="_blank" >' + (item.sender_name == null ? 'none' : item.sender_name) + '</a></td>';
            var msg = '<td>' + (item.message == "" ? MXP_FB2WP.empty : escapeHtml(item.message)) + '</td></tr>';
            table += post + action + created_time + obj + sender + msg;
        }
        table += '</table>';
        return search_box + table;
    }

    function build_search_results_table(data) {
        var items = data.data;
        var pages = data.total_pages;
        var search_box = '<div class="search_box"><input id="keyword" type="text" size="20" value="" placeholder="' + MXP_FB2WP.searchTerm + '"/><button class="button action" id="search">' + MXP_FB2WP.searchBtn + '</button></div>';
        var table = '<table id="search_results"><tr><th><a href="">返回前頁</a></th><th>' + MXP_FB2WP.action + '</th><th>' + MXP_FB2WP.time + '</th><th>' + MXP_FB2WP.object + '</th><th>' + MXP_FB2WP.sender + '</th><th>' + MXP_FB2WP.msg +'</th></tr>';
        for (var i = 0; i < items.length; ++i) {
            var item = items[i];
            var disabled = item.action != 'add' ? 'disabled' : '';
            if (item.item == 'album') {
                disabled = 'disabled';
            }
            var post = '<tr id="item_' + item.sid + '""><td><button data-id="' + item.sid + '"" class="button action post" ' + disabled + '>發文</button> | <button data-id="' + item.sid + '"" class="button action delete">刪除</button></td>';
            var action = '<td>' + item.action + '</td>';
            var created_time = '<td><a href="https://facebook.com/' + item.post_id + '" target="_blank" >' + timeConverter(item.created_time) + '</a></td>';
            var obj = '<td>' + item.item + '</td>';
            var sender = '<td><a href="https://facebook.com/' + item.sender + '" target="_blank" >' + (item.sender_name == null ? 'none' : item.sender_name) + '</a></td>';
            var msg = '<td>' + (item.message == "" ? MXP_FB2WP.empty : escapeHtml(item.message)) + '</td></tr>';
            table += post + action + created_time + obj + sender + msg;
        }
        table += '</table>';
        return search_box + table;
    }

    function event_binding() {
        $('.post').click(function() {
            if (!confirm("確定要新增此篇文章嗎？\n")) {
                return
            }
            loading(true);
            var sid = $(this).data().id;
            var data = {
                'action': 'mxp_debug_record_action',
                'nonce': MXP_FB2WP.nonce,
                'method': 'post',
                'sid': sid
            };
            $.post(ajaxurl, data, function(res) {
                if (res.success) {
                    alert('新增成功！');
                } else {
                    alert('新增失敗！');
                }
                loading(false);
            });
        });
        $('.delete').click(function() {
            if (!confirm("確定要刪除此篇記錄嗎？\n")) {
                return
            }
            loading(true);
            var sid = $(this).data().id;
            var data = {
                'action': 'mxp_debug_record_action',
                'nonce': MXP_FB2WP.nonce,
                'method': 'delete',
                'sid': sid
            };
            $.post(ajaxurl, data, function(res) {
                if (res.success) {
                    $('#item_' + sid).remove();
                } else {
                    alert('刪除失敗');
                }
                loading(false);
            });
        });
        $('.delete_all').click(function() {
            if (!confirm("確定要刪除此頁記錄嗎？\n")) {
                return
            }
            loading(true);
            var ids = [];
            jQuery('.delete').each(function(a, b) {
                ids.push(jQuery(b, '[data-id]')[0].dataset.id);
            });
            var data = {
                'action': 'mxp_debug_record_action',
                'nonce': MXP_FB2WP.nonce,
                'method': 'delete',
                'sid': ids.join(',')
            };
            $.post(ajaxurl, data, function(res) {
                if (res.success) {
                    for (var i = 0; i < ids.length; ++i) {
                        $('#item_' + ids[i]).remove();
                    }
                    location.reload();
                } else {
                    alert('刪除失敗');
                }
                loading(false);
            });
        });

        $('#page_select').change(function() {
            loading(true);
            var page = $(this).val();
            var data = {
                'action': 'mxp_debug_record_action',
                'nonce': MXP_FB2WP.nonce,
                'method': 'get',
                'page': page
            };
            $.post(ajaxurl, data, function(res) {
                if (res.success) {
                    $('#table').html(build_table(res.data));
                    event_binding();
                } else {
                    alert('發生錯誤！');
                }
                loading(false);
            });
        });

        $('#search').click(function() {
            loading(true);
            var data = {
                'action': 'mxp_debug_record_action',
                'nonce': MXP_FB2WP.nonce,
                'method': 'search',
                'keyword': $('#keyword').val()
            };
            $.post(ajaxurl, data, function(res) {
                if (res.success) {
                    $('#table').html(build_search_results_table(res.data));
                    event_binding();
                } else {
                    alert('發生錯誤！');
                }
                loading(false);
            });
        });
    }
    $(document).ready(function() {
        loading(true);
        var data = {
            'action': 'mxp_debug_record_action',
            'nonce': MXP_FB2WP.nonce,
            'method': 'get',
        };
        $.post(ajaxurl, data, function(res) {
            if (res.success) {
                $('#table').html(build_table(res.data));
                event_binding();
            } else {
                alert('發生錯誤！');
            }
            loading(false);
        });
    });
})(jQuery)

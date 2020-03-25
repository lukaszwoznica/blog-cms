function deleteConfirm(item_name = "") {
    return confirm('Are you sure you want to delete this ' + item_name + '?');
}

function searchData(url, query) {
    $.ajax({
        url: url,
        method: "get",
        data: {search_query: query},
        success: function(data) {
            $('#tableCard').html(data);
        }
    });
}

function loadNotifications(user_id, show_toast=false) {
    $.ajax({
        url: '/admin/notifications/get-notifications',
        method: 'post',
        dataType: "json",
        data: {user_id: user_id},
        success: function (data) {
            const list = document.querySelector('#notificationsDropdown');
            if (list.childElementCount - 2 !== parseInt(data['notifications_array'].length)) {
                if (show_toast) {
                    M.toast({html: 'You have a new notification.'});
                }
                $('#notificationsDropdown').html(data['html_code']);
                $('#notificationsDropdownTrigger').dropdown('recalculateDimensions');
            } else {
                let change = false;
                for (let i = 2, j = 0; i < list.children.length; i++, j++) {
                    if (parseInt(list.children[i].getAttribute('data-unseen-comments')) !==
                        parseInt(data['notifications_array'][j].unseen_comments)) {
                        change = true;
                    }
                }
                if (change) {
                    if (show_toast) {
                        M.toast({html: 'You have a new notification.'});
                    }
                    $('#notificationsDropdown').html(data['html_code']);
                }
            }
            const notifications_number = $.parseHTML(data['html_code'])[0].childNodes[1].innerText;
            if (notifications_number > 0) {
                $('#newNotificationIcon').show();
            } else {
                $('#newNotificationIcon').hide();
            }
        },
        complete: function () {
            setTimeout(function () {
                loadNotifications(user_id, true)
            }, 5000);
        }
    });
}

function deleteNotification(post_id) {
    $.ajax({
        url: '/admin/notifications/destroy-notification',
        method: 'post',
        data: {post_id: post_id},
        success: function () {
            loadNotifications(user_id)
        }
    })
}
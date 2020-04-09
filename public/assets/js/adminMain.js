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

function drawColumnChart(data_array, startup=false) {
    const data = new google.visualization.DataTable();
    data.addColumn('string', 'Post');
    data.addColumn('number', 'Comments');
    for (const row of data_array) {
        data.addRow([row.post, parseInt(row.comments)])
    }

    var view = new google.visualization.DataView(data);
    var options = {
        animation: {
            startup: startup,
            easing: 'inAndOut',
            duration: 3000
        },
        chartArea: {'width': '90%', 'height': '80%'},
        bar: {groupWidth: "15%"},
        legend: { position: "none" },
        colors: ['#303f9f']
    };
    var chart = new google.visualization.ColumnChart(document.getElementById("chart_div"));

    chart.draw(view, options);
}

function drawDonutChart(data_array) {
    const data = new google.visualization.DataTable();
    data.addColumn('string', 'Category');
    data.addColumn('number', 'Posts');
    for (const row of data_array) {
        data.addRow([row.category, parseInt(row.posts)])
    }

    const options = {
        pieHole: 0.4,
        legend: { position: "bottom" },
        chartArea: {'width': '100%', 'height': '80%'},
        colors: ['#1976D2', '#E53935' , '#43A047', '#fb8c00', '#8e24aa', '#00897b', '#d81b60'],
        pieSliceText: 'none'
    };
    const chart = new google.visualization.PieChart(document.getElementById('chart2_div'));

    chart.draw(data, options);
}
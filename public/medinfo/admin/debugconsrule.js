initrunbutton = function() {
    $("#run").click(function () {
        let data = setQueryString();
        $.ajax({
            dataType: 'json',
            url: '/admin/cons/debugrule',
            method: "POST",
            data: data,
            success: function (data, status, xhr) {
                if (typeof data.error !== 'undefined') {
                    raiseError(data.message);
                } else {
                    //$("#log").html(JSON.stringify(data.log, null, 2));
                    $("#value").html(data.value);
                    $("#log").html(syntaxHighlight(data.log));
                }
            },
            error: xhrErrorNotificationHandler
        });
    });

    // на всякий - для открытия в новом окне
    $("#newwin").click(function () {
        let dlWindow = window.open('/admin/cons/debugrule' + qstring);
    });
};

function setQueryString() {
    return "&script=" + $("#script").val() +
        "&units=" + $("#units").val() +
        "&table=" + $("#table").val()  +
        "&document=" + $("#document").val();
}

function syntaxHighlight(json) {
    if (typeof json != 'string') {
        json = JSON.stringify(json, undefined, 2);
    }
    json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
        var cls = 'number';
        if (/^"/.test(match)) {
            if (/:$/.test(match)) {
                cls = 'key';
            } else {
                cls = 'string';
            }
        } else if (/true|false/.test(match)) {
            cls = 'boolean';
        } else if (/null/.test(match)) {
            cls = 'null';
        }
        return '<span class="' + cls + '">' + match + '</span>';
    });
}
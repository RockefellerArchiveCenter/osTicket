$(document).ready(function () {
    if (window.location.search) {
        $.urlParam = function (name) {
            var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(window.location.href);
            if (results == null) {
                return null;
            } else {
                return results[1].replace('+', ' ') || 0;
            }
        }
        if ($.urlParam('query') != null) {
            $("#subject").highlight($.urlParam('query'));
            $("td").highlight($.urlParam('query'));
            $("td div").highlight($.urlParam('query'));
        }
    }
});

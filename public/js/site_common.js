/**
 * Created by shutoukin on 2017/08/19.
 */
$(document).ready(function () {
    $("a#linkArea").click( function () {
            $("a#targetArea").text($(this).text());
        }
    );
});
/**
 * Created by shutoukin on 2017/08/19.
 */
window.onload = function () {
    drawAvgPrice(
        'bar',
        "http://www.chika.com/avgs/",
         document.getElementById("post_price_trending"),
        'rgb(255, 99, 132)',
        'rgb(255, 99, 132)'
    );

    drawAvgPrice(
        'bar',
        "http://www.chika.com/avgs/",
        document.getElementById("survey_price_trendding"),
        'rgb(25, 118, 210)', //background color
        'rgb(25, 118, 210)' //border color
    );
}
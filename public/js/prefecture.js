/**
 * Created by shutoukin on 2017/08/26.
 */
window.onload = function () {
    var urlsValues = decodeURIComponent(window.location.href).split("/");
    var prefectureName = urlsValues[urlsValues.length - 1];
    drawAvgPrice(
        'bar',
        "http://www.chika.com/avgs/" + prefectureName.substring(0, 3) + '/',
        document.getElementById("post_price_trending"),
        'rgb(255, 99, 132)',
        'rgb(255, 99, 132)'
    )
}
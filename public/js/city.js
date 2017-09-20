/**
 * Created by shutoukin on 2017/09/20.
 */
window.onload = function () {
    var urlsValues = decodeURIComponent(window.location.href).split("/");
    //recreate the correct url to be accepted by the service API.
    var prefectureName = urlsValues[urlsValues.length -2].substring(0, 3);
    urlsValues.splice(3, 0, "avgs");
    urlsValues.splice(4, 1, prefectureName);
    var targetURL = urlsValues.join("/");
    landPriceDataSets[0].backgroundColor = 'rgb(255, 99, 132)';
    landPriceDataSets[0].borderColor = 'rgb(255, 99, 132)';
    landPriceDataSets[1].backgroundColor = 'rgb(25, 118, 210)';
    landPriceDataSets[1].borderColor = 'rgb(25, 118, 210)';
    drawAvgPrice('bar', targetURL, document.getElementById("post_price_trending"));
    //
    urlsValues.splice(3, 1, "mapItems");
    showMap(urlsValues.join("/"));
}
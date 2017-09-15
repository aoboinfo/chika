/**
 * Created by shutoukin on 2017/08/26.
 */
window.onload = function () {
    var urlsValues = decodeURIComponent(window.location.href).split("/");
    var prefectureName = urlsValues[urlsValues.length - 1];
    landPriceDataSets[0].backgroundColor = 'rgb(255, 99, 132)';
    landPriceDataSets[0].borderColor = 'rgb(255, 99, 132)';
    landPriceDataSets[1].backgroundColor = 'rgb(25, 118, 210)';
    landPriceDataSets[1].borderColor = 'rgb(25, 118, 210)';
    drawAvgPrice('bar', "avgs/" + prefectureName.substring(0, 3) + '/', document.getElementById("post_price_trending"));
    //
    changeRateDataSets[0].backgroundColor = 'rgb(255, 99, 132)';
    changeRateDataSets[0].borderColor = 'rgb(255, 99, 132)';
    changeRateDataSets[1].backgroundColor = 'rgb(25, 118, 210)';
    changeRateDataSets[1].borderColor = 'rgb(25, 118, 210)';
    drawPriceChangeRate('changeRate/' + prefectureName.substring(0, 3) + '/', document.getElementById("price_change_rate"));
}

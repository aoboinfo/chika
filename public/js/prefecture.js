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
    /*changeRateDataSets[0].backgroundColor = 'rgb(255, 99, 132)';
    changeRateDataSets[0].borderColor = 'rgb(255, 99, 132)';
    changeRateDataSets[1].backgroundColor = 'rgb(25, 118, 210)';
    changeRateDataSets[1].borderColor = 'rgb(25, 118, 210)';
    drawPriceChangeRate("avg2/" + prefectureName.substring(0, 3) + '/', document.getElementById("price_change_rate"));*/
    //
    var lat = 35.692507; // 新宿駅の緯度
    var lng = 139.700346;// 新宿駅の経度
    //
    map.drawMap(
        new Y.LatLng(lat, lng),
        15,  //ズームレベルは16
        Y.LayerSetId.NORMAL  //通常の地図を表示
    );
    // 地図の種類を切り換えるコントローラーを表示
    map.addControl(new Y.LayerSetControl());
    map.addControl(new Y.SliderZoomControlVertical());
    map.addControl(new Y.SearchControl());
    map.addControl(new Y.CenterMarkControl());
    map.addControl(new Y.ScaleControl());
    showMap('mapItems/' + prefectureName.substring(0, 3) + '/');
}

/**
 * Created by shutoukin on 2017/08/19.
 */
/**
 * Draw price change rate.
 */
window.chartColors = {
    red: 'rgb(255, 99, 132)',
    orange: 'rgb(255, 159, 64)',
    yellow: 'rgb(255, 205, 86)',
    green: 'rgb(75, 192, 192)',
    blue: 'rgb(54, 162, 235)',
    purple: 'rgb(153, 102, 255)',
    grey: 'rgb(201, 203, 207)',
    pink: 'rgb(255, 105, 180)',
    teal: 'rgb(0, 128, 128)',
    brown: 'rgb(128, 0, 0)',
    olive: 'rgb(128, 128, 0)',
    peru: 'rgb(205, 133, 63)',
    cyan:  'rgb(0, 255, 255)'
};
//
window.urls = {
    listPostUsage: 'list/postUsage',
    listPostCityPlan: 'list/postCityPlan',
    listSurveyUsage: 'list/surveyUsage/',
    listSurveyCityPlan: 'list/surveyCityPlan',
    listAll: 'list/all',
    listPostStation: 'list/stationPost',
    listSurveyStation: 'list/stationSurvey',
    postDetail: 'detail/post',
    surveyDetail: 'detail/survey'
};
var mapDiv = document.getElementById("map-canvas");
var map = null;
//
var changeRateDataSets = [//global data for two type bars
    {
        label:'地価公示(H29)',
        backgroundColor:'',
        borderColor:'',
        borderWidth:1,
        data:null
    }, {
        label:'地価調査(H28)',
        backgroundColor:'',
        borderColor:'',
        borderWidth:1,
        data:null
    }
];

var landPriceDataSets = [
    {
        label:'地価公示(H29)',
        backgroundColor:'',
        borderColor:'',
        borderWidth:1,
        data:null
    }, {
        label:'地価調査(H28)',
        backgroundColor:'',
        borderColor:'',
        borderWidth:1,
        data:null
    }
];

function drawPriceChangeRate(targetUrl, chartObj) {
    $.ajax (
        {
            url : targetUrl,
            type: "GET",
            success: function (result) {
                changeRateDataSets[0].data = result.postRates;
                changeRateDataSets[1].data = result.surveyRates;
                var rateChart = new Chart(chartObj.getContext("2d"), {
                        type: 'bar',
                        data: {
                            labels: result.labels,
                            datasets:changeRateDataSets
                        },
                        options: {
                            legend: {
                                labels: {
                                    fontColor:"white",
                                    fontSize: 17
                                }
                            },
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        fontColor: "white",
                                        fontSize: 17
                                    }
                                }],
                                xAxes: [{
                                    ticks: {
                                        fontColor: "white",
                                        stepSize: 1,
                                        fontSize: 14
                                    }
                                }]
                            }
                        }
                    }
                );
            }
        }
    );
}
/*
* Drawing bar for average prices of every year.
* */
function drawAvgPrice(type, targetUrl, chartObj) {
    $.ajax(
        {
            url : targetUrl,
            type : "GET",
            success: function (result) {
                landPriceDataSets[0].data = result.postPrices;
                landPriceDataSets[1].data = result.surveyPrices;
                var chart = new Chart(chartObj.getContext("2d"), {
                    // The type of chart we want to create
                    type: type,
                    // The data for our dataset
                    data: {
                        labels: result.labels,
                        datasets:landPriceDataSets
                    },
                    //Configuration options go here
                    options: {
                        legend: {
                            labels: {
                                fontSize: 17
                            }
                        },
                        scales: {
                            yAxes: [
                                {
                                    ticks: {
                                        callback: function(label, index, labels) {
                                            return label/1000;
                                        }
                                    },
                                    scaleLabel: {
                                        display: true,
                                        labelString: '千円'
                                    }
                                }
                            ],
                            xAxes: [{
                                ticks: {
                                    //fontColor: "white", // this here
                                },
                            }],
                        },
                        tooltips: {
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    return Number(tooltipItem.yLabel).toLocaleString('ja-JP', { style: 'currency', currency: 'JPY' }) + '円/m²';
                                },
                                title: function(tooltipItem){
                                    // `tooltipItem` is an object containing properties such as
                                    // the dataset and the index of the current item
                                    // Here, `this` is the char instance
                                    // The following returns the full string
                                    var year = this._data.labels[tooltipItem[0].index];
                                    return '平成' + (year - 1988) + '年';
                                }
                            }
                        }
                    }
                });
            }
        }
    );
}
function addItemMarkersToMap(priceType, value) {
    var price0 = Number(value.price0); //latest price
    var price1 = Number(value.price1); //the price year before latest.
    var caption = "地価公示";
    if (priceType == window.urls.surveyDetail) {
        caption = "地価調査";
    }
    var icon = null;
    var changeStr = '';
    if (price1 != 0) {
        var changeRate = Math.round(100 * (price0 - price1)/price1);
        if (changeRate > 0) {
            changeStr = "&nbsp;&nbsp;+" + changeRate + "%";
            icon = '../../img/up.png';
        } else if (changeRate < 0) {
            changeStr =  "&nbsp;&nbsp;" + changeRate + "%";
            icon = '../../img/down.png';
        } else {
            changeStr = "&nbsp;&nbsp;変動なし";
            icon = '../../img/equal.png';
        }
    } else {
        icon = '../../img/equal.png';
    }
    /* InfoWindowクラスのオブジェクトを作成 */
    var infoWindow = new google.maps.InfoWindow();
    /* 指定したオプションを使用してマーカーを作成 */
    var marker = new google.maps.Marker({position:new google.maps.LatLng(value.lat,value.lng), map:map,icon:icon});
    /* addListener を使ってイベントリスナを追加 */
    /* 地図上のmarkerがクリックされると｛｝内の処理を実行。*/
    google.maps.event.addListener(marker, 'click', function() {
        /* InfoWindowOptionsオブジェクトを指定します。*/
        infoWindow.setContent(caption + "："+ price0.toLocaleString('ja-JP', { style: 'currency', currency: 'JPY' }) + '円/m²' + changeStr + "<br>"
            + '<a href=\"../' + priceType + '/' + value.address + '\">' + value.address + "</a>");
        /* マーカーに情報ウィンドウを表示 */
        infoWindow.open(map,marker);
        //add the detail information under the map.

    });
}
function showMap(targetUrl) {
    //console.log(targetUrl);
    var markers = [];
    $.ajax (
        {
            url : targetUrl,
            type: "GET",
            success: function (json) {
                var i = 0;
                var lats = 0.0;
                var lngs = 0.0;
                json.postedItems.forEach(function (value) {
                    var lat = value.lat;
                    var lng = value.lng;
                    //
                    lats = lats + parseFloat(lat);
                    lngs = lngs + parseFloat(lng);
                    i++;
                });
                console.log("i:" + i);
                console.log("latlng:" + lats + "/" + lngs);
                map = new google.maps.Map( mapDiv, {
                    center: new google.maps.LatLng(lats/i, lngs/i),
                    zoom:13,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    mapTypeControl: false,
                    scrollwheel: false,
                    draggable: true,
                    styles: [{"featureType":"landscape.natural","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#e0efef"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"hue":"#1900ff"},{"color":"#c0e8e8"}]},{"featureType":"road","elementType":"geometry","stylers":[{"lightness":100},{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"visibility":"on"},{"lightness":700}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#7dcdcd"}]}],
                    mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
                    navigationControl: false,
                    navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL},
                });
                //post
                json.postedItems.forEach(function (value) {
                    addItemMarkersToMap(window.urls.postDetail, value);
                });
                //survey
                json.surveyedItems.forEach(function (value) {
                    //
                    addItemMarkersToMap(window.urls.surveyDetail, value);
                });
            }
        }
    );
}

$(document).ready(function () {
    $("a#linkArea").click( function () {
            $("a#targetArea").text($(this).text());
        }
    );
});
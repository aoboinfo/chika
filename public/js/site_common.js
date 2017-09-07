/**
 * Created by shutoukin on 2017/08/19.
 */
/**
 * Draw price change rate.
 */
var map = new Y.Map("map-canvas");
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

function showMap(targetUrl) {
    $.ajax (
        {
            url : targetUrl,
            type: "GET",
            success: function (json) {
                var markers = [];
                json.mapItems.forEach(function (value) {
                    var lat = value.lat;
                    var lng = value.lng;
                    var changed = Number(value.dif);
                    var icon = null;
                    if (changed > 0) {
                        icon = new Y.Icon('img/up.png');
                    } else if (changed < 0){
                        icon = new Y.Icon('img/down.png');
                    } else {
                        icon = new Y.Icon('img/equal.png');
                    }
                    markers.push( new Y.Marker(new Y.LatLng(lat, lng), {icon: icon}) );
                });
                map.addFeatures( markers );
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
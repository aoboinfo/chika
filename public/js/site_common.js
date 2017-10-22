/**
 * Created by shutoukin on 2017/08/19.
 */
/**
 * Draw price change rate.
 */
window.priceType = 2;
window.msg = [
    "該当データがありませんでした！入力情報を見直してから、もう一度検索してください！",
    "検索結果が１００件を超えましたため、表示できません。入力情報を絞って、もう一度試してください！",
    "該当物件は{}件が見つかりました！",
    "検索結果が２００件を超えましたため、表示できません。入力情報を絞って、もう一度試してください！",
];
window.NG = "NG";
window.OK = "OK";
window.NOTICE_COUNT_KEY = "new_notice_of_price";
//
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
window.urls = {//the latest version
    findOptionList: 'list/options', //find listing based on option
    listAll: 'list/all',
    listPostStation: 'list/stationPost', //ok, at present
    listSurveyStation: 'list/stationSurvey', //ok, at present
    detail: '/item/detail',
    listingOptions: 'listingCityPlanAndUsage', //show options
    searchAddress: '/search/address/',
    notice: '/notice/message/'
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
        label:'地価調査(H29)',
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
        label:'地価調査(H29)',
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
    if (priceType == "1") {
        caption = "地価調査";
    }
    var icon = null;
    var changeStr = '';
    var changeRate = ''; //must be '', if you want get NULL in php request
    if (price1 != 0) {
        changeRate = Math.round(100 * (price0 - price1)/price1);
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
        var localPrice = price0.toLocaleString('ja-JP', { style: 'currency', currency: 'JPY' });
        infoWindow.setContent(caption + "："+ localPrice + '円/m²' + changeStr + "<br>"
            + '<a href=\"../' + window.urls.detail + '/' + value.address + '?type=' + priceType + '&price=' + localPrice + '&rate=' + changeRate + '\">' + value.address + "</a>");
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
                if (json.postedItems.length > 0) {
                    json.postedItems.forEach(function (value) {
                        var lat = value.lat;
                        var lng = value.lng;
                        //
                        lats = lats + parseFloat(lat);
                        lngs = lngs + parseFloat(lng);
                        i++;
                    });
                } else {
                    json.surveyedItems.forEach(function (value) {
                        var lat = value.lat;
                        var lng = value.lng;
                        //
                        lats = lats + parseFloat(lat);
                        lngs = lngs + parseFloat(lng);
                        i++;
                    });
                }
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
                    addItemMarkersToMap('0', value);
                });
                //survey
                json.surveyedItems.forEach(function (value) {
                    //
                    addItemMarkersToMap("1", value);
                });
            }
        }
    );
}

//Show notice message

function showNotice() {
    var urlsItems = decodeURIComponent(window.location.href).split("/");
    $.ajax(
        {
            url: urlsItems[0] + '//' + urlsItems[2] + window.urls.notice,
            type: 'GET',
            success: function (jsonObj) {

                if (jsonObj.result.length > 0) {
                    //alert(jsonObj.result.length);
                    var noticeCount = $.cookie(window.NOTICE_COUNT_KEY);
                    var noticeDisplayedCount = 0;
                    if (noticeCount == null) {
                        noticeDisplayedCount = jsonObj.result.length;
                    } else {
                        noticeDisplayedCount = jsonObj.result.length - parseInt(noticeCount);
                    }
                    if (noticeDisplayedCount == 0) {//no red flag
                        $("small#notice_count").hide();
                        $("span#notice_count").removeClass("new badge");
                        $("span#notice_count").text(jsonObj.result.length);
                    } else {//show red flag
                        $.cookie(window.NOTICE_COUNT_KEY, jsonObj.result.length, {expires: 365 * 2});
                        $("span#notice_count").show();
                        $("span#notice_count").text(jsonObj.result.length);
                        $("span#notice_count").addClass("new badge");
                    }
                    jsonObj.result.forEach(function (value) {
                        //alert(value.notice);
                        $("ul#notifications-dropdown").append("<li><a href='#!'>" + value.notice + "</a></li>")
                    });
                }
            }
        }
    );
}
$(document).ready(function () {
    $("a#linkArea").click( function () {
            $("a#targetArea").text($(this).text());
        }
    );
    $("span#post").click( function () {
            window.priceType = 0;
            $("span.language-select").text("地価公示");
        }
    );
    $("span#survey").click( function () {
            window.priceType = 1;
            $("span.language-select").text("地価調査");
        }
    );
    $("span#post_survey").click( function () {
            window.priceType = 2;
            $("span.language-select").text("公示・調査");
        }
    );
    $("input[name='Search']").keydown(function (e){
        if(e.keyCode == 13){
            var inputTxt = $("input[name='Search']").val();
            if (inputTxt.length == 0) {
                return;
            }
            $("div#modalSearch").css({"display":"block", "margin":"auto", "bottom":"5%", "z-index":"20", "height":"70%"});
            $("span#madal_caption").text("検索中・・・");
            //alert("input:" + window.priceType + '/' + inputTxt);
            var urlsItems = decodeURIComponent(window.location.href).split("/");
            $.ajax(
                {
                    url:urlsItems[0] + '//' + urlsItems[2] + window.urls.searchAddress,
                    type: 'post',
                    data: {
                        "type": window.priceType,
                        "address":inputTxt
                    },
                    success: function (jsonObj) {
                        $("div#seach_going").hide();
                        if (jsonObj.msg == window.NG) {
                            var messageIndex = parseInt(jsonObj.msg_idx);
                            $("span#madal_caption").text(window.msg[messageIndex]);
                        } else {
                            var records = jsonObj.result;
                            $("span#madal_caption").text(window.msg[2].replace("{}", records.length));
                            var child = $("#modal_content > tbody:last-child");
                            child.empty();
                            records.forEach(function (value) {
                                var changed = value.rate;
                                if (changed == null) {
                                    changed = "";
                                }
                                var priceType = "0";
                                var recordId = value.id;
                                if (recordId.search("L02") == 0) {
                                    priceType = "1";
                                }
                                if (changed < 0) {
                                    child.append("<tr class='deep-orange-text'><td>" + value.price +
                                        "</td><td>" + changed +
                                        "</td><td>" + '<a style=\"color:black;\" href=\"' + urlsItems[0] + '//' + urlsItems[2] + window.urls.detail + '/' + value.address + '?type=' + priceType + '&price=' + value.price + '&rate=' + changed + '\">' + value.address + "</a>" +
                                        "</td><td>" + value.station +
                                        "</td><td>" + value.distance +
                                        "</td><td>" + value.structure +
                                        "</td><td>" + value.usage +
                                        "</td><td>" + value.cityPlan +
                                        "</td></tr>");
                                } else {
                                    child.append("<tr><td>" + value.price +
                                        "</td><td>" + changed +
                                        "</td><td>" + '<a style=\"color:black;\" href=\"' + urlsItems[0] + '//' + urlsItems[2] + window.urls.detail + '/' + value.address + '?type=' + priceType + '&price=' + value.price + '&rate=' + changed + '\">' + value.address + "</a>" +
                                        "</td><td>" + value.station +
                                        "</td><td>" + value.distance +
                                        "</td><td>" + value.structure +
                                        "</td><td>" + value.usage +
                                        "</td><td>" + value.cityPlan +
                                        "</td></tr>");
                                }
                             });
                        }
                    }
                }
            );
        }
    });
    $("a#modalClose").click(function () {
        $("div#modalSearch").css({"display":"none","bottom":"-100%"});
    });
});
var map;
var priceType = 2;
var address = "";
var currentUse = "";
var areaType = "smap";
var selectedKen="";
var structur = "";
var lat;
var lng;
var isInit = true;

function displayCitys(data, kenName) {
    var jsonObj = $.parseJSON(data);
    var cityies = jsonObj.list;
    var citiesDiv = $("div#cities");
    $("#dataInit").css("display","none");
    citiesDiv.empty();
    citiesDiv.append("<strong>"+ kenName + "の検索結果：</strong><br>");
    for (i=0; i < cityies.length; i++) {
        var cityInfo = cityies[i];
        citiesDiv.append("<span class=\"city\" onclick=\"getPriceOfCity($(this).text())\">" + cityInfo[1]+ "("+ cityInfo[0] +"件)</span>" );
    }
}
//Show price for selected area.
function displayData(data) {
    var jsonObj = $.parseJSON(data);
    var dataArr = jsonObj.list;
    if (dataArr.length < 1) {
        $("#dataInit").css("display","none");
        alert("該当データありません、検索条件を変えて、もう一度検索してください！");
        return;
    }
    $("#dataInit").css("display","none");
    var priceList = $("#mapList");
    //clear markers on map
    map.clearFeatures();
    //
    priceList.append("<caption>物件データ："+dataArr.length+"件</caption>");
    priceList.append("<tr><th>地価</th><th>価格(円/m²)*</th><th>変動率(%)</th><th>住所</th><th>最寄駅</th><th>最寄駅から距離(m)</th><th>利用現況</th><th>建物構造</th><th>用途地域</th><th>詳細内容</th></tr>");
    var markers = [];
    for (i=0; i < dataArr.length; i++) {
        var price = dataArr[i];
        var priceType = "";
        if (price[1].indexOf("L01") == 0){
            priceType = "公示";
        } else {
            priceType = "調査";
        }
        //var addr = price[2].split(" ");
        if (areaType == "smap") {
            var marker = new Y.Marker(new Y.LatLng(price[2],price[3]));
            marker.title = "¥"+price[4] + "<br>" + price[5];
            markers.push( marker );
        }
        var changeSpan = "";
        if (price[0].trim() == "0") {
            changeSpan = "<span style='font-size:16px;color:#000;font-weight:bold;'>"+ price[0] +"</span>";
        } else if (price[0].trim().indexOf("-") == 0) {
            changeSpan = "<span style='font-size:16px;color:#0C3;font-weight:bold;'>"+ price[0] +"</span>";
        } else {
            changeSpan = "<span style='font-size:16px;color:#f00;font-weight:bold;'>"+ price[0] +"</span>";
        }
        var line ="<tr><td>" + priceType +"</td><td>" + price[4] +"</td><td>" + changeSpan +"</td><td>" + price[5]  +"</td><td>" + price[6] +"</td><td>" + price[7] + "</td><td>" + price[8] + "</td><td>" + price[9] + "</td><td>" + price[10] + "</td><td>"+"<a href='detail.php?id="+price[1] +"&change="+ price[0] +"' target=\"_blank\">詳細</a></td></tr>";
        priceList.append(line);
    }
    priceList.css("display","block");
    if (areaType == "smap") {
        map.addFeatures( markers );
    }
}
//init screen
function showInitData() {
    $.ajax  (
        {
            type: "POST",
            url:  "initMap.php",
            success: function (data, status) {
                //alert(data);
                isInit = false;
                displayData(data);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert("エラー発生しました！\n" + errorThrown);
            }
        });
}

//Data for new Latlng
function showDataOfLatLng(lat, lng) {
    var sLat = lat.substring(0, 5);
    var sLng = lng.substring(0, 6);
    //alert(sLat + ":" + sLng);
    $.ajax(
        {
            type: "POST",
            url:  "latLng.php",
            data: {
                latitude:sLat,
                longitude:sLng,
                "type": $("input:radio[name='priceType']:checked").val()
            },
            success: function (data, status) {
                //alert(data);
                displayData(data);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert("エラー発生しました！\n" + errorThrown);
            }
        });
}
//Display data for ken
function getCityData(kenName) {
    $.ajax(
        {
            type: "POST",
            url: "cityData.php",
            data: {
                "ken": kenName,
                "type": $("input:radio[name='priceType']:checked").val()
            },
            success: function (data, status) {
                //alert(data);
                displayCitys(data, kenName);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert("エラー発生しました！\n" + errorThrown);
            }
        });
}
//Price information for selected city
function getPriceOfCity(cityName) {
    //alert(cityName);
    $("#mapList").empty();
    $("#dataInit").css("display","block");
    var names = cityName.split("(");
    $.ajax(
        {
            type: "POST",
            url: "cityPrice.php",
            data: {
                "city": names[0],
                "ken": selectedKen,
                "type": $("input:radio[name='priceType']:checked").val()
            },
            success: function (data, status) {
                //alert(data);
                displayData(data);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert("エラー発生しました！\n" + errorThrown);
            }
        });
}
//
function getPriceOfCategory() {
    $.ajax(
        {
            type: "POST",
            url: "categoryPrice.php",
            data: {
                "usage":    $("input:radio[name='usage']:checked").parent().text(),
                "location": $("input#address").val().trim(),
                "locationType": $("input:radio[name='area']:checked").parent().text(),
                "structure":  $("input:radio[name='building']:checked").val(),
                "type":   $("input:radio[name='priceType']:checked").val()
            },
            success: function (data, status) {
                //alert(data);
                displayData(data);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert("エラー発生しました！\n" + errorThrown);
            }
        });
}

//srceen event functions
$(document).ready(function() {
    //radio changed
    $("input:radio[name='priceType']").change( function () {
        if (isInit) {return;}
        if (selectedKen == "" && areaType == "sken") {return;}
        $("#dataInit").css("display","block");
        $("#mapList").empty();
        if (areaType == "smap") {
            showDataOfLatLng(lat.toFixed(8), lng.toFixed(8));
        } else if (areaType == "sken") {
            getCityData(selectedKen);
        }
    });
    //area option
    $("li#smap").click( function () {
        areaType = "smap";
        $("#mapList").empty();
        $("#dataInit").css("display","block");
        showDataOfLatLng(lat.toFixed(8), lng.toFixed(8));
    });
    $("li#sken").click( function () {
        areaType = "sken";
        $("#mapList").empty();
        map.clearFeatures();
    });
    $("li#scategory").click( function () {
        areaType = "scategory";
        $("#mapList").empty();
        map.clearFeatures();
    });
    $("button#search").click ( function () {
        var sAddress = $("input#address").val().trim();
        if (sAddress.length == 0) {
            alert("住所は入力されていません。");
            return;
        }
        $("#dataInit").css("display","block");
        $("#mapList").empty();
        getPriceOfCategory();
    });
    //clicked ken name
    $("span.ken").click ( function () {
        //alert($(this).text());
        $("#mapList").empty();
        $("#dataInit").css("display","block");
        selectedKen = $(this).text();
        getCityData(selectedKen);
    });
});

window.onload = function(){
    lat = 35.692507; // 新宿駅の緯度
    lng = 139.700346;// 新宿駅の経度
    map = new Y.Map("myMap");
    map.bind("moveend", function(){
        var pos = map.getCenter();
        lat = pos.lat();
        lng = pos.lng();
        $('#mapPos').html("緯度："+ lat.toFixed(8) + "、経度：" + lng.toFixed(8) );
        if (!isInit) {
            $("#dataInit").css("display","block");
            $("#mapList").empty();
            showDataOfLatLng(lat.toFixed(8), lng.toFixed(8));
        }
    });
    map.drawMap(
        new Y.LatLng(lat, lng),
        16,  //ズームレベルは16
        Y.LayerSetId.NORMAL  //通常の地図を表示
    );
    // 地図の種類を切り換えるコントローラーを表示
    map.addControl(new Y.LayerSetControl());
    // ズームコントローラーを表示
    map.addControl(new Y.SliderZoomControlVertical());
    map.addControl(new Y.SearchControl());
    map.addControl(new Y.CenterMarkControl());
    map.addControl(new Y.ScaleControl());
    showInitData();
}
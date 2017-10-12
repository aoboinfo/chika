//srceen event functions
function newSearchURL(offset) {
    var values = window.location.href.split("&");
    var offsets = values[1].split("=");
    var newOffsetValue = parseInt(offsets[1]) + parseInt(offset);
    values[1] = "offset=" + newOffsetValue;
    return values.join("&");
}

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
    $("a#btn_next").click( function () {
        //alert(newSearchURL(10));
        this.setAttribute("href", newSearchURL(10));
    });
    $("a#btn_prev").click( function () {
        this.setAttribute("href", newSearchURL(-10));
    });
});
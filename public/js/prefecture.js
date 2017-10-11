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
    //Set current usage and city plan menu.
    //console.log('listingCityPlanAndUsage/' + prefectureName.substring(0, 3) + '/');
    $.ajax(
        {
            url : window.urls.listingOptions + '/' + prefectureName.substring(0, 3) + '/',
            type: "GET",
            success: function (json) {
                for (var i = 0; i < json.postedUsages.length; i++) {
                    var usage = json.postedUsages[i].usage;
                    var count = json.postedUsages[i].count;
                    //var linkURL = '<li><a href="usages/' + prefectureName.substring(0, 3) + '/' + usage + '">' + usage + "<span class=\"new badge\">" + count + "</span></a></li>";
                    //console.log(linkURL);
                    $("ul#post_usages").append('<li><a href="' + window.urls.findOptionList + '/' + prefectureName.substring(0, 3) + '/?type=0&offset=0&option=usage&value=' + usage + '">' + usage + "<span class=\"new badge\">" + count + "</span></a></li>");
                }
                for (var i = 0; i < json.postedCityPlans.length; i++) {
                    var cityPlan = json.postedCityPlans[i].cityPlan;
                    var count = json.postedCityPlans[i].count;
                    //var linkURL = '<li><a href="usages/' + prefectureName.substring(0, 3) + '/' + usage + '">' + usage + "<span class=\"new badge\">" + count + "</span></a></li>";
                    //console.log(linkURL);
                    $("ul#post_cityPlans").append('<li><a href="' + window.urls.findOptionList + '/' + prefectureName.substring(0, 3) + '/?type=0&offset=0&option=cityPlan&value=' + cityPlan + '">' + cityPlan + "<span class=\"new badge\">" + count + "</span></a></li>");
                }
                //survey
                for (var i = 0; i < json.surveyedUsages.length; i++) {
                    var usage = json.surveyedUsages[i].usage;
                    var count = json.surveyedUsages[i].count;
                    //var linkURL = '<li><a href="usages/' + prefectureName.substring(0, 3) + '/' + usage + '">' + usage + "<span class=\"new badge\">" + count + "</span></a></li>";
                    //console.log(linkURL);
                    $("ul#survey_usages").append('<li><a href="' + window.urls.findOptionList + '/' + prefectureName.substring(0, 3) + '/?type=1&offset=0&option=usage&value=' + usage + '">' + usage + "<span class=\"new badge\">" + count + "</span></a></li>");
                }
                for (var i = 0; i < json.surveyedCityPlans.length; i++) {
                    var cityPlan = json.surveyedCityPlans[i].cityPlan;
                    var count = json.surveyedCityPlans[i].count;
                    //var linkURL = '<li><a href="usages/' + prefectureName.substring(0, 3) + '/' + usage + '">' + usage + "<span class=\"new badge\">" + count + "</span></a></li>";
                    //console.log(linkURL);
                    $("ul#survey_cityPlans").append('<li><a href="' + window.urls.findOptionList + '/' + prefectureName.substring(0, 3) + '/?type=1&offset=0&option=cityPlan&value=' + cityPlan + '">' + cityPlan + "<span class=\"new badge\">" + count + "</span></a></li>");
                }

            }
        }
    );
}

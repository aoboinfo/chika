/**
 * Created by shutoukin on 2017/09/20.
 */

var doughnutColor = [
    window.chartColors.red,
    window.chartColors.orange,
    window.chartColors.yellow,
    window.chartColors.green,
    window.chartColors.blue,
    window.chartColors.purple,
    window.chartColors.grey,
    window.chartColors.pink,
    window.chartColors.teal,
    window.chartColors.brown,
    window.chartColors.olive,
    window.chartColors.peru,
    window.chartColors.cyan
];

var postUsageConfig = {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [],
            backgroundColor:[],
            label: 'Dataset 1'
        }],
        labels: []
    },
    options: {
        responsive: true,
        legend: {
            position: 'right',
        },
        title: {
            display: true,
            text: '利用現況'
        },
        animation: {
            animateScale: true,
            animateRotate: true
        }
    }
};

var postCityPlanConfig = {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [],
            backgroundColor:[],
            label: 'Dataset 1'
        }],
        labels: []
    },
    options: {
        responsive: true,
        legend: {
            position: 'right',
        },
        title: {
            display: true,
            text: '都市計画区域区分'
        },
        animation: {
            animateScale: true,
            animateRotate: true
        }
    }
};

var surveyCityPlanConfig = postUsageConfig;

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
    urlsValues.splice(3, 1, "mapItems"); //exchange the key in router
    showMap(urlsValues.join("/"));
    //Draw Doughnut on the city town page
    //
    urlsValues.splice(3, 1, "listingCityPlan"); //exchange with mapItems
    //draw doughnut for post city plan.
    var ctx = document.getElementById("doughnut-cityPlan-post").getContext("2d");
    window.myDoughnut = new Chart(ctx, postUsageConfig);
    $.ajax(
        {
            url : urlsValues.join("/"),
            type: "GET",
            success: function (json) {
                for (var i = 0; i < json.postedUsages.length; i++) {
                    var usage = json.postedUsages[i].usage;
                    var count = json.postedUsages[i].count;
                    if (i == 13) {
                        break;
                    }
                    postUsageConfig.data.labels.push(usage);
                    postUsageConfig.data.datasets.forEach(function(dataset) {
                        dataset.data.push(count);
                        dataset.backgroundColor.push(doughnutColor[i]);
                    });
                }
                window.myDoughnut.update();

            }
        }
    );

}
/**
 * Created by shutoukin on 2017/08/19.
 */
window.onload = function () {
    drawAvgPrice(
        'bar',
        "http://www.chika.com/avgs/",
         document.getElementById("post_price_trending"),
        'rgb(255, 99, 132)',
        'rgb(255, 99, 132)'
    );

    drawAvgPrice(
        'bar',
        "http://www.chika.com/avg2/",
        document.getElementById("survey_price_trendding"),
        'rgb(25, 118, 210)', //background color
        'rgb(25, 118, 210)' //border color
    );
    changeRateDataSets[0].backgroundColor = 'rgb(255, 99, 132)';
    changeRateDataSets[0].borderColor = 'rgb(255, 99, 132)';
    changeRateDataSets[1].backgroundColor = 'rgb(25, 118, 210)';
    changeRateDataSets[1].borderColor = 'rgb(25, 118, 210)';
    drawPriceChangeRate('http://www.chika.com/changeRate/', document.getElementById("price_change_rate"));
}
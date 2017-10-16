/**
 * Created by shutoukin on 2017/10/16.
 */
var detailDataSets = [
    {
        label:'',
        backgroundColor:'',
        borderColor:'',
        borderWidth:1,
        data:[]
    }
];

function detailMap () {
    var lat = parseFloat(document.getElementById("map_lat").value);
    var lng = parseFloat(document.getElementById("map_lng").value);
    var myLatlng = new google.maps.LatLng(lat, lng);
    var detailMapDiv = document.getElementById("detail_map");
    var detailMap = new google.maps.Map(detailMapDiv, {
        center: myLatlng,
        zoom:14,
        scrollwheel: false,
        draggable: true
    });
    var panorama = new google.maps.StreetViewPanorama(
        document.getElementById('detail_pano'), {
            position: myLatlng,
            pov: {
                heading: 10,
                pitch: 10
            }
        });
    detailMap.setStreetView(panorama);
}
window.onload = function () {
    detailMap();
    var years = document.getElementById("year").value;
    var priceType = document.getElementById("price_type").value;
    //
    detailDataSets[0].backgroundColor = 'rgb(255, 99, 132)';
    detailDataSets[0].borderColor = 'rgb(255, 99, 132)';
    if (priceType == '0') {
        detailDataSets[0].label="地価公示(H29)";
    } else {
        detailDataSets[0].label="地価調査(H28)";
    }
    detailDataSets[0].data = JSON.parse(document.getElementById("price").value);

    var ctx = document.getElementById("price_history").getContext('2d');
    var chart = new Chart(ctx, {
        type: 'bar',
        // The data for our dataset
        data: {
            labels: JSON.parse(years),
            datasets:detailDataSets
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
jQuery(document).ready(function(){
	var $ = jQuery;

    $('.input-daterange > input').datepicker({
      format: 'dd-mm-yyyy'
    }) .on("changeDate", function(e) {
      getDashboardCharts();
    });

    getDashboardCharts();

})

function getMonthLabel(month_number){
    var labels = ["января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря"];
    return labels[month_number];
}


function getDashboardCharts(){

    var data = {
        chart : "simple",
        user_id : $('input[name=user_id]').val(),
        range: {
            from : $('.input-daterange > input[name="from"]').val(),
            to : $('.input-daterange > input[name="to"]').val()
        },
    }

     $.ajax({
        url: '/ajax/get-user-details-charts/',
        type: 'POST',
        dataType: 'json',
        data: $.param(data),
        success: function(response){

            getTrafficChart({
                unique: response.unique,
                all: response.all
            });

            getConversionChart({
                approved : response.approved,
                waiting : response.waiting,
                canceled : response.canceled,
            });

            getApproveChart(response.approve);
        }
      })
}

function getTrafficChart(chart_data) {

    if ($('#traffic_chart').size() != 1) {
        return;
    }

    var plot = $.plot($("#traffic_chart"), [{
        data: chart_data.all,
        label: "Хиты",
        lines: {
            lineWidth: 2,
        },

    }, {
        data: chart_data.unique,
        label: "Хосты",
        lines: {
            lineWidth: 2,
        },
    }], {
        series: {
            lines: {
                show: true,
            },
            points: {
                show: true,
            },
        },
        grid: {
            hoverable: true,
            borderColor: "#777777",
        },
        xaxis: {
            mode: "time",
            timeformat: "%d/%m",
            ticks: 14,
            tickDecimals: 0,
        },
        yaxis: {
            tickDecimals: 0,
        },
        colors: ["#3bafda", '#FCBB42']
    });
}

function getConversionChart(chart_data) {
    if ($('#conversion_chart').size() != 1) {
        return;
    }
    var plot = $.plot($("#conversion_chart"), [{
        data: chart_data.approved,
        label: "Принято",
    }, {
        data: chart_data.waiting,
        label: "В ожидании",
    }, {
        data: chart_data.canceled,
        label: "Отклонено",
    }], {
        series: {
            lines: {
                show: true,
            },
            points: {
                show: true,
            },
        },
        grid: {
            hoverable: true,
            borderColor: "#777777",
        },
        xaxis: {
            mode: "time",
            timeformat: "%d/%m",
            ticks: 14,
        },
        yaxis: {
            tickDecimals: 0,
        },
        colors: ["#27ae60", "#3bafda", "#ed5565"],


    });
}

function getApproveChart(chart_data) {
    if ($('#approve_chart').size() != 1) {
        return;
    }

    var plot = $.plot($("#approve_chart"), [{
        data: chart_data,
        label : "Approve"
    }], {
        series: {
            lines: {
                show: true,
            },
            points: {
                show: true,
            },
        },
        grid: {
            hoverable: true,
            borderColor: "#777777",
        },
        colors: ["#27ae60"],
        xaxis: {
            mode: "time",
            timeformat: "%d/%m",
            tickDecimals: 0,
            ticks: 14,
        },
        yaxis: {
            tickDecimals: 0,
        }
    });
}

function showTooltip(x, y, contents) {
    $('<div id="tooltip">' + contents + '</div>').css({
        position: 'absolute',
        display: 'none',
        top: y + 5,
        left: x + 15,
        border: '1px solid #333',
        padding: '4px',
        color: '#fff',
        'border-radius': '3px',
        'background-color': '#333',
        opacity: 0.80
    }).appendTo("body").fadeIn(200);
}

var previousPoint = null;
$(".chart").bind("plothover", function(event, pos, item) {
    $("#x").text(pos.x.toFixed(2));
    $("#y").text(pos.y.toFixed(2));

    var d = new Date();
    var n = d.getMonth();
    var month_label = getMonthLabel(n);

    if (item) {
        if (previousPoint != item.dataIndex) {
            previousPoint = item.dataIndex;

            $("#tooltip").remove();
            var x = item.datapoint[0],
                y = item.datapoint[1];

            var date = new Date(x);
            var day = date.getDate();
            var month = date.getMonth() + 1;

            day = (day < 10) ? '0'+day : day;
            month = (month < 10) ? '0'+month : month;
            showTooltip(item.pageX, item.pageY, item.series.label + " за " + day + "/" + month + " = " + y);
        }
    } else {
        $("#tooltip").remove();
        previousPoint = null;
    }
});

// Upload file
$("#form").on('submit', (function(e) {
    e.preventDefault();
    $("#importSubmit").attr("disabled", true);
    $("#error").removeClass('alert-success');
    $("#error").removeClass('alert-danger');
    $("#error").html('')
    var file_data = $("#fileInput").prop("files")[0];
    var form_data = new FormData(this);
    form_data.append('uploadBTN', 'Upload');
    e.preventDefault();
    $.ajax({
        url: "../public/api.php",
        type: "POST",
        data: form_data,
        contentType: false,
        cache: false,
        processData: false,
        dataType: 'json',
        beforeSend: function() {

            $(".loading").show();
        },
        success: function(data) {
            $(".loading").hide();
            if (data.error === true) {
                $("#importSubmit").attr("disabled", false);
                $("#error").addClass('alert-success');
                $("#form")[0].reset();
                $("#error").html(data.message)
                updateChart();
            } else {
                $("#importSubmit").attr("disabled", false);
                $("#error").addClass('alert-danger');
                $("#error").html(data.message)
                $("#form")[0].reset();

            }
        },
        error: function(e) {
            $("#importSubmit").attr("disabled", false);
            $("#error").html('Something went wrong, Try again')
        }
    });
}));

// Update Chart
function updateChart() {

    $.ajax({
        url: "../public/api.php",
        method: 'POST',
        dataType: 'json',
        data: {
            'refresh_chart': 'refresh'
        },
        success: function(response) {
            drawChart(response.label, response.dataset);
        },
        error: function(xhr, status, error) {
            console.error("Failed to Load data");
            console.error(xhr
                .responseText);
        },
    });

}

// Draw Chart
function drawChart(label, dataset) {
    var ctx = document.getElementById("myChart4").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',

        data: {
            labels: label,
            datasets: dataset,
        },
        options: {
            title: {
                display: true,
                text: 'Leads and Activates'
            },
            tooltips: {
                displayColors: true,
                callbacks: {
                    mode: 'x',
                },
            },
            scales: {
                xAxes: [{
                    stacked: true,
                    gridLines: {
                        display: false,
                    }
                }],
                yAxes: [{
                    stacked: true,
                    ticks: {
                        beginAtZero: true,
                    },
                    type: 'linear',
                }]
            },
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'bottom'
            },
        }
    });

}
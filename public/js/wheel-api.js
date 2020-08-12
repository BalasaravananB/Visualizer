var boxes;
var allData;
var widthAdjusted = true;

var make = $('.make').val();
var year = $('.year').val();
var model = $('.model').val();
var submodel = $('.submodel').val();

var $loading = $('.se-pre-con');
$(document).ready(function() {
    getVisualiserModal();
    getWheelPosition();
    var delay = 1000;
    setTimeout(function() {
            $loading.fadeOut("slow");
            console.log('Waiting Time Closed')
        },
        delay
    );
});

function getVisualiserModal() {

    var modalStr = `
        <!-- Visualiser Model Start -->
        <div class="modal fade" id="VisualiserModal" tabindex="-1" role="dialog" aria-labelledby="VisualiserLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="VisualiserLabel"></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12 model-car modal_canvas" id="modal_canvas_0">
                                <img id="vehicle-image" class="vehicle-image" src="" data-carid="9818" data-imagename="storage/cars/9612_cc2400_032_019.png">
                            </div>
                            <div class="car-wheel">
                                <div class="front">
                                    <img class="frontimg" src="" id="wheel-front">
                                </div>
                                <div class="back">
                                    <img class="backimg"  src="" id="wheel-back">
                                </div>
                            </div>
                        </div>
                        <div class="row model-car-body"> 
                            <div class="col-sm-4">
                                <h1 class="model-car">Wheel Diameter</h1>
                                <button class="model-button diameter-up" data-id="0">Zoom In</button>
                                <button class="model-button diameter-down" data-id="0">Zoom Out</button>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Visualiser Model End -->
          `;

    $('#Visualiser-Section').html(modalStr);
}

function getWheelPosition(key) {

    var contentType = "application/x-www-form-urlencoded; charset=utf-8";

    if (window.XDomainRequest) //for IE8,IE9
        contentType = "text/plain";

    $.ajax({
        url: "http://web9.vtdns.net/api/WheelByVehicle",
        data: data,
        type: "POST",
        dataType: "json",
        contentType: contentType,
        success: function(result) {


            if (result['status'] == true) {

                allData = result['data'];

                $loading.fadeOut("slow");

                $("#VisualiserModal").modal("show");

                WheelMapping('0')
            } else {

                $loading.fadeOut("slow");
                alert(result['message']);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert('Something Went Wrong!')
            $loading.fadeOut("slow");
        }
    });


}

function WheelMapping(key = '') {

    boxes = allData['position'];
    $('#vehicle-image').attr('src', allData['carimage']);
    $('#wheel-front').attr('src', allData['frontimage']);
    $('#wheel-back').attr('src', allData['frontimage']);
    $('#VisualiserLabel').html(allData['vehicle']);

    if (boxes[0][0] < 400) {

        f = boxes[0];

        b = boxes[1];

    } else {

        f = boxes[1];

        b = boxes[0];
    }

    var front = $('#wheel-front');
    front.css('left', f[0] - 18 + 'px');
    front.css('top', f[1] - 1 - 30 + 'px');

    if (widthAdjusted) {
        var extraWidth = 0;
        if (front.width() - f[2] > 4) {
            extraWidth = (front.width() - f[2]) / 2;
        }
        front.width(front.width() + extraWidth + 'px');
        widthAdjusted = false;
    }


    var back = $('#wheel-back');
    back.css('left', b[0] - 11.5 + 'px');
    back.css('top', b[1] + 8.5 - 25 + 'px');
}




// Year based filters for Makes 
$(document).on('change', '.year,.make,.model', function() {
    changeBy = $(this).attr('name');
    filters(changeBy);
});
$(document).ready(function() {
    var changeBy = '';
    filters(changeBy);
});



function filters(changeBy = '') {
    var make = $('.make').val();
    var year = $('.year').val();
    var model = $('.model').val();
    var submodel = $('.submodel').val();
    console.log('changeBy', changeBy, make);


    var data = {
        year: year,
        make: make,
        model: model,
        changeBy: changeBy
    }

    var contentType = "application/x-www-form-urlencoded; charset=utf-8";

    if (window.XDomainRequest) //for IE8,IE9
        contentType = "text/plain";

    $.ajax({
        url: "http://web9.vtdns.net/api/getVehicles",
        data: data,
        type: "POST",
        dataType: "json",
        contentType: contentType,
        success: function(result) {
            $('.submodel').empty().append('<option disabled selected>Select Submodel</option>');

            if (changeBy == '' || changeBy == 'year' || changeBy == 'make') {
                $('.model').empty().append('<option disabled selected>Select Model</option>');
            }
            if (changeBy == '' || changeBy == 'make') {
                $('.year').empty().append('<option disabled selected>Select Year</option>');
            }

            if (changeBy == '') {
                data.data['year'].map(function(value, key) {
                    isSelected = (value.yr == year) ? 'selected' : '';
                    $('.year').append('<option value="' + value.yr + '" ' + isSelected + '>' + value.yr + '</option>');
                });
                data.data['model'].map(function(value, key) {
                    isSelected = (value.model == model) ? 'selected' : '';
                    $('.model').append('<option value="' + value.model + '" ' + isSelected + '>' + value.model + '</option>');
                });
                data.data['driverbody'].map(function(value, key) {
                    isSelected = (value.vif == driverbody) ? 'selected' : '';
                    $('.submodel').append('<option value="' + value.vif + '"' + isSelected + '>' + value.whls + ' ' + value.drs + ' ' + value.body + '</option>');
                });
            } else {
                data.data.map(function(value, key) {
                    if (changeBy == 'make') {

                        isSelected = (value.yr == year) ? 'selected' : '';
                        $('.year').append('<option value="' + value.yr + '"' + isSelected + '>' + value.yr + '</option>');
                    }
                    if (changeBy == 'year') {
                        isSelected = (value.model == model) ? 'selected' : '';
                        $('.model').append('<option value="' + value.model + '"' + isSelected + '>' + value.model + '</option>');
                    }
                    if (changeBy == 'model') {
                        isSelected = (value.vif == driverbody) ? 'selected' : '';
                        $('.submodel').append('<option value="' + value.vif + '"' + isSelected + '>' + value.whls + ' ' + value.drs + ' ' + value.body + '</option>');
                    }
                });
            }
  
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert('Something Went Wrong!')
            $loading.fadeOut("slow");
        }
    });


}

// //  Driver / Body change your car 
// $('.submodel').on('change', function() {
//     var car_id = $(this).val();
//     if (car_id != '') {
//         updateParamsToUrl('car_id', car_id);
//     }
// });
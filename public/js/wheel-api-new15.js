var baseurl = "http://web9.vtdns.net"; 
// var baseurl = "http://localhost:8001";
var boxes = null;
var allData;
var widthAdjusted = true;

var make = $('.make').val();
var year = $('.year').val();
var model = $('.model').val();
var submodel = $('.submodel').val();
var changeBy = '';

var vehicle = '';
var vehicleid = '';
var offroadid = '';
var flag = '';
var zipcode = '';
var offroadtype = '';
var liftsize = '';
var qryData = getUrlVars();

var $loading = $('.se-pre-con');

$(document).ready(function() {
    if (qryData['pagename'] == 'list') {
        getWheelsList();
        // getVisualiserModal();
    }
    vehicleFilters();
});



// Year based filters for Makes 
$(document).on('change', '.year,.make,.model', function() {
    changeBy = $(this).attr('name');
    vehicleFilters(changeBy);
});


function showAlert(msg) {
    alert(msg);
    if ($loading) {
        $loading.fadeOut("slow");
    }
}

function vehicleFilters(changeBy = '') {

    make = $('.make').val();
    year = $('.year').val();
    model = $('.model').val();
    submodel = $('.submodel').val();


    var data = {
        year: year,
        make: make,
        model: model,
        changeBy: changeBy,
        accesstoken: accesstoken
    }
    $.ajax({
        url: baseurl + "/api/getVehicles",
        data: data,
        type: "POST",
        success: function(result) {
            if (result['status'] == false) {

                showAlert(result['message']);
            }
            $('.submodel').empty().append('<option disabled selected>Select Submodel</option>');

            if (changeBy == '' || changeBy == 'year' || changeBy == 'make') {
                $('.model').empty().append('<option disabled selected>Select Model</option>');
            }
            if (changeBy == '' || changeBy == 'make') {
                $('.year').empty().append('<option disabled selected>Select Year</option>');
            }

            if (changeBy == '') {
                result.data['make'].map(function(value, key) {
                    isSelected = (value.make == make) ? 'selected' : '';
                    $('.make').append('<option value="' + value.make + '" ' + isSelected + '>' + value.make + '</option>');
                });

                result.data['year'].map(function(value, key) {
                    isSelected = (value.year == year) ? 'selected' : '';
                    $('.year').append('<option value="' + value.year + '" ' + isSelected + '>' + value.year + '</option>');
                });
                result.data['model'].map(function(value, key) {
                    isSelected = (value.model == model) ? 'selected' : '';
                    $('.model').append('<option value="' + value.model + '" ' + isSelected + '>' + value.model + '</option>');
                });
                result.data['submodel'].map(function(value, key) {
                    $('.submodel').append('<option value="' + value.submodel + '-' + value.body + '">' + value.submodel + '-' + value.body + '</option>');
                });
            } else {
                result.data.map(function(value, key) {
                    if (changeBy == 'make') {

                        isSelected = (value.year == year) ? 'selected' : '';
                        $('.year').append('<option value="' + value.year + '"' + isSelected + '>' + value.year + '</option>');
                    }
                    if (changeBy == 'year') {
                        isSelected = (value.model == model) ? 'selected' : '';
                        $('.model').append('<option value="' + value.model + '"' + isSelected + '>' + value.model + '</option>');
                    }
                    if (changeBy == 'model') {
                        $('.submodel').append('<option value="' + value.submodel + '-' + value.body + '">' + value.submodel + '-' + value.body + '</option>');
                    }
                });
            }



        },
        error: function(jqXHR, textStatus, errorThrown) {
            showAlert('Something Went Wrong!')
        }
    });
}


$('.SearchByVehicleGo').click(function() {

    make = $('.make').val();
    year = $('.year').val();
    model = $('.model').val();
    submodel = $('.submodel').val();


    var data = {
        year: year,
        make: make,
        model: model,
        submodel: submodel,
        accesstoken: accesstoken
    }

    $loading.show();
    $.ajax({
        url: baseurl + "/api/findVehicle",
        data: data,
        type: "POST",
        success: function(result) {
            if (result['status'] == false) {
                showAlert(result['message']);
            } else {

                $loading.fadeOut("slow");
                vehicle = result['data']['vehicle'];
                vehicleid = result['data']['vehicle']['vehicle_id'];

                if (result['status'] == true && result['data']['offroad'] != '') {
                    offroadid = result['data']['offroad'];
                    loadOffroadView();
                } else {

                    flag = 'searchByVehicle';
                    loadZipcodeView();
                }
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            showAlert('Something Went Wrong!')
        }
    });
})

function loadOffroadView() {
    $('#Offroad-View-Section').html(`

                <div class="modal" id="offroadTypeModal" role="dialog">
                    <div class="modal-dialog tire-view">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Select any one for your vehicle</h4>
                            </div>
                            <div class="modal-body"  >
                                <!-- <div class="col-md-12"> -->
                                    
                                    <div style="text-align: center;">
                                        <div class="btn btn-info select-offroad" data-offroad="levelkit">
                                            <img src="` + baseurl + `/image/lifttype.jpg" >
                                            <br>
                                            <h3 style="color: white !important">Leveling Kit</h3>
                                        </div> 
                                    </div>

                                                       <br>                                 
                                    <div style="text-align: center;">    
                                        <div class="btn btn-info select-offroad" data-offroad="lift">
                                            <img src="` + baseurl + `/image/lifttype.jpg" >
                                            <br>
                                            <h3 style="color: white !important">Lift Kit</h3>
                                        </div>
                                    </div>
                                    <br>
                                    <div style="text-align: center;"> 
                                        <div class="btn btn-info select-offroad" data-offroad="stock">

                                            <img src="` + baseurl + `/image/lifttype.jpg" >
                                            <br>
                                            <h3 style="color: white !important">Stock</h3>
                                        </div>
                                    </div> 
                                <!-- </div> -->
                            </div>
                        </div>
                    </div>
                </div>


        `);

    $('#offroadTypeModal').modal('show');
}


$(document).on('click', '.select-offroad', function() {

    $("#offroadTypeModal").modal('hide');

    offroadtype = $(this).data('offroad');

    if (offroadtype != 'lift') {
        if (offroadtype == 'levelkit') {
            liftsize = 'Levelkit'
        }

        flag = 'searchByVehicle';
        loadZipcodeView();
    } else {
        var data = {
            offroadid: offroadid,
            accesstoken: accesstoken
        };

        console.log(data);

        $loading.show();
        $.ajax({
            url: baseurl + "/api/getLiftSizes",
            data: data,
            type: "POST",
            success: function(result) {
                if (result['status'] == false) {
                    showAlert(result['message']);
                } else {

                    $loading.fadeOut('slow');
                    if (result['status'] == true) {
                        console.log(result)
                        loadOffroadSizeView(result['data']);
                    } else {
                        flag = 'searchByVehicle';
                        loadZipcodeView();
                    }
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                showAlert('Something Went Wrong!')
            }
        });
    }

});



function loadOffroadSizeView(data) {

    var loadSizeStr = `
                <div class="modal" id="liftsizeModal" role="dialog">
                    <div class="modal-dialog tire-view">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Please select your vehicle's lift:</h4>
                            </div>
                            <div class="modal-body"> 
    `;

    $.each(data, function(sizekey, size) {
        loadSizeStr += `
                                    <div style="text-align: center;"> 
                                        <button class="btn btn-info select-liftsize" data-liftsize="` + sizekey + `">

                                            <img src="` + baseurl + `/image/lifttype.jpg" >
                                            <br>
                                            <h3 style="color: white !important">` + size + `</h3>
                                        </button>
                                    </div>
                                    <br>
        `;
    });

    loadSizeStr += `

                            </div>
                        </div>
                    </div>
                </div>
    `;

    $('#Offroad-Size-View-Section').html(loadSizeStr);

    $('#liftsizeModal').modal('show');
}

$(document).on('click', '.select-liftsize', function() {

    $("#liftsizeModal").modal('hide');
    liftsize = $(this).data('liftsize');
    flag = 'searchByVehicle';
    loadZipcodeView();
});


function loadZipcodeView() {
    if (zipcode == '') {
        $('#liftsizeModal').modal('hide');
        $("#Zipcode-Section").html(`
                    <div class="modal fade" id="Zipcode-Section-Modal" role="dialog">
                        <div class="modal-dialog tire-view">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Zipcode</h4>
                                </div>
                                <div class="modal-body"> 
                                        <div class="form-group has-success has-feedback">
                                            <label class="col-sm-5 control-label" for="inputSuccess">Your Zipcode</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control" id="zipcode-input" name="zipcode" required=""> 
                                            </div>
                                        </div>
                                        <div style="text-align:center;">
                                            <button class="btn btn-info" id="update-zipcode-button" type="button">Continue</button>
                                        </div> 
                                </div>
                            </div>
                        </div>
                    </div>
        `);

        $("#Zipcode-Section-Modal").modal('show');
    } else {
        redirectToList();
    }

}



$(document).on('click', '#update-zipcode-button', function() {

    $("#Zipcode-Section-Modal").modal('hide');
    zipcode = $("#zipcode-input").val();
    redirectToList();
});

function redirectToList() {
    window.location.href = "/products?" +
        "make=" + make +
        "&year=" + year +
        "&model=" + model +
        "&submodel=" + submodel +
        "&vehicleid=" + vehicleid +
        "&offroadid=" + offroadid +
        "&offroadtype=" + offroadtype +
        "&liftsize=" + liftsize +
        "&flag=" + flag +
        "&zipcode=" + zipcode +
        "&pagename=list";
}

function getUrlVars() {
    var vars = [],
        hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for (var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        vars.push(decodeURIComponent(hash[0]));
        vars[hash[0]] = decodeURIComponent(hash[1]);
    }
    return vars;
}


function getWheelsList(paginateurl = '') {

    if (paginateurl == '') {
        url = baseurl + "/api/getWheels"
    } else {
        url = paginateurl;
    }

    var data = "";
    var listType = 'html';
    if (qryData['flag'] == 'searchByVehicle') {
        data = {
            flag: 'searchByVehicle',
            vehicleid: qryData['vehicleid'],
            zipcode: qryData['zipcode'],
            offroadtype: qryData['offroadtype'],
            liftsize: qryData['liftsize'],
            listType: listType,
            accesstoken: accesstoken
        }

    }

    if (qryData['flag'] == 'searchByWheelSize') {
        data = {
            flag: searchByWheelSize,
            zipcode: zipcode,
            listType: listType,
            accesstoken: accesstoken,
        }
    }

    console.log("Passed Data");
    if (qryData['pagename'] == 'list') {
        $.ajax({
            url: url,
            data: data,
            type: "POST",
            success: function(result) {
                console.log(result);

                if (result['status'] == true) {

                    products = result['data']['products'];

                    listProducts(result['data']);
                    getVisualiserModal(result['data']['vehicleimage'], result['data']['vehiclecolors'])
                    // console.log('paginateurl', paginateurl)
                    if (paginateurl == '') {

                        getWheelByVehicle();
                    } else {
                        APIWheelMapping('0');
                    }

                } else {

                    $loading.fadeOut("slow");
                    showAlert(result['message']);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                showAlert('Something Went Wrong!')
            }
        });
    }
};


function listProducts(products) {
    console.log('listProducts', products);
    $('#Visualiser-Products-Section').html(products['htmllist']);

    $(".se-pre-con").fadeOut("slow");
}


function getVisualiserModal(vehicleData = '', vehicleColors = '') {

    var modalStr = `
        <!-- Visualiser Model Start -->
<div class="modal fade" id="VisualiserModal" role="dialog">
    <div class="modal-dialog new_visualiser">
        <div class="modal-content visualiser_content">
            <div class="modal-header visualiser_header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="VisualiserLabel">Modal Header</h4>
            </div>
            <div class="modal-body visualiser_body">
                <div class="row main-visualiser-body">
                    <div class="col-sm-12 model-visualiser" id="modal_visualiser">
                        <img id="vehicle-image" class="vehicle_image visualiser_image_responsive" src="` + baseurl + vehicleData['image'] + `">
                    </div>
                    <div class="vehicle-wheel">
                        <div class="front_wheel">
                            <img class="front_wheel" src="" id="visualiser-wheel-front">
                        </div>
                        <div class="back_wheel">
                            <img class="back_wheel" src="" id="visualiser-wheel-back">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="text-align: left;">

                <div class="col-sm-4">
                    
                                            <h1 class="visualiser-model-car">Vechicle Color</h1>
                                            <ul class="visualiser-list-color"> `;


    $.each(vehicleData['car_color'], function(ind, color) {
        modalStr += `

            <li class="visualiser-color-radius visualiser-car-color {{(` + color.code + ` ==` + vehicleData['color_code'] + ` )?'visualiser-color-selected':''}}" style="background:#` + color.rgb1 + `;" title="` + color.name + `" data-image="` + vehicleColors[color.code] + `"></li>
        `;

    });




    modalStr += `            </ul>
                                </div>
                                <div class="col-sm-4">
                                    <h1 class="model-car">Wheel Diameter</h1>
                                    <button class="model-button visualiser-diameter-up" data-id="0">Zoom In</button>
                                    <button class="model-button visualiser-diameter-down" data-id="0">Zoom Out</button>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>

                        <!-- Visualiser Model End -->
          `;

    $('#Visualiser-Section').html(modalStr);
}



// change the cars by selected color
$('body').on('click', '.visualiser-car-color', function(e) {
    e.preventDefault();
    var imagename = $(this).attr('data-image');
    $('.visualiser-color-selected').removeClass('visualiser-color-selected');
    $(this).addClass('visualiser-color-selected');
    $('#vehicle-image').attr('src', baseurl + "/" + imagename);

});



function getWheelByVehicle(key = '0', isShow) {
    console.log('getWheelByVehicle')
    console.log('boxes', boxes)

    partno = $('#frontback-image-' + key).data('partno');

    if (partno == undefined) {
        partno = '0000';
    }
    var data = {
        make: qryData['make'],
        model: qryData['model'],
        year: qryData['year'],
        submodel: qryData['submodel'],
        vehicleid: qryData['vehicleid'],
        wheelpartno: partno,
        accesstoken: accesstoken,
    };

    console.log('Object Detection', data);
    $loading.show();
    $.ajax({
        url: baseurl + "/api/WheelByVehicle",
        data: JSON.stringify(data),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        type: "POST",
        success: function(result) {

            console.log('Response Binded', result)
            if (result['status'] == true) {

                allData = result['data'];


                console.log(typeof allData['position']);
                boxes = JSON.parse(allData['position']);
                console.log(typeof boxes, boxes);

                // $("#VisualiserModal").modal("show");

                APIWheelMapping(key, isShow)
            }
 

            if (result['status'] == false && result['message']) {
                showAlert(result['message']);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            showAlert('Something Went Wrong!')
            $loading.fadeOut("slow");
        }
    });

}

function APIWheelMapping(key, isShow = null) {
    if (boxes == 'undefined') {
        getWheelByVehicle(key, isShow);
    } else {
        if (isShow == null) {

            $('#vehicle-image').attr('src', allData['carimage']);
            $('#visualiser-wheel-front').attr('src', allData['frontimage']);
            $('#visualiser-wheel-back').attr('src', allData['frontimage']);
            $('#VisualiserLabel').html(allData['vehicle']);

            if (boxes[0][0] < 400) {

                f = boxes[0];

                b = boxes[1];

            } else {

                f = boxes[1];

                b = boxes[0];
            }

            console.log(f, b)
            var front = $('#visualiser-wheel-front');
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


            var back = $('#visualiser-wheel-back');
            back.css('left', b[0] - 11.5 + 'px');
            back.css('top', b[1] + 8.5 - 30 + 'px');

                    $loading.fadeOut("slow");
        } else {
            $("#VisualiserModal").modal("show");
            $('#vehicle-image').attr('src', allData['carimage']);
            $('#visualiser-wheel-front').attr('src', $('#frontback-image-' + key).val());
            $('#visualiser-wheel-back').attr('src', $('#frontback-image-' + key).val());

                    $loading.fadeOut("slow");
        }
    }
 

}

$('body').on('click', '.pagination a', function(e) {
    e.preventDefault();
    $loading.show();
    var url = $(this).attr('href');
    getWheelsList(url);
    // window.history.pushState("", "", url);
});
$('body').on('click', '.wheeldiameter,.wheelwidth', function(e) {
    e.preventDefault();
    $loading.show();
    var url = baseurl + "/api/getWheels?" + $(this).attr('class') + "=" + $(this).val();
    getWheelsList(url);
    // window.history.pushState("", "", url);
});


// Zoom In Zoom Out

// Wheel Diameter Zoom In and Zoom Out  

var diameterStepCount = 0;
var diameterStepLimit = 8;
var currentKey = 0;


$(document).on('click', '.visualiser-diameter-up', function() {
    var key = $(this).attr('data-id');
    key = '';
    if (key != currentKey) {
        diameterStepCount = 0;
        diameterStepLimit = 8;
        currentKey = key;
    }
    if (diameterStepCount < diameterStepLimit) {
        var front = document.getElementById("visualiser-wheel-front");
        var frontWidth = front.clientWidth;
        front.style.width = (frontWidth + 20) + "px";

        frontTop = parseInt($(front).css("top"), 10);
        frontLeft = parseInt($(front).css("left"), 10);
        frontTop = frontTop - 10;
        frontLeft = frontLeft - 10;
        $(front).css({
            top: frontTop,
            left: frontLeft
        });

        var back = document.getElementById("visualiser-wheel-back");
        var backWidth = back.clientWidth;
        back.style.width = (backWidth + 20) + "px";

        backTop = parseInt($(back).css("top"), 10);
        backLeft = parseInt($(back).css("left"), 10);
        backTop = backTop - 10;
        backLeft = backLeft - 10;
        $(back).css({
            top: backTop,
            left: backLeft
        });

        diameterStepCount = diameterStepCount + 1;
    }
    console.log(diameterStepCount, front.clientWidth);

});
$(document).on('click', '.visualiser-diameter-up', function() {
    var key = $(this).attr('data-id');
    if (key != currentKey) {
        diameterStepCount = 0;
        diameterStepLimit = 8;
        currentKey = key;
    }
    if (diameterStepCount > 0) {

        var front = document.getElementById("visualiser-wheel-front");
        var frontWidth = front.clientWidth;
        front.style.width = (frontWidth - 20) + "px";


        frontTop = parseInt($(front).css("top"), 10);
        frontLeft = parseInt($(front).css("left"), 10);
        frontTop = frontTop + 10;
        frontLeft = frontLeft + 10;
        $(front).css({
            top: frontTop,
            left: frontLeft
        });

        var back = document.getElementById("visualiser-wheel-back");
        var currWidth = back.clientWidth;
        back.style.width = (currWidth - 20) + "px";


        backTop = parseInt($(back).css("top"), 10);
        backLeft = parseInt($(back).css("left"), 10);
        backTop = backTop + 10;
        backLeft = backLeft + 10;
        $(back).css({
            top: backTop,
            left: backLeft
        });

        diameterStepCount = diameterStepCount - 1;
    }
    // console.log(diameterStepCount,front.clientWidth);
});
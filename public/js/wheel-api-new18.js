var baseurl = "http://web9.vtdns.net";
// var baseurl = "http://localhost:8001";

var inventoryurl = "http://inventory.discountedwheelwarehouse.net";

var boxes = null;
var allData;
var widthAdjusted = true;

var actionFrom = "load";

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
var current_page = 1;
var $loading = $('.waiting-loader');


var cmake = $('.cmake').val() ?? '';
var cyear = $('.cyear').val() ?? '';
var cmodel = $('.cmodel').val() ?? '';
var csubmodel = $('.csubmodel').val() ?? '';
var cchangeBy = '';


$(document).ready(function() {
    if (qryData['pagename'] == 'list' || qryData['pagename'] == 'list#') {
        getWheelsList();
    }
    changeVehicleModal();
    vehicleFilters();
    changeVehicleFilters();
    $loading.fadeOut("slow");
});

// Year based filters for Makes 
$(document).on('change', '.year,.make,.model', function() {
    changeBy = $(this).attr('name');
    vehicleFilters(changeBy);
});

// Year based filters for Makes 
$(document).on('change', '.cyear,.cmake,.cmodel', function() {
    cchangeBy = $(this).attr('name');
    console.log('cchangeBy', cchangeBy)
    changeVehicleFilters(cchangeBy, $(this));
});



$('body').on('click', '.SearchByVehicleGoChange', function(e) {
    e.preventDefault();

    actionFrom = 'change';

    $('#changeVehicleModal').modal('hide');

    var data = {
        year: cyear,
        make: cmake,
        model: cmodel,
        submodel: csubmodel,
        accesstoken: accesstoken
    }

    console.log('Search Data', data);
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

$('.SearchByVehicleGo').click(function() {

    actionFrom = 'load';
    var data = {
        year: year,
        make: make,
        model: model,
        submodel: submodel,
        accesstoken: accesstoken
    }


    console.log('Search Data', data);
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
    console.log(data);
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


function changeVehicleFilters(cchangeBy = '', elem) {
    if (elem != undefined) {
        console.log($(elem).attr('name'))
        console.log($('.c' + $(elem).attr('name')).val($(elem).val()))
    }
    cmake = $('.cmake').val();
    cyear = $('.cyear').val();
    cmodel = $('.cmodel').val();
    csubmodel = $('.csubmodel').val();

    var data = {
        year: cyear,
        make: cmake,
        model: cmodel,
        changeBy: cchangeBy,
        accesstoken: accesstoken
    }
    console.log('ChangeData', data);
    $.ajax({
        url: baseurl + "/api/getVehicles",
        data: data,
        type: "POST",
        success: function(result) {
            if (result['status'] == false) {

                showAlert(result['message']);
            }
            $('.csubmodel').empty().append('<option disabled selected>Select Submodel</option>');

            if (cchangeBy == '' || cchangeBy == 'year' || cchangeBy == 'make') {
                $('.cmodel').empty().append('<option disabled selected>Select Model</option>');
            }
            if (cchangeBy == '' || cchangeBy == 'make') {
                $('.cyear').empty().append('<option disabled selected>Select Year</option>');
            }

            if (cchangeBy == '') {
                result.data['make'].map(function(value, key) {
                    isSelected = (value.make == make) ? 'selected' : '';
                    $('.cmake').append('<option value="' + value.make + '" ' + isSelected + '>' + value.make + '</option>');
                });

                result.data['year'].map(function(value, key) {
                    isSelected = (value.year == year) ? 'selected' : '';
                    $('.cyear').append('<option value="' + value.year + '" ' + isSelected + '>' + value.year + '</option>');
                });
                result.data['model'].map(function(value, key) {
                    isSelected = (value.model == model) ? 'selected' : '';
                    $('.cmodel').append('<option value="' + value.model + '" ' + isSelected + '>' + value.model + '</option>');
                });
                result.data['submodel'].map(function(value, key) {
                    $('.csubmodel').append('<option value="' + value.submodel + '-' + value.body + '">' + value.submodel + '-' + value.body + '</option>');
                });
            } else {
                result.data.map(function(value, key) {
                    if (cchangeBy == 'make') {
                        isSelected = (value.year == year) ? 'selected' : '';
                        $('.cyear').append('<option value="' + value.year + '"' + isSelected + '>' + value.year + '</option>');
                    }
                    if (cchangeBy == 'year') {
                        isSelected = (value.model == model) ? 'selected' : '';
                        $('.cmodel').append('<option value="' + value.model + '"' + isSelected + '>' + value.model + '</option>');
                    }
                    if (cchangeBy == 'model') {
                        $('.csubmodel').append('<option value="' + value.submodel + '-' + value.body + '">' + value.submodel + '-' + value.body + '</option>');
                    }
                });
            }



        },
        error: function(jqXHR, textStatus, errorThrown) {
            showAlert('Something Went Wrong!')
        }
    });
}


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


    if (actionFrom == 'load') {

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
    } else {

        window.location.href = "/products?" +
            "make=" + cmake +
            "&year=" + cyear +
            "&model=" + cmodel +
            "&submodel=" + csubmodel +
            "&vehicleid=" + vehicleid +
            "&offroadid=" + offroadid +
            "&offroadtype=" + offroadtype +
            "&liftsize=" + liftsize +
            "&flag=" + flag +
            "&zipcode=" + zipcode +
            "&pagename=list";
    }
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

    console.log('Load Wheels', data)
    if (qryData['pagename'] == 'list') {
        $.ajax({
            url: url,
            data: data,
            type: "POST",
            success: function(result) {


                if (result['status'] == true) {

                    products = result['data']['products'];
                    console.log(products)
                    current_page = products.current_page;
                    listProducts(result['data']);
                    getVisualiserModal(result['data']['vehicleimage'], result['data']['vehiclecolors'])

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

    $('#Visualiser-Products-Section').html(products['htmllist']);

    // $(".waiting-loader").fadeOut("slow");
}


function getVisualiserModal(vehicleData = '', vehicleColors = '') {
    console.log('getVisualiserModal')
    var vehicleimage = "#";
    var vehiclecolors = [];
    if (vehicleData != null) {
        vehicleimage = baseurl + "/" + vehicleData['image'];
    }

    if (vehicleData != null) {
        vehiclecolors = vehicleData['car_color'];
    }

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
                        <img id="vehicle-image" class="vehicle_image visualiser_image_responsive" src="` + vehicleimage + `">
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


    $.each(vehiclecolors, function(ind, color) {
        if (vehicleColors[color.code] != undefined) {

            modalStr += `

                <li class="visualiser-color-radius visualiser-car-color " style="background:#` + color.rgb1 + `;" title="` + color.name + `" data-image="` + vehicleColors[color.code] + `"></li>
            `;
        }

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

function changeVehicleModal() {


    var modalStr = `
                            <div class="modal fade " id="changeVehicleModal" role="dialog">
                                <div class="modal-dialog wheel-view">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title text-left">Change Your Vehcile Selection</h4>
                                        </div>
                                        <div class="modal-body">
                                            <h2 class="modal-title"><b>Shop By Vehicle</b></h2>
         

                                            <div class="row">
                                                <div class="col-sm-12">  
                                                    <br>
                                                        <div class="vehicle-list">

                                                            <form action="{{url('/setFiltersByProductVehicle')}}"  id="WheelVehicleSearchChange">
                                                                <input type="hidden" name="flag" value="searchByVehicle">
                                                                <div class="dropdown">
                                                                    <select required="" class="form-control browser-default custom-select cmake" name="make">
                                                                        <option value="">Select Make</option>
                                                                        
                                                                    </select>
                                                                </div>

                                                                <div class="dropdown">
                                                                    <select required="" class="form-control browser-default custom-select cyear" name="year">
                                                                        <option value="">Select Year</option>
                                                                      
                                                                    </select>
                                                                </div>

                                                                <div class="dropdown">
                                                                    <select required="" class="form-control browser-default custom-select cmodel" name="model">
                                                                        <option value="">Select Model</option>
                                                                        
                                                                    </select>
                                                                </div>

                                                                <div class="dropdown">
                                                                    <select required="" class="form-control browser-default custom-select csubmodel" name="submodel">
                                                                        <option value="">Select Trim</option>
                                                                        
                                                                    </select>
                                                                </div>
                                                       <!--          <div class="dropdown">
                                                                    <input required="" type="text" class="form-control" name="zip" placeholder="Enter ZIP">
                                                                </div> -->
                                                                <!-- <a href="" disabled> -->
                                                                    <button type="button" class="btn vehicle-go SearchByVehicleGoChange ">GO</button>
                                                                <!-- </a> -->
                                                            </form>
                                                        </div> 
                                                </div>
                                            </div> 
           
                                        </div>
                                    </div>
                                </div>
                            </div>
    `;
    $('#ChangeVehicleSection').html(modalStr);
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



    partno = $('#frontback-image-' + key).data('partno');
    if (partno == undefined) {
        partno = '0000';
    }
    var data = {
        make: qryData['make'],
        model: qryData['model'],
        year: qryData['year'],
        submodel: qryData['submodel'],
        vehicleid: qryData['v'],
        wheelpartno: partno,
        accesstoken: accesstoken,
    };

    console.log(partno, data)

    $(".waiting-loader").show();
    $.ajax({
        url: baseurl + "/api/WheelByVehicle",
        data: JSON.stringify(data),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        type: "POST",
        success: function(result) {

            console.log(key, isShow)
            if (result['status'] == true) {

                allData = result['data'];




                if (typeof allData['position'] != 'object') {

                    boxes = JSON.parse(allData['position']);
                } else {
                    boxes = allData['position'];
                }



                // $("#VisualiserModal").modal("show");

                APIWheelMapping(key, isShow)
            }


            if (result['status'] == false && result['message']) {
                showAlert(result['message']);
            }

            $loading.fadeOut("slow");
        },
        error: function(jqXHR, textStatus, errorThrown) {
            showAlert('Something Went Wrong!')
            $loading.fadeOut("slow");
        }
    });
}

function APIWheelMapping(key, isShow = null) {
    console.log(boxes);
    if (boxes == 'undefined' || boxes == null) {
        getWheelByVehicle(key, isShow);
    } else {
        if (isShow == null) {
            console.log(allData)
            // if(!$('#VisualiserModal').hasClass('in')){
            //         getVisualiserModal();
            //         $("#VisualiserModal").modal("show");
            // }
            if (allData['carimage'] == null) {

                showAlert('Vehicle Image Not Found!!')
                $("#VisualiserModal").modal("hide");
            }
            $('#vehicle-image').attr('src', allData['carimage']);
            $('#visualiser-wheel-front').attr('src', allData['frontimage']);
            $('#visualiser-wheel-back').attr('src', allData['frontimage']);
            $('#VisualiserLabel').html(allData['vehicle']);

            if (boxes.length > 0) {

                if (boxes[0][0] < 400) {

                    f = boxes[0];

                    b = boxes[1];

                } else {

                    f = boxes[1];

                    b = boxes[0];
                }


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
            }

            $loading.fadeOut("slow");

        } else {

            // $("#VisualiserModal").modal("
            if (allData['carimage'] == null) {

                showAlert('Vehicle Image Not Found!!')
                $("#VisualiserModal").modal("hide");
            }

            $('#vehicle-image').attr('src', allData['carimage']);
            $('#visualiser-wheel-front').attr('src', $('#frontback-image-' + key).val());
            $('#visualiser-wheel-back').attr('src', $('#frontback-image-' + key).val());

            $loading.fadeOut("slow");
        }
    }
}
 

function ApplyOnCar(partno, vehicleid, vehicleDetails = {}) {

    $(".waiting-loader").show();
    var data = {
        wheelpartno: partno,
        vehicleid: vehicleid,
        accesstoken: accesstoken
    }
    console.log(vehicleDetails, Object.keys(vehicleDetails).length)
    if (Object.keys(vehicleDetails).length > 0) {
        var data = {
            wheelpartno: partno,
            make: vehicleDetails['make'],
            year: vehicleDetails['year'],
            model: vehicleDetails['model'],
            submodel: vehicleDetails['submodel'],
            accesstoken: accesstoken
        }
    }
    $.ajax({
        url: baseurl + '/api/WheelByVehicle',
        data: data,
        type: "POST",
        success: function(result) {

            if (result['status'] == true) {
                allData = result['data'];
                if (typeof allData['position'] != 'object') {
                    boxes = JSON.parse(allData['position']);
                } else {
                    boxes = allData['position'];
                }
                getVisualiserModal(result['data']['vehicleimage'], result['data']['vehiclecolors'])
                $('#VisualiserModal').modal('show');
                APIWheelMapping('0');
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
 

$('body').on('click', '.visualiser-pagination .pagination a', function(e) {
    e.preventDefault();
    $loading.show();
    var url = $(this).attr('href');
    getWheelsList(url);
    // window.history.pushState("", "", url);
});
$('body').on('change', '.visualiserdiameter,.visualiserwidth,.visualiserbrand,.visualiserfinish', function(e) {
    e.preventDefault();
    $loading.show();
    var values = $('.' + $(this).attr('class') + ':checked').map(function() {
        return $(this).val();
    }).get();
    console.log(values)
    var url = baseurl + "/api/getWheels?page=" + current_page + "&" + $(this).attr('class') + "=" + values;
    getWheelsList(url);
    // window.history.pushState("", "", url);
});


// Zoom In Zoom Out

// Wheel Diameter Zoom In and Zoom Out  

var diameterStepCount = 0;
var diameterStepLimit = 8;
var currentKey = 0;


$('body').on('click', '.visualiser-diameter-up', function(e) {
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
});

$('body').on('click', '.visualiser-diameter-down', function(e) {
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
});


function getProductAvailability(partno='',zipcode=''){ 
    var data={
        partno:partno,
        zipcode:zipcode
    }
     $.ajax({
            url: inventoryurl + "/api/getProductAvailability",
            data: data,
            type: "POST",
            success: function(result) {
                if (result['status'] == false) {
                    showAlert(result['message']);
                } else {

                    $loading.fadeOut('slow');
                     console.log(result)
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                showAlert('Something Went Wrong!')
            }
        });
}

function CheckNearByDropshippers(radius='',zipcode=''){ 
    var data={
        radius:radius,
        zipcode:zipcode
    }
     $.ajax({
            url: inventoryurl + "/api/CheckNearByDropshippers",
            data: data,
            type: "POST",
            success: function(result) {
                if (result['status'] == false) {
                    showAlert(result['message']);
                } else {

                    $loading.fadeOut('slow');
                     console.log(result)
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                showAlert('Something Went Wrong!')
            }
        });
}
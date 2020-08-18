var baseurl = "http://web9.vtdns.net"; 
// var baseurl = "http://localhost:8001";
var boxes;
var allData;
var widthAdjusted = true;

var make = $('.make').val();
var year = $('.year').val();
var model = $('.model').val();
var submodel = $('.submodel').val();
var changeBy = '';

var vehicle='';
var vehicleid='';
var offroadid='';
var flag='';
var zipcode='';
var offroadtype = '';
var liftsize = '';
var qryData = getUrlVars();

var $loading = $('.se-pre-con');
$(document).ready(function() {
    if("{{$request->pagename}}"){
        getWheelsList();
        getVisualiserModal();
    } 
    vehicleFilters(changeBy); 
});



// Year based filters for Makes 
$(document).on('change', '.year,.make,.model', function() {
    changeBy = $(this).attr('name');
    vehicleFilters(changeBy);
});

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

                alert(result['message']);
            }
            $('.submodel').empty().append('<option disabled selected>Select Submodel</option>');

            if (changeBy == '' || changeBy == 'year' || changeBy == 'make') {
                $('.model').empty().append('<option disabled selected>Select Model</option>');
            }
            if (changeBy == '' || changeBy == 'make') {
                $('.year').empty().append('<option disabled selected>Select Year</option>');
            }

            if (changeBy == '') {
                console.log(result)
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
            alert('Something Went Wrong!')
            $loading.fadeOut("slow");
        }
    });
}


$('.SearchByVehicleGo').click(function(){

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

    $.ajax({
        url: baseurl + "/api/findVehicle",
        data: data,
        type: "POST", 
        success: function(result) {
            if (result['status'] == false) {
                alert(result['message']);
            }else{

                vehicle = result['data']['vehicle'];
                vehicleid = result['data']['vehicle']['vehicle_id'];

                if(result['status']==true &&  result['data']['offroad'] != ''){
                    offroadid = result['data']['offroad'];
                    loadOffroadView();
                }else{

                    flag = 'searchByVehicle';
                    loadZipcodeView(); 
                }
            } 
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert('Something Went Wrong!')
            $loading.fadeOut("slow");
        }
    });
})

function loadOffroadView(){
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
                                            <img src="`+baseurl+`/image/lifttype.jpg" >
                                            <br>
                                            <h3 style="color: white !important">Leveling Kit</h3>
                                        </div> 
                                    </div>

                                                       <br>                                 
                                    <div style="text-align: center;">    
                                        <div class="btn btn-info select-offroad" data-offroad="lift">
                                            <img src="`+baseurl+`/image/lifttype.jpg" >
                                            <br>
                                            <h3 style="color: white !important">Lift Kit</h3>
                                        </div>
                                    </div>
                                    <br>
                                    <div style="text-align: center;"> 
                                        <div class="btn btn-info select-offroad" data-offroad="stock">

                                            <img src="`+baseurl+`/image/lifttype.jpg" >
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


$(document).on('click','.select-offroad',function(){

    $("#offroadTypeModal").modal('hide');
    
    offroadtype = $(this).data('offroad'); 
    
    if(offroadtype != 'lift'){
        if(offroadtype == 'levelkit'){
            liftsize ='Levelkit'
        }

        flag = 'searchByVehicle';
        loadZipcodeView(); 
    }else{
        var data = {
            offroadid:offroadid,
            accesstoken:accesstoken
        };

        console.log(data);
        $.ajax({
            url: baseurl + "/api/getLiftSizes",
            data: data,
            type: "POST", 
            success: function(result) {
                if (result['status'] == false) {
                    alert(result['message']);
                }else{
                    if(result['status']==true ){
                        console.log(result)
                        loadOffroadSizeView(result['data']);
                    }else{
                        flag = 'searchByVehicle';
                        loadZipcodeView(); 
                    }
                } 
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Something Went Wrong!')
                $loading.fadeOut("slow");
            }
        });
    }

});

 

function loadOffroadSizeView(data){

    var loadSizeStr=`
                <div class="modal" id="liftsizeModal" role="dialog">
                    <div class="modal-dialog tire-view">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Please select your vehicle's lift:</h4>
                            </div>
                            <div class="modal-body"> 
    `;

    $.each(data, function(sizekey, size ){
        loadSizeStr+=`
                                    <div style="text-align: center;"> 
                                        <button class="btn btn-info select-liftsize" data-liftsize="`+sizekey+`">

                                            <img src="`+baseurl+`/image/lifttype.jpg" >
                                            <br>
                                            <h3 style="color: white !important">` + size + `</h3>
                                        </button>
                                    </div>
                                    <br>
        `;
    });
    
    loadSizeStr+=`

                            </div>
                        </div>
                    </div>
                </div>
    `;

    $('#Offroad-Size-View-Section').html(loadSizeStr);

    $('#liftsizeModal').modal('show');
}

$(document).on('click','.select-liftsize',function(){

    $("#liftsizeModal").modal('hide');
    liftsize = $(this).data('liftsize');  
    flag = 'searchByVehicle';
    loadZipcodeView(); 
});


function loadZipcodeView(){ 
    if(zipcode ==''){
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
    }else{
        redirectToList();
    }

}



$(document).on('click','#update-zipcode-button',function(){

    $("#Zipcode-Section-Modal").modal('hide');
        zipcode = $("#zipcode-input").val();
    redirectToList();
});

function redirectToList(){
    window.location.href = "/products?"+
    "make="+make+
    "&year="+year+
    "&model="+model+
    "&submodel="+submodel+
    "&vehicleid="+vehicleid+
    "&offroadid="+offroadid+
    "&offroadtype="+offroadtype+
    "&liftsize="+liftsize+
    "&flag="+flag+
    "&zipcode="+zipcode+
    "&pagename=list";
}

function getUrlVars()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(decodeURIComponent(hash[0]));
        vars[hash[0]] = decodeURIComponent(hash[1]);
    }
    return vars;
}

// function getJsonFromUrl(url) {
//   if(!url) url = location.search;
//   var query = url.substr(1);
//   var result = {};
//   query.split("&").forEach(function(part) {
//     var item = part.split("=");
//     result[item[0]] = decodeURIComponent(item[1]);
//   });
//   return result;
// }


//  Driver / Body change your car 
function getWheelsList() {  
    var data =""; 
    var listType = 'html';
    if(qryData['flag'] == 'searchByVehicle'){
        data={
            flag:'searchByVehicle',
            vehicleid:qryData['vehicleid'],
            zipcode:qryData['zipcode'],
            offroadtype:qryData['offroadtype'],
            liftsize: qryData['liftsize'],
            listType: listType,
            accesstoken: accesstoken
        }

    }

    if(qryData['flag'] == 'searchByWheelSize'){
        data={
            flag:searchByWheelSize, 
            zipcode:zipcode, 
            listType: listType,
            accesstoken: accesstoken,
        }
    }
  
    console.log("Passed Data",data);
    if(qryData['pagename'] == 'list'){
        $.ajax({
            url: baseurl + "/api/getWheels",
            data: data,
            type: "POST",
            success: function(result) {
                console.log(result);

                if (result['status'] == true) {

                    products = result['data']['products'];
                    if(listType == 'html'){
                        listProducts(result['data']);
                    }

                    // $loading.fadeOut("slow");

                } else {

                    // $loading.fadeOut("slow");
                    alert(result['message']);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Something Went Wrong!')
                // $loading.fadeOut("slow");
            }
        });
    }
};


function listProducts(products) {
    console.log('listProducts', products);
    $('#Visualiser-Products-Section').html(products['htmllist']);

    $(".se-pre-con").fadeOut("slow");
}


function getVisualiserModal() {

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
                        <img id="vehicle-image" class="vehicle_image visualiser_image_responsive" src="new_car.png">
                    </div>
                    <div class="vehicle-wheel">
                        <div class="front_wheel">
                            <img class="front_wheel" src="front_wheel.png" id="visualiser-wheel-front">
                        </div>
                        <div class="back_wheel">
                            <img class="back_wheel" src="front_wheel.png" id="visualiser-wheel-back">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                
                            <div class="col-sm-4">
                                <h1 class="model-car">Wheel Diameter</h1>
                                <button class="model-button diameter-up" data-id="0">Zoom In</button>
                                <button class="model-button diameter-down" data-id="0">Zoom Out</button>
                            </div> 
            </div>
        </div>
    </div>
</div>

        <!-- Visualiser Model End -->
          `;

    $('#Visualiser-Section').html(modalStr);
}

function getWheelPosition(partno = '') {

 
    var data = { 
        make:qryData['make'],
        model:qryData['model'],
        year:qryData['year'],
        submodel:qryData['submodel'],
        vehicleid:qryData['vehicleid'],
        wheelpartno: partno,
        accesstoken: accesstoken,
    };

    console.log('Object Detection',data);
 
    $.ajax({
        url: baseurl + "/api/WheelByVehicle",
        data: data,
        type: "POST", 
        success: function(result) {


            if (result['status'] == true) {

                allData = result['data'];

                $loading.fadeOut("slow");


                $("#VisualiserModal").modal("show");

                WheelMapping('0')
            } else {

                $loading.fadeOut("slow");
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert('Something Went Wrong!')
            $loading.fadeOut("slow");
        }
    });
}

function WheelMapping(key = '') {
    // console.log(allData);
    boxes = allData['position'];

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

    var front = $('#wheel-front');
        front.css('left',f[0]-18+'px');
        front.css('top',f[1]-1+'px');

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
    back.css('top', b[1] + 8.5  + 'px');
}


 
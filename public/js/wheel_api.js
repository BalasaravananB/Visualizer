var boxes;
var allData;
var widthAdjusted = true;
var $loading = $('.se-pre-con');
$(document).ready(function() {
    getWheelPosition();
    var delay = 1000;
    setTimeout(function() {
            $loading.fadeOut("slow");
            console.log('Waiting Time Closed')
        },
        delay
    );
});

function getWheelPosition(key) {
    var data = {
        year: "{{$request->year?:'2015'}}", 
        make: "{{$request->make?:'Volvo'}}",
        model: "{{$request->model?:'S60'}}",
        submodel: "{{$request->submodel?:'T6 Polestar-4 Dr Sedan'}}",
        wheelpartno: "{{$request->wheelpartno?:'2610Z20KOSAGB'}}", 
        accesstoken: "{{$request->accesstoken?:'Ykc5allXeG9iM04w'}}",
    }


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
            } else {

                $loading.fadeOut("slow");
                alert(result['message']);
                DisableModal();
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert('Something Went Wrong!')
            DisableModal();
            $loading.fadeOut("slow");
        }
    });


}

function DisableModal(key = '') {
    $('#WheelMapButton').attr('disabled', true);
}

function WheelMapping(key = '') {

    boxes = allData['position'];
    $('#car_image').attr('src', allData['carimage']);
    $('#image-diameter-front').attr('src', allData['frontimage']);
    $('#image-diameter-back').attr('src', allData['frontimage']);
    $('#myModalLabel').html(allData['vehicle']);

    if (boxes[0][0] < 400) {

        f = boxes[0];

        b = boxes[1];

    } else {

        f = boxes[1];

        b = boxes[0];
    }


    var front = $('#image-diameter-front-0');
    front.css('left', f[0] - 18 + 'px');
    front.css('top', f[1] - 1 + 'px');
    if (widthAdjusted) {
        console.log(widthAdjusted)
        var extraWidth = 0;
        if (front.width() - f[2] > 4) {
            extraWidth = (front.width() - f[2]) / 2;
        }
        console.log(extraWidth)
        front.width(front.width() + extraWidth + 'px');
        widthAdjusted = false;
    }


    var back = $('#image-diameter-back-0');
    back.css('left', b[0] - 11.5 + 'px');
    back.css('top', b[1] + 8.5 + 'px');

}
@extends('layouts.app') @section('shop_by_vehicle_css')
<link rel="stylesheet" href="{{ asset('css/wheels.css') }}"> 

@endsection 
@section('metakeywords')
<?=@MetaViewer('Contact');?>
@endsection 
@section('content')

<style type="text/css">
    .contact-container {
        border: 1px solid #ccc;
        /*box-shadow: 1px 1px 2px 1px rgba(0, 0, 0, .05);*/
        /*background-color: #fff;*/
        border-radius: 2px !important;
    }
</style>
<style>
    .ban-ser{border: 1px solid #ddd9d9 !important;}
    .wheel-list{column-width: 15em;padding: 10px 15px !important;}
    .wheel-list li a{color: #474646;display: block;font-size: 12px !important;text-align: center;font-family: Montserrat !important;}
    .wheel-list ul{margin: 0;padding: 0;list-style-type: none;}
    .wheel-list li{padding: 3px;margin: 3px;margin-top: 3px;margin-top: 3px;border: 1px solid #ccc;box-shadow: 1px 1px 2px 1px rgba(0, 0, 0, .05);background-color: #fff;border-radius: 2px !important;}
    .wheel-list ul li:first-child{margin-top: 0;}
    #heading h1{font-family: Montserrat;color: #121214;font-size: 18px !important;text-align: center;font-weight: 700 !important;margin:20px 0px;}
    .col-sm-3.payments3-card img{width: 100% !important;}
    .col-sm-3.payments-card{text-align: center !important;}
    .prod-headinghome p{text-align: justify;margin: 10px 0px;color: #121214;line-height: 30px;font-family: poppins !important;font-size: 12px !important;}
    .col-sm-4.wheel-img{text-align: center !important;}
    /* pro Start */
    .hometabled{display: table;text-align: center; width: 100%;background: #fff;box-shadow: 0 2px 3px 0 rgba(180, 180, 180, .6) !important;border: 1px solid #d8d7d7;margin-bottom: 15px;padding: .5%;border-radius: 2px;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
    .pTopBar{display: table;width: 100%;padding: .5% 1%;margin-bottom: 1%;border-radius: 2px;-moz-border-radius: 2px;-webkit-border-radius: 2px;background: #0e1661 !important;}
    .pTopCell{display: table-cell;width: 50%;color: #fff;text-shadow: 0 1px 1px rgba(0, 0, 0, .75);font-size: 12px;font-family: Montserrat !important;}
    .pTopCell.Phone a{color: #fff;text-decoration: none;}
    .asItems{border: 0px;}
    .asItems{padding: 0;padding-top: 0px;width: 100%;padding-top: 5px;text-align: center;margin: 0 auto 10px;background: #fff;border: 1px solid #cecece;border-radius: 2px;-moz-border-radius: 2px;-webkit-border-radius: 2px;box-shadow: 0 0 3px rgba(0, 0, 0, .125);font-family: open sans, Arial, sans-serif;}
    .asItems{text-align: center;font-family: open sans, Arial, sans-serif;}
    .gridList{margin: 0 auto;padding: 0;width: auto;display: table;}
    .gridItem{display: inline-block;width: 210px;text-align: center;padding: 5px;}
    .homecelld b{color: #222 !important;font-size: 12px !important;font-family: Montserrat !important;}
    .hometabled{margin: 25px 0px !important;}
    .gridList.wheels.suggested .gridItem homeapge1{height: 180px;}
    .asItems{border: 0px;}
    .prod-headinghome h2{color: #0e1661 !important;text-align: center;font-family: Montserrat !important;font-size: 18px !important;font-weight: 700 !important;}
    .prod-heading p{color: rgb(18, 18, 20) !important;font-family: Montserrat !important;font-size: 12px !important;}
    .prod-heading-center{text-align: center;}
    .prod-headinghome h3{width: 100%;font-size: medium;font-family: open sans, Arial, sans-serif;color: #000 !important;font-weight: 600 !important;text-align: center;}
    .prod-heading-center p{color: #0e1661 !important;font-size: 15px;line-height: 30px !important;font-family: Montserrat !important;font-weight: 700 !important;}
    .prod-heading-bold{font-family: open sans, Arial, sans-serif;color: #121214;}
    b{font-weight: bold;}
    .h1-nl{font-size: 20px;}
    hr{border: 0.5px solid #ccc !important;}
    #produst,#special-product,footer,#bott,.container.brand-logo{display: none !important;}
    .container-fluid.home-page{padding: 0px 0px !important;background: #f1f1f1 !important;}
    .prod-heading-bold a{color: #337ab7 !important;}
     label{float: left;color: #121214;line-height: 30px !important;font-family: poppins !important;font-size: 12px !important;}
     .form-control{border-radius: 2px;}
    #contact-us{padding: 20px 0px !important;}
    .contact-head h1{font-family: Montserrat !important;font-size: 18px !important;font-weight: 700 !important;margin: 20px 0px;}
    .contact-head h2{font-family: Montserrat !important;font-size: 15px !important;font-weight: 700 !important;margin: 20px 0px;color: #0e1661 !important;}
    .contact-head h3{font-family: Montserrat !important;font-size: 14px !important;font-weight: 700 !important;margin: 20px 0px;color: #0e1661 !important;}
    .contact-head h4{font-family: Montserrat !important;font-size: 15px !important;line-height:30px;font-weight: 700 !important;margin: 20px 0px;color: #0e1661 !important;}
    .contact-head p{color: #121214;line-height: 30px !important;font-family: poppins !important;font-size: 12px !important;margin: 0px !important;}
    .cont-details{text-align: center;}
    .main-contact-2{margin:50px 0px;}
    .btn.btn-success.checkout-btn.btn-send{background: #0e1661 !important;color: #fff !important;border: none !important;}
    .cont-form{border: 1px solid #ccc;box-shadow: 0 0 4px 0 rgba(0,0,0,0.2);border-radius: 5px;}
    .cont-form .form-group{margin-bottom: 0px !important;}
    .col-sm-4.cont-img{padding: 50px 0px !important;text-align: center;}
    .review-img{
        width: 567px;
    height: 372px;
    }

</style>

<br>

@include('include.sizelinks')

<!-- Contact Us Section Start -->
<section id="contact-us" class="contact-page">
  <div class="container">

    <div class="about-page title-header">
      <div id="heading" class="title">
        <h1>Discounted Wheel Warehouse - Order Information</h1>
      </div>
    </div>

    <div class="row main-contact-2">   
      <div class="col-sm-4">
      </div>
      <div class="col-sm-4 cont-form">
        <div id="heading" class="title">
          <h1>Your Order Status</h1>
        </div>
        <form id="review-form" action="{{url('/orderstatus')}}" method="post">
            {{@csrf_field()}}
            <div class="messages"></div>
            <div class="controls col-md-12">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="form_email">Email Address *</label>
                            <input id="form_email" type="email" name="email" class="form-control" placeholder="Please enter your email *" required="required" data-error="Valid email is required."  value="{{old('email')}}">
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row text-center">
                <input type="submit" class="btn btn-success checkout-btn btn-send" value="Check">
                </div>
            </div>
        </form>
      </div>

      <div class="col-sm-4">
      </div>
    </div>

  </div>
</section>
<!-- Contact Us Section End -->



@endsection
@section('custom_scripts')
<script src="{{ asset('js/ajax/jquery.min.js') }}"></script>
<script src="{{ asset('js/contact-form-validator.js') }}"></script>
<script type="text/javascript">
    $(function() {
        $('#review-form').validator();
    });
</script>
@endsection

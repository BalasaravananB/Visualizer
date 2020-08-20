    <div class="container">
        <div class="row main-pro">
            <div class="col-sm-3 main-pro-inner-category">
                <div class="header-bottom col-sm-12">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="price-heading">SIZE</div>
                                <!--  -->
                                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="headingOne">
                                            <h4 class="panel-title">
                                                <a role="button" data-toggle="collapse" data-parent="#accordion123456" href="#collapseOne" class="{{(@Request::get('diameter'))?'':'collapsed123456'}}" aria-expanded="{{(@Request::get('diameter'))?'true':'false'}}" aria-controls="collapseOne">
                                                    Diameter
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseOne" class="panel-collapse1 collapse in in123456 {{(@Request::get('diameter'))?' in':''}} " role="tabpanel" aria-labelledby="headingOne">
                                            <div class="panel-body">
                                                <ul style="list-style-type: none;">
                                                    @forelse($wheeldiameter as $diameter)
                                                    <li><input type="checkbox" name="wheeldiameter[]" class="wheeldiameter" value="{{$diameter->wheeldiameter}}" > {{$diameter->wheeldiameter.'('.$diameter->total.')'}}
                                                    </li>
                                                    @empty
                                                    <li>No Diameter Available</li>
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="headingTwo">
                                            <h4 class="panel-title">
                                                <a role="button" data-toggle="collapse" data-parent="#accordion123456" href="#collapseTwo" class="{{(@Request::get('width'))?'':'collapsed123456'}}" aria-expanded="{{(@Request::get('width'))?'true':'false'}}" aria-controls="collapseTwo">
                                                    Width
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseTwo" class="panel-collapse2 collapse in in123456  {{(@Request::get('width'))?' in':''}}  " role="tabpanel" aria-labelledby="headingTwo">
                                            <div class="panel-body">
                                                <ul style="list-style-type: none;">
                                                    @forelse($wheelwidth as $width)
                                                    <li><input type="checkbox" name="wheelwidth[]" class="wheelwidth" value="{{$width->wheelwidth}}" > {{$width->wheelwidth.'('.$width->total.')'}} </li>
                                                    @empty
                                                    <li>No width Available</li>
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="headingThree">
                                            <h4 class="panel-title">
                                                <a role="button" data-toggle="collapse" data-parent="#accordion123456" href="#collapseThree" class="{{(@Request::get('brand'))?'':'collapsed123456'}}" aria-expanded="{{(@Request::get('brand'))?'true':'false'}}" aria-controls="collapseThree">
                                                    Brand
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseThree" class="panel-collapse3 collapse in in123456" role="tabpanel" aria-labelledby="headingThree">
                                            <div class="panel-body">
                                                <ul style="list-style-type: none;">
                                                    @forelse($brands as $brand)
                                                    <li><input type="checkbox" name="brand[]" class="brand" value="{{$brand->prodbrand}}">
                                                        @if(@$countsByBrand[$brand->prodbrand])
                                                        {{$brand->prodbrand}} ( {{$countsByBrand[$brand->prodbrand]}} )
                                                        @endif
                                                    </li>
                                                    @empty
                                                    <li>No Brands Available</li>
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="headingFour">
                                            <h4 class="panel-title">
                                                <a role="button" data-toggle="collapse" data-parent="#accordion123456" href="#collapseFour" class="{{(@Request::get('width'))?'':'collapsed123456'}}" aria-expanded="{{(@Request::get('width'))?'true':'false'}}" aria-controls="collapseFour">
                                                    Finish
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseFour" class="panel-collapse4 collapse in in123456  {{(@Request::get('finish'))?' in':''}}  " role="tabpanel" aria-labelledby="headingFour">
                                            <div class="panel-body">
                                                <ul style="list-style-type: none;">
                                                    @forelse($wheelfinish as $finish)
                                                    <li><input type="checkbox" name="finish[]" class="finish" value="{{$finish->prodfinish}}" 
                                                        > {{$finish->prodfinish.'('.$finish->total.')'}} </li>
                                                    @empty
                                                    <li>No Finish Available</li>
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="col-sm-9 col-sm-9 main-pro-inner">
                <div id="load" style="position: relative;">

                <div class="row">
                  @if(@$vehicle || @$flag=='searchByWheelSize')
                  <div class="wheel-list-change-tab ">
                      <div class="row">
                          <div class="col-md-8 left-head">
                            <p> 
                                @if(@$vehicle)
                                Your Selected Vehicle: 
                                    <b>{{@$vehicle->year}} {{@$vehicle->make}} {{@$vehicle->model}} {{@$vehicle->submodel}} </b>
                                    @if(@$liftsize) &  Liftsize :  <b> {{@$liftsize}} </b> @endif
                                    @if(@$vehicle->dually=='1' && (@$offroadtype == 'stock' || @$offroadtype == null)) &  <b> Dually Wheels </b> @endif
                                    
                                <br>
                                @endif
                                @if(@$flag == 'searchByWheelSize' && @$request->wheeldiameter)

                                    Your Selected  
                                    @if(@$request->wheeldiameter)

                                    Diameter:
                                        <b>{{@$request->wheeldiameter}}</b> ,
                                    @endif

                                    @if(@$request->wheelwidth)
                                    Width:
                                        <b>{{@$request->wheelwidth}}</b> ,
                                    @endif

                                    @if(@$request->boltpattern)
                                    Bolt Pattern:
                                        <b>{{showBoltPattern(@$request->boltpattern)}}</b> ,
                                    @endif

                                    @if(@$request->minoffset)
                                    Offset:
                                        <b>{{@$request->minoffset}}</b> 
                                        @if(@$request->maxoffset)<b> to {{@$request->maxoffset}}</b> @endif
                                    @endif
                                @endif
                            </p> 
                          </div>
                          <!-- <div class="col-md-4 right-button"><button type="submit" class="btn vehicle-change"><a href="{{url('/products')}}">Change</a></button></div> -->
                      </div>
                  </div>
                  @endif
                  @if(@$request->wheeltype)
                  <div class="wheel-list-change-tab "> 
                        <div class="row">
                          <div class="col-md-8 left-head">
                            <p> 
                                {{(base64_decode(@$request->wheeltype) == 'D')?'Dually Wheels':''}}
                                {{(base64_decode(@$request->wheeltype) == 'O')?'Offroad Wheels':''}}
                                {{(base64_decode(@$request->wheeltype) == 'C')?'Classic Wheels':''}} 
                                </p>
                            </div>
                        </div>

                  </div>
                  @endif
                  @if(@$zipcode)
                  <div class="wheel-list-change-tab ">
                      <div class="row">
                          <div class="col-md-8 left-head"> 
                                <p> 
                                    @if(@$zipcode)
                                    Your Zipcode: 
                                        <b>{{@$zipcode}}</b> 
                                    @endif 
                                    @if(!empty(\Session::get('user.state')))
                                    , State: 
                                        <b>{{\Session::get('user.state')}}</b> 
                                    @endif 
                                    @if(!empty(\Session::get('user.city')))
                                    , City: 
                                        <b>{{\Session::get('user.city')}}</b> 
                                    @endif 
                                </p>
                          </div>
                          <div class="col-md-4 right-button"><button type="submit" class="btn vehicle-change"><a href="{{url('/zipcodeClear')}}">Change</a></button></div>
                      </div>
                  </div>
                  @endif
                </div>
                <div class="row">
                    @forelse($products as $key => $product)
                        <?php $product = (object)$product; ?>
                        <div class="col-sm-4">
                            <div class="product-layouts">
                                <div class="product-thumb transition">
                                    <div class="image">
                                        <img class="wheelImage image_thumb" src="{{ViewWheelProductImage(@$product->prodimage)}}" title="{{@$product->prodbrand}}" alt="{{@$product->prodbrand}}">
                                        <img class="wheelImage image_thumb_swap" src="{{ViewWheelProductImage($product->prodimage)}}" title="{{$product->prodbrand}}" alt="{{$product->prodbrand}}">
                                        <div class="sale-icon"><a>Sale</a></div>
                                    </div>

                                    <div class="thumb-description">
                                        <div class="caption">
                                            <h4><a href="{{url('/wheelproductview',$product->id)}}{{@$flag?'/'.$flag:''}}{{'/'.str_replace(' ', '+', $product->detailtitle)}}">{{$product->detailtitle}}
                                                @if(@Request::get('diameter'))
                                                    <br> {{'Diameter : '.$product->wheeldiameter}}
                                                @endif
                                                @if(@Request::get('width'))
                                                    <br> {{'Width : '.$product->wheelwidth}}
                                                @endif
                                                    <!-- <br> {{'Diameter : '.$product->wheeldiameter}}
                                                    <br> {{'Width : '.$product->wheelwidth}}
                                                    <br> {{'prodmodel : '.$product->prodmodel}}
                                                     -->
                                                    <!-- <br> {{'PN : '.$product->partno}}  -->
                                    
                                                    
                                                     @if(@$product->distance)
                                                    <br> {{'Min.Distance : '.@$product->distance}} 
                                                    @endif
                                                </a>
                                              </h4>

                                            <!-- <div class="price">
                                                <span class="price-new">Starting at : {{roundCurrency(@$product->price)}}</span>
                                            </div> -->

                                        @if(@$product->wheel)
                                            @if(front_back_filecheck(@$product->wheel['image']) && $vehicleimage)
                                            <input type="hidden" id="frontback-image-{{$key}}" value="{{url('/')}}/{{front_back_path(@$product->wheel['image'])}}" data-partno="{{$product->partno}}">
                                                   <button class="btn btn-primary" data-toggle="modal" data-target="#VisualiserModal" onclick="APIWheelMapping('{{$key}}','show')" >See On Your Car
                                        </button> 

                                            @endif
                                        @else

                                            @if(front_back_filecheck(@$product->prodimage) && $vehicleimage)
                                            <input type="hidden" id="frontback-image-{{$key}}" value="{{url('/')}}/{{front_back_path(@$product->prodimage)}}" data-partno="{{$product->partno}}">
                                                <button class="btn btn-primary" data-toggle="modal" data-target="#VisualiserModal" onclick="APIWheelMapping('{{$key}}','show')" >See On Your Car
                                                </button> 
                                            @endif
                                        @endif



 



                                    
                                        </div>
                                        <div class="button-group">
                                            <a href="{{url('/wheelproductview',$product->id)}}{{@$flag?'/'.$flag:''}}">
                                            <button class="btn-cart" type="button" title="Add to Cart"><i class="fa fa-shopping-cart"></i>
                                                <span class="hidden-xs hidden-sm hidden-md">Add to Cart</span>
                                            </button>
                                            </a>
                                            <button class="btn-wishlist" title="Add to Wish List" onclick="wishlist.add('46');"><i class="fa fa-heart"></i>
                                                <span title="Add to Wish List">Add to Wish List</span>
                                            </button>
                                            <button class="btn-compare" title="Add to compare" onclick="compare.add('46');"><i class="fa fa-exchange"></i>
                                                <span title="Add to compare">Add to compare</span>
                                            </button>

                                            <button class="btn-quickview" type="button" title="Quick View"> <i class="fa fa-eye"></i>
                                                <span>Quick View</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="thumb-description-price-details">
                                      <span class="price-new">Starting at : {{roundCurrency(@$product->price)}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty 
                     <div class="col-md-12 left-head text-center" >
                        <br>
                          <h5> <b>No Results found for your selected.Please try selecting a different brand or attribute on the left.</b> </h5>
                      </div>

                        @endforelse
                </div>

                <div class="row pro-pagination">
                    <div class="col-sm-6 pagi-left">
                        <p>{{(@$products->total())?@$products->total().' Wheels Found':''}} </p>
                    </div>
                    <div class="col-sm-6 pagi-right">
                        {{$products->appends([ 'diameter' => @Request::get('diameter'), 'width' => @Request::get('width'), 'brand' => @Request::get('brand'), 'car_id' => @Request::get('car_id'), 'page' => @Request::get('page'), 'flag' => @Request::get('flag'), 'make' => @Request::get('make'), 'year' => @Request::get('year'), 'model' => @Request::get('model'), 'submodel' => @Request::get('submodel'), 'zip' => @Request::get('zip'), 'wheeldiameter'=> @Request::get('wheeldiameter'), 'wheelwidth'=> @Request::get('wheelwidth'), 'boltpattern'=> @Request::get('boltpattern'), 'minoffset'=> @Request::get('minoffset'), 'maxoffset'=> @Request::get('maxoffset'),'liftsize' => @Request::get('liftsize'), ])->links()}}

                    </div>
                </div>

            </div>
        </div>
        
    </div>
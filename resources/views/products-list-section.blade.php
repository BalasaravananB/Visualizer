
        <div class="row main-pro">
            <div class="col-sm-3 main-pro-inner-category">
            </div>
            <div class="col-sm-9 col-sm-9 main-pro-inner">
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
 
                                        <button class="btn btn-primary {{
                                            (!file_exists(front_back_path(@$product->wheel['image'])) && !file_exists(front_back_path(@$product->prodimage)) )?'disabled':''}}" {{(!file_exists(front_back_path(@$product->wheel['image'])) && !file_exists(front_back_path(@$product->prodimage)) )?' ':'data-toggle=modal'}} data-target="#myModal{{$key}}" onclick="getWheelPosition('{{$product->partno}}')" >See On Your Car</button> 
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
                   <!--  <div class="col-sm-6 pagi-right">
                        {{$products->appends([ 'diameter' => @Request::get('diameter'), 'width' => @Request::get('width'), 'brand' => @Request::get('brand'), 'car_id' => @Request::get('car_id'), 'page' => @Request::get('page'), 'flag' => @Request::get('flag'), 'make' => @Request::get('make'), 'year' => @Request::get('year'), 'model' => @Request::get('model'), 'submodel' => @Request::get('submodel'), 'zip' => @Request::get('zip'), 'wheeldiameter'=> @Request::get('wheeldiameter'), 'wheelwidth'=> @Request::get('wheelwidth'), 'boltpattern'=> @Request::get('boltpattern'), 'minoffset'=> @Request::get('minoffset'), 'maxoffset'=> @Request::get('maxoffset'),'liftsize' => @Request::get('liftsize'), ])->links()}}

                    </div> -->
                </div>

            </div>
        </div>
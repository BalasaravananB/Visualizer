@extends('admin.layouts.app')

@section('content')


<?php
$is_read_access = VerifyAccess('client','read');
$is_write_access = VerifyAccess('client','write');
?>





<style type="text/css">
    .req {
        color: red;
    }

    .edit_modal {
        margin: 6%;
        padding: 20px;
    }

    td.scrollable {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        overflow: auto !important;
    }

    .items-modal {
        width: 1000px !important;
    }

    .td-center {
        text-align: center !important;
    }

    .admin-form .btn.btn-default {
        color: #333 !important;
    }

    div.show-image:hover img{
        opacity:0.5;
    }
    div.show-image:hover input {
        display: block;
    }
    div.show-image input {
        position:absolute;
        display:none;
    } 
    div.show-image input.delete {
        top:0;
        left:55%;
    }
    /*1131px*/
</style>

<div class="product-status mg-b-15">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="product-status-wrap drp-lst">
                    <h4>List of Client Sites</h4>
                    <div style="text-align:right;padding-bottom: 20px">
                    @if($is_write_access)                        
                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal">Add New</button> 
                    @endif                    
                    <a  class="btn btn-info"  href="{{url('admin/exportTable')}}?module=ClientSite">Export CSV </a>
                    
                    </div>
                    <div class="asset-inner">
                        <table>
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Site Name</th>
                                    <th>Site URL</th>
                                    <th>Accesstoken</th> 
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            @forelse(@$clients as $key => $client)
                            <tr>
                                <td>{{@$key+1}}</td>
                                <td>{{@$client->sitename}}</td> 
                                <td>{{@$client->siteurl}}</td>
                                <td>{{@$client->accesstoken}}</td>
   
                                @if($is_write_access)
                                <td>
                                    <a type="button" class="btn btn-info" data-toggle="modal" data-target="#editModal{{$key}}"><i class="fa fa-edit"></i></a>


                                    <a type="button" class="btn btn-danger delete-client" data-key="{{$key}}"><i class="fa fa-trash"></i></a>
                                    <form id="delete-form-{{$key}}" action="{{route('admin.client.destroy',$client->id)}}" method="POST" novalidate="">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    </form>
                                </td>
                                @endif
                            </tr>


                             
                    @if($is_write_access)                   <!--  New Model Start-->
                    <div class="modal fade" id="editModal{{$key}}" role="dialog">
                        <div class="modal-dialog admin-form">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Edit Client Site</h4>
                                </div>
                                <div class="modal-body">
                                    <div id="dropzone1" class="pro-ad addcoursepro">
                                        <form action="{{ route('admin.client.update', $client->id)}}" class=" needsclick addcourse" method="POST" id="update-client" enctype="multipart/form-data">
                                            {{csrf_field()}}
                                            {{method_field('PATCH')}}
 
                                            <br>
                                            <div class="row"> 
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="sitename">Site Name</label>
                                                        <input type="text" name="sitename" class="form-control" placeholder="Give the name of the Client Site" value="{{@$client->sitename}}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="siteurl">Site URL ( Except Http / Https)</label>
                                                        <input type="text" name="siteurl" class="form-control" placeholder="Give the URL of the Client Site" value="{{@$client->siteurl}}">
                                                    </div>
                                                </div> 
                                            </div>
  
                                            <br>
                                          <!--   <div class="row"> 
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="sitename">Application URL</label>
                                                        <input type="text" name="sitename" class="form-control" placeholder="Give the name of the Client Site" value="{{@$client->sitename}}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="siteurl">Site URL ( Except Http / Https)</label>
                                                        <input type="text" name="siteurl" class="form-control" placeholder="Give the URL of the Client Site" value="{{@$client->siteurl}}">
                                                    </div>
                                                </div> 
                                            </div>  -->
                                            <br> 
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="payment-adress">
                                                        <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="payment-adress">
                                                        <a class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Cancel</a>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    </form>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
                            </div>
                        </div>
                    </div>
                    @endif

                            @empty
                            <tr>
                                <td colspan="5">No Client Sites found</td>
                            </tr>
                            @endforelse
                        </table>

                        {{$clients->links()}}
                    </div>

 

                    <!--  New Model Start-->
                    <div class="modal fade" id="myModal" role="dialog">
                        <div class="modal-dialog admin-form">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Add New Site</h4>
                                </div>
                                <div class="modal-body">
                                    <div id="dropzone1" class="pro-ad addcoursepro">
                                        <form action="{{ route('admin.client.store')}}" class=" needsclick addcourse" method="POST" id="demo1-upload" enctype="multipart/form-data">
                                            {{csrf_field()}} 
                                             <br>
                                            <div class="row"> 
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="sitename">Site Name</label>
                                                        <input type="text" name="sitename" class="form-control" placeholder="Give the name of the Client Site" value="">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="siteurl">Site URL ( Except Http / Https)</label>
                                                        <input type="text" name="siteurl" class="form-control" placeholder="Give the URL of the Client Site" value="">
                                                    </div>
                                                </div> 
                                            </div>
   
                                            <br> 
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="payment-adress">
                                                        <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="payment-adress">
                                                        <a class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Cancel</a>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    </form>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
                            </div>
                        </div>
                    </div>


                </div>
                <!--New Model End  -->

            </div>
        </div>
    </div>
</div>
</div>

@endsection
@section('custom_scripts')
<script type="text/javascript">

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = (function (input) {   
                
                var key = $(input).data('key');
                return function(e){
                    $('#featured-img-'+key).attr('src', e.target.result);
                };

            })(input); 
            reader.readAsDataURL(input.files[0]);
        }
    } 

    $('.featured-img').change(function(){ 
        readURL(this); 
    });

    $('.featured-img-delete').click(function(){
        var key = $(this).data('key');
        $('#featured-img-input-'+key).val('');
        $('#featured-img-'+key).attr('src',$('#featured-img-list-'+key).attr('src'));
    })

    $('.delete-client').click(function(){
            if (confirm("Are you sure want to remove client?")) {
                $('#delete-form-'+$(this).data('key')).submit();
            }
            return false;
    })
</script>
@endsection
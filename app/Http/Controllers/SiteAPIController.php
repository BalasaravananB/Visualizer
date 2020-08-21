<?php
namespace App\Http\Controllers;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\WheelProduct;
use App\Viflist;
use App\Vehicle;
use App\Wheel;
use App\CarImage;
use App\User;
use App\ClientSite;
use App\Chassis;
use App\ChassisModel; 
use App\PlusSize; 
use App\Offroad; 
use Illuminate\Http\Request;
use Validator;
use Session;
use Log;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use App\Http\Controllers\ZipcodeController as Zipcode;

class SiteAPIController extends Controller
{


    public function __construct(Request $request)
    {
       
 
        
    }


    public function getVehicles(Request $request)
    {
            
        try
        {
            $vehicle = new Vehicle;
            $data = [];
            // dd($request->all());
            if (!isset($request->make))
            {
                $allData['make'] = $data = $vehicle->select('make')
                    ->distinct('make')
                    ->orderBy('make', 'ASC')
                    ->get();

            }
            // Make change or Loading filter
            if (isset($request->make) && $request->changeBy == 'make' || $request->changeBy == '') $allData['year'] = $data = $vehicle->select('year')
                ->distinct('year')
                ->wheremake($request->make)
                ->orderBy('year', 'DESC')
                ->get();

            // Year change  or Loading Filter
            if (isset($request->make) && isset($request->year) && $request->changeBy == 'year' || $request->changeBy == '') $allData['model'] = $data = $vehicle->select('model')
                ->distinct('model')
                ->where('year', $request->year)
                ->wheremake($request->make)
                ->orderBy('model', 'ASC')
                ->get();

            // Model change  or Loading Filter
            if (isset($request->make) && isset($request->year) && isset($request->model) && $request->changeBy == 'model' || $request->changeBy == '') $allData['submodel'] = $data = $vehicle->select('submodel', 'body')
                ->distinct('submodel', 'body')
                ->where('year', $request->year)
                ->wheremake($request->make)
                ->wheremodel($request->model)
                ->orderBy('submodel', 'ASC')
                ->get();
            // dd($allData['submodel']);
            if ($request->changeBy == '')
            {
                return response()
                    ->json(['status'=>true,'data' => $allData]);
            }
            return response()->json(['status'=>true,'data' => $data]);

        }

        catch(ModelNotFoundException $notfound)
        {
            return response()->json(['status' =>false,'message' => $notfound->getMessage() ]);
        }
        catch(Exception $error)
        {
            return response()->json(['status' =>false,'message' => $error->getMessage() ]);
        }
    }



    public function findVehicle(Request $request){
         
        $vehicle = $this->findVehicleData($request);

        if($vehicle){
            return response()->json(['status'=>true,'data'=>[

                    'vehicle'=>$vehicle,
                    'offroad'=>@$vehicle->offroad??null,
                ]
            ]);
        }else{

            return response()->json(['status' =>false,'message' => "Vehicle Not Found!!"]);
        }
    }



    public function getLiftSizes(Request $request){
         
        $liftsizes = Offroad::where('offroadid',@$request->offroadid)->whereNotIn('plussizetype',['Levelkit'])->select('plussizetype')->distinct('plussizetype')->pluck('plussizetype'); 

        $newLiftSizes = [];
        
        foreach ($liftsizes as $key => $size) {
            $newSize = str_replace('lift', '" Lift', $size);
            $newLiftSizes[$size]=$newSize;
        }


        return response()->json([
            'status'=>true,
            'data'=>$newLiftSizes]);

    

    }



    public function zipcodeUpdate(Request $request)
    { 

    }

    public function zipcodeClear(Request $request)
    {  
    }


    public function getWheels(Request $request)
    {
        
        if($request->flag == 'searchByWheelSize'){

            $validatorRules =[
                'wheeldiameter'=>'required',
                'wheelwidth'=>'required',
                'boltpattern'=>'required', 
            ];
 
        }elseif($request->flag == 'searchByVehicle'){
 
            $validatorRules =[
                'vehicleid'=>'required',
                'flag'=>'required'
            ];
 
        }else{

            $validatorRules =[];
        }


        $validator = Validator::make($request->all() ,$validatorRules);
        if ($validator->fails())
        {
            return response()->json(['status' => false, 'error' => $validator->messages() ]);
        }

        try
        { 
            $vehicle = Vehicle::where('vehicle_id',$request->vehicleid)->first();

            if($vehicle == null && $request->flag == 'searchByVehicle'){
                return response()->json(['status' =>false,'message' => 'Vehicle Not Found!!' ]);
            }

  
            $zipcode = @$request->zipcode;//Session::get('user.zipcode');

            // if($request->flag == 'searchByWheelSize'){ 
            //     Session::put('user.searchByWheelSize',$request->all());
            // } 
 
            $products = WheelProduct::with('wheel')->select('id', 'prodbrand','detailtitle', 'prodmodel', 'prodfinish', 'prodimage', 'wheeldiameter', 'wheelwidth', 'prodtitle', 'price', 'partno','partno_old','wheeltype','rf_lc','boltpattern1','offset1','offset2','boltpattern1','wheeltype');
 

 
            // $car_images='';
            $offroadtype=null;
            $liftsize=null;
            $vehicleimage = null;
            $vehiclecolors =null;

            // if(@$request->wheeltype){
            //     $wheeltype = base64_decode($request->wheeltype);
            //     // dd($wheeltype);
            //     if($request->wheeltype){
            //         $products = $products->where('wheeltype','LIKE','%'.$wheeltype.'%');  
            //     }
            // }
             

            // Search By Wheels Size in products
            if (isset($request->flag) && $request->flag == 'searchByWheelSize')
            {

                if (isset($request->wheeldiameter) && $request->wheeldiameter) 
                    $products = $products->where('wheeldiameter', $request->wheeldiameter);

                if (isset($request->wheelwidth) && $request->wheelwidth) 
                    $products = $products->where('wheelwidth', $request->wheelwidth);

                if (isset($request->boltpattern) && $request->boltpattern) 
                    $products = $products->where('boltpattern1', $request->boltpattern);
                    // ->orWhere('boltpattern2', $request->boltpattern)
                    // ->orWhere('boltpattern3', $request->boltpattern);

                if (isset($request->minoffset) && $request->minoffset) 
                    $products = $products->where('offset1','>=', $request->minoffset);
                    // $products = $products->whereBetween('offset1', [$request->minoffset, $request->maxoffset]);

                if (isset($request->maxoffset) && $request->maxoffset) 
                    $products = $products->where('offset1','<=',$request->maxoffset);
            }
            elseif (isset($request->flag) && $request->flag == 'searchByVehicle')
            {

                // $vehicle = Session::get('user.vehicle');
                $liftsize = @$request->liftsize; //Session::get('user.liftsize');
                $offroadtype =@$request->offroadtype; //Session::get('user.offroadtype');
                    
                    // dd($liftsize);

                if(@$vehicle->dually =='1' && ($offroadtype == 'stock' || $offroadtype == null)){
                    $products->where('wheeltype','LIKE','%D%'); 
                }
 
            
                //Offroad Checking Flow for Shop by Vehicle

                $offroadSizes = [];

                if(@$liftsize){

                    $offroadSizes = Offroad::where('offroadid',@$vehicle->offroad)->where('plussizetype',$liftsize)->get(); 
                }



                // Wheel Visualiser Flow for Shop by Vehicle
                if(@$vehicle->vif != null){
                    $vehicleimage = CarImage::select('car_id','image','color_code')->wherecar_id(@$vehicle->vif)->where('image', 'LIKE', '%.png%')
                    ->with(['CarColor'])->first();
                    $vehiclecolors = CarImage::wherecar_id($vehicle->vif)->where('image', 'LIKE', '%.png%')->pluck('image','color_code');
                }

                $chassis_models = ChassisModel::where('model_id', @$vehicle->dr_model_id)->first();
 

                $chassis = Chassis::where('chassis_id', @$vehicle->dr_chassis_id)->first(); 

                 
                if(@$liftsize){
                     
                        $products = $products->where(function ($query) use($offroadSizes) {
                             
                            foreach ($offroadSizes as $key => $offroadSize) {  
                                $query->orwhere('detailtitle', 'like',  '%' . $offroadSize->wheeldiameter.'x'.$offroadSize->wheelwidth .'%')->whereBetween('offset1', [$offroadSize->offsetmin, $offroadSize->offsetmax]);
                             }      
                        });  
                }else{



                        //*********************** Offset checking **************************
                        
                        if($chassis_models->rim_size_r == null || $chassis_models->rim_size_r == 'NULL'){
                            $products = $products->whereBetween('offset1', [$chassis->min_et_front, $chassis->max_et_front]);
                        }else{

                            $products = $products->whereBetween('offset1', [$chassis->min_et_front, $chassis->max_et_front]);
                            $products = $products->whereBetween('offset1', [$chassis->min_et_rear, $chassis->max_et_rear]);
                        }

                        //*********************** Plus Size checking **************************


                        $plusSizes = PlusSize::where('chassis_id',@$vehicle->dr_chassis_id)->get(); 


                        $plusSizesArray=array(); $diameterSizesArray=array();

                        $rimsizearray = explode('x', $chassis_models->rim_size);
                        $widthPart2 = $widthPart1 = str_replace(" ", "", $rimsizearray[0])?:$rimsizearray[0];
                        $diameterPart2 = $diameterPart1 = str_replace(" ", "", $rimsizearray[1])?:$rimsizearray[1];

                        foreach ($plusSizes as $key => $plusSize) {
                            
                            $wheelsizearray = explode('x', $plusSize->wheel_size);
                            $width = str_replace(" ", "", $wheelsizearray[0])?:$wheelsizearray[0];
                            $diameter = str_replace(" ", "", $wheelsizearray[1])?:$wheelsizearray[1];
                            if($width > $widthPart2 ){
                                $widthPart2 = $width;
                            }
                            if($diameter > $diameterPart2 ){
                                $diameterPart2 = $diameter;
                            }
                        }

                        // dd([$diameterPart1,$diameterPart2],[$widthPart1,$widthPart2]);
                        $products = $products->whereBetween('wheeldiameter',[$diameterPart1,$diameterPart2]);
                        $products = $products->whereBetween('wheelwidth',[$widthPart1,$widthPart2]);

                }


                //*********************** BCD Bolt Pattern checking **************************
                if (strpos($chassis->pcd, '.') !== false)
                {
                    $str = substr($chassis->pcd, 0, strpos($chassis->pcd, "."));
                }
                else
                {
                    $str = $chassis->pcd;
                }

                $boltpattern = (str_replace("x", "", $str)?:'')?:'Blank5';
                if($boltpattern != ''){
                    $products = $products->whereIn('boltpattern1', [$boltpattern,'Blank5']);
                }

                // $request->flag = 'searchByWheelSize';
                // dd($plusSizesArray,$boltpattern,$diameterPart);
            }

 

            // if zipcode is available....
            // if($zipcode != null){
            //     $zipcodes = Zipcode::getZipcodesByRadius($zipcode,'150'); 
            //     $products = $products->with([
            //                         'DropshipperInventories'=>function ($query) use($zipcodes){ 
            //                                                 $query->where('qty','>=',4); 
            //                                                 $query->Where(function ($query1) use($zipcodes) { 
            //                                                     foreach ($zipcodes as $key => $zipcode) {
            //                                                         $query1->orwhere('zip', 'like',  '%' . $zipcode.'%');
            //                                                     }     
            //                                                 });  
            //                                                 $query->orderBy('qty','DESC'); 
            //                         }
            //                     ])
   
            //     ->orderBy('price', 'ASC'); 
 
            // }else{


            //     $products = $products->with([
            //                          'DropshipperInventories'=>function ($query){ 
            //                                                 $query->where('qty','>=',4); 
            //                                                 $query->orderBy('qty','DESC'); 
            //                         }
            //                     ])
            //     ->orderBy('price', 'ASC');
            // }                       
         
            // ----------------------------------------------------------------------------------------------------
            
            $visualiserbrand = explode(',', $request->visualiserbrand?:'');
            $visualiserfinish = explode(',', $request->visualiserfinish?:'');
            $visualiserdiameter = explode(',', $request->visualiserdiameter?:'');
            $visualiserwidth = explode(',', $request->visualiserwidth?:'');

            // Wheel Width size search in the Sidebar
            $wheelwidth = clone $products;

            
            if (isset($request->visualiserbrand) && $request->visualiserbrand) {
                $wheelwidth = $wheelwidth->whereIn('prodbrand', $visualiserbrand);
            }

            if (isset($request->visualiserdiameter) && $request->visualiserdiameter) {
                $wheelwidth = $wheelwidth->whereIn('wheeldiameter', $visualiserdiameter);
            }
            
            if (isset($request->visualiserfinish) && $request->visualiserfinish) {
                $wheelwidth = $wheelwidth->whereIn('prodfinish', $visualiserfinish);
            }

            $wheelwidth =  $wheelwidth->select('wheelwidth', \DB::raw('count(DISTINCT prodtitle) as total'))
            ->groupBy('wheelwidth')
            ->get()
            ->sortBy('wheelwidth');

            // Wheel Diameter size search in the Sidebar

            $wheeldiameter = clone $products;
            // dd($wheeldiameter);
            if (isset($request->visualiserbrand) && $request->visualiserbrand) {
                $wheeldiameter = $wheeldiameter->whereIn('prodbrand', $visualiserbrand);
            }

            if (isset($request->visualiserwidth) && $request->visualiserwidth) {
                $wheeldiameter = $wheeldiameter->whereIn('wheelwidth', $visualiserwidth);
            }
            
            if (isset($request->visualiserfinish) && $request->visualiserfinish) {
                $wheeldiameter = $wheeldiameter->whereIn('prodfinish', $visualiserfinish);
            }

            $wheeldiameter =  $wheeldiameter->select('wheeldiameter', \DB::raw('count(DISTINCT prodtitle) as total'))
            ->groupBy('wheeldiameter')
            ->get()
            ->sortBy('wheeldiameter');


            // Wheel Brands size search in the Sidebar

            $countsByBrand = clone $products;
            
            if (isset($request->visualiserdiameter) && $request->visualiserdiameter) {
                $countsByBrand = $countsByBrand->whereIn('wheeldiameter', $visualiserdiameter);
            }

            if (isset($request->visualiserwidth) && $request->visualiserwidth) {
                $countsByBrand = $countsByBrand->whereIn('wheelwidth', $visualiserwidth);
            }

            if (isset($request->visualiserfinish) && $request->visualiserfinish) {
                $countsByBrand = $countsByBrand->whereIn('prodfinish', $visualiserfinish);
            }
            
            $brands =  $countsByBrand->select('prodbrand')
            ->groupBy('prodbrand')
            ->get()
            ->sortBy('prodbrand');
            
            $countsByBrand = $countsByBrand->select('prodbrand', \DB::raw('count(DISTINCT prodtitle) as total'))
            ->groupBy('prodbrand')
            ->pluck('total','prodbrand');

            // Wheel Finish size search in the Sidebar

            $wheelfinish = clone $products;

            if (isset($request->visualiserbrand) && $request->visualiserbrand) {
                $wheelfinish = $wheelfinish->whereIn('prodbrand', $visualiserbrand);
            }

            if (isset($request->visualiserdiameter) && $request->visualiserdiameter) {
                $wheelfinish = $wheelfinish->whereIn('wheeldiameter', $visualiserdiameter);
            }
            
            if (isset($request->visualiserwidth) && $request->visualiserwidth) {
                $wheelfinish = $wheelfinish->whereIn('wheelwidth', $visualiserwidth);
            }

            $wheelfinish =  $wheelfinish->select('prodfinish', \DB::raw('count(DISTINCT prodtitle) as total'))
            ->groupBy('prodfinish')
            ->get()
            ->sortBy('prodfinish');



              // Filters  for Main listing products ------------------------->

            if (isset($request->visualiserbrand) && $request->visualiserbrand)
            {
                $products = $products->whereIn('prodbrand', $visualiserbrand);
                $branddesc = WheelProduct::select('prodbrand','proddesc')->whereIn('prodbrand', $visualiserbrand)
                    ->get()
                    ->unique('prodbrand');
            }

            if (isset($request->visualiserfinish) && $request->visualiserfinish) 
                    $products = $products->whereIn('prodfinish', $visualiserfinish);

            if (isset($request->visualiserdiameter) && $request->visualiserdiameter) 
                    $products = $products->whereIn('wheeldiameter', $visualiserdiameter);

            if (isset($request->visualiserwidth) && $request->visualiserwidth) 
                    $products = $products->whereIn('wheelwidth', $visualiserwidth);
 


            // --------------------------------------------------------------------------------------------------


            $products = $products->orderBy('price', 'ASC')->get()->unique('prodtitle');  
 
            $products = MakeCustomPaginator($products, $request, 9); 
            
            $listHtml = '';

            $flag = $request->flag;

            if(@$request->listType == 'html'){
                $listHtml = view('products-list-section', compact(
                    'products',
                    'flag',
                    'vehicle',
                    'wheelwidth',
                    'wheeldiameter',
                    'countsByBrand',
                    'wheelfinish',
                    'brands',
                    'offroadtype',
                    'liftsize',
                    'vehicleimage',
                    'visualiserbrand',
                    'visualiserfinish',
                    'visualiserdiameter',
                    'visualiserwidth',
            ))->render();
            }



            return response()->json(['status' =>true,'data'=>[
                'products'=>$products, 
                'vehicle'=>$vehicle,
                'zipcode'=>$zipcode,
                'offroadtype'=>$offroadtype,
                'liftsize'=>$liftsize,
                'flag'=>$request->flag,
                'htmllist'=>$listHtml,
                'vehicleimage'=>$vehicleimage,
                'vehiclecolors'=>$vehiclecolors

            ]]);
 

        }
        catch(ModelNotFoundException $notfound)
        {
            return response()->json(['status' =>false,'message' => $notfound->getMessage() ]);
        }
        catch(Exception $error)
        {
            return response()->json(['status' =>false,'message' => $error->getMessage() ]);
        }
    }

    public function WheelByVehicle(Request $request)
    {

        
        $validator = Validator::make($request->all() , [
            'make'=>'required',
            'model'=>'required',
            'year'=>'required',
            'submodel'=>'required',
            'wheelpartno' => 'required|max:255',
            // 'vehicleid' => 'required|max:255'
        ]);

        if ($validator->fails())
        {

            return response()->json(['status' => false, 'error' => $validator->messages() ]);
        }
        try
        {

            // $vehicle = Vehicle::where('vehicle_id',$request->vehicleid)->first();

            $vehicle = $this->findVehicleData($request);
            $carimage = null;
            $car_images = null;
            $detectimage = null;
            $wheel = null;
            $frontback = null;
            $position = [];
            if($vehicle != null)
            {
                // return response()->json(['status' => false, 'message' => 'Vehicle Not Found!']);
                if (@$vehicle->vif != null)
                {
                    $car_images = CarImage::select('car_id', 'image', 'color_code')->wherecar_id(@$vehicle->vif)
                        ->where('image', 'LIKE', '%.png%')->with(['CarViflist' => function ($query)
                    {
                        $query->select('vif', 'yr', 'make', 'model', 'body', 'drs', 'whls');

                    }
                    , 'CarColor'])
                        ->first();
                    if ($car_images != null)
                    {
                        
                        $carimage = asset($car_images->image);
                        $detectimage = public_path() . '/' . $car_images->image;
                        // return response()->json(['status' => false, 'message' => 'Vehicle Image Not Found!']);

                    }
                }
            }


            if ($request->wheelpartno)
            {
                $wheelpro = WheelProduct::with('wheel')->where('partno', $request->wheelpartno)
                    ->first();
                // dd($wheelpro->wheel);
                if ($wheelpro)
                {
                    if (@$wheelpro->wheel)
                    {
                        $frontback = front_back_path(@$wheelpro
                            ->wheel
                            ->image);
                    }
                    else
                    {
                        $frontback = front_back_path(@$wheelpro->prodimage);
                    }
                }
                else
                {

                    // return response()->json(['status' => false, 'message' => 'Wheel Product Not Found!']);
                }
            }


            if($carimage != null){

                Log::info('Process Initiate');
                $process = new Process("python3 " . public_path() . "/js/detect-wheel.py " . $detectimage . " " . public_path() . " " . @$car_images->car_id);

                $process->run();
                Log::info('Process Run');
                // $process->setIdleTimeout(60);
                // executes after the command finishes

                while ($process->isRunning()) {

                   Log::info('Process Running check');

                    // waiting for process to finish
                }

                Log::info('Condition Check');
                if ($process->isSuccessful())
                {

                    Log::info('Run Successful');
                    $position = $process->getOutput();
                }
                else
                {

                    Log::info('Run Fail Part');
                    $position = [[301.4070587158203, 313.35447692871094, 62.829010009765625, 99.53854370117188, 269.9925537109375, 263.585205078125, 269.9925537109375, 263.585205078125], [526.0646209716797, 293.32891845703125, 42.812530517578125, 79.39599609375, 504.6583557128906, 253.63092041015625, 504.6583557128906, 253.63092041015625]];

                }

                Log::info('Response Binded');
            }else{
                Log::info('Car Image Not Found');
            }

            
            $data = [
                'baseurl' => asset('/') , 
                'vehicle' => $vehicle->year_make_model_submodel, 
                'carimage' => $carimage, 
                'frontimage' => asset($frontback) , 
                'backimage' => asset($frontback) , 
                'position' => $position
            ];
            return response()->json(['status' => true, 'data' => $data ]);
        }
        catch(Exception $e)
        {
            return response()->json(['status' => false, 'message' => 'Something went wrong!']);
        }
    }

    public function findVehicleData($data)
    {
        $vehicle = Vehicle::with('Plussizes', 'ChassisModels', 'Offroads')->select('vehicle_id', 'vif', 'year', 'make', 'model', 'submodel', 'dr_chassis_id', 'dr_model_id', 'year_make_model_submodel', 'sort_by_vehicle_type', 'wheel_type', 'rf_lc', 'offroad', 'dually')
            ->where('year', $data->year)
            ->where('make', $data->make)
            ->where('model', $data->model);

        if (@$data->submodel)
        {

            $submodelBody = explode('-', $data->submodel);
            // dd($submodelBody);
            if (count($submodelBody) == 2)
            {

                $vehicle = $vehicle->where('submodel', $submodelBody[0])->where('body', $submodelBody[1]);
            }
            elseif (count($submodelBody) == 3)
            {

                $vehicle = $vehicle->where('submodel', $submodelBody[0] . '-' . $submodelBody[1])->where('body', $submodelBody[2]);
            }
        }
        $vehicle = $vehicle->orderBy('offroad', 'desc')
            ->orderBy('dually', 'desc')
            ->first();
        return $vehicle;
    }






    public function checkDropshippble(Request $request){
        
        $dropshippable = 0;
        if(@$request->productid){ 

            $wheelproduct = WheelProduct::find($request->productid);
            $dropshippable = $wheelproduct->dropshippable;
        }

        return response()->json(['status'=>true,'data'=>$dropshippable]);

    

    }

    public function setWheelVehicleFlow(Request $request){

        try {
            
            if($request->flag == 'searchByVehicle'){ 
                $vehicle = $this->findVehicleData($request);
                Session::put('user.searchByVehicle',$request->all());  
                Session::put('user.offroadtype',null);
                Session::put('user.liftsize',null); 
                Session::put('user.vehicle',$vehicle); 
            }

            if($request->offroad){    
                if($request->offroad == 'levelkit'){
                    Session::put('user.liftsize','Levelkit'); 
                }
                Session::put('user.offroadtype',$request->offroad); 
            }
      
            if($request->liftsize){     
                Session::put('user.liftsize',$request->liftsize); 
            }

            $zipcode = Session::get('user.zipcode');
            $offroadtype = Session::get('user.offroadtype');
            $liftsize = Session::get('user.liftsize'); 
            $vehicle =  Session::get('user.vehicle'); 

            return response()->json(['status'=>true,'zipcode'=>$zipcode,'offroadtype'=>$offroadtype,'liftsize'=>$liftsize,'vehicle'=>$vehicle]);


        } catch (Exception $e) {
             return response()->json(['status'=>false]);
        } 
    }


}


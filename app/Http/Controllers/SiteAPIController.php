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
class SiteAPIController extends Controller
{

    public $is_valid = true;
    public $error_message = '';

    public function __construct(Request $request)
    {
        if ($request->has('accesstoken'))
        {

            $site = ClientSite::where('accesstoken', $request->accesstoken)
                ->first();
            if ($site != null)
            {
                $host_token = base64_decode(base64_decode($request->accesstoken));
                $requestHost = parse_url($request
                    ->headers
                    ->get('origin') , PHP_URL_HOST);

                if ($requestHost != $host_token)
                {
                    $this->is_valid = false;
                    $this->error_message = 'You couldn\'t access from Invalid Site!!';
                }
            }
            else
            {
                $this->is_valid = false;
                $this->error_message = 'Token is Invalid!!';
            }
        }
        else
        {
            $this->is_valid = false;
            $this->error_message = 'Access Token is Required!!';
        }

        // $this->middleware('admin.guest', ['except' => 'logout']);
        
    }

    public function WheelByVehicle(Request $request)
    {

        if (!$this->is_valid)
        {
            return ['status' => $this->is_valid, 'message' => $this->error_message];
        }

        $validator = Validator::make($request->all() , ['year' => 'required|max:255', 'make' => 'required|max:255', 'model' => 'required|max:255', 'submodel' => 'required|max:255', 'wheelpartno' => 'required|max:255', ]);

        if ($validator->fails())
        {

            return ['status' => false, 'error' => $validator->messages() ];
        }
        try
        {

            $vehicle = $this->findVehicle($request);
            $carimage = null;
            $car_images = null;
            $detectimage = null;
            $wheel = null;
            $frontback = null;
            if ($vehicle == null)
            {
                return ['status' => false, 'message' => 'Vehicle Not Found!'];

            }
            if (@$vehicle->vif != null)
            {
                $car_images = CarImage::select('car_id', 'image', 'color_code')->wherecar_id(@$vehicle->vif)
                    ->where('image', 'LIKE', '%.png%')->with(['CarViflist' => function ($query)
                {
                    $query->select('vif', 'yr', 'make', 'model', 'body', 'drs', 'whls');

                }
                , 'CarColor'])
                    ->first();
                $carimage = asset($car_images->image);
                $detectimage = public_path() . '/' . $car_images->image;
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

                    return ['status' => false, 'message' => 'Wheel Product Not Found!'];
                }
            }

            Log::info('Process Initiate');
            $process = new Process("python3 " . public_path() . "/js/detect-wheel.py " . $detectimage . " " . public_path() . " " . @$car_images->carid);

            $process->run();
            Log::info('Process Run');
            // $process->setIdleTimeout(60);
            // executes after the command finishes
            Log::info('Condition Check');
            if ($process->isSuccessful())
            {

            	Log::info('Run Successful');
                $position = json_encode($process->getOutput());
            }
            else
            {

            	Log::info('Run Fail Part');
                $position = [[301.4070587158203, 313.35447692871094, 62.829010009765625, 99.53854370117188, 269.9925537109375, 263.585205078125, 269.9925537109375, 263.585205078125], [526.0646209716797, 293.32891845703125, 42.812530517578125, 79.39599609375, 504.6583557128906, 253.63092041015625, 504.6583557128906, 253.63092041015625]];

            }

            	Log::info('Response Binded');
            $data = ['baseurl' => asset('/') , 'vehicle' => $vehicle->year_make_model_submodel, 'carimage' => $carimage, 'frontimage' => asset($frontback) , 'backimage' => asset($frontback) , 'position' => $position];
            return ['status' => true, 'data' => $data, ];
        }
        catch(Exception $e)
        {
            return ['status' => false, 'message' => 'Something went wrong!'];
        }
    }

    public function findVehicle($data)
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

    public function getVehicles(Request $request)
    {

        if (!$this->is_valid)
        {
            return ['status'=>$this->is_valid,'message'=>$this->error_message];
            // return response()
            //     ->json(['error' => $this->error_message]);
        }
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
                    ->json(['data' => $allData]);
            }
            return response()->json(['data' => $data]);

        }
        catch(ModelNotFoundException $notfound)
        {
            return response()->json(['error' => $notfound->getMessage() ]);
        }
        catch(Exception $error)
        {
            return response()->json(['error' => $error->getMessage() ]);
        }
    }




    public function getWheels(Request $request)
    {
  		if (!$this->is_valid)
        {
            return ['status'=>$this->is_valid,'message'=>$this->error_message];
            
        }

   		try
        { 
            $vehicle = $this->findVehicle($request);


            $products = WheelProduct::with('wheel')->select('id', 'prodbrand','detailtitle', 'prodmodel', 'prodfinish', 'prodimage', 'wheeldiameter', 'wheelwidth', 'prodtitle', 'price', 'partno','partno_old','wheeltype','rf_lc','boltpattern1','offset1','offset2','boltpattern1','wheeltype');
 
   
 
  
            $chassis_models = ChassisModel::where('model_id', @$vehicle->dr_model_id)->first(); 

            $chassis = Chassis::where('chassis_id', @$vehicle->dr_chassis_id)->first(); 
 

                //*********************** Offset checking **************************
                
                if(@$chassis_models->rim_size_r == null || @$chassis_models->rim_size_r == 'NULL'){
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
 
 
                $products = $products->with([
                                     'DropshipperInventories'=>function ($query){ 
                                                            $query->where('qty','>=',4); 
                                                            $query->orderBy('qty','DESC'); 
                                    }
                                ])
                ->orderBy('price', 'ASC'); 
         
            $products = $products->get()->unique('prodtitle');  
 
            $products = MakeCustomPaginator($products, $request, 12); 

            return ['status' =>true,'data'=>[
            	'products'=>$products, 
            	'vehicle'=>$vehicle,
            ] ];
 

        }
        catch(ModelNotFoundException $notfound)
        {
            return response()->json(['error' => $notfound->getMessage() ]);
        }
        catch(Exception $error)
        {
            return response()->json(['error' => $error->getMessage() ]);
        }
    }



    public function getLiftSizes(Request $request){
        
        $vehicle = Session::get('user.vehicle'); 

        $liftsizes = Offroad::where('offroadid',@$vehicle->offroad)->whereNotIn('plussizetype',['Levelkit'])->select('plussizetype')->distinct('plussizetype')->pluck('plussizetype'); 

        return $liftsizes?:null;

    

    }

    public function checkDropshippble(Request $request){
        
        $dropshippable = 0;
        if(@$request->productid){ 

            $wheelproduct = WheelProduct::find($request->productid);
            $dropshippable = $wheelproduct->dropshippable;
        }

        return ['dropshippable'=>$dropshippable];

    

    }


    public function setWheelVehicleFlow(Request $request){

        try {
            
            if($request->flag == 'searchByVehicle'){ 
                $vehicle = $this->findVehicle($request);
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

            return ['status'=>true,'zipcode'=>$zipcode,'offroadtype'=>$offroadtype,'liftsize'=>$liftsize,'vehicle'=>$vehicle];


        } catch (Exception $e) {
             return ['status'=>false];
        } 
    }


}


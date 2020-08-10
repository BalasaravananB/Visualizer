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
use App\UserSite; 
use Illuminate\Http\Request;  
use Validator;


class SiteAPIController extends Controller
{

	public $is_valid =true;
	public $error_message ='';

	public function __construct(Request $request)
    {
    	if(@$request->accesstoken != ''){

	    	$site = UserSite::where('api_token',$request->accesstoken)->first();
	    	if($site != null){
	    		$host_token = base64_decode(base64_decode($request->accesstoken));
	    		$requestHost = parse_url($request->headers->get('origin'),  PHP_URL_HOST);

	    		if($requestHost != $host_token){
	    			$this->is_valid=false;
	    			$this->error_message='You couldn\'t access from Invalid Site!!'; 
	    		}
	    	}else{
	    			$this->is_valid=false;
	    			$this->error_message='Token is Invalid!!'; 
	    	}
	    }else{
	    			$this->is_valid=false;
	    			$this->error_message='Access Token is Required!!'; 
	    }


        // $this->middleware('admin.guest', ['except' => 'logout']);

    }



    public function WheelByVehicle(Request $request)
    {


    	if(!$this->is_valid){
    		return ['status'=>$this->is_valid,'message'=>$this->error_message];
    	}


        // dd($request->all());
        

        $validator = Validator::make($request->all() , ['year' => 'required|max:255', 'make' => 'required|max:255', 'model' => 'required|max:255', 'submodel' => 'required|max:255', 'wheelpartno' => 'required|max:255', ]);

        if ($validator->fails())
        {

            return ['status' => false, 'error' => $validator->messages() ];
        }
        try
        {

            $vehicle = $this->findVehicle($request);
            $carimage = null;
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
                $detectimage = public_path().'/'.$car_images->image;
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

             	$process = new Process("python3 ".public_path()."/js/detect-wheel.py ".$detectimage." ".public_path()." ".$car_images->carid);

        		$process->run(); 

        		// $process->setIdleTimeout(60);

		        // executes after the command finishes
		        if ($process->isSuccessful()) {
		            
		            $position = json_encode($process->getOutput());
		        }else{

		            $position = [[301.4070587158203, 313.35447692871094, 62.829010009765625, 99.53854370117188, 269.9925537109375, 263.585205078125, 269.9925537109375, 263.585205078125], [526.0646209716797, 293.32891845703125, 42.812530517578125, 79.39599609375, 504.6583557128906, 253.63092041015625, 504.6583557128906, 253.63092041015625]];

		        }




            // $position = ['front' => array(
            //     'left' => 80,
            //     'top' => 275,
            //     'width' => 90
            // ) , 'back' => array(
            //     'left' => 280,
            //     'top' => 275,
            //     'width' => 70
            // ) ,

            // ];
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

}


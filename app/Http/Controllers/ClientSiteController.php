<?php

namespace App\Http\Controllers;

use App\ClientSite;
use Illuminate\Http\Request;

class ClientSiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clients = ClientSite::paginate(10);
        return  view('admin.client.index',compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
    public function createAccessToken($url)
    { 
        $url = preg_replace( "#^[^:/.]*[:/]+#i", "", $url ); 
        $enc1 = base64_encode($url);
        $enc2 = base64_encode($enc1);
        return $enc2;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [ 
            'siteurl'=>'required|unique:client_sites,siteurl', 
            'sitename'=>'required', 
        ]);
        try{  

                $data = $request->except(['_token']); 

                $data['accesstoken']=$this->createAccessToken($request->siteurl);
                
                if(ClientSite::where('accesstoken',$data['accesstoken'])->first()){
                    return back()->with('error','This Site Already Exists!!');
                }

                $client = ClientSite::create($data);  
                
                return back()->with('success','Site Added Successfully!!');

            }catch(Exception $e){
                return back()->withInput(Input::all())->with('error',$e->getMessage());
            }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ClientSite  $clientSite
     * @return \Illuminate\Http\Response
     */
    public function show(ClientSite $clientSite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ClientSite  $clientSite
     * @return \Illuminate\Http\Response
     */
    public function edit(ClientSite $clientSite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ClientSite  $clientSite
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {   

        $this->validate($request, [ 
            'siteurl'=>'required|unique:client_sites,siteurl,'.$id, 
            'sitename'=>'required', 
        ]);
        // dd($request->all());
        try{  

                $client = ClientSite::find($id);

                $data = $request->except(['_token']); 
                
                if($request->siteurl != $client->siteurl)
                    $data['accesstoken']=$this->createAccessToken($request->siteurl);
                
                if(ClientSite::where('id', '!=',$id)->where('accesstoken',$data['accesstoken'])->first()){
                    return back()->with('error','This Site Already Exists!!');
                }

                $client = $client->update($data);  
                
                return back()->with('success','Site Updated Successfully!!');

            }catch(Exception $e){
                return back()->withInput(Input::all())->with('error',$e->getMessage());
            }
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ClientSite  $clientSite
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClientSite $clientSite)
    {
        //
    }
}

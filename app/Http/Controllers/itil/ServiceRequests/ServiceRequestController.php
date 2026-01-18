<?php

namespace App\Http\Controllers\itil\ServiceRequests;

use Illuminate\Http\Request;
use App\Models\EntitySubGroup;

use App\Models\EntityMainGroup;
use App\Http\Controllers\Controller;

use App\Models\itil\ServiceRequests\ServiceRequest;
use PhpOffice\PhpSpreadsheet\Calculation\Web\Service;
use App\Models\itil\ServiceRequests\ServiceRequestResponse;
use App\Models\itil\ServiceRequests\ServiceRequestAttachment;

class ServiceRequestController extends Controller
{
    
    public function index()
    {
        $serviceRequests=ServiceRequest::orderBy("created_at")->get();

        
        return view('itil.servis_istekleri.index',compact('serviceRequests'));
    }

  
    public function create()
    {
        $mainGroupId=EntityMainGroup::where('name','uygulamalar')->pluck('id')->first();
        $services=EntitySubGroup::where('main_group_id',$mainGroupId)->get();
        
        //dd($services);
        return view('itil.servis_istekleri.add',compact('services'));
    }

  
    public function store(Request $request)
    {
        
        $request->validate([
            'title'=> 'required',
            'description'=> 'required',
            //'serviceRequestFiles.*' => 'required|file|max:51200|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf,image/jpeg,image/gif', // Her bir dosya için maksimum 50MB
        ]);

        $data=[
            'service_id'=> $request->service_id,
            'service_type'=> $request->service_type,
            'title'=> $request->title,
            'description'=> $request->description,
            'level'=> $request->level,
            'request_filter_id'=> 1,
        ];

        ServiceRequest::create($data);

        $serviceRequestId=ServiceRequest::orderBy('id')->get()->last();
        $serviceRequestId=$serviceRequestId->id;
     

       

        $str='Servis_talebi_eki_'.$serviceRequestId;

        //dd($request->hasFile('serviceRequestFiles'));

        if ($request->hasFile('serviceRequestFiles')) {

            //dd($request->file('serviceRequestFiles'));

            foreach ($request->file('serviceRequestFiles') as $key=>$file) {
                
                
                $fileName = $str .'.('.($key+1).')'.$file->getClientOriginalExtension();
            
                $file->move(public_path('uploads\itil\service_request_attachments'), $fileName);
                $data2['file_name'] = $fileName;
                $data2['service_request_id']=$serviceRequestId;
               
                 
                ServiceRequestAttachment::create($data2);
            }
            
        
        }

        return redirect()->route('service_requests.index')->with('message','Servis isteği başarıyla oluşturuldu');

      

    }

    public function show(string $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);

        $serviceRequestResponses=ServiceRequestResponse::where('service_request_id',$id)->get();
    
        return view('itil.servis_istekleri.detail',compact('serviceRequest','serviceRequestResponses'));
    }

   
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function storeResponse(Request $request)
    {

        //dd($request->all());
        $data=[
            'service_request_id'=> $request->id,
            'response'=> $request->response,
        ];

        //dd($data);

        ServiceRequestResponse::create($data);
        ServiceRequest::where('id',$request->id)->update(['request_filter_id'=>4]);

        return redirect()->route('service_requests.index');



    }
}

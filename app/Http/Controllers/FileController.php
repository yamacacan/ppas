<?php

namespace App\Http\Controllers;

use App\Events\NewDocUploadedEvent;
use App\Http\Requests\FileNameUpdateRequest;
use App\Http\Requests\FileRequest;
use App\Models\File;
use App\Models\FileCategory;
use App\Models\FileName;
use App\Models\Threat;
use App\Models\User;
use App\MYS\Datatable\DocumentDataTable;
use App\MYS\Datatable\RevisionDatatable;
use App\MYS\File\FileUpload;
use App\MYS\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $categories=FileCategory::all();
        return view('dokumanlar.add',compact('categories'));
    }
    public function insert(FileRequest $request)
    {
        $categories=FileCategory::all();
        $validated = $request->validated();
        if($validated)
        {
            $file_upload=new FileUpload($request->file,'BGYS_dokumanlar');
            $sonuc=$file_upload->upload($request->file_name,$request->file_category);
            $cvp='Hata';
            if (is_object($sonuc))
            {
                event(new NewDocUploadedEvent($sonuc));
                $cvp='Başarılı bir şekilde doküman yüklendi';

            }



        }
        return view('dokumanlar.add',compact('categories','cvp'));
    }
    public function get(Request $request)
    {
        $page=isset($request->page) && !empty($request->page) ? $request->page : 1;
        $shownumber=isset($request->shownumber) && !empty($request->shownumber) ? $request->shownumber : 10;
        $search=isset($request->search) && !empty($request->search) ? $request->search : 0;
        $file_category_id=isset($request->file_category_id) && !empty($request->file_category_id) ? $request->file_category_id : 0;
        $denetim_tablo = new DocumentDataTable($page,$shownumber,$file_category_id,$search);
        echo $denetim_tablo->datatable();
    }
    public function list(Request $request)
    {
        return view('dokumanlar.list');
    }

    public function download(Request $request)
    {
        return Storage::download($request->path);
    }

    public function upload(Request $request)
    {

        $file_upload=new FileUpload($request->file,'BGYS_dokumanlar');
        $file_upload->update_file($request->file_id,$request->file_name_id,$request->revision_explain);
    }
    public function versions(Request $request)
    {
        $files=File::where('file_name_id',$request->file_name_id)->where('status','revision')->where('deleted_at',null)->get();
        $html='';
        if(count($files)>0)
        {
            $html.='<ul class="list-group">';
            foreach ($files as  $s=>$file)
            {
                $rev=empty($file->revision_order) ? 'İlk Versiyon ' : 'REV.'.$file->revision_order;
                $doc_name=$file->file_name->title.'('.$rev.')';
                $html.='<a href="'.route('dokuman.download',['path'=>$file->url]).'">'.++$s.' ) '.$doc_name.'</a>';
            }
            $html.='</ul>';
        }
        echo $html;
    }
    public function getversions(Request $request)
    {
        $page=isset($request->page) && !empty($request->page) ? $request->page : 1;
        $shownumber=isset($request->shownumber) && !empty($request->shownumber) ? $request->shownumber : 5;
        $file_name_id=isset($request->file_name_id) && !empty($request->file_name_id) ? $request->file_name_id : 0;
        $denetim_tablo = new RevisionDatatable($page,$shownumber,$file_name_id);
        echo $denetim_tablo->datatable();
    }

    public function getfilename(Request $request)
    {
        $filename=FileName::find($request->file_name_id);
        echo isset($filename->title) ? $filename->title :'';
    }
    public function updatefilename(FileNameUpdateRequest $request)
    {
        if($request->validated())
        {
            $filename=FileName::find($request->file_name_id);
            if(isset($filename->id) )
            {
                $filename->title=Helper::title($request->new_file_name);
                $filename->save();
                echo 'Doküman İsmi Başarıyla güncellendi';
            }
            else
            {
                echo 'Hata!';
            }

        }


    }
    public function test()
    {

        $user=Threat::find(3);

        foreach ($user->clauses as $c)
        {
            dd($c->id);
        }
    }

}

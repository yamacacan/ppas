<?php

namespace App\MYS\File;
use App\Models\File;
use App\Models\FileCategory;
use App\Models\FileName;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class FileUpload
{
    protected $permitted_file_types;
    protected $file_obj;
    protected $store_direction;
    protected $max_size;

    public function __construct($file_obj,$store_direction)
    {
        $this->permitted_file_types=['application/pdf','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/msword','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-excel'];
        $this->file_obj=$file_obj;
        $this->store_direction=$store_direction;
        $this->max_size=10*1024*1024;
    }

    public function is_permitted_file()
    {
        return in_array($this->file_obj->getMimeType(),$this->permitted_file_types);
    }

    public function is_filesize_ok()
    {
        return $this->file_obj->getSize() <= $this->max_size;
    }

    public function generate_name($file_name)
    {
        $ext=$this->file_obj->extension();

     $name=Str::slug($file_name,'_','tr').'.'.$ext;
     return $name;
    }

    public function upload($file_name,$file_cat)
    {
        if(!$this->is_permitted_file())
        {
            return 'Dosya türü word,pdf,xls,xlsx olmalıdır';
        }
        if(!$this->is_filesize_ok())
        {
            return 'Dosya boyutu 10 MB olmalıdır';
        }
        $file_name2=FileName::where('file_category_id',$file_cat)->orderBy('order', 'desc')->first();
        $order=1;
        if(isset($file_name2->order))
        {
            $order=$file_name2->order+1;
        }

        $file_Category=FileCategory::where('id',$file_cat)->first();
        if(isset($file_Category->prefix))
        {
            $file_n=FileName::where('title',$file_name)->first();
            if(!isset($file_n->id))
            {
                $saved_file_name=FileName::create([
                    'title'=>$file_name,
                    'order'=>$order,
                    'file_category_id'=>$file_cat,
                ]);
                if(isset($saved_file_name->id))
                {
                    $name=$this->generate_name($file_Category->prefix.'_'.$order);
                    $file_nme_uploaded= $this->file_obj->storeAs('BGYS_dokumanlar',$name);
                    if($file_nme_uploaded)
                    {
                        $sonuc=File::create([
                            'url'=>$file_nme_uploaded,
                            'file_name_id'=>$saved_file_name->id,
                            'status'=>'publish',
                            'uploaded_date'=>now(),
                            'uploaded_user'=>Auth::id(),
                            'revision_date'=>null,
                            'revision_order'=>null,
                            'deleted_at'=>null
                        ]);

                        return $sonuc;
                    }

                    return 'HATA!';
                }

                return 'HATA!';



            }
            else
            {
                return 'Doküman ismi kayıtlıdır';
            }
        }

        return 'HATA!';



    }

    public function update_file($file_id,$file_name_id,$revision_explain)
    {

        //$file_obj=File::orderBy('revision_order', 'desc')->first();
        $file_obj=File::where('id',$file_id)->where('deleted_at',null)->orderBy('revision_order', 'desc')->first();
        $file_name_obj=FileName::find($file_name_id);

        $rev_order=1;
        if(!empty($file_obj->revision_order))
        {
            $rev_order=$file_obj->revision_order+1;
        }
        $file_name=Str::lower($file_name_obj->category->prefix).'_'.$file_name_obj->order.'_REV_'.$rev_order.'.'.$this->file_obj->extension();
        $file_obj->status='revision';
        $file_obj->save();

        $file_nme_uploaded= $this->file_obj->storeAs('BGYS_dokumanlar',$file_name);

        if($file_nme_uploaded)
        {

            File::create([
                'url'=>$file_nme_uploaded,
                'file_name_id'=>$file_name_id,
                'status'=>'publish',
                'uploaded_date'=>now(),
                'uploaded_user'=>Auth::id(),
                'revision_date'=>now(),
                'revision_order'=>$rev_order,
                'revision_explain'=>$revision_explain,
                'deleted_at'=>null
            ]);
        }







    }

}
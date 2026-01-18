<?php

namespace App\MYS\Datatable;

use App\Models\FileCategory;
use Illuminate\Support\Facades\Storage;

class DocumentDataTable extends BaseDatatable
{
    private $file_category_id;
    private $search;

    public function __construct($page, $show_number,$file_category_id,$search)
    {

        parent::__construct($page, $show_number);
        $this->file_category_id=$file_category_id;
        $this->search=$search;
    }

    public function base_sql()
    {
        return 'SELECT file.id,file.url,file.status,file.revision_date,file.revision_explain,CONCAT(users.name," ",users.last_name) AS u_user,file.revision_order,file_name.id AS file_name_id,file_name.title,file_category.text,CONCAT(file_category.prefix,".",file_name.order) AS file_no FROM `file` LEFT JOIN file_name ON file_name.id=file.file_name_id LEFT JOIN file_category ON file_category.id=file_name.file_category_id LEFT JOIN users ON users.id=file.uploaded_user  ';
    }

    public function base_wheresql()
    {
       return ' WHERE file.deleted_at IS NULL AND file.status=? ';
    }

    public function base_wheresql_datas()
    {
        return ['publish'];
    }

    public function base_ordersql()
    {
        return ' ORDER BY file.id DESC ';
    }

    public function base_groupbysql()
    {
        return '';
    }

    public function search_wheresql()
    {
        $sql='';
       if(!empty($this->file_category_id))
       {
           $sql.=' AND file_category.id= ? ';
       }
        if(!empty($this->search))
        {
            $sql.=' AND file_name.title LIKE  ? ';
        }

        return $sql;
    }

    public function search_wheresql_datas()
    {
        $datas=$this->base_wheresql_datas();
        if(!empty($this->file_category_id))
        {
            $datas[]=$this->file_category_id;
        }
        if(!empty($this->search))
        {
            $datas[]='%'.$this->search.'%';
        }
        return $datas;
    }

    public function paginate_metot($page)
    {
       return 'get_documan('.$page.','.$this->getShowNumber().','.$this->file_category_id.',"'.$this->search.'")';
    }

    public function search_area()
    {
        $arama_val=empty($this->search) ? '' : $this->search;
        $file_val=empty($this->file_category_id) ? 0 : $this->file_category_id;
        $html='<div class="form-group mb-3">';
        $html.='<label>Doküman Kategorileri</label>';

        $html.='<select class="form-control" id="doc_cat">';
       $datas= FileCategory::all();
        $html.='<option value="0">Seçiniz</option>';
           foreach ($datas as $data)
           {    $ek='';
               if(!empty($file_val))
               {
                 $ek=$file_val==$data->id ? 'selected' :'';
               }
               $html.='<option value="'.$data->id.'" '.$ek.'>'.$data->text.'</option>';
           }
        $html.='</select>';
        $html.='</div>';
        $html.='<div class="form-group mb-3">';
        $html.='<label>Arama</label>';
        $html.='<input type="search" id="search" class="form-control" value="'.$arama_val.'">';
        $html.='</div>';
        $html.='<div class="form-group mb-3">';
        $mtt="get_documan(".$this->getPage().",".$this->getShowNumber().",$('#doc_cat').val(),$('#search').val())";
        $html.='<button type="button" onclick="'.$mtt.'" class="btn btn-primary">Ara</button>';
        $html.='</div>';
        return $html;
    }

    public function tablo_olustur()
    {
        $html=$this->search_area();
        $html.='<table class="table table-bordered table-striped">';
        $html.='<thead>';
        $html.='<tr><th>#</th><th>Doküman Kategorisi</th><th>Doküman No</th><th>Doküman Adı</th><th>Doküman</th><th>Revizyon No</th><th>Revizyon Açıklaması</th><th>Yükleyen Kullanıcı</th><th>Diğer Versiyonları</th>
<th>Yeniden Dosya Yükle</th>
</tr>';
        $html.='</thead>';
        $html.='<tbody>';
        if($this->get_datas()>0)
        {
            foreach ($this->get_datas() as $key=>$data)
                {
                    $sira=$this->calc_limit()+$key+1;
                    $rev_no='';
                    if(!empty($data->revision_order))
                    {
                        $rev_no='REV.'.$data->revision_order;
                    }

                    $html.='<tr>';
                    $html.='<td>'.$sira.'</td>';
                    $html.='<td>'.$data->text.'</td>';
                    $html.='<td>'.$data->file_no.'</td>';
                    $mtt_file_name="open_update_filename_modal(".$this->getPage().",".$this->getShowNumber().",".$this->file_category_id.",'".$this->search."',".$data->file_name_id.")";
                    $html.='<td><a href="javascript:void" onclick="'.$mtt_file_name.'">'.$data->title.'</a></td>';
                    $html.='<td><a href="'.route('dokuman.download',['path'=>$data->url]).'">İndir</a></td>';
                    $html.='<td>'.$rev_no.'</td>';
                    $html.='<td>'.$data->revision_explain.'</td>';
                    $html.='<td>'.$data->u_user.'</td>';
                    $mtt3="show_versions(".$data->file_name_id.",'".$data->title."')";
                    $html.='<td><button class="btn btn-secondary" onclick="'.$mtt3.'">Versiyonları Görüntüle</button> </td>';
                    //$mtt2="open_modal(".$this->getPage().",".$this->getShowNumber().",".$this->file_category_id.",".$this->search.",".$data->title.",".$data->id.",".$data->file_name_id.")";
                    //$html.='<td><button type="button" onclick="'.$mtt2.'">Yeniden Dosya Yükle</button> </td>';
                    $mtt2="file_upload(".$this->getPage().",".$this->getShowNumber().",".$this->file_category_id.",'".$this->search."','".$data->title."',".$data->id.",".$data->file_name_id.",$('#upload_file".$data->id."')[0].files,$('#explain".$data->id."').val())";
                    $html.='<td><form id="upload_form" enctype="multipart/form-data">';
                    $html.='<textarea id="explain'.$data->id.'" class="form-control" row="2" placeholder="Lütfen Değişiklik İçin Bir Açıklama Giriniz"></textarea><input type="file" id="upload_file'.$data->id.'" name="upload_file" class="form-control"><button type="button" onclick="'.$mtt2.'" class="btn btn-info" onclick="">Yükle</button>';
                    $html.='</form></td>';
                    $html.='</tr>';
                }
        }


        $html.='</tbody>';
        $html.='</table>';

        return $html;
    }
}
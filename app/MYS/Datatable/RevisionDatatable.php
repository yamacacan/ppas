<?php

namespace App\MYS\Datatable;

class RevisionDatatable extends BaseDatatable
{
    protected $file_name_id;
    public function __construct($page, $show_number,$file_name_id)
    {
        parent::__construct($page, $show_number);
        $this->file_name_id=$file_name_id;
    }

    public function base_sql()
    {
        return 'SELECT file.*,file_name.title FROM file LEFT JOIN file_name ON file_name.id=file.file_name_id ';
    }

    public function base_wheresql()
    {
        return ' WHERE file.file_name_id=? AND file.status=? AND file.deleted_at IS NULL ';
    }

    public function base_wheresql_datas()
    {
       return [$this->file_name_id,'revision'];
    }

    public function base_ordersql()
    {
       return ' ORDER BY file.revision_order ASC ';
    }

    public function base_groupbysql()
    {
        return '';
    }

    public function search_wheresql()
    {
        return '';
    }

    public function search_wheresql_datas()
    {
      return $this->base_wheresql_datas();
    }

    public function paginate_metot($page)
    {
        return "get_revisions(".$page.",".$this->getShowNumber().",".$this->file_name_id.")";
    }

    public function tablo_olustur()
    {
       $html='<table class="table table-bordered">';
        $html.='<thead>';
        $html.='<tr><th>#</th><th>Doküman</th><th>Revizyon Açıklaması</th></tr>';
        $html.='</thead>';
        $html.='<tbody>';
        if(count($this->get_datas())>0)
        {

            foreach ($this->get_datas() as $key=>$data)
            {
                $sira=$this->calc_limit()+$key+1;
                $rev=empty($data->revision_order) ? 'İlk Versiyon ' : 'REV.'.$data->revision_order;
                $doc_name=$data->title.'('.$rev.')';
                $html.='<tr><td>'.$sira.'</td><td><a href="'.route('dokuman.download',['path'=>$data->url]).'">'.$doc_name.'</a></td><td>'.$data->revision_explain.'</td></tr>';
            }
        }
        $html.='</tbody>';
        $html.='</table>';

        return $html;
    }
}
<?php

namespace App\MYS\Datatable;

use Illuminate\Support\Facades\DB;

abstract class  BaseDatatable
{
    private $page;
    private $show_number;
    private $artis;
    private $paginate_start;
    private $paginate_end;

    public function __construct($page,$show_number)
    {
        $this->page = $page;
        $this->show_number = $show_number;
        $this->artis=5;
        if(($this->artis*2)>=$this->get_totalpages())
        {
            $this->paginate_start=1;
            $this->paginate_end=$this->get_totalpages();
        }
        else
        {
            $this->paginate_start=$this->aktif_oncesi();
            $this->paginate_end=$this->aktif_sonrasi();
        }
    }
    abstract public function base_sql();
    abstract public function base_wheresql();
    abstract public function base_wheresql_datas();
    abstract public function base_ordersql();
    abstract public function base_groupbysql();
    abstract public function search_wheresql();
    abstract public function search_wheresql_datas();
    abstract public function paginate_metot($page);
    abstract public function tablo_olustur();

    public function sql()
    {
        return $this->base_sql().' '.$this->base_wheresql().' '.$this->search_wheresql().' '.$this->base_groupbysql().' '.$this->base_ordersql().' '.$this->limit_sql();
    }
    public function sql_total()
    {
        return $this->base_sql().' '.$this->base_wheresql().' '.$this->base_groupbysql();
    }
    public function sql_limitsiz()
    {
        return $this->base_sql().' '.$this->base_wheresql().' '.$this->search_wheresql().' '.$this->base_groupbysql().' '.$this->base_ordersql();
    }
    public function get_datas()
    {
      return   DB::select($this->sql(),$this->search_wheresql_datas());
    }

    public function get_totaldatas()
    {
        return count(DB::select($this->sql_limitsiz(),$this->search_wheresql_datas()));

    }
    public function get_fulldatas()
    {

        return count(DB::select($this->sql_total(),$this->base_wheresql_datas()));
    }


    public function previous_page()
    {
        if(($this->getPage()-1)>0)
        {
            return ($this->getPage()-1);
        }
        return 1;
    }
    public function next_page()
    {
        if(($this->getPage()+1)<=$this->get_totalpages())
        {
            return ($this->getPage()+1);
        }
        return $this->get_totalpages();
    }
    public function get_totalpages()
    {
        if($this->get_fulldatas()>0)
        {
            return ceil($this->get_totaldatas()/$this->getShowNumber());
        }
        return 0;
    }
    public function calc_limit()
    {
        if($this->get_fulldatas()>0)
        {
            return ($this->getPage()-1)*$this->getShowNumber();
        }
        return 0;
    }
    public function limit_sql()
    {
        return ' LIMIT '.$this->calc_limit().' , '.$this->getShowNumber();
    }

    public function getPage()
    {
        return $this->page;
    }

    public function setPage($page): void
    {
        $this->page = $page;
    }

    public function getShowNumber()
    {
        return $this->show_number;
    }

    public function setShowNumber($show_number): void
    {
        $this->show_number = $show_number;
    }

    public function sayfalama_olustur_eski()
    {
        $html='<div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups" style="margin-top:20px;">
  <div class="btn-group me-2" role="group" aria-label="First group">
    <button type="button" class="btn btn-primary" onclick="'.$this->paginate_metot(1).'">İlk</button>
    <button type="button" class="btn btn-primary" onclick="'.$this->paginate_metot($this->previous_page()).'">Önceki</button>
    <button type="button" class="btn btn-primary" onclick="'.$this->paginate_metot($this->next_page()).'">Sonraki</button>
    <button type="button" class="btn btn-primary" onclick="'.$this->paginate_metot($this->get_totalpages()).'">En Son</button>
  </div>
  </div>';

        return $html;
    }

    public function aktif_oncesi()
    {
        $html='';
        $fark=$this->getPage()-$this->artis;
        $baslangic= $fark>0 ? $fark : 1;
        $bitis=$this->getPage();
        for($i=$baslangic;$i<$bitis;$i++)
        {
            $active=$i==$this->getPage() ? 'active' :'';
            $html.='<li  class="page-item '.$active.' "><a class="page-link"  href="javascript:void;" onclick="'.$this->paginate_metot($i).'">'.$i.'</a></li>';
        }
        return $baslangic;
    }
    public function aktif_sonrasi()
    {
        $html='';
        $toplam=$this->getPage()+$this->artis;
        $bitis= $toplam<=$this->get_totalpages() ? $toplam : $this->get_totalpages();
        $baslangic=$this->getPage();
        for($i=$baslangic;$i<=$bitis;$i++)
        {
            $active=$i==$this->getPage() ? 'active' :'';
            $html.='<li  class="page-item '.$active.' "><a class="page-link"  href="javascript:void;" onclick="'.$this->paginate_metot($i).'">'.$i.'</a></li>';
        }
        return $bitis;
    }
    public function onlu_sayfalama()
    {
        $html='';
        for($i=1;$i<=$this->get_totalpages();$i++)
        {
            $active=$i==$this->getPage() ? 'active' :'';
            $html.='<li  class="page-item '.$active.' "><a class="page-link"  href="javascript:void;" onclick="'.$this->paginate_metot($i).'">'.$i.'</a></li>';
        }
        return $html;
    }
    public function sayfalama_olustur()
    {
        $html='<nav style="margin:10px;" style="border: 1px solid white;">
    <ul class="pagination justify-content-end" style="border: 1px solid #F4F4F4;">
    <li class="page-item">
     <a class="page-link" href="javascript:void;" onclick="'.$this->paginate_metot($this->previous_page()).'">Önceki</a>
    </li>';
        for($i=$this->paginate_start;$i<=$this->paginate_end;$i++)
        {
            $active=$i==$this->getPage() ? 'active' :'';
            $html.='<li  class="page-item '.$active.' "><a class="page-link"  href="javascript:void;" onclick="'.$this->paginate_metot($i).'">'.$i.'</a></li>';
        }
        $html.='<li  class="page-item">
      <a class="page-link" href="javascript:void;"  onclick="'.$this->paginate_metot($this->next_page()).'">Sonraki</a>
    </li>
  </ul>
</nav>';


        return $html;
    }

    public function datatable()
    {
        $html=$this->tablo_olustur();
        $html.='<div>';
        $html.='<div class="float-end">'.$this->sayfalama_olustur().'</div>';
        $html.='<div class="float-start>'.$this->tablobilgilendirme().'</div>';
        $html.='</div>';
        return $html;
    }

    public function tablobilgilendirme()
    {
        $last=($this->calc_limit()+$this->getShowNumber())<=$this->get_totaldatas() ? ($this->calc_limit()+$this->getShowNumber()) : $this->get_totaldatas();
        $html='
  <div class="dataTables_info" style="margin:10px">
   '.$this->get_totaldatas().' kayıttan '.($this->calc_limit()+1).'- '.$last.' arasındaki kayıtlar gösteriliyor.
  </div>
';
        return $html;
    }
}
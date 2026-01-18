<?php

namespace App\MYS\Datatable;

use App\Models\EntitySubGroup;
use App\Models\ProbabilityScore;
use App\Models\RiskManage;
use App\Models\RiskMatrix;
use App\MYS\Risk\RiskManagement;
use App\MYS\Risk\RiskMeter;

class ThreatDataTable extends BaseDatatable
{
    private $arama;
    private $entity_sub_group_id;
    private $entity_main_group_id;
public function __construct($page, $show_number,$arama,$entity_sub_group_id,$entity_main_group_id)
{
    $this->arama=$arama;
    $this->entity_sub_group_id=$entity_sub_group_id;
    $this->entity_main_group_id=$entity_main_group_id;
    parent::__construct($page, $show_number);
}

    public function base_sql()
    {
        return 'SELECT threats.id,threats.text,threats.vulnernability FROM entity_main_group_threat LEFT JOIN threats ON threats.id=entity_main_group_threat.threat_id ';
    }

    public function base_wheresql()
    {
       return ' WHERE entity_main_group_threat.entity_main_group_id = ? ';
    }

    public function base_wheresql_datas()
    {
       return [$this->entity_main_group_id];
    }

    public function base_ordersql()
    {
        return ' ORDER BY entity_main_group_threat.id DESC ';
    }

    public function base_groupbysql()
    {
        return '';
    }

    public function search_wheresql()
    {
        $sql='';

        if(!empty($this->arama))
        {
            $sql.=' AND (threats.text LIKE ? OR threats.vulnernability	 LIKE ? ) ';
        }
        return $sql;
    }

    public function search_wheresql_datas()
    {
        $datas=$this->base_wheresql_datas();

        if(!empty($this->arama))
        {
            $datas[]='%'.$this->arama.'%';
            $datas[]='%'.$this->arama.'%';
        }
        return $datas;
    }


    public function arama_alani()
    {
        $ek=!empty($this->arama) ? $this->arama :'';

        $metot="get_threat(1,10,$('#search_threat').val(),".$this->entity_sub_group_id.",".$this->entity_main_group_id.")";
        $html='<div style="margin:30px 0 30px 0">';
        $html.='<div class="float-end"><span style="padding-right:10px;font-size:14px;">Ara: </span><input type="search" autofocus style="border:1px solid #EFEFEF;height:38px" id="search_threat" value="" onfocusout="'.$metot.'"></div>';
        $html.='<div class="float-start"> Sayfada <select style="width: 75px;padding:10px;border:1px solid #F4F4F4;display: inline-block"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select> kayıt göster</div>';
        $html.='</div>';
        return $html;
    }

    public function paginate_metot($page)
    {
        return "get_threat(".$page.",".$this->getShowNumber().",'".$this->arama."',".$this->entity_sub_group_id.",".$this->entity_main_group_id.")";
    }

    public function tablo_olustur()
    {

        $probability_scores=ProbabilityScore::first();
        $html='
              
            <div class="table-responsive">
           
              <table class="table table-bordered table-striped">';
        $html.='<tr><th>#</th><th>Tehdit</th><th>Açıklık</th><th>Varlık Değeri</th><th>Etki Değeri</th><th>Kontrol Maddesi</th><th>Risk Olasılığı</th><th>Risk Seviye Değeri </th><th>Risk Seviyesi</th><th>Risk Puanı</th></tr>';
        if(count($this->get_datas())>0)
        {

            $sira=$this->calc_limit()+1;
            foreach ($this->get_datas() as $data)
            {

               $clauses=RiskManagement::get_clauses($data->id);
               $html_clauses='';
               foreach ($clauses as $key=>$clause)
               {
                   $mtt="show_clause_detail('".$clause->item_title."','".$clause->item_number."')";
                   if($key==0)
                   {
                       $html_clauses.='<a style="color:black" href="javascript:void" onclick="'.$mtt.'">'.$clause->item_number.'</a>';
                   }
                   else
                   {
                       $html_clauses.=' ,<a style="color:black" href="javascript:void" onclick="'.$mtt.'"> '.$clause->item_number.'</a>';
                   }

               }

                $risk_management=RiskManage::where('entity_sub_group_id',$this->entity_sub_group_id)->where('threat_id',$data->id)->where('deleted_at',null)->first();
                $prop_score=0;
                $prop_statu='';
                $color='white';
                $prop_val=0;
                $prop_code='';
                $entity_avg_score=\App\MYS\Risk\RiskManagement::calc_entity_score($this->entity_sub_group_id);
                $entity_effecient_score=\App\MYS\Risk\RiskManagement::calc_efficiency_score($this->entity_sub_group_id);
                if(isset($risk_management->probability_score))
                {

                    $prop_score=\App\MYS\Risk\RiskManagement::calc_risk_score($this->entity_sub_group_id,$risk_management->probability_score);
                    $prop_statu=\App\MYS\Risk\RiskManagement::calc_risk_status($this->entity_sub_group_id,$risk_management->probability_score)->text;
                    $prop_code=\App\MYS\Risk\RiskManagement::calc_risk_status($this->entity_sub_group_id,$risk_management->probability_score)->code;
                    $color=\App\MYS\Risk\RiskManagement::calc_risk_status($this->entity_sub_group_id,$risk_management->probability_score)->color;
                    $prop_val=$risk_management->probability_score;
                }

              $html.='<tr style="background-color: '.$color.'">';
                $html.='<td>'.$sira++.'</td>';
                $html.='<td>'.$data->text.'</td>';
                $html.='<td>'.$data->vulnernability.'</td>';
                $html.='<td>'.number_format($entity_avg_score,2,',','.').'</td>';
                $html.='<td>'.number_format($entity_effecient_score,2,',','.').'</td>';
                $html.='<td>'.$html_clauses.'</td>';
                $mtt="risk_kaydet(".$this->getPage().",".$this->getShowNumber().",'".$this->arama."',$(this).val(),".$data->id.",".$this->entity_sub_group_id.",".$this->entity_main_group_id.")";
                $html.='<td><select onchange="'.$mtt.'" class="form-select">';
                if(!isset($risk_management->probability_score))
                {
                    $html.='<option value="0">Seçiniz</option>';
                }
                else
                {
                    $html.='<option value="-1">Riski Sil</option>';
                }
                for ($i=$probability_scores->min; $i<=$probability_scores->max;$i=$i+$probability_scores->increment)
                {
                    $ek= $i==$prop_val ? 'selected' :'';
                    $html.='<option value="'.$i.'" '.$ek.'>'.$i.'</option>';
                }
                $html.='</select> </td>';
                $html.='<td>'.number_format($prop_score,2,',','.').'</td>';
                $html.='<td>'.$prop_statu.'</td>';
                $html.='<td>'.$prop_code.'</td>';

                $html.='</tr>';

            }
        }
        $html.='</table></div>';

        return $html;
    }
}
<?php

class Ivory extends Giji_Old
{
  public $RGL_IVORY = [
     'M.hollow'      =>Data::RGL_MILL
    ,'Dead or Alive' =>Data::RGL_DEATH
    ,'Trouble Aliens'=>Data::RGL_TA
    ,'Mystery'       =>Data::RGL_MIST
    ];

  protected function fetch_name()
  {
    $name = $this->fetch->find('p.multicolumn_left',0)->plaintext;
    // $this->village->name = mb_ereg_replace("(.+)\r\n.+","\\1",$name);
    $this->village->name = preg_replace("/(.+)\r\n.+/","$1",$name);
  }
  protected function check_sprule()
  {
    //タブラの人狼以外ならDBから引く
    $rule= trim($this->fetch->find('dl.mes_text_report dt',1)->plaintext);
    if(strpos($rule,"Lupus in Tabula") === false)
    {
      if(array_key_exists($rule,$this->RGL_IVORY))
      {
        $this->village->rglid = $this->RGL_IVORY[$rule];
      }
      else
      {
        $this->output_comment("undefined",__FUNCTION__,$rule);
      }
    }
    else if(preg_match("/秘話/",$this->village->name))
    {
      $this->village->rglid = Data::RGL_SECRET;
    }
  }
  protected function fetch_rp()
  {
    $rp = trim($this->fetch->find('dl.mes_text_report dt',0)->plaintext);
    // $rp = mb_ereg_replace('文章セット：「(.+)」','\\1',$rp);
    $rp = preg_replace("/文章セット：「(.+)」/","$1",$rp);
    $this->village->rp = $rp;
    if(!isset($this->syswords[$rp]))
    {
      $this->fetch_sysword($rp);
    }
  }
}

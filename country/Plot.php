<?php

class Plot extends Giji_Old
{
  protected function check_sprule()
  {
    $rule= trim($this->fetch->find('dl.mes_text_report dt',1)->plaintext);
    if(empty($rule))
    {
      //陰謀に集う胡蝶
      $this->village->rp = trim($this->fetch->find('dl.mes_text_report dt',0)->plaintext);
    }
    else if(strpos($rule,'タブラの人狼') === false)
    {
      //タブラの人狼以外ならDBから引く
      $sql = "SELECT id FROM regulation where name='$rule'";
      $stmt = $this->db->query($sql);
      if($stmt === false)
      {
        $this->output_comment('undefined',__FUNCTION__,$rule);
      }
      else
      {
        $stmt = $stmt->fetch();
        $this->village->rglid = (int)$stmt['id'];
      }
    }
    else if(preg_match("/秘話/",$this->village->name))
    {
      $this->village->rglid = Data::RGL_SECRET;
    }
  }
  protected function fetch_rp()
  {
    $this->check_sprule();
    //既に陰謀に集う胡蝶が入っているならスキップ
    if(empty($this->village->rp))
    {
      $rp = trim($this->fetch->find('dl.mes_text_report dt',0)->plaintext);
      $this->village->rp = $rp;
    }
    if(!isset($GLOBALS['syswords'][$this->village->rp]))
    {
      $this->fetch_sysword($this->village->rp);
    }
  }
}

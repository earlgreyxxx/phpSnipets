<?php
/*************************************************************************************
SQLServer名前付きインスタンスの情報を得る。
File: query_sqlbrowser.php
*************************************************************************************/
function querySQLServerNamedInstance($host,$instanceName,$queryPort = 1434)
{
  static $timeout = 5;
  $fp = fsockopen('udp://'.$host,$queryPort,$errno,$errstr,$timeout);
  if($fp === false)
    throw new RuntimeException($errstr);
 
  $str = sprintf('%s%s%s',hex2bin('04'),$instanceName,hex2bin('00'));
 
  if(false === fwrite($fp,$str,strlen($str)))
    throw new RuntimeException(_('failed to fwrite to resouce pointer'));
 
  $rv = fread($fp,1500);
  fclose($fp);
 
  $head = substr($rv,0,1);
  list(,$len) = unpack('v',substr($rv,1,2));
  $data = explode(';',substr($rv,3,$len));
  $rv = array();
  $i = 0;
  while(array_key_exists($i,$data) && array_key_exists($i+1,$data))
  {
    if(!empty($data[$i]))
      $rv[$data[$i]] = $data[$i+1];
 
    $i+=2;
  }
 
  return $rv;
}

<?php
/**
* @author eric.quinton
*/
$smarty->assign("version",$APPLI_version);
$smarty->assign("versiondate",$APPLI_versiondate);
$smarty->assign("corps","apropos_".$language.".tpl");
$message->set( $LANG["menu"][9]);
?>
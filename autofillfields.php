<?php
@ini_set("display_errors","1");
@ini_set("display_startup_errors","1");

require_once("include/dbcommon.php");
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT"); 


$mainTable = postvalue('mainTable');
$pageType = postvalue('pageType');

if (!checkTableName($mainTable))
	exit(0);
require_once("include/".$mainTable."_variables.php");

$gSettings = new ProjectSettings($strTableName, $pageType);

$mainField = postvalue('mainField');
$linkFieldName = postvalue('linkField');

if( $strTableName != "users" )
{
	if(!isLogged())  
		return;	
	
	if(!CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Edit") && !CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Add") && !CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Search")) 
		return;
}
else 
{
	$checkResult = true;
	if($mainField=="username")
		$checkResult=false;

	if($mainField=="password")
		$checkResult=false;

	if($checkResult)
	{
		if(!isLogged()) 
			return;
			
		if(!CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Edit") && !CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Add") && !CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Search"))
			return;
	}
}
$autoCompleteFields = array();

if($strTableName == "employee" && $mainField == "golongan_id")
{
	$autoCompleteFields[] = array('masterF'=>"gaji_pokok", 'lookupF'=>"gaji_pokok");
	$lookupTable = "golongan";
	$lookupConnection = $cman->byTable( $lookupTable );
	if( !$lookupConnection )
	{
		$connId = $gSettings->getNotProjectLookupTableConnId( $mainField );
		$lookupConnection = strlen( $connId ) ? $cman->byId( $connId ) : $cman->getDefault();
	}
}
if($strTableName == "employee" && $mainField == "id_jabatan")
{
	$autoCompleteFields[] = array('masterF'=>"nama_jabatan", 'lookupF'=>"nama_jabatan");
	$autoCompleteFields[] = array('masterF'=>"tnj_jabatan", 'lookupF'=>"tnj_jabatan");
	$autoCompleteFields[] = array('masterF'=>"tnj_transport", 'lookupF'=>"tnj_transport");
	$autoCompleteFields[] = array('masterF'=>"tnj_luarkota", 'lookupF'=>"tnj_luarkota");
	$autoCompleteFields[] = array('masterF'=>"uang_makan", 'lookupF'=>"uang_makan");
	$autoCompleteFields[] = array('masterF'=>"uang_pulsa", 'lookupF'=>"uang_pulsa");
	$autoCompleteFields[] = array('masterF'=>"sewa_motor", 'lookupF'=>"sewa_motor");
	$lookupTable = "jabatan";
	$lookupConnection = $cman->byTable( $lookupTable );
	if( !$lookupConnection )
	{
		$connId = $gSettings->getNotProjectLookupTableConnId( $mainField );
		$lookupConnection = strlen( $connId ) ? $cman->byId( $connId ) : $cman->getDefault();
	}
}
if($strTableName == "employee" && $mainField == "id_departemen")
{
	$autoCompleteFields[] = array('masterF'=>"nama_departemen", 'lookupF'=>"nama_departemen");
	$lookupTable = "departemen";
	$lookupConnection = $cman->byTable( $lookupTable );
	if( !$lookupConnection )
	{
		$connId = $gSettings->getNotProjectLookupTableConnId( $mainField );
		$lookupConnection = strlen( $connId ) ? $cman->byId( $connId ) : $cman->getDefault();
	}
}
if($strTableName == "employee" && $mainField == "area_id")
{
	$autoCompleteFields[] = array('masterF'=>"area_name", 'lookupF'=>"area_name");
	$lookupTable = "area";
	$lookupConnection = $cman->byTable( $lookupTable );
	if( !$lookupConnection )
	{
		$connId = $gSettings->getNotProjectLookupTableConnId( $mainField );
		$lookupConnection = strlen( $connId ) ? $cman->byId( $connId ) : $cman->getDefault();
	}
}
if($strTableName == "payroll" && $mainField == "nip")
{
	$autoCompleteFields[] = array('masterF'=>"nama_karyawan", 'lookupF'=>"nama_karyawan");
	$autoCompleteFields[] = array('masterF'=>"gaji_pokok", 'lookupF'=>"gaji_pokok");
	$autoCompleteFields[] = array('masterF'=>"tnj_jabatan", 'lookupF'=>"tnj_jabatan");
	$autoCompleteFields[] = array('masterF'=>"tnj_transport", 'lookupF'=>"tnj_transport");
	$autoCompleteFields[] = array('masterF'=>"tnj_luarkota", 'lookupF'=>"tnj_luarkota");
	$autoCompleteFields[] = array('masterF'=>"uang_makan", 'lookupF'=>"uang_makan");
	$autoCompleteFields[] = array('masterF'=>"uang_pulsa", 'lookupF'=>"uang_pulsa");
	$autoCompleteFields[] = array('masterF'=>"sewa_motor", 'lookupF'=>"sewa_motor");
	$lookupTable = "employee";
	$lookupConnection = $cman->byTable( $lookupTable );
	if( !$lookupConnection )
	{
		$connId = $gSettings->getNotProjectLookupTableConnId( $mainField );
		$lookupConnection = strlen( $connId ) ? $cman->byId( $connId ) : $cman->getDefault();
	}
}
if($strTableName == "Export TXT" && $mainField == "periode_id")
{
	$autoCompleteFields[] = array('masterF'=>"startday", 'lookupF'=>"tgl_awal");
	$autoCompleteFields[] = array('masterF'=>"endday", 'lookupF'=>"tgl_akhir");
	$autoCompleteFields[] = array('masterF'=>"date", 'lookupF'=>"tgl_efektif");
	$lookupTable = "periode";
	$lookupConnection = $cman->byTable( $lookupTable );
	if( !$lookupConnection )
	{
		$connId = $gSettings->getNotProjectLookupTableConnId( $mainField );
		$lookupConnection = strlen( $connId ) ? $cman->byId( $connId ) : $cman->getDefault();
	}
}
if($strTableName == "Export TXT" && $mainField == "nip")
{
	$autoCompleteFields[] = array('masterF'=>"nama_karyawan", 'lookupF'=>"nama_karyawan");
	$autoCompleteFields[] = array('masterF'=>"gaji_pokok", 'lookupF'=>"gaji_pokok");
	$autoCompleteFields[] = array('masterF'=>"tnj_jabatan", 'lookupF'=>"tnj_jabatan");
	$autoCompleteFields[] = array('masterF'=>"tnj_transport", 'lookupF'=>"tnj_transport");
	$autoCompleteFields[] = array('masterF'=>"tnj_luarkota", 'lookupF'=>"tnj_luarkota");
	$autoCompleteFields[] = array('masterF'=>"uang_makan", 'lookupF'=>"uang_makan");
	$autoCompleteFields[] = array('masterF'=>"uang_pulsa", 'lookupF'=>"uang_pulsa");
	$autoCompleteFields[] = array('masterF'=>"sewa_motor", 'lookupF'=>"sewa_motor");
	$lookupTable = "employee";
	$lookupConnection = $cman->byTable( $lookupTable );
	if( !$lookupConnection )
	{
		$connId = $gSettings->getNotProjectLookupTableConnId( $mainField );
		$lookupConnection = strlen( $connId ) ? $cman->byId( $connId ) : $cman->getDefault();
	}
}
if($strTableName == "Rekap Gaji" && $mainField == "nip")
{
	$autoCompleteFields[] = array('masterF'=>"nama_karyawan", 'lookupF'=>"nama_karyawan");
	$autoCompleteFields[] = array('masterF'=>"gaji_pokok", 'lookupF'=>"gaji_pokok");
	$autoCompleteFields[] = array('masterF'=>"tnj_jabatan", 'lookupF'=>"tnj_jabatan");
	$autoCompleteFields[] = array('masterF'=>"tnj_transport", 'lookupF'=>"tnj_transport");
	$autoCompleteFields[] = array('masterF'=>"tnj_luarkota", 'lookupF'=>"tnj_luarkota");
	$autoCompleteFields[] = array('masterF'=>"uang_makan", 'lookupF'=>"uang_makan");
	$autoCompleteFields[] = array('masterF'=>"uang_pulsa", 'lookupF'=>"uang_pulsa");
	$autoCompleteFields[] = array('masterF'=>"sewa_motor", 'lookupF'=>"sewa_motor");
	$lookupTable = "employee";
	$lookupConnection = $cman->byTable( $lookupTable );
	if( !$lookupConnection )
	{
		$connId = $gSettings->getNotProjectLookupTableConnId( $mainField );
		$lookupConnection = strlen( $connId ) ? $cman->byId( $connId ) : $cman->getDefault();
	}
}

$nLookupType = $gSettings->getLookupType($mainField);
$cipherer = new RunnerCipherer($nLookupType == LT_QUERY ? $lookupTable : $strTableName);
$linkFieldVal = postvalue('linkFieldVal');
$linkFieldVal = $cipherer->MakeDBValue($nLookupType == LT_QUERY ? $linkFieldName : $mainField, $linkFieldVal, "", true);
$strLookupWhere = GetLWWhere($mainField, $pageType, $strTableName);

if($nLookupType == LT_QUERY)
{
	$lookupSettings = new ProjectSettings($lookupTable, $pageType);
	$lookupQueryObj = $lookupSettings->getSQLQuery();
	$lookupQueryObj->ReplaceFieldsWithDummies($lookupSettings->getBinaryFieldsIndices());
	$strWhere = whereAdd($lookupQueryObj->m_where->toSql($lookupQueryObj), 
		RunnerPage::_getFieldSQLDecrypt( $linkFieldName, $lookupConnection, $lookupSettings, $cipherer ).'='.$linkFieldVal);
	$strWhere = whereAdd($strWhere, $strLookupWhere);
	$strSQL = $lookupQueryObj->toSql(whereAdd($lookupQueryObj->m_where->toSql($lookupQueryObj), $strWhere));
}
else
{
	$strSQL = 'SELECT ';
	for($i=0; $i<count($autoCompleteFields); $i++)
	{
		$strSQL .= $lookupConnection->addFieldWrappers( $autoCompleteFields[$i]['lookupF'] ).', ';
	}
	$strSQL = substr($strSQL, 0, strlen($strSQL)-2);
	
	$strSQL .= " FROM ".$lookupConnection->addTableWrappers($lookupTable);
	$linkFieldName = $cipherer->GetLookupFieldName( $lookupConnection->addFieldWrappers($linkFieldName), $mainField );
	$strWhere = $linkFieldName.'='.$linkFieldVal;
	$strWhere = whereAdd($strWhere, $strLookupWhere);
	$strSQL .= " WHERE ".$strWhere;
}

$row = $lookupConnection->query( $strSQL )->fetchAssoc();
if($nLookupType == LT_QUERY)
	$row =  $cipherer->DecryptFetchedArray( $row );

$lookupConnection->close();	
	
if( !$row )
	$row = array($mainField=>'');
	
echo printJSON(array('success'=>true, 'data'=>$row));
exit();
?>
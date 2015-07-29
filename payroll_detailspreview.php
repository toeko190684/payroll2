<?php
@ini_set("display_errors","1");
@ini_set("display_startup_errors","1");

require_once("include/dbcommon.php");
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT"); 

require_once("include/payroll_variables.php");

$mode = postvalue("mode");

if(!isLogged())
{ 
	return;
}
if(!CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Search"))
{
	return;
}

require_once("classes/searchclause.php");

$cipherer = new RunnerCipherer($strTableName);

require_once('include/xtempl.php');
$xt = new Xtempl();





$layout = new TLayout("detailspreview", "Office1City1", "MobileCity1");
$layout->version = 2;
$layout->blocks["bare"] = array();
$layout->containers["dcount"] = array();
$layout->container_properties["dcount"] = array(  );
$layout->containers["dcount"][] = array("name"=>"detailspreviewheader", 
	"block"=>"", "substyle"=>1  );

$layout->containers["dcount"][] = array("name"=>"detailspreviewdetailsfount", 
	"block"=>"", "substyle"=>1  );

$layout->containers["dcount"][] = array("name"=>"detailspreviewdispfirst", 
	"block"=>"display_first", "substyle"=>1  );

$layout->skins["dcount"] = "empty";

$layout->blocks["bare"][] = "dcount";
$layout->containers["detailspreviewgrid"] = array();
$layout->container_properties["detailspreviewgrid"] = array(  );
$layout->containers["detailspreviewgrid"][] = array("name"=>"detailspreviewfields", 
	"block"=>"details_data", "substyle"=>1  );

$layout->skins["detailspreviewgrid"] = "grid";

$layout->blocks["bare"][] = "detailspreviewgrid";
$page_layouts["payroll_detailspreview"] = $layout;

$layout->skinsparams = array();
$layout->skinsparams["empty"] = array("button"=>"button2");
$layout->skinsparams["menu"] = array("button"=>"button1");
$layout->skinsparams["hmenu"] = array("button"=>"button1");
$layout->skinsparams["undermenu"] = array("button"=>"button1");
$layout->skinsparams["fields"] = array("button"=>"button1");
$layout->skinsparams["form"] = array("button"=>"button1");
$layout->skinsparams["1"] = array("button"=>"button1");
$layout->skinsparams["2"] = array("button"=>"button1");
$layout->skinsparams["3"] = array("button"=>"button1");



$recordsCounter = 0;

//	process masterkey value
$mastertable = postvalue("mastertable");
$masterKeys = my_json_decode(postvalue("masterKeys"));
if($mastertable != "")
{
	$_SESSION[$strTableName."_mastertable"]=$mastertable;
//	copy keys to session
	$i = 1;
	if(is_array($masterKeys) && count($masterKeys) > 0)
	{
		while(array_key_exists ("masterkey".$i, $masterKeys))
		{
			$_SESSION[$strTableName."_masterkey".$i] = $masterKeys["masterkey".$i];
			$i++;
		}
	}
	if(isset($_SESSION[$strTableName."_masterkey".$i]))
		unset($_SESSION[$strTableName."_masterkey".$i]);
}
else
	$mastertable = $_SESSION[$strTableName."_mastertable"];

$params = array();
$params['id'] = 1;
$params['xt'] = &$xt;
$params['tName'] = $strTableName;
$params['pageType'] = "detailspreview";
$pageObject = new RunnerPage($params);

if($mastertable == "periode")
{
	$where = "";
		$where .= $pageObject->getFieldSQLDecrypt("periode_id") . "=" . make_db_value("periode_id",$_SESSION[$strTableName."_masterkey1"]);
}
if($mastertable == "employee")
{
	$where = "";
		$where .= $pageObject->getFieldSQLDecrypt("nip") . "=" . make_db_value("nip",$_SESSION[$strTableName."_masterkey1"]);
}

$str = SecuritySQL("Search");
if(strlen($str))
	$where.=" and ".$str;
$strSQL = $gQuery->gSQLWhere($where);

$strSQL.=" ".$gstrOrderBy;

$rowcount = $gQuery->gSQLRowCount($where, $pageObject->connection);
$xt->assign("row_count",$rowcount);
if($rowcount) 
{
	$xt->assign("details_data",true);

	$display_count = 10;
	if($mode == "inline")
		$display_count*=2;
		
	if($rowcount>$display_count+2)
	{
		$xt->assign("display_first",true);
		$xt->assign("display_count",$display_count);
	}
	else
		$display_count = $rowcount;

	$rowinfo = array();
	
	require_once getabspath('classes/controls/ViewControlsContainer.php');
	$pSet = new ProjectSettings($strTableName, PAGE_LIST);
	$viewContainer = new ViewControlsContainer($pSet, PAGE_LIST);
	$viewContainer->isDetailsPreview = true;

	$b = true;
	$qResult = $pageObject->connection->query( $strSQL );
	$data = $cipherer->DecryptFetchedArray( $qResult->fetchAssoc() );
	while($data && $recordsCounter<$display_count) {
		$recordsCounter++;
		$row = array();
		$keylink = "";
		$keylink.="&key1=".runner_htmlspecialchars(rawurlencode(@$data["payroll_id"]));
	
	
	//	payroll_id - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("payroll_id", $data, $keylink);
			$row["payroll_id_value"] = $value;
			$format = $pSet->getViewFormat("payroll_id");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("payroll_id")))
				$class = ' rnr-field-number';
			$row["payroll_id_class"] = $class;
	//	nip - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("nip", $data, $keylink);
			$row["nip_value"] = $value;
			$format = $pSet->getViewFormat("nip");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("nip")))
				$class = ' rnr-field-number';
			$row["nip_class"] = $class;
	//	nama_karyawan - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("nama_karyawan", $data, $keylink);
			$row["nama_karyawan_value"] = $value;
			$format = $pSet->getViewFormat("nama_karyawan");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("nama_karyawan")))
				$class = ' rnr-field-number';
			$row["nama_karyawan_class"] = $class;
	//	jml_masuk - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("jml_masuk", $data, $keylink);
			$row["jml_masuk_value"] = $value;
			$format = $pSet->getViewFormat("jml_masuk");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("jml_masuk")))
				$class = ' rnr-field-number';
			$row["jml_masuk_class"] = $class;
	//	startday - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("startday", $data, $keylink);
			$row["startday_value"] = $value;
			$format = $pSet->getViewFormat("startday");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("startday")))
				$class = ' rnr-field-number';
			$row["startday_class"] = $class;
	//	endday - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("endday", $data, $keylink);
			$row["endday_value"] = $value;
			$format = $pSet->getViewFormat("endday");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("endday")))
				$class = ' rnr-field-number';
			$row["endday_class"] = $class;
	//	date - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("date", $data, $keylink);
			$row["date_value"] = $value;
			$format = $pSet->getViewFormat("date");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("date")))
				$class = ' rnr-field-number';
			$row["date_class"] = $class;
	//	periode_id - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("periode_id", $data, $keylink);
			$row["periode_id_value"] = $value;
			$format = $pSet->getViewFormat("periode_id");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("periode_id")))
				$class = ' rnr-field-number';
			$row["periode_id_class"] = $class;
	//	gaji_pokok - Currency
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("gaji_pokok", $data, $keylink);
			$row["gaji_pokok_value"] = $value;
			$format = $pSet->getViewFormat("gaji_pokok");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("gaji_pokok")))
				$class = ' rnr-field-number';
			$row["gaji_pokok_class"] = $class;
	//	tnj_jabatan - Currency
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("tnj_jabatan", $data, $keylink);
			$row["tnj_jabatan_value"] = $value;
			$format = $pSet->getViewFormat("tnj_jabatan");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("tnj_jabatan")))
				$class = ' rnr-field-number';
			$row["tnj_jabatan_class"] = $class;
	//	tnj_transport - Currency
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("tnj_transport", $data, $keylink);
			$row["tnj_transport_value"] = $value;
			$format = $pSet->getViewFormat("tnj_transport");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("tnj_transport")))
				$class = ' rnr-field-number';
			$row["tnj_transport_class"] = $class;
	//	tnj_luarkota - Currency
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("tnj_luarkota", $data, $keylink);
			$row["tnj_luarkota_value"] = $value;
			$format = $pSet->getViewFormat("tnj_luarkota");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("tnj_luarkota")))
				$class = ' rnr-field-number';
			$row["tnj_luarkota_class"] = $class;
	//	uang_makan - Currency
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("uang_makan", $data, $keylink);
			$row["uang_makan_value"] = $value;
			$format = $pSet->getViewFormat("uang_makan");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("uang_makan")))
				$class = ' rnr-field-number';
			$row["uang_makan_class"] = $class;
	//	uang_pulsa - Currency
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("uang_pulsa", $data, $keylink);
			$row["uang_pulsa_value"] = $value;
			$format = $pSet->getViewFormat("uang_pulsa");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("uang_pulsa")))
				$class = ' rnr-field-number';
			$row["uang_pulsa_class"] = $class;
	//	sewa_motor - Currency
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("sewa_motor", $data, $keylink);
			$row["sewa_motor_value"] = $value;
			$format = $pSet->getViewFormat("sewa_motor");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("sewa_motor")))
				$class = ' rnr-field-number';
			$row["sewa_motor_class"] = $class;
	//	incentif - Currency
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("incentif", $data, $keylink);
			$row["incentif_value"] = $value;
			$format = $pSet->getViewFormat("incentif");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("incentif")))
				$class = ' rnr-field-number';
			$row["incentif_class"] = $class;
	//	lain_lain - Currency
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("lain_lain", $data, $keylink);
			$row["lain_lain_value"] = $value;
			$format = $pSet->getViewFormat("lain_lain");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("lain_lain")))
				$class = ' rnr-field-number';
			$row["lain_lain_class"] = $class;
	//	potongan - Currency
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("potongan", $data, $keylink);
			$row["potongan_value"] = $value;
			$format = $pSet->getViewFormat("potongan");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("potongan")))
				$class = ' rnr-field-number';
			$row["potongan_class"] = $class;
	//	gaji_kotor - Currency
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("gaji_kotor", $data, $keylink);
			$row["gaji_kotor_value"] = $value;
			$format = $pSet->getViewFormat("gaji_kotor");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("gaji_kotor")))
				$class = ' rnr-field-number';
			$row["gaji_kotor_class"] = $class;
	//	gaji_bersih - Currency
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("gaji_bersih", $data, $keylink);
			$row["gaji_bersih_value"] = $value;
			$format = $pSet->getViewFormat("gaji_bersih");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("gaji_bersih")))
				$class = ' rnr-field-number';
			$row["gaji_bersih_class"] = $class;
		$rowinfo[] = $row;
		if ($b) {
			$rowinfo2[] = $row;
			$b = false;
		}
		$data = $cipherer->DecryptFetchedArray( $qResult->fetchAssoc() );
	}
	$xt->assign_loopsection("details_row",$rowinfo);
	$xt->assign_loopsection("details_row_header",$rowinfo2); // assign class for header
}
$returnJSON = array("success" => true);
$xt->load_template(GetTemplateName("payroll", "detailspreview"));
$returnJSON["body"] = $xt->fetch_loaded();

if($mode!="inline")
{
	$returnJSON["counter"] = postvalue("counter");
	$layout = GetPageLayout(GoodFieldName($strTableName), 'detailspreview');
	if($layout)
	{
		foreach($layout->getCSSFiles(isRTL(), isMobile()) as $css)
		{
			$returnJSON['CSSFiles'][] = $css;
		}
	}	
}	

echo printJSON($returnJSON);
exit();
?>
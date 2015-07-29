<?php
@ini_set("display_errors","1");
@ini_set("display_startup_errors","1");

require_once("include/dbcommon.php");
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT"); 

require_once("include/employee_variables.php");

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
$page_layouts["employee_detailspreview"] = $layout;

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

if($mastertable == "area")
{
	$where = "";
		$where .= $pageObject->getFieldSQLDecrypt("area_id") . "=" . make_db_value("area_id",$_SESSION[$strTableName."_masterkey1"]);
}
if($mastertable == "departemen")
{
	$where = "";
		$where .= $pageObject->getFieldSQLDecrypt("id_departemen") . "=" . make_db_value("id_departemen",$_SESSION[$strTableName."_masterkey1"]);
}
if($mastertable == "jabatan")
{
	$where = "";
		$where .= $pageObject->getFieldSQLDecrypt("id_jabatan") . "=" . make_db_value("id_jabatan",$_SESSION[$strTableName."_masterkey1"]);
}
if($mastertable == "golongan")
{
	$where = "";
		$where .= $pageObject->getFieldSQLDecrypt("golongan_id") . "=" . make_db_value("golongan_id",$_SESSION[$strTableName."_masterkey1"]);
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

	$display_count = 5;
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
		$keylink.="&key1=".runner_htmlspecialchars(rawurlencode(@$data["nip"]));
	
	
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
	//	tempat_lahir - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("tempat_lahir", $data, $keylink);
			$row["tempat_lahir_value"] = $value;
			$format = $pSet->getViewFormat("tempat_lahir");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("tempat_lahir")))
				$class = ' rnr-field-number';
			$row["tempat_lahir_class"] = $class;
	//	tgl_lahir - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("tgl_lahir", $data, $keylink);
			$row["tgl_lahir_value"] = $value;
			$format = $pSet->getViewFormat("tgl_lahir");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("tgl_lahir")))
				$class = ' rnr-field-number';
			$row["tgl_lahir_class"] = $class;
	//	agama - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("agama", $data, $keylink);
			$row["agama_value"] = $value;
			$format = $pSet->getViewFormat("agama");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("agama")))
				$class = ' rnr-field-number';
			$row["agama_class"] = $class;
	//	alamat - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("alamat", $data, $keylink);
			$row["alamat_value"] = $value;
			$format = $pSet->getViewFormat("alamat");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("alamat")))
				$class = ' rnr-field-number';
			$row["alamat_class"] = $class;
	//	no_telp - Phone Number
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("no_telp", $data, $keylink);
			$row["no_telp_value"] = $value;
			$format = $pSet->getViewFormat("no_telp");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("no_telp")))
				$class = ' rnr-field-number';
			$row["no_telp_class"] = $class;
	//	no_ktp - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("no_ktp", $data, $keylink);
			$row["no_ktp_value"] = $value;
			$format = $pSet->getViewFormat("no_ktp");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("no_ktp")))
				$class = ' rnr-field-number';
			$row["no_ktp_class"] = $class;
	//	pendidikan_akhir - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("pendidikan_akhir", $data, $keylink);
			$row["pendidikan_akhir_value"] = $value;
			$format = $pSet->getViewFormat("pendidikan_akhir");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("pendidikan_akhir")))
				$class = ' rnr-field-number';
			$row["pendidikan_akhir_class"] = $class;
	//	tgl_masuk_kerja - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("tgl_masuk_kerja", $data, $keylink);
			$row["tgl_masuk_kerja_value"] = $value;
			$format = $pSet->getViewFormat("tgl_masuk_kerja");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("tgl_masuk_kerja")))
				$class = ' rnr-field-number';
			$row["tgl_masuk_kerja_class"] = $class;
	//	tgl_berhenti - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("tgl_berhenti", $data, $keylink);
			$row["tgl_berhenti_value"] = $value;
			$format = $pSet->getViewFormat("tgl_berhenti");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("tgl_berhenti")))
				$class = ' rnr-field-number';
			$row["tgl_berhenti_class"] = $class;
	//	jenis_kelamin - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("jenis_kelamin", $data, $keylink);
			$row["jenis_kelamin_value"] = $value;
			$format = $pSet->getViewFormat("jenis_kelamin");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("jenis_kelamin")))
				$class = ' rnr-field-number';
			$row["jenis_kelamin_class"] = $class;
	//	status_kawin - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("status_kawin", $data, $keylink);
			$row["status_kawin_value"] = $value;
			$format = $pSet->getViewFormat("status_kawin");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("status_kawin")))
				$class = ' rnr-field-number';
			$row["status_kawin_class"] = $class;
	//	no_jamsostek - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("no_jamsostek", $data, $keylink);
			$row["no_jamsostek_value"] = $value;
			$format = $pSet->getViewFormat("no_jamsostek");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("no_jamsostek")))
				$class = ' rnr-field-number';
			$row["no_jamsostek_class"] = $class;
	//	no_asuransi - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("no_asuransi", $data, $keylink);
			$row["no_asuransi_value"] = $value;
			$format = $pSet->getViewFormat("no_asuransi");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("no_asuransi")))
				$class = ' rnr-field-number';
			$row["no_asuransi_class"] = $class;
	//	id_jabatan - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("id_jabatan", $data, $keylink);
			$row["id_jabatan_value"] = $value;
			$format = $pSet->getViewFormat("id_jabatan");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("id_jabatan")))
				$class = ' rnr-field-number';
			$row["id_jabatan_class"] = $class;
	//	id_departemen - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("id_departemen", $data, $keylink);
			$row["id_departemen_value"] = $value;
			$format = $pSet->getViewFormat("id_departemen");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("id_departemen")))
				$class = ' rnr-field-number';
			$row["id_departemen_class"] = $class;
	//	area_id - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("area_id", $data, $keylink);
			$row["area_id_value"] = $value;
			$format = $pSet->getViewFormat("area_id");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("area_id")))
				$class = ' rnr-field-number';
			$row["area_id_class"] = $class;
	//	npwp - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("npwp", $data, $keylink);
			$row["npwp_value"] = $value;
			$format = $pSet->getViewFormat("npwp");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("npwp")))
				$class = ' rnr-field-number';
			$row["npwp_class"] = $class;
	//	tgl_daftarnpwp - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("tgl_daftarnpwp", $data, $keylink);
			$row["tgl_daftarnpwp_value"] = $value;
			$format = $pSet->getViewFormat("tgl_daftarnpwp");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("tgl_daftarnpwp")))
				$class = ' rnr-field-number';
			$row["tgl_daftarnpwp_class"] = $class;
	//	no_rek_bank - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("no_rek_bank", $data, $keylink);
			$row["no_rek_bank_value"] = $value;
			$format = $pSet->getViewFormat("no_rek_bank");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("no_rek_bank")))
				$class = ' rnr-field-number';
			$row["no_rek_bank_class"] = $class;
	//	nama_bank - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("nama_bank", $data, $keylink);
			$row["nama_bank_value"] = $value;
			$format = $pSet->getViewFormat("nama_bank");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("nama_bank")))
				$class = ' rnr-field-number';
			$row["nama_bank_class"] = $class;
	//	jumlah_anak - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("jumlah_anak", $data, $keylink);
			$row["jumlah_anak_value"] = $value;
			$format = $pSet->getViewFormat("jumlah_anak");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("jumlah_anak")))
				$class = ' rnr-field-number';
			$row["jumlah_anak_class"] = $class;
	//	foto - Database Image
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("foto", $data, $keylink);
			$row["foto_value"] = $value;
			$format = $pSet->getViewFormat("foto");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("foto")))
				$class = ' rnr-field-number';
			$row["foto_class"] = $class;
	//	nama_jabatan - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("nama_jabatan", $data, $keylink);
			$row["nama_jabatan_value"] = $value;
			$format = $pSet->getViewFormat("nama_jabatan");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("nama_jabatan")))
				$class = ' rnr-field-number';
			$row["nama_jabatan_class"] = $class;
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
	//	area_name - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("area_name", $data, $keylink);
			$row["area_name_value"] = $value;
			$format = $pSet->getViewFormat("area_name");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("area_name")))
				$class = ' rnr-field-number';
			$row["area_name_class"] = $class;
	//	nama_departemen - 
			$viewContainer->recId = $recordsCounter;
		    $value = $viewContainer->showDBValue("nama_departemen", $data, $keylink);
			$row["nama_departemen_value"] = $value;
			$format = $pSet->getViewFormat("nama_departemen");
			$class = "rnr-field-text";
			if($format==FORMAT_FILE) 
				$class = ' rnr-field-file'; 
			if($format==FORMAT_AUDIO)
				$class = ' rnr-field-audio';
			if($format==FORMAT_CHECKBOX)
				$class = ' rnr-field-checkbox';
			if($format==FORMAT_NUMBER || IsNumberType($pSet->getFieldType("nama_departemen")))
				$class = ' rnr-field-number';
			$row["nama_departemen_class"] = $class;
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
$xt->load_template(GetTemplateName("employee", "detailspreview"));
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
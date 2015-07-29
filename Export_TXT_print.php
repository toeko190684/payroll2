<?php
@ini_set("display_errors","1");
@ini_set("display_startup_errors","1");
require_once("include/dbcommon.php");
require_once('classes/searchclause.php');
require_once('include/xtempl.php');

if( @$_REQUEST["format"] != "excel" && @$_REQUEST["format"] != "word" ) 
	add_nocache_headers();

require_once("include/Export_TXT_variables.php");


if( !Security::processPageSecurity( $strTableName, 'P' ) )
	return;

	
require_once('classes/reportpage.php');
require_once('classes/reportprintpage.php');	
	



$layout = new TLayout("report_print", "Office1City1", "MobileCity1");
$layout->version = 2;
$layout->blocks["top"] = array();
$layout->skins["master"] = "empty";

$layout->blocks["top"][] = "master";
$layout->containers["pdf"] = array();
$layout->container_properties["pdf"] = array(  "print" => "none"  );
$layout->containers["pdf"][] = array("name"=>"printbuttons", 
	"block"=>"printbuttons", "substyle"=>1  );

$layout->skins["pdf"] = "empty";

$layout->blocks["top"][] = "pdf";
$layout->containers["grid"] = array();
$layout->container_properties["grid"] = array(  );
$layout->containers["grid"][] = array("name"=>"report_print", 
	"block"=>"", "substyle"=>1  );

$layout->skins["grid"] = "empty";

$layout->blocks["top"][] = "grid";
$page_layouts["Export_TXT_rprint"] = $layout;

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



$xt = new Xtempl();
$id = postvalue("id");
$id = $id != "" ? $id : 1;

// an array of ReportPrintPage constructor params
$params = array();
$params["id"] = $id;
$params["xt"] = &$xt;
$params["mode"] = REPORT_SIMPLE;
$params["tName"] = $strTableName;
$params["pageType"] = PAGE_RPRINT;

$params["allPagesMode"] = postvalue("all");
$params["pdfMode"] = postvalue("pdf");
$params["pdfContent"] = postvalue("htmlPdfContent");
$params["pdfWidth"] = postvalue("width");
$params["format"] = postvalue("format");



$pageObject = new ReportPrintPage($params);
$pageObject->init();

$pageObject->assignPDFFormatSettings( @$_REQUEST["format"], postvalue("exportPdf") );


$forExport = false;
if (@$_REQUEST["format"] == "excel")
{
	$forExport = "excel";
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment;Filename=Export_TXT.xls");
	echo "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">";
	echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$cCharset."\">";
}
else if (@$_REQUEST["format"] == "word")
{
	$forExport = "word";
	header("Content-Type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=Export_TXT.doc");
	echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$cCharset."\">";
}

if( @$_REQUEST["format"] )
	$xt->assign("stylesheetlink", false);
else
	$xt->assign("stylesheetlink", true);

	
// the table info params
$extraParams = $pageObject->getExtraReportParams();	
$extraParams['forExport'] = $forExport; 

$pageObject->setGoogleMapsParams( $extraParams['fieldsArr'] );
$pageObject->setReportData( $extraParams );


// add button events if exist
$pageObject->addButtonHandlers();
$pageObject->addCommonJs();

$xt->assign("divideintopages_block", true);


$pageObject->body["begin"] .= GetBaseScriptsForPage(false);
$pageObject->body['end'] = array();
AssignMethod($pageObject->body['end'], "assignBodyEnd", $pageObject); 

$xt->assignbyref("body", $pageObject->body);
$xt->assign("grid_block", true);
$xt->assign("grid_header", true);

$xt->assign("no_rek_bank_fieldheader", true);
$xt->assign("gaji_bersih_fieldheader", true);
$xt->assign("nip_fieldheader", true);
$xt->assign("nama_karyawan_fieldheader", true);
$xt->assign("id_departemen_fieldheader", true);



if( @$_REQUEST["format"] && $_REQUEST["format"] != "pdf" )
{
	//$pages[0]["page_summary"] = false;
	//$xt->assign_loopsection("pages", $pages);
	$xt->assign("pdflink_block", false);
	$xt->assign("divideintopages_block", false);
	$pageObject->body["begin"] = "";
	$pageObject->body["end"] = "";
	$xt->assignbyref("body", $pageObject->body);
}

if (@$_REQUEST["format"] !== "pdf")
	$xt->assign("bodyattrs", 'style="margin:10px;"');

$usePage2pdf = postvalue("pdf");

if( !$usePage2pdf )
{
	if(@$_REQUEST["format"] == "excel" || @$_REQUEST["format"] == "word") 
	{
		$xt->load_template($pageObject->templatefile);
		$contents = $pageObject->prepareWordOrExcelTemplate($xt->template);
		$xt->template = $contents;
		xt_process_template($xt, $xt->template);
	}
	else
	{
		$pageObject->display($pageObject->templatefile);		
	}
}
else
{
}

?>
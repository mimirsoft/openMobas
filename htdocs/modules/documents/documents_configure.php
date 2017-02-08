<?
include("../../../framework/framework_masterinclude.php");
require_once("../../../framework/classes/Document.class.php");
include("documents_include.php");
Framework::authenticate('Unlimited');//the two includes must be before the authentica, to supply the needed module name for authentication
$ACTION = 'NULL';
$doc_cat_name = '';
$doc_cat_priority = 0;
$doc_cat_is_active = 1;
$category_id = 'NULL';
$document_cat_edit = '';
if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    $doc_cat_name = $_POST['doc_cat_name'];
    $doc_cat_priority = $_POST['doc_cat_priority'];
    $doc_cat_is_active = $_POST['doc_cat_is_active'];
    $category_id = $_POST['category_id'];
    $document_cat_edit = $_POST['document_cat_edit'];
}
if ($ACTION == "Save Document Category")
{
    $category_id = Document::add_document_category($category_id, $doc_cat_name);
	Document::set_document_priority($category_id, $doc_cat_priority);
    Document::set_document_active($category_id, $doc_cat_is_active);
    $doc_cat_name = "";
	$doc_cat_priority = '';
    $doc_cat_is_active = 1;
    $category_id = 'NULL';
}
if ($ACTION == "Edit Document Category")
{
	$doc = Document::get_document_cat_by_id($document_cat_edit);
	$doc_cat_name = $doc['category_name'];
	$category_id = $doc['category_id'];
    $doc_cat_priority = $doc['category_priority'];
    $doc_cat_is_active = $doc['is_active'];
	
}
if ($ACTION == "Delete Document Category")
{
	Document::deleteDocumentCategory($document_cat_edit);
}
$docTypes = Document::getall_document_categories();

include("documents_configure.phtml");

?>

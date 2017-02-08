<?
include("../../../framework/framework_masterinclude.php");
include("documents_include.php");
require_once("../../../framework/classes/Document.class.php");
Framework::authenticate("Unlimited");//the two includes must be before the authentica, to supply the needed module name for authentication

$ACTION = '';
$files = '';
$date = date("Y-m-d");
$docdate_year = "";
$docdate_month = "";
$docdate_day = "";
foreach ($_POST as $key => $value)
{
	$$key = $value;
}
$document_date = $docdate_year."-".$docdate_month."-".$docdate_day;
if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "documents_module"))
{
    echo "Permission Denied to access documents module.";
    exit;
}

switch($ACTION)
{
    case "Upload Files":        
        if(Rbac_User::IsAllowedTo($USER->GetUserID(), "upload_documents", "documents_module"))
        {
            require_once("../../../framework/classes/Fileupload.class.php");
	        $upload = new fileupload();
    		// restrict filesize to xxx bytes (x.xxkb) 
    		$upload->set_max_filesize(50000000);
    		// limit types of files based on MIME type
    		// 'image' - accept any MIME type containing 'image' (.gif, .jpg, .png, .tif, etc...)
    		// 'image/gif' - only accept gif images
    		// 'image/gif, image/png' - only accept gif and png images
    		// 'image/gif, text' - accept gif images and any MIME type containing 'text'
    		//$upload->set_acceptable_types('pdf'); // comma separated string, or array
    		// Set mode to manage identically named files 
    		// 
    		// user uploads 'foo.gif', but that file already exists in the upload directory:
    		//    1 = (overwrite) overwrite existing file with uploaded foo.gif
    		//    2 = (rename) keep original foo.gif, upload new file but call it 'foo_copy1.gif'
    		//    3 = (do nothing) keep original foo.gif, do nothing with uploaded file, raise error
    		$upload->set_overwrite_mode(1);
    		
    		$filename = $upload->upload("userfile");
    		if ($filename) 
                    {
                        if(is_array($filename))
                        {
                            foreach($filename as $key => $file)
                            {
                                Document::linkfile("NULL", $date, $file, $document_name[$key]);
                                //
                            }
                        }
                        else
                        {
                            Document::linkfile("NULL", $date, $filename);
                        }
    		} 
                    else {
    			echo $upload->get_error();
    		}
        }

         break;
/*   case "Update Description":        
            include("../../../framework/filehandler.class.php");
            $file = new filehandler($file_id);
            $file->update_desc($fileDesc);    
        break;*/
    case "Remove Tag":
        Document::delete_tag($document_id, $document_cat);
    break;
    case "Remove Tags":
        foreach($document_ids as $document_id)
        {
            try{
                Document::delete_tag($document_id, $document_cat);
            }
            catch(CVException $exception)
            {
                $WARNING['show'] = true;
                $WARNING['message'] .= $exception->message;
            }
        }
        $SEARCH = $previous_search;
        
    break;    case "Add Tag":
        foreach($document_ids as $document_id)
        {
            try{
                Document::tag_document($document_id, $document_cat);
            }
            catch(CVException $exception)
            {
                $WARNING['show'] = true;
                $WARNING['message'] .= $exception->message;
            }
        }
        $SEARCH = $previous_search;
        
    break;
    case "Update Doc Info":        
        Document::update_document($document_id, $document_name, $document_date, $document_security);
        break;
    case "Delete Document":        
        if(Rbac_User::IsAllowedTo($USER->GetUserID(), "delete_documents", "documents_module"))
        {
            Document::delete_document($document_id);
        }
        break;
}

$docs = Document::getall_documents();
$docTypes = Document::getall_document_categories();

if(Rbac_User::IsAllowedTo($USER->GetUserID(), "edit_documents", "documents_module"))
{
    include("documents_main.phtml");
}
else
{
    include("documents_main_view.phtml");
}


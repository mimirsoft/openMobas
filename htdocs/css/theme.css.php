<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');

include("../../../openMobas/framework_masterinclude.php");


header("Content-type: text/css"); ?>

    body {
        font-family: sans-serif;
        color: black;
        font-size: 8pt;
        background-color: <?php echo $FRAMEWORK_COLORS['background_color'] ?>
    }
    td {
        font-family: sans-serif;
        color: black;
        font-size: 8pt;
        background-color: <?php echo $FRAMEWORK_COLORS['background_color'] ?>
    }
    input {   
        font-family: sans-serif;
        font-size: 10pt }
    }
    a:link {
        text-decoration: none; 
        color: <?php echo $FRAMEWORK_COLORS["link_color"] ?>; 
    }
    a:visited {
        text-decoration: none; 
        color: <?php echo $FRAMEWORK_COLORS["link_color"] ?>; 
    }
    a:hover {
        text-decoration: underline; 
        color: <?php echo $FRAMEWORK_COLORS["link_highlightcolor"] ?>;
    }
    a.alert {
        color: #FF69B4 }
    a {   
        color: <?php echo $FRAMEWORK_COLORS["link_color"] ?>; 
        text-decoration: none; 
        font-weight: bold;
    }
    div.divheader{
        width: 100%;
        font-weight: bold;
        font-size: 150%;
        text-transform: uppercase;
        color: <?php echo $FRAMEWORK_COLORS["divheader_color"] ?>;
        background-color: <?php echo $FRAMEWORK_COLORS["divheader_background"] ?> 
    }
  
    .permission_error
    {
        width: auto;
        text-align: center;
        font-size: 20pt;
        clear: both;
        border-top: 1px solid #035156;
        border-bottom: 1px solid #035156;
        border-left: 1px solid #035156;
        border-right: 1px solid #035156;

    } 

    form {
       margin: 0;
    }

    .bold {
        font-weight: bold;
        }


    .black{
        color: black }
    .neg {
        color: red }

    .warning {
        font-weight: bold;
        font-size: 200%;
        color: <?php echo $FRAMEWORK_COLORS["warningtext_color"] ?> ;
        text-transform: uppercase;
    }
    .warning_box {
        text-transform: uppercase;
        position:absolute;
        top:100;
        left:30%;
        BORDER: firebrick solid; 
        PADDING:10px;  
        FONT-SIZE: 11pt;  
        WIDTH: 40%; 
        height: 40%;
        COLOR: black; 
        background-color:white; 
        z-index: 10;
        clip:rect(auto);

   }




    .smallsans {
        font-family: sans-serif;
        font-size: 10pt;
        margin: 0 0 0 0;
        padding: 0 0 0 0;
         }

/*!!!!!!!!!!! QuickMenu Core CSS [Do Not Modify!] !!!!!!!!!!!!!*/
.qmclear {font-size:1px;height:0px;width:0px;clear:left;line-height:0px;display:block;}.qmmc {position:relative;}.qmmc a {float:left;display:block;white-space:nowrap;}.qmmc div a {float:none;}.qmmc div {display:none;position:absolute;}


/*!!!!!!!!!!! QuickMenu Styles [Please Modify!] !!!!!!!!!!!*/


	/* Remove the comments bleow for vertical mains and change the false value to
           true in the qm_create function after the menus structure. */
	/*.qmmc a {float:none}*/

	
	/*"""""""" (MAIN) Container """"""""*/
	#qm0
	{ 
		background-color: <?php echo $FRAMEWORK_COLORS["divheader_background"] ?>;
		position: fixed;
                padding-left:0px;
		border-width:1px 0px 1px 0px;
		border-style:solid;
		border-color:#000000;
		margin-left: 0px;
		
	}


	 /*"""""""" (MAIN) Items """"""""*/
	#qm0 a
	{
		color:#ffffff;
		background-color:transparent;
		font-family:Arial;
		font-size:1em;
		text-decoration:none;
		padding:5px 20px 5px 8px;
		border-style:solid;
		border-color:#000000;
		border-width:0px 0px 0px 1px;
		
	}


	/*"""""""" (MAIN) Hover State """"""""*/
	#qm0 a:hover
	{ 
		/*border-color:#333333;*/

		color:#000000;
		background-color:#EFF4FA;
		text-decoration:underline;
	}


	/*"""""""" (MAIN) Active State """"""""*/	
	#qm0 .qmactive
	{ 
		/* Note: Add '!important' after each style */
		/*border-color:#333333 !important;*/

		color:#000000 !important;
		background-color:#EFF4FA !important;
		text-decoration:underline !important;
		/*background-image:url(images/bullet_black_down.gif) !important;*/
	}


	/*"""""""" (MAIN) Parent Items """"""""*/
	#qm0 .qmparent
	{
		/*background-image:url(images/bullet_white_down.gif);*/
		background-repeat:no-repeat;
		background-position:92%;
		cursor:default;
	}

	


	/*"""""""" [SUB] Containers """"""""*/
	#qm0 div
	{
		
		background-color:#D6DCE2;
		padding:5px 5px 5px 5px;
		border-style:solid;
		border-width:1px 1px 1px 1px;
		margin:0px;
		border-color:#333333;
		
	}


	/*"""""""" [SUB] Items """"""""*/
	#qm0 div a
	{		
		font-size: 1em;
		color:#333333;
		border-width:0px 1px 0px 1px;
		border-color:#D6DCE2;
		border-style:solid;
		padding:3px 30px 3px 5px;
		
	}
	
	/*"""""""" [SUB] Hover State """"""""*/
	#qm0 div a:hover
	{

		border-color:#666666;
		background-color:#EFF4FA;
		color:#000000;
		text-decoration:none;
	}

	#qm0 div div
	{
		margin:0px 0px 0px 3px;
	}

	/*""""""""[SUB] Active State """"""""*/
	#qm0 div .qmactive
	{
		/* Note: Add '!important' after each style */
		border-color:#666666 !important;
		background-color:#EFF4FA !important;
		color:#000000 !important;
		text-decoration:underline !important;
		background-image:url(images/bullet_black_right.gif) !important;
		
	}


	/*"""""""" [SUB] Parent Items """"""""*/
	#qm0 div .qmparent 
	{

		background-image:url(images/bullet_black_right.gif);

	}
    div.menuspacer{
        clear: both;
        position:relative;
        padding-top: 10pt;
        
    }

   
        

<?php 

include("../../../framework/framework_masterinclude.php");

header("Content-type: text/css"); ?>

    .column_left {
        float: left;
   }
    .column_right {
        float: right;
   }
.mportlet {
        width: auto;
        float: left;
        clear: both;
        border-top: 1px solid #035156;
        border-bottom: 1px solid #035156;
        border-left: 1px solid #035156;
        border-right: 1px solid #035156;
   }

.mportlet_r {
        width: auto;
        float: right;
        clear: right;
        border-top: 1px solid #035156;
        border-bottom: 1px solid #035156;
        border-left: 1px solid #035156;
        border-right: 1px solid #035156;
   }
.casehidden {
		background: <? echo framework::$colors["theme_color1"] ?>;
		color: red;
	}
div.row {
  clear: both;
  padding-top: .5em;
  }

div.row span.formw {
  float: left;
  width: auto;
  text-align: left;
  
  } 
div.row span.label {
  float: left;
  width: 10em;
  text-align: right;
  }
  
input{
  vertical-align: top;
  padding: 0px;
  margin: 0px;
}
  
div.row span.col1 {
  float: left;
  text-align: right;
  width: 8em;
  color: black;  
}
div.row span.col2 {
  float: left;
  text-align: right;
  width: 15em;
  }
div.row span.col3 {
  float: left;
  text-align: right;
  width: 30em;
  }
div.row span.hidden {
  float: left;
  text-align: left;
  color: <? echo framework::$colors["theme_color3"] ?>;
  background-color: <? echo framework::$colors["theme_color1"] ?> 
  }
   
div.row span.colleft {
  float: left;
  text-align: left;
  width: 20em;
  }
div.row span.colright {
  float: left;
  text-align: right;
  width: 20em;
  }
tr.action0 td {

}

tr.action1 td {
    
    background-color: #F54D70;
}
tr.action2 td {
    
    background-color: #5DFC0A;
}
tr.action3 td {
    
    background-color:#FBEC5D ;
}
div.cvtag{
    float:left;
   color:green;  
   background-color:white;
   display: block;
   margin: 2px;
}

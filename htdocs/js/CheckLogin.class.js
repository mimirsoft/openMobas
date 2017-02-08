
var xmlHttp;
var myform;

function createXMLHttpRequest() {
  try {
    xmlHttp = new XMLHttpRequest();
  } catch (trymicrosoft) {
    try {
      xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (othermicrosoft) {
      try {
        xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (failed) {
        xmlHttp = false;
      }
    }
  }
  if (!xmlHttp)
    alert("Error initializing XMLHttpRequest!");
}

function checkLoginSubmitForm(formname, buttonValue) {
	myform = formname; 
	myform.elements["ACTION"].value = buttonValue;
    createXMLHttpRequest();
    var queryString = "";
    //window.open(queryString);
    xmlHttp.open("POST", "../tools/check_login.php", true);
    xmlHttp.onreadystatechange = checkLoginSubmitFormStateChange;
    xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");    
    xmlHttp.send(queryString);
alert('woot');
    return false;
}


function checkLoginSubmitFormStateChange() {
     if (xmlHttp.readyState == 4) {
       if (xmlHttp.status == 200)
       {
			var xmlDoc = xmlHttp.responseXML;
            if(xmlDoc.getElementsByTagName("login").length > 0)
            {
                var login_status = xmlDoc.getElementsByTagName("login")[0].childNodes[0].nodeValue;
                if(login_status == "FAILED")
                {
                    
                    loginwindow = window.open('../interface/login_popup.html', 'Login', 'height=100,width=300');
                    //loginwindow.document.write(xmlHttp.responseText);
                }
                if(login_status == "TRUE")
                {
                    
                    //if true then finally submit the form
                	myform.submit();
                	
                }
            }
       }
       else if (xmlHttp.status == 404)
         alert("Request URL does not exist");
       else
         alert("Error: status code is " + xmlHttp.status);
      }
}


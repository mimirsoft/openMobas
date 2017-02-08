  try {
	    xmlHttp = new XMLHttpRequest();
	    xmlHttp2 = new XMLHttpRequest();
  } catch (trymicrosoft) {
    try {
        xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
        xmlHttp2 = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (othermicrosoft) {
      try {
          xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
          xmlHttp2 = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (failed) {
          xmlHttp = false;
          xmlHttp2= false;
      }
    }
  }

  if (!xmlHttp)
    alert("Error initializing XMLHttpRequest!");
  

function form_extender(formElement) {
    form_Name = formElement.getAttribute("name");
    var queryString = "ACTION="+form_Name;
    form_Value = formElement.value;
    queryString += "&VALUE="+form_Value;
    xmlHttp.open("POST", "maintenance_request.php", true);
    xmlHttp.onreadystatechange = handleStateChange;
    xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");  
    xmlHttp.send(queryString);
}

function handleStateChange() {
     if (xmlHttp.readyState == 4) {
       if (xmlHttp.status == 200)
       {
    	   	var result = eval('('+ xmlHttp.responseText +')');
    	   	
    	   	for(var i=0; i < result.length; i++)
    		{
    	   		addrow = result[i];
    	   		if(addrow.removerow.length > 0)
	            {
	                var rowname = addrow.removerow;
	                thisrow = document.getElementById(rowname);
	                if(thisrow != null)
	                {
	                    while(thisrow.hasChildNodes())
	                    {
	                        thisrow.removeChild(thisrow.lastChild)
	                    }
	                    thisrow.parentNode.removeChild(thisrow);
	                }
	            }
	            var rowname = addrow.rowname;
	            var rowformname = addrow.rowformname;
                var previousrow = addrow.previousrow;
                var label = addrow.label;
                thisrow = document.getElementById(previousrow);
                newdiv = document.createElement("div");
                newdiv.setAttribute("id", rowname);
                newdiv.setAttribute("class", "row");
                newspan = document.createElement("span");
                newspan.innerHTML = label;
                newspan.setAttribute("class", "label");
                newdiv.appendChild(newspan);
                newspan = document.createElement("span");
                newspan.setAttribute("class", "formw");
                if(addrow['meta'])
                {
                	for(var j = 0; j < addrow.meta.length; j++)
                    {
                        newspan.innerHTML += addrow.meta[j]+"<BR />";
                    }
                }
                if(addrow['id'])
                {
                    newSelect = document.createElement("select");
                    newSelect.setAttribute("name", rowformname);
                    newSelect.setAttribute("id", rowformname);
                    newSelect.setAttribute("onchange", "form_extender(this)");
                    for(var j = 0; j < addrow.id.length; j++)
                    {
                        newoption  = document.createElement("option");
                        newoption.setAttribute("value", addrow.id[j]);
                        newoption.innerHTML = addrow.name[j];
                        newSelect.appendChild(newoption);
                    }
                    newspan.appendChild(newSelect);
                }
                newdiv.appendChild(newspan);
                thisrow.parentNode.insertBefore(newdiv, thisrow.nextSibling);
            }
       }
       else if (xmlHttp.status == 404)
         alert("Request URL does not exist");
       else
         alert("Form Extender Error: status code is " + xmlHttp.status);
      }
}

function maintenanceFinder(formElement) {
    form_Name = formElement.getAttribute("name");
    var queryString = "ACTION="+form_Name;
    form_Value = formElement.value;
    queryString += "&VALUE="+form_Value;
    xmlHttp2.open("POST", "maintenance_search.php", true);
    xmlHttp2.onreadystatechange = searchResults;
    xmlHttp2.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");  
    xmlHttp2.send(queryString);
}
function searchResults() {
	if (xmlHttp2.readyState == 4) {
		if (xmlHttp2.status == 200)
      	{
			var xmlDoc = xmlHttp2.responseXML;
           	if(xmlDoc.getElementsByTagName("results").length > 0)
           	{
        		thisTable = document.getElementById("results_table");
    	      	for(var i = thisTable.rows.length - 1; i > 0; i--)
				{
    	      		thisTable.deleteRow(i);
				}
				
    	      	for(var i = 0; i < xmlDoc.getElementsByTagName("row").length; i++)
                {
    	      		thisTable.insertRow(i+1);
    	      		thisTable.rows[i+1].className="action"+xmlDoc.getElementsByTagName("actionneed")[i].childNodes[0].nodeValue;
    	      		thisTable.rows[i+1].insertCell(0);
    	      		thisTable.rows[i+1].cells[0].innerHTML = xmlDoc.getElementsByTagName("itemname")[i].childNodes[0].nodeValue;

    	      		thisTable.rows[i+1].insertCell(1);
    	      		thisTable.rows[i+1].cells[1].innerHTML = "<a href =\"maintenance_update.php?maintenance_id="+xmlDoc.getElementsByTagName("id")[i].childNodes[0].nodeValue+"\" target=\"_blank\">"+xmlDoc.getElementsByTagName("title")[i].childNodes[0].nodeValue+"</a>";
    	    	      			
    	      		thisTable.rows[i+1].insertCell(2);
    	      		if(xmlDoc.getElementsByTagName("status")[i].childNodes.length > 0)
    	      		{
    	      			thisTable.rows[i+1].cells[2].innerHTML = xmlDoc.getElementsByTagName("status")[i].childNodes[0].nodeValue;
    	      		}
    	      		thisTable.rows[i+1].insertCell(3);
    	      		if(xmlDoc.getElementsByTagName("vendors")[i].childNodes.length > 0)
    	      		{
    	      			thisTable.rows[i+1].cells[3].innerHTML = xmlDoc.getElementsByTagName("vendors")[i].childNodes[0].nodeValue;
    	      		}
    	      		thisTable.rows[i+1].insertCell(4);
    	      		thisTable.rows[i+1].cells[4].innerHTML = xmlDoc.getElementsByTagName("whenopen")[i].childNodes[0].nodeValue;
    	      		thisTable.rows[i+1].insertCell(5);
    	      		if(xmlDoc.getElementsByTagName("whenclosed")[i].childNodes.length > 0)
    	      		{
        	      		thisTable.rows[i+1].cells[5].innerHTML = xmlDoc.getElementsByTagName("whenclosed")[i].childNodes[0].nodeValue;
    	      		}
    	      		thisTable.rows[i+1].insertCell(6);
    	      		if(xmlDoc.getElementsByTagName("whenupdate")[i].childNodes.length > 0)
    	      		{
        	      		thisTable.rows[i+1].cells[6].innerHTML = xmlDoc.getElementsByTagName("whenupdate")[i].childNodes[0].nodeValue;
    	      		}
    	      		thisTable.rows[i+1].insertCell(7);
    	      		if(xmlDoc.getElementsByTagName("whenfollow")[i].childNodes.length > 0)
    	      		{
        	      		thisTable.rows[i+1].cells[7].innerHTML = xmlDoc.getElementsByTagName("whenfollow")[i].childNodes[0].nodeValue;
    	      		}
    	      		thisTable.rows[i+1].insertCell(8);
    	      		var checkbox = document.createElement('input');
    	      		checkbox.type = "checkbox";
    	      		checkbox.name = "maintenance_tickets[]";
    	      		checkbox.value = xmlDoc.getElementsByTagName("id")[i].childNodes[0].nodeValue;
    	      		checkbox.id = "maintenance_tickets[]";
    	      		checkbox.checked = true;
    	      		thisTable.rows[i+1].cells[8].appendChild(checkbox);
                }
           }
      }
      else if (xmlHttp2.status == 404)
        alert("Request URL does not exist");
      else
        alert("Maintenance Finder Error: status code is " + xmlHttp2.status);
     }
}

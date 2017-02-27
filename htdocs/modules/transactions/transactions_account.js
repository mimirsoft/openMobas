
function doSomething (obj,evt) {
    var e=(evt)?evt:window.event;
    if (window.event) {
        e.cancelBubble=true;
    } else {
        //e.preventDefault();
        e.stopPropagation();
    }
}
function delete_checked()
{
    comfList = document.forms['transactions_form'].elements['multi_select[]'];
    //go thru all transactions
    for (i = 0; i < comfList.length; i++) 
    {
        //see if they are checked
        if(comfList[i].checked == true)
        {
            //delete all checked
            multi_trans_stack.push(comfList[i].value);
        }
    }
    // start the delete
    delete_transaction_multi();
    //reload the page
}
function delete_transaction_multi() {
    
	createXMLHttpRequest();
    var queryString = "ACTION=delete_transaction_multi";
    for (i = 0; i < multi_trans_stack.length; i++) 
    {
        queryString += "&transaction_multi[]=" + multi_trans_stack[i];
    }
    xmlHttp.open("POST", "transactions_savetransaction.php", true);
    xmlHttp.onreadystatechange = handleStateChange_multi;
    xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");    
    last_query = queryString;
    //window.open("transactions_savetransaction.php?"+queryString);
    //alert(last_query);
    xmlHttp.send(queryString);
}

function move_checked()
{
    comfList = document.forms['transactions_form'].elements['multi_select[]'];
    //go thru all transactions
    for (i = 0; i < comfList.length; i++) 
    {
        //see if they are checked
        if(comfList[i].checked == true)
        {
            //delete all checked
            multi_trans_stack.push(comfList[i].value);
        }
    }
    // start the delete
    move_transaction_multi();
    //reload the page
}


function move_transaction_multi() 
{
    multi_move =  document.getElementById("multi_move").value;
    createXMLHttpRequest();
    var queryString = "ACTION=move_transaction_multi&from_account=" + this_account + "&to_account=" + multi_move;
    for (i = 0; i < multi_trans_stack.length; i++) 
    {
        queryString += "&transaction_multi[]=" + multi_trans_stack[i];
    }
    xmlHttp.open("POST", "transactions_savetransaction.php", true);
    xmlHttp.onreadystatechange = handleStateChange_multi;
    xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");    
    last_query = queryString;
    xmlHttp.send(queryString);
    alert(queryString);
}

function handleStateChange_multi() {
     if (xmlHttp.readyState == 4) {
       if (xmlHttp.status == 200)
       {
            var xmlDoc = xmlHttp.responseXML;
            if(xmlDoc.getElementsByTagName("transaction").length > 0)
            {
                var transaction_status = xmlDoc.getElementsByTagName("transaction")[0].childNodes[0].nodeValue;
                if(transaction_status == "SAVED")
                { 
                    //if everything went through, then call the next delete
                    draw_table();
                    return;
                }
            }    
            if(xmlDoc.getElementsByTagName("login").length > 0)
            {
                var login_status = xmlDoc.getElementsByTagName("login")[0].childNodes[0].nodeValue;
                if(login_status == "FAILED")
                {
                    
                    loginwindow = window.open('../interface/login_popup.html', 'Login', 'height=100,width=300');
                    //loginwindow.document.write(xmlHttp.responseText);
                }
            }
            if(xmlDoc.getElementsByTagName("show").length > 0)
            {
                var message = xmlDoc.getElementsByTagName("message")[0].childNodes[0].nodeValue;
                var failed_id = xmlDoc.getElementsByTagName("transaction")[0].childNodes[0].nodeValue;
                test_id = multi_trans_stack.shift();
                while(failed_id != test_id) 
                {
                    test_id = multi_trans_stack.shift();
                }
                multi_trans_stack.unshift(test_id);
                alert(multi_trans_stack);
                message_div = document.getElementById("warning_box_message");
                if(xmlDoc.getElementsByTagName("override").length > 0)
                {   
                    message += "<BUTTON onclick=retry_with_override()>OVERRIDE</BUTTON>";
                }
                message_div.innerHTML = message;
                popUp = document.getElementById("warning_box");
                popUp.style.visibility="visible";
                
            }
               
       }
       else if (xmlHttp.status == 404)
         alert("Request URL does not exist");
       else
         alert("Error: status code is " + xmlHttp.status);
      }
}

function check_all(frm, chAll, field) {
    comfList = document.forms[frm].elements[field];
    checkAll = (chAll.checked)?true:false; // what to do? Check all or uncheck all.
    if (checkAll) {
            for (i = 0; i < comfList.length; i++) {
                comfList[i].checked = true;
            }
        }
    else {
        for (i = 0; i < comfList.length; i++) {
                comfList[i].checked = false;
            }
        }



}

function findPosY(obj)
{
    var curtop = 0;
    if(obj.offsetParent)
        while(1)
        {
          curtop += obj.offsetTop;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.y)
        curtop += obj.y;
    return curtop;
}



function ts_resortTable(lnk) {
    // get the span
    var span;
    for (var ci=0;ci<lnk.childNodes.length;ci++) {
        if (lnk.childNodes[ci].tagName && lnk.childNodes[ci].tagName.toLowerCase() == 'span') span = lnk.childNodes[ci];
    }
    var td = lnk.parentNode;
    var column = td.cellIndex;
    var trow = td.parentNode;
    var table = trow.parentNode;
    
    // Work out a type for the column
    if (transactions.length <= 1) return;
    sortfn = ts_sort_caseinsensitive;
    if (column == 0) sortfn = ts_sort_date;
    if (column == 1){ SORT_COLUMN_INDEX = 3;
    sortfn = ts_sort_numeric;
    }
    if (column == 2){  SORT_COLUMN_INDEX = 4;
    }
    if (column == 3) SORT_COLUMN_INDEX = 9;
    if (column == 4) SORT_COLUMN_INDEX = 10;
    if (column == 5){ SORT_COLUMN_INDEX = 11;}
    if (column == 6){ SORT_COLUMN_INDEX = 7;
    sortfn = ts_sort_numeric;
    }
    
    transactions.sort(sortfn);

    if (span.getAttribute("sortdir") == 'down') {
        ARROW = '&nbsp;&nbsp;&uarr;';
       transactions.reverse();
        span.setAttribute('sortdir','up');
    } else {
        ARROW = '&nbsp;&nbsp;&darr;';
        span.setAttribute('sortdir','down');
    }
    
    draw_table();
        
    // Delete any other arrows there may be showing
    var allspans = document.getElementsByTagName("span");
    for (var ci=0;ci<allspans.length;ci++) {
        if (allspans[ci].className == 'sortarrow') {
                allspans[ci].innerHTML = '&nbsp;&nbsp;&nbsp;';
        }
    }
    span.innerHTML = ARROW;
}

function ts_sort_date(a,b) {
    // y2k notes: two digit years less than 50 are treated as 20XX, greater than 50 are treated as 19XX
    dt1 = a[0]+a[1]+a[2];
    dt2 = b[0]+b[1]+b[2];
    if (dt1==dt2) return 0;
    if (dt1<dt2) return -1;
    return 1;
}

function ts_sort_currency(a,b) { 
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]).replace(/[^0-9.]/g,'');
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]).replace(/[^0-9.]/g,'');
    return parseFloat(aa) - parseFloat(bb);
}

function ts_sort_numeric(a,b) { 
    aa = parseFloat(a[SORT_COLUMN_INDEX]);
    if (isNaN(aa)) aa = 0;
    bb = parseFloat(b[SORT_COLUMN_INDEX]); 
    if (isNaN(bb)) bb = 0;
    return aa-bb;
}

function ts_sort_caseinsensitive(a,b) {
    aa = a[SORT_COLUMN_INDEX].toLowerCase();
    bb = b[SORT_COLUMN_INDEX].toLowerCase();
    if (aa==bb) return 0;
    if (aa<bb) return -1;
    return 1;
}

function ts_sort_default(a,b) {
    aa = a[SORT_COLUMN_INDEX];
    bb = b[SORT_COLUMN_INDEX];
    if (aa==bb) return 0;
    if (aa<bb) return -1;
    return 1;
}

function add_debit_credit()
{
    var s =  document.getElementById("transaction[transaction_account]");
    var account =  document.getElementById("transaction[transaction_account]").value;
    var dc =  document.getElementById("transaction[transaction_dc]").value;
    var amount =    document.getElementById("transaction[transaction_amount]").value;
    var account_name = s.options[s.selectedIndex].text;
    add_dc_box(account, dc, amount, account_name);
}
function add_dc_box(account, dc, amount, account_name)
{
    for(keyVar in transaction_edit_dc)
    {
        if(account ==  transaction_edit_dc[keyVar][0])
        {
            popUp = document.getElementById("warning_box");
            popUp.style.visibility="visible";
            message_div = document.getElementById("warning_box_message");
            message_div.innerHTML = "CANNOT ADD DEBIT/CREDIT, ACCOUNT "+account_name+"ALREADY HAS DEBIT/CREDIT FOR THIS TRANSACTION";            
            return;
        }
    }

    amount = parseFloat(amount);
    amount = amount.toFixed(2);
    newdiv_checkbox = document.createElement('input');
    newdiv_checkbox.type = 'button';
    newdiv_checkbox.name = 'del'+DC_COUNT;
    newdiv_checkbox.value = "DEL";
    newdiv_checkbox.setAttribute('id', 'del'+DC_COUNT);
    newdiv_checkbox.setAttribute('class', 'dc_account2');
    newdiv_checkbox.setAttribute('onclick', 'remove_dc('+DC_COUNT+')');

    newdiv_account = document.createElement('div');
    newdiv_account.setAttribute('class', 'dc_account');
    newdiv_account.innerHTML = account_name;
    newdiv_amount = document.createElement('div');
    newdiv_amount.setAttribute('class', 'dc_amount');
    newdiv_amount.innerHTML = amount;
    
    newdiv = document.createElement('div');
    newdiv.appendChild(newdiv_account);
    newdiv.appendChild(newdiv_amount);
    newdiv.setAttribute('class', 'DEBIT_CREDIT_BOX');
    if(dc == 'DEBIT')
    {
        var div_box = document.getElementById("debits_column");
    }
    if(dc == 'CREDIT')
    {
        var div_box = document.getElementById("credits_column");
    }
    div_box.appendChild(newdiv);
    div_box.appendChild(newdiv_checkbox);
    transaction_edit_dc[DC_COUNT] = new Array(3);
    transaction_edit_dc[DC_COUNT][0] = account;
    transaction_edit_dc[DC_COUNT][1] = dc;
    transaction_edit_dc[DC_COUNT][2] = amount;
    var balance_box = document.getElementById("unbalanced_amount");
    if(dc == 'DEBIT')
    {
        balance_amount = parseFloat(balance_amount)-parseFloat(amount);
    }
    if(dc == 'CREDIT')
    {
        balance_amount = parseFloat(balance_amount)+parseFloat(amount);
    }
    balance_box.innerHTML = balance_amount.toFixed(2);
    DC_COUNT++;
}
function remove_dc(dc_number)
{ 
    if(transaction_edit_dc[dc_number][1] == 'DEBIT')
    {
        balance_amount = parseFloat(balance_amount)+parseFloat(transaction_edit_dc[dc_number][2]);
    }
    if(transaction_edit_dc[dc_number][1] == 'CREDIT')
    {
        balance_amount = parseFloat(balance_amount)-parseFloat(transaction_edit_dc[dc_number][2]);
    }
    var balance_box = document.getElementById("unbalanced_amount");
    balance_box.innerHTML = balance_amount.toFixed(2);
    delete transaction_edit_dc[dc_number];
    del_button = document.getElementById('del'+dc_number);
    del_button.parentNode.removeChild(del_button.previousSibling);
    del_button.parentNode.removeChild(del_button);
}

function check_date(evt, datetype)
{ 
    createXMLHttpRequest();
    var objectID = (evt.target) ? evt.target.id : ((evt.srcElement) ? evt.srcElement.id :null);
    var date = document.getElementById(objectID);
    var url = "../tools/validate_date.php?type="+datetype+"&target="+objectID+"&num=" + escape(date.value);
    xmlHttp.open("GET", url, true);
    xmlHttp.onreadystatechange = callback;
    xmlHttp.send(null);
}
function callback()
{
    if (xmlHttp.readyState == 4) {
        if (xmlHttp.status == 200) {
            var xmlDoc = xmlHttp.responseXML;
            var objectID = xmlDoc.getElementsByTagName("target")[0].childNodes[0].nodeValue;
            var num = xmlDoc.getElementsByTagName("num")[0].childNodes[0].nodeValue;
            var currentElement = document.getElementById(objectID);
            currentElement.value = num;
        }
    }
}


function toggle_vis(divheader)
{
	document.getElementById("simple_edit_box").style.top = "20px";
    document.getElementById("simple_edit_box").style.visibility = 'visible';
}
function hide_simplebox()
{
    document.getElementById("simple_edit_box").style.visibility = 'hidden';
}
function hide_editbox()
{
    document.getElementById("new_trans").style.visibility = 'hidden';
}
function show_editbox()
{
    document.getElementById("new_trans").style.visibility = 'visible';
}
function set_credit_null()
{
    document.getElementById("transaction_simple[transaction_credit]").value = '';
}
function set_debit_null()
{
    document.getElementById("transaction_simple[transaction_debit]").value = '';
}


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
function clear_transaction()
{
    document.getElementById("transaction[date_year]").value = '';
    document.getElementById("transaction[date_month]").value = '';
    document.getElementById("transaction[date_day]").value = '';
    document.getElementById("transaction[transaction_checkno]").value = '';
    document.getElementById("transaction[transaction_comment]").value = '';
    document.getElementById("transaction[transaction_reconcile]").value = '';
    document.getElementById("transaction[reconciledate_year]").value = '';
    document.getElementById("transaction[reconciledate_month]").value = '';
    document.getElementById("transaction[reconciledate_day]").value = '';
    transaction_id = 'NULL';
    transaction_edit_dc = new Object();
    box_clear = document.getElementById("debits_column");
    if ( box_clear.hasChildNodes() )
    {
        while ( box_clear.childNodes.length >= 1 )
        {
            box_clear.removeChild( box_clear.firstChild );       
        } 
    }
    box_clear = document.getElementById("credits_column");
    if ( box_clear.hasChildNodes() )
    {
        while ( box_clear.childNodes.length >= 1 )
        {
            box_clear.removeChild( box_clear.firstChild );       
        } 
    }
    document.getElementById("transaction_simple[date_year]").value = '';
    document.getElementById("transaction_simple[date_month]").value = '';
    document.getElementById("transaction_simple[date_day]").value = '';
    document.getElementById("transaction_simple[transaction_checkno]").value = '';
    document.getElementById("transaction_simple[transaction_comment]").value = '';
    document.getElementById("transaction_simple[transaction_reconcile]").value = '';
    document.getElementById("transaction_simple[reconciledate_year]").value = '';
    document.getElementById("transaction_simple[reconciledate_month]").value = '';
    document.getElementById("transaction_simple[reconciledate_day]").value = '';
    document.getElementById("transaction_simple[transaction_debit]").value = '';
    document.getElementById("transaction_simple[transaction_credit]").value = '';
}

function open_editbox()
{
    document.getElementById("transaction[date_year]").value = '';
    document.getElementById("transaction[date_month]").value = '';
    document.getElementById("transaction[date_day]").value = '';
    document.getElementById("transaction[transaction_checkno]").value = '';
    document.getElementById("transaction[transaction_comment]").value = '';
    document.getElementById("transaction[transaction_reconcile]").value = '';
    document.getElementById("transaction[reconciledate_year]").value = '';
    document.getElementById("transaction[reconciledate_month]").value = '';
    document.getElementById("transaction[reconciledate_day]").value = '';
    transaction_edit_dc = new Object();
    box_clear = document.getElementById("debits_column");
    if ( box_clear.hasChildNodes() )
    {
        while ( box_clear.childNodes.length >= 1 )
        {
            box_clear.removeChild( box_clear.firstChild );       
        } 
    }
    box_clear = document.getElementById("credits_column");
    if ( box_clear.hasChildNodes() )
    {
        while ( box_clear.childNodes.length >= 1 )
        {
            box_clear.removeChild( box_clear.firstChild );       
        } 
    }
    createXMLHttpRequest();
    var queryString = "ACTION=get_transaction&transaction_id=" + transaction_id;
    xmlHttp.open("POST", "transactions_edittransaction.php", true);
    xmlHttp.onreadystatechange = handleStateChange;
    xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");    
    xmlHttp.send(queryString);
}

function open_simplebox(id, event)
{
    createXMLHttpRequest();
    var queryString = "ACTION=get_transaction&transaction_id=" + id;
    xmlHttp.open("POST", "transactions_edittransaction.php", true);
    xmlHttp.onreadystatechange = handleStateChange_simple;
    xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");    
    xmlHttp.send(queryString);
    window.open(queryString);
}

function save_simplebox(id, e)
{
    var e = e || window.event;
    e.cancelBubble = true;
    if (e.stopPropagation) e.stopPropagation();
    //save it

    //remove the cells
    thisrow = document.getElementById(id);
    thisrow.deleteCell(thisrow.cells.length-1);
    thisrow = document.getElementById(id);
    thisrow.deleteCell(thisrow.cells.length-1);
    // then reenable onlick open_simplebox
    alert('save_simplebox');
    thisrow.setAttribute('onclick', "open_simplebox(\'"+id+"\')");
}

function save_simple_transaction() {
	//alert("SAVING!!");
    if(document.getElementById("transaction_simple[transaction_debit]").value != ''  &&
        document.getElementById("transaction_simple[transaction_credit]").value != ''
)
    {
        alert("CAN'T HAVE BOTH DEBIT AND CREDIT SET FOR SIMPLE TRANSACTION");
        return;
    } 
    createXMLHttpRequest();
    var dateYear =  document.getElementById("transaction_simple[date_year]").value;
    var dateMonth = document.getElementById("transaction_simple[date_month]").value;
    var dateDay =   document.getElementById("transaction_simple[date_day]").value;
    var checkno =   document.getElementById("transaction_simple[transaction_checkno]").value;
    var comment =   document.getElementById("transaction_simple[transaction_comment]").value;
    var reconcile =    document.getElementById("transaction_simple[transaction_reconcile]").value;
    var reconciledateYear =  document.getElementById("transaction_simple[reconciledate_year]").value;
    var reconciledateMonth = document.getElementById("transaction_simple[reconciledate_month]").value;
    var reconciledateDay =   document.getElementById("transaction_simple[reconciledate_day]").value;
    
    var queryString = "ACTION=save_transaction&date_year=" + dateYear + "&date_month=" + dateMonth + "&date_day=" + dateDay
        + "&reconciledate_year=" + reconciledateYear + "&reconciledate_month=" + reconciledateMonth + "&reconciledate_day=" + reconciledateDay
        + "&checkno=" + checkno + "&comment=" + encodeURIComponent(comment) + "&transaction_id=" + transaction_id + "&reconcile=" + reconcile;
    
    queryString += "&account[0]=" + document.getElementById("transaction_simple[transaction_account]").value;
    queryString += "&account[1]=" + this_account;
    if(document.getElementById("transaction_simple[transaction_debit]").value != '')
    {
        queryString += "&dc[0]=CREDIT";
        queryString += "&dc[1]=DEBIT";
        queryString += "&amount[0]=" + document.getElementById("transaction_simple[transaction_debit]").value;
        queryString += "&amount[1]=" + document.getElementById("transaction_simple[transaction_debit]").value;
    }
    else if(document.getElementById("transaction_simple[transaction_credit]").value != '')
    {
        queryString += "&dc[0]=DEBIT";
        queryString += "&dc[1]=CREDIT";
        queryString += "&amount[0]=" + document.getElementById("transaction_simple[transaction_credit]").value;
        queryString += "&amount[1]=" + document.getElementById("transaction_simple[transaction_credit]").value;
    }
    //this is for debugging, if we need to test our query, it will attempt to open our query string as 
    //a url in a new window
    //window.open(queryString);  
    //alert(queryString);
    //window.open(queryString);
   
    xmlHttp.open("POST", "transactions_savetransaction.php", true);
    xmlHttp.onreadystatechange = handleStateChange;
    xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");    
    xmlHttp.send(queryString);
    
}
function save_transaction() {
	createXMLHttpRequest();
    var dateYear =  document.getElementById("transaction[date_year]").value;
    var dateMonth = document.getElementById("transaction[date_month]").value;
    var dateDay =   document.getElementById("transaction[date_day]").value;
    var checkno =   document.getElementById("transaction[transaction_checkno]").value;
    var comment =   document.getElementById("transaction[transaction_comment]").value;
    var reconcile =    document.getElementById("transaction[transaction_reconcile]").value;
    var reconciledateYear =  document.getElementById("transaction[reconciledate_year]").value;
    var reconciledateMonth = document.getElementById("transaction[reconciledate_month]").value;
    var reconciledateDay =   document.getElementById("transaction[reconciledate_day]").value;
    
    var queryString = "ACTION=save_transaction&date_year=" + dateYear + "&date_month=" + dateMonth + "&date_day=" + dateDay
        + "&reconciledate_year=" + reconciledateYear + "&reconciledate_month=" + reconciledateMonth + "&reconciledate_day=" + reconciledateDay
        + "&checkno=" + checkno + "&comment=" + encodeURIComponent(comment) + "&transaction_id=" + transaction_id + "&reconcile=" + reconcile;
    for(keyVar in transaction_edit_dc)
    {
        queryString += "&account["+ keyVar +"]=" + transaction_edit_dc[keyVar][0]
        queryString += "&dc["+ keyVar +"]=" + transaction_edit_dc[keyVar][1]
        queryString += "&amount["+ keyVar +"]=" + transaction_edit_dc[keyVar][2]
    }
    xmlHttp.open("POST", "transactions_savetransaction.php", true);
    xmlHttp.onreadystatechange = handleStateChange;
    xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");    
    xmlHttp.send(queryString);
    
}

function delete_transaction(id) {
    createXMLHttpRequest();
    var queryString = "ACTION=delete_transaction&transaction_id=" + transaction_id;
    xmlHttp.open("POST", "transactions_savetransaction.php", true);
    xmlHttp.onreadystatechange = handleStateChange;
    xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");    
    last_query = queryString;
    xmlHttp.send(queryString);
    //window.open(queryString);
    
}

function retry_with_override() {

    last_query += "&override=true";
    xmlHttp.open("POST", "transactions_savetransaction.php", true);
    xmlHttp.onreadystatechange = handleStateChange;
    xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");    
    xmlHttp.send(last_query);
}

function view_details(id) 
{
    window.open('transactions_details.php?transaction_id='+transaction_id, 'Transaction Details');
}

function handleStateChange_simple() {
     if (xmlHttp.readyState == 4) {
       if (xmlHttp.status == 200)
       {
    	    var xmlDoc = xmlHttp.responseXML;
            if(xmlDoc.getElementsByTagName("transaction").length > 0)
            {
                var transaction_status = xmlDoc.getElementsByTagName("transaction")[0].childNodes[0].nodeValue;
                if(transaction_status == "SAVED")
                { 
                     draw_table();
                }
            }    
            if(xmlDoc.getElementsByTagName("login").length > 0)
            {
                var login_status = xmlDoc.getElementsByTagName("login")[0].childNodes[0].nodeValue;
                if(login_status == "FAILED")
                {
                    
                    loginwindow = window.open('../interface/login_popup.html', 'Login', 'height=100,width=300');
                    //loginwindow.document.write(xmlHttp.responseText);
                }
            }
            if(xmlDoc.getElementsByTagName("retrieved").length > 0)
            {
                if(xmlDoc.getElementsByTagName("is_split")[0].childNodes[0].nodeValue == 1)
                {
                    handleStateChange();
                    return
                }
                if(xmlDoc.getElementsByTagName("id")[0].childNodes.length > 0)
                {   
                    transaction_id =  xmlDoc.getElementsByTagName("id")[0].childNodes[0].nodeValue;
                }
                if(xmlDoc.getElementsByTagName("checkno")[0].childNodes.length > 0)
                {   
                    document.getElementById("transaction_simple[transaction_checkno]").value =  xmlDoc.getElementsByTagName("checkno")[0].childNodes[0].nodeValue;
                }
                if(xmlDoc.getElementsByTagName("comments")[0].childNodes.length > 0)
                {   
                    document.getElementById("transaction_simple[transaction_comment]").value =  xmlDoc.getElementsByTagName("comments")[0].childNodes[0].nodeValue;
                }
                if(xmlDoc.getElementsByTagName("reconcile")[0].childNodes.length > 0)
                {   
                    document.getElementById("transaction_simple[transaction_reconcile]").value =  xmlDoc.getElementsByTagName("reconcile")[0].childNodes[0].nodeValue;
                }
                if(xmlDoc.getElementsByTagName("date_year")[0].childNodes.length > 0)
                {   
                    document.getElementById("transaction_simple[date_year]").value =  xmlDoc.getElementsByTagName("date_year")[0].childNodes[0].nodeValue;
                }
                if(xmlDoc.getElementsByTagName("date_month")[0].childNodes.length > 0)
                {   
                    document.getElementById("transaction_simple[date_month]").value =  xmlDoc.getElementsByTagName("date_month")[0].childNodes[0].nodeValue;
                }
                if(xmlDoc.getElementsByTagName("date_day")[0].childNodes.length > 0)
                {   
                    document.getElementById("transaction_simple[date_day]").value =  xmlDoc.getElementsByTagName("date_day")[0].childNodes[0].nodeValue;
                }
                if(xmlDoc.getElementsByTagName("r_date_year")[0].childNodes.length > 0)
                {   
                    document.getElementById("transaction_simple[reconciledate_year]").value =  xmlDoc.getElementsByTagName("r_date_year")[0].childNodes[0].nodeValue;
                }
                if(xmlDoc.getElementsByTagName("r_date_month")[0].childNodes.length > 0)
                {   
                    document.getElementById("transaction_simple[reconciledate_month]").value =  xmlDoc.getElementsByTagName("r_date_month")[0].childNodes[0].nodeValue;
                }
                if(xmlDoc.getElementsByTagName("r_date_day")[0].childNodes.length > 0)
                {   
                    document.getElementById("transaction_simple[reconciledate_day]").value =  xmlDoc.getElementsByTagName("r_date_day")[0].childNodes[0].nodeValue;
                }
                debit_credit = xmlDoc.getElementsByTagName("debit_credit");
                for(dc_iterate=0; dc_iterate < 2; dc_iterate++)
                {
                	//if this is set
                	if(xmlDoc.getElementsByTagName("debit_credit")[dc_iterate])
            		{
	                	//if it is this account, add the deb/credit
	                    if(xmlDoc.getElementsByTagName("debit_credit")[dc_iterate].childNodes[1].firstChild.nodeValue == this_account)
	                    {
	                        if(xmlDoc.getElementsByTagName("debit_credit")[dc_iterate].childNodes[5].firstChild.nodeValue == 'DEBIT')
	                        {
	                            document.getElementById("transaction_simple[transaction_debit]").value =  xmlDoc.getElementsByTagName("debit_credit")[dc_iterate].childNodes[3].firstChild.nodeValue;
	                            document.getElementById("transaction_simple[transaction_credit]").value = '';
	                        }
	                        else{
	                            document.getElementById("transaction_simple[transaction_credit]").value =  xmlDoc.getElementsByTagName("debit_credit")[dc_iterate].childNodes[3].firstChild.nodeValue;
	                            document.getElementById("transaction_simple[transaction_debit]").value = '';
	                        }
	                    }
	                    //build the account selector, and select the account
	                    if(xmlDoc.getElementsByTagName("debit_credit")[dc_iterate].childNodes[1].firstChild.nodeValue != this_account)
	                    {
	                        selected_account = xmlDoc.getElementsByTagName("debit_credit")[dc_iterate].childNodes[1].firstChild.nodeValue
	                        select_box = document.getElementById("transaction_simple[transaction_account]");
	                        for(i=0; i < select_box.length; ++i)
	                        {
	                            if(select_box.options[i].value == selected_account)
	                            {
	                                document.getElementById("transaction_simple[transaction_account]").selectedIndex = i;
	                            }
	                        }
	                    }   
            		}
                }
                document.getElementById("transaction_simple[transaction_id]").value =  transaction_id;
                thisrow = document.getElementById(transaction_id);
                yOffset = findPosY(thisrow);
                yOffset = yOffset+thisrow.offsetHeight;
                document.getElementById("new_trans").style.visibility = 'hidden';
                simpletrans = document.getElementById("simple_edit_box");
                simpletrans.style.top = yOffset+"px";
                simpletrans.style.visibility = 'visible';
            }
            if(xmlDoc.getElementsByTagName("show").length > 0)
            {
            	var message = xmlDoc.getElementsByTagName("message")[0].childNodes[0].nodeValue;
                message_div = document.getElementById("warning_box_message");
                if(xmlDoc.getElementsByTagName("override").length > 0)
                {   
                    message += "<BUTTON onclick=retry_with_override()>OVERRIDE</BUTTON>";
                }
                message_div.innerHTML = message;
                popUp = document.getElementById("warning_box");
                popUp.style.visibility="visible";
                
            }
               
       }
       else if (xmlHttp.status == 404)
         alert("Request URL does not exist");
       else
         alert("Error: status code is " + xmlHttp.status);
      }
}

function handleStateChange() {
	 //alert("handling");
     if (xmlHttp.readyState == 4) {
       if (xmlHttp.status == 200)
       {
    	    var xmlDoc = xmlHttp.responseXML;
            if(xmlDoc.getElementsByTagName("transaction").length > 0)
            {
                var transaction_status = xmlDoc.getElementsByTagName("transaction")[0].childNodes[0].nodeValue;
                if(transaction_status == "SAVED")
                { 
                     draw_table();
                }
            }    
            if(xmlDoc.getElementsByTagName("login").length > 0)
            {
                var login_status = xmlDoc.getElementsByTagName("login")[0].childNodes[0].nodeValue;
                if(login_status == "FAILED")
                {
                    
                    loginwindow = window.open('../interface/login_popup.html', 'Login', 'height=100,width=300');
                    //loginwindow.document.write(xmlHttp.responseText);
                }
            }
            if(xmlDoc.getElementsByTagName("retrieved").length > 0)
            {
                if(xmlDoc.getElementsByTagName("id")[0].childNodes.length > 0)
                {   
                    transaction_id =  xmlDoc.getElementsByTagName("id")[0].childNodes[0].nodeValue;
                }
                if(xmlDoc.getElementsByTagName("checkno")[0].childNodes.length > 0)
                {   
                    document.getElementById("transaction[transaction_checkno]").value =  xmlDoc.getElementsByTagName("checkno")[0].childNodes[0].nodeValue;
                }
                if(xmlDoc.getElementsByTagName("comments")[0].childNodes.length > 0)
                {   
                    document.getElementById("transaction[transaction_comment]").value =  xmlDoc.getElementsByTagName("comments")[0].childNodes[0].nodeValue;
                }
                if(xmlDoc.getElementsByTagName("reconcile")[0].childNodes.length > 0)
                {   
                    document.getElementById("transaction[transaction_reconcile]").value =  xmlDoc.getElementsByTagName("reconcile")[0].childNodes[0].nodeValue;
                }
                if(xmlDoc.getElementsByTagName("date_year")[0].childNodes.length > 0)
                {   
                    document.getElementById("transaction[date_year]").value =  xmlDoc.getElementsByTagName("date_year")[0].childNodes[0].nodeValue;
                }
                if(xmlDoc.getElementsByTagName("date_month")[0].childNodes.length > 0)
                {   
                    document.getElementById("transaction[date_month]").value =  xmlDoc.getElementsByTagName("date_month")[0].childNodes[0].nodeValue;
                }
                if(xmlDoc.getElementsByTagName("date_day")[0].childNodes.length > 0)
                {   
                    document.getElementById("transaction[date_day]").value =  xmlDoc.getElementsByTagName("date_day")[0].childNodes[0].nodeValue;
                }
                if(xmlDoc.getElementsByTagName("r_date_year")[0].childNodes.length > 0)
                {   
                    document.getElementById("transaction[reconciledate_year]").value =  xmlDoc.getElementsByTagName("r_date_year")[0].childNodes[0].nodeValue;
                }
                if(xmlDoc.getElementsByTagName("r_date_month")[0].childNodes.length > 0)
                {   
                    document.getElementById("transaction[reconciledate_month]").value =  xmlDoc.getElementsByTagName("r_date_month")[0].childNodes[0].nodeValue;
                }
                if(xmlDoc.getElementsByTagName("r_date_day")[0].childNodes.length > 0)
                {   
                    document.getElementById("transaction[reconciledate_day]").value =  xmlDoc.getElementsByTagName("r_date_day")[0].childNodes[0].nodeValue;
                }
                debit_credit_length = xmlDoc.getElementsByTagName("debit_credit").length;
                debit_credit = xmlDoc.getElementsByTagName("debit_credit");
                for(dc_iterate=0; dc_iterate < debit_credit_length; dc_iterate++)
                {
                   add_dc_box(xmlDoc.getElementsByTagName("debit_credit")[dc_iterate].childNodes[1].firstChild.nodeValue, xmlDoc.getElementsByTagName("debit_credit")[dc_iterate].childNodes[5].firstChild.nodeValue, xmlDoc.getElementsByTagName("debit_credit")[dc_iterate].childNodes[3].firstChild.nodeValue, xmlDoc.getElementsByTagName("debit_credit")[dc_iterate].childNodes[7].firstChild.nodeValue);
                }
                document.getElementById("simple_edit_box").style.visibility = 'hidden';
                document.getElementById("new_trans").style.visibility = 'visible';
                javascript:scroll(0,0);
                
            }
            if(xmlDoc.getElementsByTagName("show").length > 0)
            {
				var message = xmlDoc.getElementsByTagName("message")[0].childNodes[0].nodeValue;
				message_div = document.getElementById("warning_box_message");
                if(xmlDoc.getElementsByTagName("override").length > 0)
                {   
                    message += "<BUTTON onclick=retry_with_override()>OVERRIDE</BUTTON>";
                }
                message_div.innerHTML = message;
                popUp = document.getElementById("warning_box");
                popUp.style.visibility="visible";
                javascript:scroll(0,0);
            }
               
       }
       else if (xmlHttp.status == 404)
         alert("Request URL does not exist");
       else
         alert("Error: status code is " + xmlHttp.status);
      }
}


function hidetip2(){
    var popUp;
    popUp = document.getElementById("warning_box");
    popUp.style.visibility="hidden";
}

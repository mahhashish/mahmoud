// global variables
var tag_reg = 're_c';            // id of tag used with Ajax, for registration
var log_form = '';               // will store the login form
var regx_chr = /^([A-Za-z0-9_-]+)$/;    // RegExp with the characters allowed in Name and Password
var regx_mail = /^([a-zA-Z0-9]+[a-zA-Z0-9._%-]*@([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,4})$/;    // RegExp for e-mail address
var close = '<span class="clos_re" onclick="objLogare.adLogInr();">X</span>';
var close2 = '<div class="clos_re" onclick="show_hide(0, this.parentNode.parentNode.id)">X</div>';

// Show hide elements
function show_hide(show, hide) {
  // if parameters different from 0
  if(show!=0) {
    if(document.getElementById(show)) document.getElementById(show).style.display = 'block';
  }
  if(hide!=0) {
    if(document.getElementById(hide)) { document.getElementById(hide).style.display = 'none'; }
  }
}

// creates the element that displays the area for registration and recovery
function adBox(ad_re, id_re) {
  // if "id_re" (which displays the response) is 1, uses the "in_box" ID
  if(id_re==1) {
    objLogare.adLogInr();  // calls adLogInr() method, to display Login - Register buttons

    // if exists id="in_box", include "ad_re" into it, oterwise create the el_box element
    if(document.getElementById('in_box')) document.getElementById('in_box').innerHTML = ad_re+ close2;
    else {
      // create hidding DIV
      var el_box = document.createElement('div');
      el_box.id = 'el_box';
      el_box.style.height = '100%';
      el_box.innerHTML = '<div id="transp_box"></div><div id="in_box">'+ ad_re+ close2+ '</div>';

      // Adds el_box in body
      var p_baza = document.body;
      var repr = p_baza.childNodes[0];
      p_baza.insertBefore(el_box, repr);
    }
    document.getElementById('el_box').style.display = 'block';
  }
  else {
    document.getElementById(id_re).innerHTML = ad_re+ log_form;
    regEvents();
  }
}

// create an object to work with login form
var objLogare = new Object();
  objLogare.adLogInr = function() {
    // if exists id="log_form"
    if(document.getElementById('log_form')) {
      // if log_form='' store the login form + Close button
      if(log_form=='') log_form = document.getElementById('log_form').innerHTML+ close;

      // replace the form with Login - Register buttons
      document.getElementById('log_form').innerHTML = '<button id="jslog" onclick="objLogare.adLog_form();">Login</button> <button id="jsinr" onclick="ajaxSend(\'submit=Register\', 1); return false;">Register</button>';
    }
  }
  objLogare.adLog_form = function() {
    // add the form, and focus on "nume"
    document.getElementById('log_form').innerHTML = log_form;
    document.getElementById('log_form').nume.focus();
    regEvents();
  }
  objLogare.datLog = function(frm) {
    // gets data from login form, if checkForm() is true
    if(checkForm(frm)) {
      // gets form data and send them to ajaxSend()
      var nume = frm.nume.value;
      var pass = frm.pass.value;
      // if "Remember" checkbox is checked, adds it in "datele"
      var dat_rem = (frm.rem.checked==true) ? '&rem=rem' : '';

      // create the string with data to be send to PHP (name=value pairs) with ajaxSend()
      var  datele = 'login=Login&nume='+nume+'&pass='+pass+ dat_rem;
      ajaxSend(datele, 'log_form');
    }
    return false;
  }

// function that returns the XMLHttpRequest object according to browser
function get_XmlHttp() {
  // the variable that will contain the instance of the XMLHttpRequest object (initially with null value)
  var xmlHttp = null;

  if(window.XMLHttpRequest) {         // for Forefox, Opera, Safari, ...
    xmlHttp = new XMLHttpRequest();
  }
  else if(window.ActiveXObject) {  // for Internet Explorer
    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
  }

  return xmlHttp;
}

// the Ajax function, sends data to a php script and returns the answer
function ajaxSend(datele, id_re) {
  var php_file = 'users/index.php';             // the php file

  if(datele!='o') {
    adBox('<h2 id="lgw">Wait, Loading...</h2>', id_re);      // message displayed till the response is returned
    var datele = datele+ '&ajax='+id_re;              // string with data to be send
  }

  var cerere_http =  get_XmlHttp();		// calls the function for the XMLHttpRequest object
  cerere_http.open("POST", php_file, true);			// Create the request

  // adds  a header to tell the PHP script to recognize the data as is sent via POST
  cerere_http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  cerere_http.send(datele);	      	// calls the send() method with datas as parameter

  // if "datele" different from "o" (is set "o" when the call is with set_interval(), to update online users)
  if(datele!='o') {
    // Check request status. If the response is received completely, will be returned
    cerere_http.onreadystatechange = function() {
      if (cerere_http.readyState == 4) {
        // if in response there is "Welcome", perform Refresh
        if(cerere_http.responseText.search('Welcome')>=0) window.location.reload(true);
        else adBox(cerere_http.responseText, id_re);         // otherwise, pass the response to adBox()
      }
    }
  }
}

// gets data from register form
// if checkForm() is true, processes and transfers the data to ajaxSend()
function datReg(frm) {
  if(checkForm(frm)) {
    // gets values from form fields
	  var nume = frm.nume.value;
    var pass = frm.pass.value;
    var pass2 = frm.pass2.value
    var email = frm.email.value;
    var nrv = frm.nrv.value;

    // create the string with data to be send to PHP (name=value pairs) with ajaxSend()
    var  datele = 'nume='+nume+'&pass='+pass+'&pass2='+pass2+'&email='+email+'&nrv='+nrv+'&submit=Register';
	  ajaxSend(datele, 1);
  }
  return false;
}

// gets data from 'Recover data' link and from the form for Recover / Confirm
function datReCon(frm) {
  // check data with checkForm(), if the result is true, send data to ajaxSend()
  if(checkForm(frm)) {
    // gets data from form fields
    var email = frm.email.value;
    var nrv = frm.nrv.value;
    var submit = frm.submit.value;

    // create the string with data to be send to PHP (name=value pairs) with ajaxSend()
    var  datele = 'email='+email+'&nrv='+nrv+'&rc='+submit+'&submit='+submit;
    ajaxSend(datele, 1);
  }
  return false;
}

// this function is used to check form data
function checkForm(frm) {
  // gets form data
  if(frm.nume) var nume=frm.nume.value.length;
  if(frm.pass) var parola=frm.pass.value.length;
  if(frm.pass2) var parola2=frm.pass2.value.length;
  if(frm.passnew) var passnew=frm.passnew.value.length;
  if(frm.email) var email=frm.email.value;
  if(frm.nrv0) var nr0=frm.nrv0.value;
  if(frm.nrv) var nr=frm.nrv.value;

  // Check name length and to contain only the characters from "regx_chr"
  if (frm.nume && (nume<3 || frm.nume.value.search(regx_chr) == -1)) {
    alert("The Name must contain minimum 3 characters \n\nOnly letters, numbers, and \"-\", \"_\"");
    frm.nume.select();    // makes the nume field selected
    return false;
  }
  // Check password length and to contain only the characters from "regx_chr"
  else if (frm.pass && (parola<7 || frm.pass.value.search(regx_chr) == -1)) {
    alert("The Password must contain minimum 7 characters \n\nOnly letters, numbers, and \"-\", \"_\"");
    frm.pass.select();    // makes the pass field selected
    return false;
  }
  // Check if it's the same password in "Retype password"
  else if (frm.pass2 && parola!=parola2) {
     alert('You must write the same password in the field "Retype password"');
    frm.pass2.select();    // makes the pass2 field selected
    return false;
  }
  // Check the length of the new password (the form in "usrbody.php")
  else if (frm.passnew && (passnew<7 || frm.passnew.value.search(regx_chr) == -1)) {
    alert("The New Password must contains minimum 7 characters \n\nOnly letters, numbers, and \"-\", \"_\"");
    frm.passnew.select();    // makes the passnew field selected
    return false;
  }
  // validate the email
  else if (frm.email && email.search(regx_mail)==-1) {
    alert('Add a correct email address');
    frm.email.select();    // makes the email field selected
    return false;
  }
  // check the verification code
  else if (frm.nrv && frm.nrv0 && nr!=nr0) {
    alert("Incorrect verification code. \n\nAdd: "+nr0);
    frm.nrv.select();    // makes the nrv box selected
    return false;
  }
  else return true;
}

// register events
function regEvents() {
  // to display the image in full window
  if(document.getElementById('imgusr')) document.getElementById('imgusr').onclick = function () {adBox('<img src="'+this.src+'" />', 1);};
  // for the link "Recover data"
  if(document.getElementById('recdat')) document.getElementById('recdat').onclick = function () {ajaxSend('rc=Recover', 1); return false;};
  // for the link "Register"
  if(document.getElementById('linkreg')) document.getElementById('linkreg').onclick = function () {ajaxSend('submit=Register', 1); return false;};
  // for login form
  if(document.getElementById('log_form')) document.getElementById('log_form').onsubmit = function () {return objLogare.datLog(this);};
}



// this function is used to access the function we need after loading page
function addLoadEvent(func) {
  var oldonload = window.onload; 

  // if the parameter is a function, calls it with "onload"
  // otherwise, adds the parameter into a function, and then call it
  if (typeof window.onload != 'function') window.onload = func;
  else { 
    window.onload = function() { 
      if (oldonload) { oldonload(); } 
      func();
    } 
  } 
} 

// access the addLoadEvent() function with the functions that must be executed after loading page
addLoadEvent(regEvents);      // this register the events
addLoadEvent(objLogare.adLogInr);

setInterval("ajaxSend('o',0)", 120000);            // calls ajaxSend() every 2 minutes, to upload online users
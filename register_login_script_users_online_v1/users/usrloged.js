var ar_el_idtitl = new Array();      // will store the elements for Tab-effect
var id_re = 'usreror';        // the id of the tag in which the error wil be displayed
if(document.getElementById('imgusr')) var imgusr = document.getElementById('imgusr').src;       // gets the initial image in user page

// For Tabs effect, parse the elements stored in "ar_el_idtitl", hides each item, then makes visible "vidtitl"
function ftabEfect(ar_el_idtitl, vidtitl) {
  for(var i=0; i<ar_el_idtitl.length; i++) {  
    ar_el_idtitl[i].style.display = 'none';
  }
  vidtitl.style.display = 'block';

  // hides the form for Upload imagie and display a button in its place
  document.getElementById('usrupimg').style.display = 'none';
  document.getElementById('forupimg').style.display = 'block';
}

// For Tabs effect
function tabEfect() {
  // if exist tag with id="ultabs"
  if(document.getElementById('ultabs')) {
    // gets the tag with id="ultabs" (an UL), and makes it visible
    var ultabs = document.getElementById('ultabs');
    ultabs.style.display = 'block';
    var litabs = ultabs.getElementsByTagName('li');        // gets all <li> from "ultabs"

    // traverse "litabs", gets the "title" and adds the element with the ID from title into "ar_el_idtitl"
    // register "onclick" event to each LI, that will call the ftabEfect() function
    for(var i=0; i<litabs.length; i++) {
      if(i==0) litabs[0].style.background = '#d1fed2';      // apply a background to the first item
      ar_el_idtitl[i] = document.getElementById(litabs[i].title);
      litabs[i].onclick = function() {
        this.style.background = '#d1fed2';
        var vidtitl = document.getElementById(this.title);
        ftabEfect(ar_el_idtitl, vidtitl);
      }
    }

    // this function will hide al=l items in "ar_el_idtitl", and makes visible the first element
    ftabEfect(ar_el_idtitl, ar_el_idtitl[0]);
  }
}

// gets data from the form used to change user's email/password (usrModf)
// if checkForm() returns true, adds the values into a string and send it to usrAjaxSend()
function usrModf(frm) {
  if(checkForm(frm)) {
    // gets the values from each form field
    var pass = frm.pass.value;
    var passnew = frm.passnew.value
    var email = frm.email.value;
    var usr = frm.modf.value;

    // create the string with data to be send to PHP (name=value pairs) with usrAjaxSend()
    var datele = 'usr='+usr+'&pass='+pass+'&passnew='+passnew+'&email='+email+'&modf='+usr+'&submit=Modify';

	  usrAjaxSend(datele, id_re);
  }
  return false;
}

// gets data from the form used for optional user's data (usersDat)
// if checkNrChr() returns true, adds the values into a string and send it to usrAjaxSend()
function usersDat(frm) {
  if(checkNrChr(frm, 0, 0)) {
    // gets the value of each field
    var usrnume = frm.usrnume.value;
    var usrpronoun = frm.usrpronoun.value;
    var country = frm.usrcountry.value;
    var city = frm.usrcity.value;
    var adres = frm.usradres.value;
    var bday = frm.usrbday.value;
    var bmonth = frm.usrbmonth.value
    var byear = frm.usrbyear.value;
    var ym = frm.usrym.value
    var msn = frm.usrmsn.value;
    var site = frm.usrsite.value;
    var ocupation = frm.usrocupation.value;
    var interes = frm.usrinteres.value
    var transmit = frm.usrtransmit.value;
    var usr = 'user';

    // create the string with data to be send to PHP (name=value pairs) with usrAjaxSend()
    var datele = 'usr='+usr+'&usrnume='+usrnume+'&usrpronoun='+usrpronoun+'&usrcountry='+country+'&usrcity='+city+'&usradres='+adres+'&usrbday='+bday+'&usrbmonth='+bmonth+'&usrbyear='+byear+'&usrym='+ym+'&usrmsn='+msn+'&usrsite='+site+'&usrocupation='+ocupation+'&usrinteres='+interes+'&usrtransmit='+transmit+'&submit=Trimite';

	  usrAjaxSend(datele, id_re);
  }
  return false;
}

// the function for Upload image
// adds an iframe into the "ifrmup" element, to submit the image through this iframe
function uplImg(frm) {
  // if "frm" is a string
  if(typeof(frm)=='string') {
    // if there is "Error" into the response, adds the initial image, and Alert the error, else replace the image
    if(frm.search('Error')>=0) {
      document.getElementById('imgusr').src = imgusr;
      alert(frm);
    }
    else document.getElementById('imgusr').src = frm+'?'+Math.floor(Math.random()*11);
  }
  else {
    // display the "Loading..." image, and adds the iframe to submit the image
    document.getElementById('imgusr').src = 'usersimg/loading.gif';
    document.getElementById('ifrmup').innerHTML = '<iframe name="sendimg" id="sendimg" src="users/index.php" width="400" height="150" />';
  }
}

// checks the number of characters
function checkNrChr(text, maxlength, countchr) {
  // if maxlength, and countchr different from 0, it's a call from "onkeydown"/"onkeyup"
  // if their value is 0, it's a call from "onsubmit"
  if(maxlength!=0 && countchr!=0) {
    // check if the maximum numbers of characters is exceded
    if (text.value.length>maxlength) alert("Plese add maximum "+maxlength+" characters");
    // Show the number of characters left (into the tag with id passed in "countchr")
    else document.getElementById(countchr).innerHTML = '<b>'+(maxlength-text.value.length)+'</b> characters remaining';
  }
  else if(maxlength==0 && countchr==0) {
    // gets the number of characters in: usrocupation, usrinteres si usrtransmit
    // check if it was exceeded the maximum number of characters in each
    if(text.usrocupation.value.length>500) {
      alert('Please add maximum 500 characters to "Ocupation"');
      text.usrocupation.focus();
      return false;
    }
    else if(text.usrinteres.value.length>500) {
      alert('Please add maximum 500 characters to "Interests / Hobbies"');
      text.usrinteres.focus();
    }
    else if(text.usrtransmit.value.length>1000) {
      alert('Please add maximum 500 characters to "Things I want to say"');
      text.usrtransmit.focus();
      return false;
    }
  }
  return true;
}

// Ajax function that sends data to a PHP file
function usrAjaxSend(datele, id_re) {
  var ad_re = document.getElementById(id_re);       // element in which Ajax displays the response
  ad_re.innerHTML = '<h3>Wait Loading...</h3>';      // message  displayed until the response is received
  ad_re.style.display = 'block';
  window.scroll(0,0);                // Scroll to the top of the page

  // define the string that will be send, and the php file address
  var datele = datele+ '&ajax='+id_re;
  var php_file = 'users/index.php';

  var cerere_http =  get_XmlHttp();		// calls the function that create the XMLHttpRequest object
  cerere_http.open("POST", php_file, true);			// create the request

  // adds  a header to tell the PHP script to recognize the data as is sent via POST
  cerere_http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  cerere_http.send(datele);		// Sends the request, and the string with data

    // Check request status. If the response is received completely, will be returned
  cerere_http.onreadystatechange = function() {
    if (cerere_http.readyState == 4) {
      alert(cerere_http.responseText);

      // if there is "Error" into response, display it, otherwise, reloads the page
      if(cerere_http.responseText.search('Error')>=0) {
        ad_re.innerHTML = cerere_http.responseText;
        ad_re.style.display = 'block';
      }
      else window.location.reload(true);    // reloads the page
    }
  }
}

// register events
function regEventsUsr() {
  // for the button that shows the form for upload image
  document.getElementById('forupimg').onclick = function () {
    // hides the button and makes the Upload form visible
  document.getElementById('usrupimg').style.display = 'block';
  this.style.display = 'none';
  };

  // for the Upload form
  document.getElementById('usrupimg').onsubmit = function () {return uplImg(this);};
  // for the form used to add user's optional data
  document.getElementById('usrform2').onsubmit = function () {return usersDat(this);};
}

addLoadEvent(tabEfect);       // call the function that will access tabEfect() after loading the page
addLoadEvent(regEventsUsr);      // to execute the function that registers events
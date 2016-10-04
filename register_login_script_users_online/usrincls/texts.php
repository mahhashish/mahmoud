<?php
// Texts added in script (English)
$en_site = array(
  'create_tables'=>'<h4>The "%s" table was created</h4>',
  'create_admin'=>'<h4>Admin account successfully created</h4>',
  'max'=>'Maximum',
  'code'=>'Code: ',
  'rank'=>'Rank',
  'title'=>'Title: ',
  'name'=>'Name',
  'pronoun'=>'Pronoun: ',
  'favorites'=>'Favorites',
  'birthday'=>'Birthday: ',
  'location'=>'Location: ',
  'country'=>'Country: ',
  'city'=>'City: ',
  'address'=>'Address: ',
  'day'=>'Day: ',
  'month'=>'Month: ',
  'year'=>'Year: ',
  'pass'=>'Password: ',
  'site'=>'Your website: ',
  'loading'=>'Loading...',
  'modify'=>'Modify',
  'delete'=>'Delete',
  'delsel'=>'Delete selected',
  'delusr'=>'Delete User',
  'confdel'=>'The selected element will be deleted without possibility of recovery.\nIf you want to delete, click OK',
  'close'=>'Close',
  'codev'=>'Verification code: ',
  'codev0'=>'Add this verification code: ',
  'delok'=>' - Successfully deleted',
  'unsubscribe'=>'Unsubscribe',
  'prev'=>'Previous',
  'first'=>'First',
  'next'=>'Next',
  'last'=>'Last',
  'send'=>'Send',
  'allusr'=>'All Users',
  'selby'=>'Select by',
  'eror_name'=>'The Name must contain between 3 and 32 characters. \n Only letters, numbers, - and _',
  'eror_pass'=>'Incorrect password',
  'eror_email'=>'Add a correct e-mail address',
  'eror_codev'=>'Incorrect verification code',
  'eror_noselchk'=>'Not selected element to delete',
  'eror_delfile'=>'Unable to delete the file: ',
  'eror_rnkdeladmin'=>'The first Admin can not be deleted nor his rank changed',
  'eror_base'=>array(
    'construct'=>'The argument in accesing the class must be an Array',
    'setconn'=>'Unable to connect to MySQL: ',
    'sqlexecute'=>'Can`t execute the sql query',
    'upext'=>'Error: The file %s has not an allowed extension type',
    'upmaxsize'=>'Error: The file %s exceeds the allowed size %s KB',
    'upimgwh'=>'Error: image width and height must be maximum %s x %s',
    'upfiledb'=>'Error: The file path could not be added in database: ',
    'upfile'=>'Error: Unable to Upload the file: %s'
  ),
  'msgs'=>array(
    'title'=>'Messages',
    'title2'=>'Messages posted by visitors',
    'usrpg'=>'%s page',
    'allowmsg'=>'To add messages you must be logged in',
    'nomsg'=>'No messages',
    'addmsg'=>'Add Message',
    'fcamail'=>'Notify me when new message',
    'fcupimg'=>'Optional, you can include a picture',
    'fcshowmail'=>'Display my e-mail',
    'nrchrtxt'=>'Remaining characters: ',
    'chrmsg'=>'Write your message (<span id="countdown">Maximum 600 characters</span>)',
    'jsadd'=>'Thank you %s \\n Your message was added.',
    'jsdelete'=>'Message(s) successfully deleted',
    'notifysub'=>'New message added on %s',
    'notifymsg'=>'Hi,<br/>
      Email sent from %s <br/>
      New message added on the website %s .<br/>
      You can see the new message on the page: %s .<br/><br/><br/>
      If you want to not receive other notifications when new messages are added on that page, to unsubscribe click on this link:<br/>
      %s , or copy and access it in your browser.<br/><br/>
      If you want, visit <a href="http://coursesweb.net">coursesweb.net</a><br/><br/>
      <i>With respect,<br/>
      Admin</i>',
    'unstitl'=>'Unsubscribe notification on new messages',
    'unsmsg'=>'To unsubscribe, add your e-mail address',
    'unsubscribe'=>'Succesfully unsubscribed',
    'noreset'=>'No new messages after the last reset',
    'lastreset'=>'Last reset was done on the message added on',
    'newmsg'=>'Latest Messages Posted to all Users',
    'newmsgad'=>'Since then were added the following messages:',
    'resetdt'=>'Reset date of the last check',
    'eror_form'=>'Not all form fields are received',
    'eror_sesadd'=>'You already added a message in the last 5 minutes',
    'eror_msg'=>'The message must contain between 5 and 600 characters (including the tags)',
    'eror_logadmin'=>'Incorrect admin Name or Password',
    'eror_maxchrtxt'=>'You can add maximum 600 characters',
    'eror_delete'=>'Unable to delete the messages in database, ',
    'eror_resetcheck'=>'Reseting time of the last check failed',
    'eror_sesunsub'=>'You already unsubscribed',
    'eror_unsubscribe'=>'Incorrect URL address to unsubscribe',
    'eror_unsub'=>'Error on update data for Unsubscribe notification'
  ),
  'datetime'=>array(
    'sec'=>'second/s',
    'min'=>'minute/s',
    'd'=>'day/s',
    'm'=>'month/s',
    'h'=>'hour/s',
    'y'=>'year/s'
  ),
  // for Users class
  'users_logform'=>array(
    'email'=>'E-Mail',
    'name'=>' Name: ',
    'pass'=>' Password: ',
    'rem'=>'Remember',
    'recdat'=>'Recovery Data',
    'orlogw'=>'Or Log In with:',
    'fblogin'=>'Login with Facebook',
    'yhlogin'=>'Login with Yahoo',
    'gologin'=>'Login with Google',
    'myacc'=>'My Account',
    'login'=>'LOGIN',
    'register'=>'Register'
  ),
  'users_loged'=>array(
    'userpage'=>'Personal page',
    'lout'=>'LogOut',
    'alertlout'=>'Logged Out',
    'forcelout'=>'Logged Out\nThere is another Login with this account.\nYou can re-login'
  ),
  'register'=>array(
    'regmsg'=>'<center><h1>Succes!</h1>
    <font size="4">Thank you <b><font color="blue">%s</font></b>, the registration has been completed successfully.</font><br/><br/>You can Log in.</center>',
    'mailsubject'=>'Confirm the registration on: ',
    'mailmsg'=>"               Hi, <br/>
    You received this message because you have to confirm your registration on the website %s <br/><br/>
To confirm the registration, click on the following link (<i>or copy it in the address bar of your browser</i>):<br/><br/>
      <center><b> %s </b></center><br/><br/>
    Your login data:<br/><br/>
      E-Mail = %s <br/>
      Password = %s <br/><br/><br/>
      <center><i>If you want, visit also <a href=\"http://coursesweb.net/\">coursesweb.net</a></i></center><br/><br/>
<i>Thanks, respectfully,<br/> Admin</i><br/>",
    'mailsent'=>'<center><h3>Registration performed successfully</h3>A message with a link to confirm your registration will be send to the e-mail<u> %s </u>.<br/><br/> If you have not received the email, check the Spamm folder, too.<br/><br/> After confirmation you can log in.</center>',
    'regtxt'=>'<h2>Registration</h2><div id="form_re">
      <p><br/><b> - Add your registration data and this code: <span  id="codev0">%s</span></b></p>- <i>You must use a valid e-mail address, you will receive a message to confirm the registration.</i><hr style="width:88px;" /><br/>',
    'pass2'=>'Retype password: ',
  ),
  'recov'=>array(
    'eror_re'=>'<div class="eror">Error when checking your data.<br/>Try again.</div><br/>',
    'eror_confirm'=>'Confirmation failed, error: ',
    'formcodev'=>'<div id="form_re">
  <p>Add the e-mail address you used for registration and this verification code: <span id="codev0">%s</span></p><br/>',
    'mailsubj1'=>'Recovery registration data',
    'mailmsg1'=>"               Hy<br/> \n
          You received this email due to a request to recover your registration data on %s \n\n",
    'mailsubj2'=>'Registration Confirmation',
    'mailmsg2'=>"               Hi<br/> \n
          You received this email due to a request to resend the link for registration confirmation.<br/> \n\n
      To confirm the registration on %s , click on the following link:<br/><br/> \n
      %s <br/> \n\n",
    'mailmsgld'=>"<br/>Your login data are:<br/><br/> \n
              E-Mail = %s <br/> \n
              Password = %s <br/><br/> \n\n
      <center><i>If you want, visit also: <a href=\"http://coursesweb.net/\">coursesweb.net</a></i></center><br/><br/> \n\n
        Have a good day<br/> \n
        With respect, Admin",
    're'=>'<center>The requested data are sent to: <b> %s </b>.<br/>
          Check the Spamm folder, too. If you have not received the email, please contact the site administrator.
          <br/><br/>Thank you</center>',
    'reconfirm'=>'<center><h2 style="color:blue;">Confirmation approved</h2><h4>Now you can log on the site. <a href="/">Home Page</a></h4></center>',
    'reunconfirm'=>'<center><font color="red"><h2>Confirmation Unapproved</h2></font><h4>The URL for confirmation is incorrect</h4><br/><br/> - To request a new e-mail with the link for confirmation: %s <br/><br/><i>Or contact the site administrator.</i></center>'
  ),
  'userpage'=>array(
    'title'=>'User page: ',
    'setrankmsg'=>'Change Rank, from -1 to 9 (<i>-1 = banned, 0 = unconfirmed, 9 = Administrator</i>)',
    'setrank'=>'Set Rank',
    'setrankok'=>'<b style="color:blue;">Rank updated</b>',
    'dtreg'=>'Registered date',
    'dtvisit'=>'Last logged date',
    'notloged'=>'Not logged yet',
    'visits'=>'Visits number: ',
    'usrdata'=>'Additional Data',
    'modfdata'=>'Your data successfully updated',
    'adimg'=>'Add image:',
    'forupimg'=>'Upload / Change image',
    'totalusr'=>'Total registered users: ',
    'newusr'=>'Newest User: ',
    'online'=>'Online users:',
    'changeep'=>'Change E-mail /Password',
    'editopt'=>'Edit optional data',
    'ocupation'=>'Occupation:',
    'interes'=>'Interests / Hobbies:',
    'editreg'=>'Edit registration data',
    'chgmail'=>'If you chahge the e-mail address, you will receive a link to the new e-mail address, to confirm it.',
    'pass'=>'Current password:',
    'passnew'=>'New password:',
    'transmit'=>'Things I want to say:',
    'aditionals'=>'Aditional Data',
    'optionals'=>'Optional Data',
    'nofav'=>'Not favorite links',
    'adfav'=>'Add Favorite link (without http://)<br/><i>Each Link, and Name can have maximum 110 characters</i>',
    'favhave'=>'You can have maximum 12 Favorite links',
    'adfavok'=>'Favorite link successfully registered',
    'adfavbt'=>'Add Favorite',
    'max500chr'=>'You can add maximum 500 characters',
    'max1000chr'=>'You can add maximum 1000 characters',
    'maxoptdata'=>' maximum characters allowed',
    'usetags'=>'In the last textarea you can use these BBCODE for HTML format:<br/>[b]text[/b] = <i>&lt;b&gt;text&lt;/b&gt;</i> / [i]text[/i] = <i>&lt;i&gt;text&lt;/i&gt;</i><br/>[u]text[/u] = <i>&lt;u&gt;text&lt;/u&gt;</i><br/>[block]text[/block] = <i>&lt;blockquote&gt;text&lt;/blockquote&gt;</i>',
    'mailsubject'=>'Registration data updated',
    'mailmsg'=>"            Hi,<br/><br/>
              Your new registration data on the website %s :<br/> %s <br/>
        E-mail = %s <br/>
        Password = %s <br/><br/><br/>
  <i>Respectfully,<br/> Admin</i><br/><center>",
    'mailsent'=>'\n An email with your new data is sent to: %s',
    'regdata'=>'Your data were successfully registered',
    'eror_urlformat'=>'Incorrect URL address.\n Add an URL address without http:// \n Ex.: coursesweb.net/ajax',
    'eror_notusr'=>'<h2>User "<i>%s</i>" not registered</h2>',
    'eror_erortitl'=>'The Title must contain between 3 and 110 characters',
    'eror_moddata'=>'Error: Incomplete fields from form',
    'eror_pass'=>'Error: Incorrect current password',
    'eror_modmp'=>'Error: Accessing modfMP with incorrect data',
    'eror_regdata'=>'Error: Your optional data could not be saved: '
  ),
  'allusers'=>array(
    'allyhusr'=>'Yahoo Users',
    'allgousr'=>'Google Users',
    'allfbusr'=>'Facebook Users',
    'allregusr'=>'Registered Users',
    'findusr'=>'&nbsp; &nbsp; // Or, Find Users with: %s in: ',
    'usrid'=>'User ID',
    'dtreg'=>'Registered Date',
    'visit'=>'Visits',
  ),
  'eror_users'=>array(
    'eror_regmail'=>'<div class="eror">There is not registration with the e-mail:<br/><i><u> %s </u></i></div><br/>',
    'username'=>'The Name must contain between 3 and 32 characters. \n Only letters, numbers, - and _',
    'insession'=>'Incorrect data logging session',
    'inpass'=>'Incorrect password',
    'ban'=>'The account: <b><em>%s</em></b> is banned.<br/>Contact the Website Admin',
    'datachr'=>'The data should contain only letters, numbers, - and _',
    'pass'=>'The Password must contain between 7 and 18 characters. \n Only letters, numbers, - and _',
    'unconfirmed'=>'<center><h4 class="eror">Registration for <u>%s</u> is unconfirmed.</h4>Check your e-mail used for registration (including in Spamm directory), for the message with the confirmation link.<br/><br/>If you want to request a new confirmation mail <a href="%s?rc=Confirm" id="reconfirm">Re-Confirm</a></center>',
    'logattempt'=>'Exceeding number of login attempts.<br/>You can retry after:<br/><b>%s</b>',
    'findusr'=>'The Text field must contain between 3 and 55 characters. \n Only letters, numbers, space, dot, @, - and _'
  ),
  'eror_reg'=>array(
    'nofields'=>'Incorrect form fields',
    'construct'=>'The first parameter should be an array',
    'sendmailreg'=>'The email with the confirmation link can`t be sent',
    'register'=>'<h4>Error:</h4><i> %s </i><br/>Unable to perform your registration for the E-Mail: <b> %s </b>.',
    'pass2'=>'You must write the same password in the field Retype password',
    'passnew'=>'The New Password must contains minimum 7 characters. \n Only letters, numbers, - and _',
    'namexist'=>"The name: <u> %s </u> already registered, please choose other name",
    'mailexist'=>"The e-mail: <u> %s </u> is already used for registration",
    'ipexist'=>'There is already a registration with your IP.<br/>If you think that is an error, contact the administrator'
  )
);


// For Rumano language
$ro_site = array(
  'create_tables'=>'<h4>Tabelul "%s" a fost creat</h4>',
  'create_admin'=>'<h4>Contul pentru Administrator a fost creat</h4>',
  'max'=>'Maxim',
  'code'=>'Cod: ',
  'rank'=>'Rang',
  'title'=>'Titlu: ',
  'name'=>'Nume',
  'pronoun'=>'Pronume: ',
  'favorites'=>'Favorite',
  'birthday'=>'Data nastere: ',
  'location'=>'Adresa: ',
  'country'=>'Tara: ',
  'city'=>'Oras: ',
  'address'=>'Domiciliu: ',
  'day'=>'Zi: ',
  'month'=>'Luna: ',
  'year'=>'An: ',
  'pass'=>'Parola: ',
  'site'=>'Web site: ',
  'loading'=>'Incarcare...',
  'modify'=>'Modifica',
  'delete'=>'Sterge',
  'delsel'=>'Sterge selectari',
  'delusr'=>'Sterge Utilizator',
  'confdel'=>'Elementul selectat va fi sters fara sa mai poata fi recuperat,\nDaca doriti sa stergeti, clic OK',
  'close'=>'Inchide',
  'codev'=>'Cod de verificare: ',
  'codev0'=>'Adaugati acest cod de verificare: ',
  'delok'=>' - Stergere efectuata',
  'unsubscribe'=>'Dezabonare',
  'prev'=>'Anterior',
  'first'=>'Primul',
  'next'=>'Urmator',
  'last'=>'Ultimul',
  'send'=>'Trimite',
  'allusr'=>'Toti Utilizatorii',
  'selby'=>'Selectare dupa',
  'eror_name'=>'Numele trebuie sa contina intre 3 si 32 caractere. \n Numai litere, numere si liniute -, _',
  'eror_pass'=>'Parola incorecta',
  'eror_email'=>'Adaugati o adresa de e-mail corecta',
  'eror_codev'=>'Cod de verificare incorect',
  'eror_noselchk'=>'Nici un element selectat pentru stergere',
  'eror_delfile'=>'Nu poate sterge fisierul: ',
  'eror_rnkdeladmin'=>'Administratorul principal nu poate fi sters, nici rangul lui modificat',
  'eror_base'=>array(
    'construct'=>'Argumentul la accesarea clasei trebuie sa fie de tip Array',
    'setconn'=>'Nu se poate conecta la MySQL: ',
    'sqlexecute'=>'Nu se poate executa comanda SQL',
    'upext'=>'Error: Fisierul %s nu are tipul de extensie permis',
    'upmaxsize'=>'Error: Fisierul %s depaseste marimea permisa %s KB',
    'upimgwh'=>'Error: Lungimea si Inaltimea imaginii trebuie sa nu depaseasca %s x %s',
    'upfiledb'=>'Error: Numele fisierului nu poate fi adaugat in baza de date: ',
    'upfile'=>'Error: Nu poate copia fisierul: %s'
  ),
  'msgs'=>array(
    'title'=>'Mesaje',
    'title2'=>'Mesaje adaugate de utilizatori',
    'usrpg'=>'%s pagina',
    'allowmsg'=>'Ca sa puteti adauga mesaj, trebuie sa fiti autentificat',
    'nomsg'=>'Nici un mesaj',
    'addmsg'=>'Adauga Mesaj',
    'fcamail'=>'Anunta-ma cand e adaugat mesaj',
    'fcupimg'=>'Optional, puteti include o imagine',
    'fcshowmail'=>'Afiseaza adresa de e-mail',
    'nrchrtxt'=>'Caractere ramase: ',
    'chrmsg'=>'Scrieti mesajul dv. (<span id="countdown">Maxim 600 caractere</span>)',
    'jsadd'=>'Multumim %s \\n YMesajul dv. a fost adaugat',
    'jsdelete'=>'Mesajul a fost sters',
    'notifysub'=>'Mesaj nou adaugat pe %s',
    'notifymsg'=>'Salut,<br/>
      Email trimis de la %s <br/>
      Un nou mesaj a fost adaugat pe site-ul %s .<br/>
      Puteti citi noul mesaj la pagina: %s .<br/><br/><br/>
      Daca doriti sa nu mai primiti instiintare cand e adaugat alt mesaj in acea pagina, pentru dezabonare clic pe acest link:<br/>
      %s , sau copiati-l in bara de adrese a browser-ului.<br/><br/>
      Daca doriti, vizitati <a href="http://www.coursesweb.net">www.coursesweb.net</a><br/><br/>
      <i>Cu respect,<br/>
      Admin</i>',
    'unsubscribe'=>'Ati fost dezabonat',
    'noreset'=>'Nu sunt mesaje noi de la ultima resetare',
    'lastreset'=>'Ultima resetare a fost facuta la mesajul din data',
    'newmsg'=>'Ultimele mesaje adaugate la toti Utilizatorii',
    'newmsgad'=>'Au fost adaugate urmatoarele mesaje:',
    'resetdt'=>'Reseteaza data ultimei verificari',
    'eror_form'=>'Nu sunt transmise toate campurile de formular necesare',
    'eror_sesadd'=>'Ati adaugat deja un mesaj in ultimele 5 minute',
    'eror_msg'=>'Mesajul trebuie sa contina intre 5 si 600 caractere (incluzand si tagurile)',
    'eror_logadmin'=>'Nume sau Parola administrator incorecte',
    'eror_maxchrtxt'=>'Puteti adauga maxim 600 caractere',
    'eror_delete'=>'Nu poate sterge mesajul din baza de date, ',
    'eror_resetcheck'=>'Nu a putut fi resetata data ultimei verificari',
    'eror_sesunsub'=>'Ati fost deja dezabonat',
    'eror_unsubscribe'=>'ID sau e-mail incorect pentru dezabonare',
    'eror_unsub'=>'Eroare efectuare dezabonare in baza de date'
  ),
  'datetime'=>array(
    'sec'=>'secunde',
    'min'=>'minut/e',
    'd'=>'zi/zile',
    'm'=>'luna/luni',
    'h'=>'ora/ore',
    'y'=>'an/i'
  ),
  // for Users class
  'users_logform'=>array(
    'email'=>'E-Mail',
    'name'=>' Nume: ',
    'pass'=>' Parola: ',
    'rem'=>'Tine minte',
    'recdat'=>'Recuperare Date',
    'orlogw'=>'Sau autentifica-te cu:',
    'fblogin'=>'Logare cu Facebook',
    'yhlogin'=>'Logare cu Yahoo',
    'gologin'=>'Logare cu Google',
    'myacc'=>'Contul meu',
    'login'=>'LOGARE',
    'register'=>'Inregistrare'
  ),
  'users_loged'=>array(
    'userpage'=>'Pagina Personala',
    'lout'=>'Iesire',
    'alertlout'=>'Logged Out',
    'forcelout'=>'Logged Out\nExista o alta autentificare cu acest cont.\nVa puteti re-autentifica'
  ),
  'register'=>array(
    'regmsg'=>'<center><h1>Suces!</h1>
    Multumim <b style="color:blue">%s</b>, inregistrarea a fost efectuata.<br/><br/>Va puteti autentifica.</center>',
    'mailsubject'=>'Confirmare inregistrare pe: ',
    'mailmsg'=>'               Salut, <br/>
    Primiti acest mesaj pentru a confirma inregistrarea pe site-ul %s <br /><br />
    Pentru confirmare, efectuati clic pe urmatorul link (<i>sau copiati-l in bara de adrese a site-ului</i>):<br/><br/>
      <center><b> %s </b></center><br/><br/>
    Datele dv. de autentificare:<br/><br/>
      E-Mail = %s <br/>
      Parola = %s <br/><br/><br/>
      Acest site a fost realizat din pasiune si pentru a fi de ajutor celor care doresc sa invete Limba Spaniola, Engleza, sau Creare de site-uri.<br />
Totul este gratuit, o fapta este cu adevarat de ajutor atunci cand este oferita gratuit.<br />
<b>Si tu poti fi de ajutor, prin a face cunoscut acest site</b>.<br />
- In primul rand ii vei ajuta pe cei care doresc sa invete si cauta pe net printr-o multime de site-uri, iar datorita tie poate vor gasi aici materiale care le sunt de folos si iti vor multumi.<br />
- In al doilea rand, vei ajuta si la promovarea acestui site.<br />
Daca ai un site sau blog personal si doresti sa adaugi o legatura catre MarPlo.net, copie codul de mai jos in una din paginile site-ului sau blog-ului tau.<br><br /><center>
<form method="POST" action=""><b>Cod pentru legatura la MarPlo.net</b><br />
  <textarea rows="2" cols="50"><a href="http://www.marplo.net/" title="Cursuri Jocuri si Anime">Cursuri Gratuite</a></textarea>
</form><br/><br/>
<em>Cu respect,<br/> <a href="http://www.marplo.net/" title="Cursuri Jocuri si Anime">www.MarPlo.net</a></em></center><br/>',
    'mailsent'=>'<center><h3>Inregistrare efectuata</h3>Un mesaj cu un link de confirmare a inregistrarii va fi trimis la adresa de e-mail <u> %s </u>.<br/><br/> Daca nu ati primit email-ul, verificati si in directorul Spamm.<br/><br/>Dupa confirmare va puteti autentifica pe site.</center>',
    'regtxt'=>'<h2>Inregistrare</h2><div id="form_re">
      <p><br/><b> - Adaugati datele dv. pentru inregistrare, si codul: <span  id="codev0">%s</span></b></p>- <i>Trebuie sa folositi o adresa de e-mail corecta, la acea adresa veti primi un mesaj pentru confirmarea inregistrarii.</i><hr style="width:88px;" /><br/>',
    'pass2'=>'Confirmare parola: ',
  ),
  'recov'=>array(
    'eror_re'=>'<div class="eror">Eroare la verificarea datelor.<br/>Incercati din nou.</div><br/>',
    'eror_confirm'=>'Confirmare esuata, eroare: ',
    'formcodev'=>'<div id="form_re">
  <p>Adaugati adresa de e-mail folosita la inregistrare, si codul de verificare: <span id="codev0">%s</span></p><br/>',
    'mailsubj1'=>'Recuperare date de inregistrare',
    'mailmsg1'=>"               Salut<br/> \n
          Ati primit acest email datorita solicitarii de recuperare date de autentificare pe %s \n\n",
    'mailsubj2'=>'Confirmare Inregistrare',
    'mailmsg2'=>"               Salut<br/> \n
          Ati primit acest email datorita solicitarii de retrimitere a link-ului pentru confirmare inregistrare.<br/> \n\n
      Pentru a confirma inregistrarea pe %s , clic pe urmatorul link:<br/><br/> \n
      %s <br/> \n\n",
    'mailmsgld'=>"<br/>Datele dv. de autentificare:<br/><br/> \n
              E-Mail = %s <br/> \n
              Parola = %s <br/><br/> \n\n
      <center><i>Daca doriti, vizitati si: <a href=\"http://www.marplo.net/\">www.marplo.net</a></i></center><br/><br/> \n\n
        Sa aveti o zi buna<br/> \n
       Cu respect, Admin",
    're'=>'<center>Datele solicitate sunt trimise la: <b> %s </b>.<br/>
         Verificati si in directorul de Spamm. Daca nu ati primit email-ul, contactati administratorul site-ului.
          <br/><br/>Cu respect</center>',
    'reconfirm'=>'<center><h2 style="color:blue;">Confirmare aprobata</h2><h4>Acum va puteti autentifica pe site. <a href="/">Pagina Principala</a></h4></center>',
    'reunconfirm'=>'<center><h2 style="color:red">Confirmare neaprobata</h2><h4>Adresa URL pentru confirmare este incorecta</h4><br/><br/> - Pentru a solicita un nou email cu link de confirmare: %s <br/><br/><i>Sau contactati administratorul.</i></center>'
  ),
  'userpage'=>array(
    'title'=>'Pagina: ',
    'setrankmsg'=>'Modifica Rang, intre -1 si 9 (<i>-1 = banat, 0 = neconfirmat, 9 = Administrator</i>)',
    'setrank'=>'Setare Rang',
    'setrankok'=>'<b style="color:blue;">Rang actualizat</b>',
    'dtreg'=>'Data Inregistrare',
    'dtvisit'=>'Data ultima Logare',
    'notloged'=>'Nici o logare',
    'visits'=>'Numar vizitari: ',
    'usrdata'=>'Date suplimentare',
    'modfdata'=>'Datele dv. au fost actualizate',
    'adimg'=>'Adauga imagine:',
    'forupimg'=>'Upload / Schimba imagine',
    'totalusr'=>'Total utilizatori inregistrati: ',
    'newusr'=>'Ultimul Utilizator: ',
    'online'=>'Utilizatori Online:',
    'changeep'=>'Modifica E-mail /Parola',
    'editopt'=>'Editare optionale',
    'ocupation'=>'Ocupatie:',
    'interes'=>'Interese / Hobiuri:',
    'editreg'=>'Editare date inregistrare',
    'chgmail'=>'Daca schimbati adresa de e-mail, veti primi un link la noua adresa de e-mail, pentru confirmare.',
    'pass'=>'Parola Actuala:',
    'passnew'=>'Parola noua:',
    'transmit'=>'Lucruri pe care vreau sa le transmit:',
    'aditionals'=>'Date Suplimentare',
    'optionals'=>'Date Optionale',
    'nofav'=>'Nu sunt link-uri favorite',
    'adfav'=>'Adauga link Favorit (Fara http://)<br/><i>Fiecare Link, si Nume poate avea maxim 110 caractere</i>',
    'favhave'=>'Puteti avea maxim 12 link-uri Favorite',
    'adfavok'=>'Link-ul adaugat a fost inregistrat',
    'adfavbt'=>'Adauga Favorite',
    'max500chr'=>'Puteti adauga maxim 500 caractere',
    'max1000chr'=>'Maxim 1000 caractere',
    'maxoptdata'=>' caractere ramase',
    'usetags'=>'In ultimul textarea puteti folosi aceste BBCODE pt. format HTML:<br/>[b]text[/b] = <i>&lt;b&gt;text&lt;/b&gt;</i> / [i]text[/i] = <i>&lt;i&gt;text&lt;/i&gt;</i><br/>[u]text[/u] = <i>&lt;u&gt;text&lt;/u&gt;</i><br/>[block]text[/block] = <i>&lt;blockquote&gt;text&lt;/blockquote&gt;</i>',
    'mailsubject'=>'Date de inregistrare modificate',
    'mailmsg'=>"            Buna ziua,<br/><br/>
              Noile date de inregistrare pe %s :<br/> %s <br/>
        Parola = %s <br/>
        E-mail = %s <br/><br/><br/>
  <i>Cu bine,<br/> Admin</i><br/><center>",
    'mailsent'=>'\n Un email cu noile date e trimis la: %s',
    'regdata'=>'Datele dv. au fost inregistrate',
    'eror_urlformat'=>'Adresa URL incorecta.\n Adaugati o adresa URL, fara http:// \n Ex.: marplo.net/ajax',
    'eror_notusr'=>'<h2>Utilizator "<i>%s</i>" nu e inregistrat</h2>',
    'eror_erortitl'=>'Numele poate avea intre 3 si 110 caractere',
    'eror_moddata'=>'Error: Campuri incomplete de la formular',
    'eror_pass'=>'Error: Parola actuala incorecta',
    'eror_modmp'=>'Error: Accesare modfMP cu date incorecte',
    'eror_regdata'=>'Error: Datele dv. optionale nu au putut fi salvate: '
  ),
  'allusers'=>array(
    'allyhusr'=>'Utilizatori Yahoo',
    'allgousr'=>'Utilizatori Google',
    'allfbusr'=>'Utilizatori Facebook',
    'allregusr'=>'Utilizatori Inregistrati',
    'findusr'=>'&nbsp; &nbsp; // Sau, gaseste utilizatorii cu: %s in: ',
    'usrid'=>'ID',
    'dtreg'=>'Data Inregistrare',
    'visit'=>'Vizite',
  ),
  'eror_users'=>array(
    'eror_regmail'=>'<div class="eror">Nu exista inregistrare cu adresa de e-mail: <i><u> %s </u></i></div><br/>',
    'insession'=>'Date Sesiune logare incorecte',
    'inpass'=>'Parola Incorecta',
    'ban'=>'Contul <b>%s</b> e dezactivat. Contactati Administratorul',
    'datachr'=>'Datele trebuie sa contina numai Litere, Numere si Liniute -, _',
    'username'=>'Numele trebuie sa contina intre 3 si 32 caractere. \n Numai litere, numere si liniute -, _',
    'pass'=>'Parola trebuie sa contina intre 3 si 32 caractere. \n Numai litere, numere si liniute -, _',
    'namenoreg'=>'Numele <b>%s</b> nu e inregistrat',
    'unconfirmed'=>'<center><h4 class="eror">Inregistrarea pt. <u>%s</u> e neconfirmata.</h4>Verificati adresa dv. de e-mail folosita la inregistrare (inclusiv in directorul Spamm), pentru mesajul cu link-ul de confirmare.<br /><br />Daca doriti sa primiti un nou mesaj cu link-ul de confirmare <a href="%s?rc=Confirm" id="reconfirm">Re-Confirm</a></center>',
    'logattempt'=>'Ati depasit nr. incercari permise pt. autentificare.<br/>Puteti reincerca dupa:<br/><b>%s</b>',
    'findusr'=>'Textul in caseta trbuie sa contina intre 3 si 55 caractere. \n Numai litere, numere, spatiu, punct, @, - si _'
  ),
  'eror_reg'=>array(
    'nofields'=>'Campuri de formular Incorecte',
    'construct'=>'Primul parametru trebuie sa fie de tip array',
    'sendmailreg'=>'Email-ul cu link-ul de confirmare nu poate fi trimis',
    'register'=>'<h4>Error:</h4><i> %s </i><br/>Nu se poate realiza inregistrarea pt. contul <b> %s </b>.',
    'pass2'=>'Trebuie sa adaugati aceeasi parola in casuta Confirmare Parola',
    'passnew'=>'Parola trebuie sa contina minim  caractere. \n Litere, Numere si Liniute -, _',
    'namexist'=>"Numele: <u> %s </u> e deja inregistrat, scrieti alt nume",
    'mailexist'=>"Adresa de e-mail: <u> %s </u> e deja folosita",
    'ipexist'=>'Exista deja o inregistrare de pe IP-ul dv.<br/>Daca considerati ca e o eroare, contactati administratorul'
  )
);


// Sets an json object for JavaScript with text messages according to language set
function jsTexts($lsite) {
  // define the JavaScript json object
$texts = 'var texts = {
 "loading":"<h4 id=\"loading\">'.$lsite['loading'].'</h4>",
 "lout":"'.$lsite['users_loged']['lout'].'",
 "username":"'.$lsite['eror_users']['username'].'",
 "pass":"'.$lsite['eror_users']['pass'].'",
 "pass2":"'.$lsite['eror_reg']['pass2'].'",
 "passnew":"'.$lsite['eror_reg']['passnew'].'",
 "myacc":"'.$lsite['users_logform']['myacc'].'",
 "register":"'.$lsite['users_logform']['register'].'",
 "urlformat":"'.$lsite['userpage']['eror_urlformat'].'",
 "erortitl":"'.$lsite['userpage']['eror_erortitl'].'",
 "maxchrtxt":"'.$lsite['msgs']['eror_maxchrtxt'].'",
 "maxoptdata":"'.$lsite['userpage']['maxoptdata'].'",
 "nrchrtxt":"'.$lsite['msgs']['nrchrtxt'].'",
 "name":"'.$lsite['eror_name'].'",
 "email":"'.$lsite['eror_email'].'",
 "coment":"'.$lsite['msgs']['eror_msg'].'",
 "codev":"'.$lsite['eror_codev'].'",
 "noupext":"'.$lsite['eror_base']['upext'].'",
 "noselchk":"'.$lsite['eror_noselchk'].'",
 "confdel":"'.$lsite['confdel'].'",
 "err_findusr":"'.$lsite['eror_users']['findusr'].'"
};';

  return '<script type="text/javascript"><!--'.PHP_EOL.
  $texts.PHP_EOL.
  '//-->
  </script>';
}
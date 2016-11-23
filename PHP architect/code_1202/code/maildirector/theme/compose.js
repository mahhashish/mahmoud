function newComposer(F, R, A, T)
{
	var url = 'compose.php?F=' + F + '&R=' + R + '&A=' + A + '&T=' + T;
	window.open(url, '', 'toolbar=no,menubar=no,location=no,width=810,height=600,resizable=yes');
}

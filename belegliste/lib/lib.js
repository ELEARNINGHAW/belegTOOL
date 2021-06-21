function update(col  ,value, id)
{   
	checksum ="";
	fachsemester	= document.param.fachsemester.value	=  document.belegliste.fachsemester.value;
	studiengang		= document.param.studiengang.value	=  document.belegliste.studiengang.value;
	matrikelnr 		= document.param.matrikelnr.value	=  document.belegliste.matrikelnr.value;
  
	if(id)
	{
		veranstaltungID = eval("document.belegliste.veranstaltung"+ id +".value");
		checksum = matrikelnr + ";" + veranstaltungID + ";" + akennung;
	}
    document.param.col.value	= col;
    document.param.val.value	= value;
    document.param.id.value		= id;

	document.param.checksum.value	= checksum 
    
	document.param.submit();
}


function update2(col  ,value, id , matrikelnr)
{   
	if(id)
	{
		veranstaltungID = eval("document.beleglisteGesamt.veranstaltung"+ id +".value");
		checksum = matrikelnr + "# "+veranstaltungID;
	}
    document.param.col.value	= col;
    document.param.val.value	= value;
    document.param.id.value		= id;

	document.param.checksum.value	= checksum 
    
	document.param.submit();
}
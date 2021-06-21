function update( col, value, id, filterID )
{   
    x1  = "document.vlvz.professor"     + id +".value";
    x2  = "document.vlvz.studiengang"   + id +".value";
    x3  = "document.vlvz.veranstaltung" + id +".value";
    sum = eval( x1 ) + "#"+ eval( x2 ) + "#"+ eval( x3 );
	
    document.param.sum.value = sum;
    document.param.col.value = col;
    document.param.val.value = value;
    document.param.id.value  = id;

	  document.param.submit();
}



function update2( col, value, id, matrikelnr, akennung, status, fID )
{   
	if( id )
	{
		veranstaltungID	 = eval("document.beleglisteGesamt.veranstaltung"+ id +".value");
		checksum = akennung  + ";" + veranstaltungID + ";"  + matrikelnr ;
	}
	
    document.param.col.value	    = col;
    document.param.val.value	    = value;
    document.param.id.value		    = id;
    document.param.status.value	  = status;
    document.param.filterID.value	= fID;

    document.param.checksum.value	= checksum 
    
	  document.param.submit();
}


function show_block(block, anzBlocks)
{

 try {
if (animatedcollapse)
{
animatedcollapse.toggle(block);
}
else
{
if(anzBlocks) hide_block(anzBlocks);
if ( document.getElementById(block).style.display == "block" )
 { document.getElementById(block).style.display = "none";        }
 else
 { document.getElementById(block).style.display = "block";    }
}
 }

catch (e)
{

{

 
if(anzBlocks) hide_block(anzBlocks);

if ( document.getElementById(block).style.display == "block" )
 { document.getElementById(block).style.display = "none";        }
 else
 { document.getElementById(block).style.display = "block";    }
}

}
}
function hide_block(anzBlocks)
{
 /* Originaltext vestecken 
 if (document.getElementById("block0"))
    document.getElementById("block0").style.visibility = "hidden";
*/  
 /* Alle Blöcke verschwinden lassen */  
 for (i = 1; i <= anzBlocks ; i++ )
 {  
  tmp = ("document.getElementById(\"block0"+i+"\").style.display = \"none\";");   
  eval( tmp );
 }
}
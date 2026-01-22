/*
RESUMEN DE FUNCIONES
elimina_comillas(form1.nombre)   ELIMINA LAS COMILLAS SIMPLES Y DOBLES DE UN CAMPO
elimina_blancos_inicio_fin(e)    ELIMINA LOS ESPACIOS BLANCOS AL COMIENZO Y AL FINAL DE UN CAMPO
elimina_puntos(e)                ELIMINA LOS PUNTOS DE UN CAMPO
solo_numeros(e)                  VERIFICA QUE EN EL CAMPO SE INGRESEN SOLO NUMEROS, SINO ADVIERTE Y PONE EN BLANCO EL CAMPO.
valida_mail(e)                   VALIDA QUE EL MAIL POSEA "@" Y UN "." COMO MINIMO.
limita(e,max)                    LIMITA LA CANTIDAD DE TEXTO A INGRESAR EN UN CAMPO (TEXTAREA)
elimina_slash(e)                 Elimina el slash \


NOTAS:
       1.- La función elimina_blancos_inicio_fin(e) debe utilizarse en último lugar (después de
	   eliminar comillas, deliminar puntos, etc....). Porque si se usa antes, al realizar otro 
	   tipo de eliminación pueden quedar blancos al principio y/o final.
	 
*/


function elimina_comillas(e){  //llamada ejemplo onBlur="elimina_comillas(this);"
var resultado = "";
for (i=0;i<=e.value.length-1;i++) { 
if ((e.value.charAt(i)!='"')&&(e.value.charAt(i)!="'")    )
resultado = resultado + e.value.charAt(i);
}  //fin for 
e.value=resultado;
} // fin function elimina_comillas  

// ----------------------------------- 0 ---------------------------------------------

function elimina_blancos_inicio_fin(e){ //llamada ejemplo onBlur="elimina_blancos_inicio_fin(this);"
var resultado="";
var inicio=0;
var fin=0;
for (i=0;i<=e.value.length-1;i++) {  //cuento los espacios en blanco al inicio
if(e.value.charAt(i)!=" "){
i=e.value.length;
}//fin if
if(e.value.charAt(i)==" ")
inicio=inicio+1;
}//fin for
for (i=e.value.length-1;i>=0;i--) {  //cuento los espacios en blanco al final
if(e.value.charAt(i)!=" "){
i=-1;
}//fin if
if(e.value.charAt(i)==" ")
fin=(e.value.length)-i;
}//fin for
var t=e.value.length-fin-1;
for(i=0;i<=t;i++){  //elimino los espacios y los guardo en resultado
if(i>=inicio)
resultado=resultado + e.value.charAt(i);
}//fin for
e.value=resultado;
}//fin function elimina_blancos_inicio_final 

// ----------------------------------- 0 ---------------------------------------------

function elimina_puntos(e){  //llamada ejemplo onBlur="elimina_puntos(this);"
var resultado = ""; 
for (i=0;i<=e.value.length-1;i++) { 
if(e.value.charAt(i)!=".")
resultado = resultado + e.value.charAt(i);
}  //fin for 
e.value=resultado;
} //fin function elimina_puntos 

// ----------------------------------- 0 ---------------------------------------------

function solo_numeros(e){  //llamada ejemplo onBlur="solo_numeros(this);"
for(i=0;i<=e.value.length-1;i++) { 
if (  (e.value.charAt(i)!="0")&&(e.value.charAt(i)!="1")&&(e.value.charAt(i)!="2")&&(e.value.charAt(i)!="3")&&(e.value.charAt(i)!="4")&&(e.value.charAt(i)!="5")&&(e.value.charAt(i)!="6")&&(e.value.charAt(i)!="7")&&(e.value.charAt(i)!="8")&&(e.value.charAt(i)!="9")  ) {
alert(" Usted puede ingresar solo numeros enteros en este campo.\n No se admiten puntos, comas, espacios u otros simbolos que no sean numeros.");
i=e.value.length;
e.value="";
e.focus(); 
}//fin if
//resultado = resultado + e.value.charAt(i);
}//fin for 
}//fin function solo_numeros  

// ----------------------------------- 0 ---------------------------------------------

function valida_mail(e){   //llamada ejemplo onBlur="valida_mail(this);"
if(e.value!=""){
if(e.value.indexOf("@")==-1 || e.value.indexOf(".")==-1){ 
return false; 
} //fin if indexOf
} //Fin if !=""
return true;
} //fin function valida_mail  

// ----------------------------------- 0 ---------------------------------------------

function limita(e,max){     //llamada ejemplo onKeyDown="limita(this,25);" donde 25 es la cantidad de caracteres
if(e.value.length>=max-1){e.value=e.value.substring(0,max-1);}
}  //fin function limita 

// ----------------------------------- 0 ---------------------------------------------

function elimina_slash(e){  //llamada ejemplo onBlur="elimina_slash(this);"
var resultado = ""; 
for (i=0;i<=e.value.length-1;i++) { 
if(e.value.charAt(i)!='\\')
resultado = resultado + e.value.charAt(i);
}  //fin for 
e.value=resultado;
} //fin function elimina_puntos

//*******************************************************************************

function desvalidar(){
	
$(".required").each( function () {

      $(this).css("border-color", "#d9bc6c");
    
  });
	
}//function desvalidar()

function validar() {
  var valid = 1
  $(".required").each( function () {
    if ($(this).val() == "") {
      $(this).css("border-color", "red");
	   $(this).css("border", "1px solid red");
      valid = 0
    } else {
      $(this).css("border-color", "#d9bc6c");
    }
  });

  if (!valid) {
    
    return false;
  }else{
	return true;
	}
}

//*****************************************************************************************
 
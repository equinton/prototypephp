$(document).ready(
	function() {
	$('.datatable').DataTable({
		language : {
			url : 'display/javascript/fr_FR.json'
		}
	});
	$('.taux,nombre').attr('title','{$LANG[message].34}');
	$('.taux').attr( {
		'pattern': '[0-9]+(\.[0-9]+)?',
		'maxlength' : "10"
	});
	$('.nombre').attr( {
		'pattern': '[0-9]+',
		'maxlength' : "10"
	});

	$('.button-delete').keypress(function() {
		if (confirm("Confirmez-vous la suppression ?") == true) {
			$(this.form).find("input[name='action']").val("Delete");
			$(this.form).submit();
		} else
			return false;
	});
	$( ".button-delete" ).click(function() {
		if (confirm("Confirmez-vous la suppression ?") == true) {
			$(this.form).find("input[name='action']").val("Delete");
			$(this.form).submit();
		} else {
			return false;
		}
		});

});
/**
 * Fonction permettant de verifier que les mots de passe entres dans les deux zones
 * sont identiques
 * @param pass1
 * @param pass2
 * @returns {Boolean}
 */
function verifieMdp(pass1, pass2) {
	if (pass1.value != pass2.value && pass1.value.length > 0
			&& pass2.value.length > 0) {
		alert("\erreur: les mots de passes ne correspondent pas");
		action.value = "X";
		return false;
	} else {
		return true;
	}
}
/**
 * Fonction verifiant la volonte de suppression
 * @returns {Boolean}
 */
function confirmSuppression(texte) {
	if (texte.length==0) texte = "Confirmez-vous la suppression ?";
	return confirmEnvoi(texte);
}
/**
 * Fonction de generation aleatoire d'un mot de passe
 * @returns {String}
 */
function GeneratePassword() {
	  
    if (parseInt(navigator.appVersion) <= 3) {
        alert("Sorry this only works in 4.0+ browsers");
    }
  
    var length = 10;
    var sPassword = "";
    //length = document.aForm.charLen.options[document.aForm.charLen.selectedIndex].value;
     
    //var lowercase = 1;
    var uppercase = 1;
    var figures = 1;
    var punction = 1;  
    var i = 0;
  
    for (i=1; i <= length; i++) {
  
        numI = getRandomNum();
        if ((punction == 0 && checkPunc(numI)) || (figures == 0 && checkFigures(numI)) ||(uppercase == 0 && checkUppercase(numI))) {i -= 1;}
        else {sPassword = sPassword + String.fromCharCode(numI);}
    }
  
    //document.aForm.passField.value = sPassword;
  
    return sPassword;
}
  
function getRandomNum() {
    // between 0 - 1
    var rndNum = Math.random();
    // rndNum from 0 - 1000
    rndNum = parseInt(rndNum * 1000);
    // rndNum from 33 - 127
    rndNum = (rndNum % 94) + 33;
    return rndNum;
}
  
function checkPunc(num) {
    if (((num >=33) && (num <=47)) || ((num >=58) && (num <=64))) { return true; }
    if (((num >=91) && (num <=96)) || ((num >=123) && (num <=126))) { return true; }
    return false;
}
 
function checkFigures(num) {
    if ((num >=48) && (num <=57)) { return true; }
    else { return false; }
}
 
function checkUppercase(num) {
    if ((num >=65) && (num <=90)) { return true; }
    else { return false; }
}
/*
* Fin de la fonction de generation aleatoire d'un mot de passe
*/

function getPassword(password1, password2, display) {
	sPassword = GeneratePassword();
	document.getElementById(password1).value = sPassword;
	document.getElementById(password2).value = sPassword;
	document.getElementById(display).value = sPassword;	
}
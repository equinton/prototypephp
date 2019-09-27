<!-- Jquery -->
<script src="display/node_modules/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap -->
<link rel="stylesheet"	href="display/javascript/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet"	href="display/javascript/bootstrap/css/bootstrap-theme.min.css">
<script	src="display/javascript/bootstrap/js/bootstrap.min.js"></script>

<!--alpaca -->
<script type="text/javascript" src="display/node_modules/handlebars/dist/handlebars.runtime.min.js"></script>
<script type="text/javascript" src="display/node_modules/alpaca/dist/alpaca/bootstrap/alpaca.min.js"></script>
<link rel="stylesheet" href="display/node_modules/alpaca/dist/alpaca/bootstrap/alpaca.min.css" >

<!-- leaflet -->
<link rel="stylesheet" href="display/node_modules/leaflet/dist/leaflet.css">
<script src="display/node_modules/leaflet/dist/leaflet.js"></script>
<script src="display/node_modules/pouchdb/dist/pouchdb.min.js"></script>
<script src="display/node_modules/leaflet.tilelayer.pouchdbcached/L.TileLayer.PouchDBCached.js"></script>
<!-- extension pour le menu -->
<script src="display/node_modules/smartmenus/dist/jquery.smartmenus.min.js" type="text/javascript"></script>
<link type="text/css" href="display/node_modules/smartmenus/dist/addons/bootstrap/jquery.smartmenus.bootstrap.css" rel="stylesheet">
<script src="display/node_modules/smartmenus/dist/addons/bootstrap/jquery.smartmenus.bootstrap.min.js" type="text/javascript"></script>

<!-- Datatables -->
<script src="display/node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="display/node_modules/datatables.net-bs/js/dataTables.bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="display/node_modules/datatables.net-bs/css/dataTables.bootstrap.min.css" />

<!-- Boutons d'export associes aux datatables - classe datatable-export -->
<script src="display/node_modules/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="display/node_modules/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="display/node_modules/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="display/node_modules/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="display/node_modules/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" />

<!-- Rajout du tri sur la date/heure -->
<script type="text/javascript" src="display/node_modules/moment/min/moment.min.js"></script>
<script type="text/javascript" src="display/node_modules/datetime-moment/datetime-moment.js"></script>

<!-- composant date/heure -->
<script type="text/javascript" charset="utf-8" src="display/node_modules/jquery-ui-dist/jquery-ui.min.js"></script>
<script type="text/javascript" charset="utf-8" src="display/node_modules/jquery-ui/ui/i18n/datepicker-en-GB.js"></script>
<script type="text/javascript" charset="utf-8" src="display/node_modules/jquery-ui/ui/i18n/datepicker-fr.js"></script>
<script type="text/javascript" charset="utf-8" src="display/javascript/jquery-timepicker-addon/jquery-ui-timepicker-addon.min.js"></script>
<script type="text/javascript" charset="utf-8" src="display/javascript/jquery-timepicker-addon/i18n/jquery-ui-timepicker-fr.js"></script>
<link rel="stylesheet" type="text/css" href="display/node_modules/jquery-ui-dist/jquery-ui.min.css"/>
<link rel="stylesheet" type="text/css" href="display/node_modules/jquery-ui-dist/jquery-ui.theme.min.css"/>
<link rel="stylesheet" type="text/css" href="display/javascript/jquery-timepicker-addon/jquery-ui-timepicker-addon.min.css"/>
<script type="text/javascript" charset="utf-8" src="display/javascript/jquery-ui-custom/combobox.js"></script>

<!-- Affichage des photos -->
<link rel="stylesheet" href="display/node_modules/magnific-popup/dist/magnific-popup.css"> 
<script src="display/node_modules/magnific-popup/dist/jquery.magnific-popup.min.js"></script> 

<!-- Cookies -->
<script src="display/javascript/js-cookie-master/src/js.cookie.js"></script> 

<!-- Code specifique -->
<link rel="stylesheet" type="text/css" href="display/CSS/bootstrap-prototypephp.css" >
<script type="text/javascript" src="display/javascript/bootstrap-prototypephp.js"></script>


<!--  implementation automatique des classes -->
<script>
var dataTableLanguage = {
    "sProcessing":     "{t}Traitement en cours...{/t}",
    "sSearch":         "{t}Rechercher :{/t}",
    "sLengthMenu":     "{t}Afficher _MENU_ éléments{/t}",
    "sInfo":           "{t}Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments{/t}",
    "sInfoEmpty":      "{t}Affichage de l'élément 0 à 0 sur 0 élément{/t}",
    "sInfoFiltered":   "{t}(filtré de _MAX_ éléments au total){/t}",
    "sInfoPostFix":    "",
    "sLoadingRecords": "{t}Chargement en cours...{/t}",
    "sZeroRecords":    "{t}Aucun élément à afficher{/t}",
    "sEmptyTable":     "{t}Aucune donnée disponible dans le tableau{/t}",
    "oPaginate": {
        "sFirst":      "{t}Premier{/t}",
        "sPrevious":   "{t}Précédent{/t}",
        "sNext":       "{t}Suivant{/t}",
        "sLast":       "{t}Dernier{/t}"
    },
    "oAria": {
        "sSortAscending":  "{t}: activer pour trier la colonne par ordre croissant{/t}",
        "sSortDescending": "{t}: activer pour trier la colonne par ordre décroissant{/t}"
    }
};
$(document).ready(function() {
		var pageLength = Cookies.get("pageLength");
	if (! pageLength) {
		pageLength = 10;
	}
	$.fn.dataTable.moment( '{$LANG["date"]["formatdatetime"]}' );
	$.fn.dataTable.moment( '{$LANG["date"]["formatdate"]}' );
	$('.datatable').DataTable({
		"language" : dataTableLanguage,
		"searching": false,
		"pageLength": pageLength,
		"lengthMenu": [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]]
	});
	$('.datatable-searching').DataTable({
		"language" : dataTableLanguage,
		"searching": true,
		"pageLength": pageLength,
		"lengthMenu": [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]]
	});
	$('.datatable-nopaging').DataTable({
		"language" : dataTableLanguage,
		"paging" : false,
		"searching": false
	});
	$('.datatable-nopaging-nosort').DataTable({
		"language" : dataTableLanguage,
		"paging" : false,
		"searching": false,
		"ordering": false
	});
	
	$('.datatable-export').DataTable({	
		 dom: 'Bfrtip',
		"language" : dataTableLanguage,
		"paging" : false,
		"searching": false,
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
           /* {
                extend: 'pdfHtml5',
                orientation: 'landscape'
            },*/
            'print'
        ]
	});
	$('.datatable-export-paging').DataTable({	
		 dom: 'Bfrtip',
		"language" : dataTableLanguage,
		"paging" : true,
		"searching": true,
		"pageLength": pageLength,
		"lengthMenu": [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
       buttons: [
           'copyHtml5',
           'excelHtml5',
           'csvHtml5',
          /* {
               extend: 'pdfHtml5',
               orientation: 'landscape'
           },*/
           'print'
       ]
	});

		$(".datatable, .datatable-export-paging, .datatable-searching").on('length.dt', function ( e, settings, len ) { 
		Cookies.set('pageLength', len, { expires: 180});
	});
	/* Initialisation for paging datatables */
	$(".datatable, .datatable-export-paging, .datatable-searching").DataTable().page.len(pageLength);

	$('.taux,nombre').attr('title', '{t}Valeur numérique...{/t}');
	$('.taux').attr({
		'pattern' : '-?[0-9]+(\.[0-9]+)?',
		'maxlength' : "10"
	});
	$('.nombre').attr({
		'pattern' : '-?[0-9]+',
		'maxlength' : "10"
	});
	
	$(".date").datepicker( $.datepicker.regional['{$LANG["date"]["locale"]}'] );
	$(".datepicker").datepicker( $.datepicker.regional['{$LANG["date"]["locale"]}'] );
	$.datepicker.setDefaults($.datepicker.regional['{$LANG["date"]["locale"]}']);
	$('.timepicker').attr('pattern', '[0-9][0-9]\:[0-9][0-9]\:[0-9][0-9]');
	$.timepicker.setDefaults($.timepicker.regional['{$LANG["date"]["locale"]}']);
	$('.datetimepicker').datetimepicker({ 
		dateFormat: '{$LANG["date"]["formatdatecourt"]}',
		timeFormat: 'HH:mm:ss',
		timeInput: true
	});
	$('.date, .datepicker, .timepicker, .datetimepicker').attr('autocomplete','off');

	var lib = "{t}Confirmez-vous la suppression ?{/t}" ;
	$('.button-delete').keypress(function() {
		if (confirm(lib) == true) {
			$(this.form).find("input[name='action']").val("Delete");
			$(this.form).submit();
		} else
			return false;
	});
	$(".button-delete").click(function() {
		if (confirm(lib) == true) {
			$(this.form).find("input[name='action']").val("Delete");
			$(this.form).submit();
		} else {
			return false;
		}
	});
	/*
	 * Initialisation des combobox
	 */
	$(".combobox").combobox();

});

</script>

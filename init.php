<?php
	if( $docroot == '' ){
		die( _("Accesso negato!") );
	}
	
	//inserire il modulo
	//INSERT INTO `zz_modules` (`id`, `name`, `name2`, `module_dir`, `options`, `options2`, `icon`, `version`, `compatibility`, `order`, `level`, `parent`, `default`, `enabled`, `type`, `new`) VALUES (33, 'Importa xml Fattura PA', '', 'fattura_pa', '{ "main_query": [ { "type": "custom" } ]}', '', 'fa fa-download', '2.1', '2.1', 2, 1, 12, 1, 1, 'menu', 0);
	//INSERT co_tipidocumento (descrizione,dir) VALUES ('Fattura PA vendita','entrata');
	//insert into co_tipidocumento (descrizione,dir) VALUES ('Fattura PA acquisto','uscita');
	//alter table an_anagrafiche add column codice_ufficio varchar(10);
	
	//modifica riga update anagrafiche e form
?>

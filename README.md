# fattura_pa
Openstamanager import module for xml based Fattura PA files

Al momento non è ancora disponibile un installer automatico e vanno eseguite le seguenti operazioni manuali:

Nel database lanciare le seguenti query
- Per inserire il modulo nel menu
	INSERT INTO `zz_modules` (`id`, `name`, `name2`, `module_dir`, `options`, `options2`, `icon`, `version`, `compatibility`, `order`, `level`, `parent`, `default`, `enabled`, `type`, `new`) VALUES (33, 'Importa xml Fattura PA', '', 'fattura_pa', '{ "main_query": [ { "type": "custom" } ]}', '', 'fa fa-download', '2.1', '2.1', 2, 1, 12, 1, 1, 'menu', 0);
- Per gestire il tipo di documenti (al momento ancora non attivo)
	INSERT INTO co_tipidocumento (descrizione,dir) VALUES ('Fattura PA vendita','entrata');
	INSERT INTO co_tipidocumento (descrizione,dir) VALUES ('Fattura PA acquisto','uscita');
- Rendere disponibile il codice univoco dell'ufficio nelle anagrafiche
	ALTER TABLE an_anagrafiche ADD COLUMN codice_ufficio VARCHAR(10);
	
- Modificare il file modules/anagrafiche/edit.html per attivare il campo codice_ufficio
	Aggiungere prima del blocco contatti una nuova riga 

			<div class="row">
				<div class="col-md-2">
					{[ "type": "text", "label": "Codice univoco ufficio", "name": "codice_ufficio", "maxlength": 6, "required": 0, "class": "text-center", "value": "$codice_ufficio$", "extra": "" ]}
				</div>
			</div>

- Modificare le actions delle anagrafiche per salvare il codice_ufficio modules/anagrafiche/actions.php
	Nella query di update dopo a 
	
	"foro_competenza=\"".$html_post['foro_competenza']."\",".
	
	inserire
	
	"codice_ufficio=\"".$html_post['codice_ufficio']."\",".


<?php
include_once($docroot."/modules/fattura_pa/modutil.php");
include_once($docroot."/modules/fatture/modutil.php");

if ( isset($_FILES['blob']['name'])) {

	$filename	= $_FILES['blob']['name'];

	// copia il file nella cartella /modules/fattura_pa/upload
	move_uploaded_file($_FILES["blob"]["tmp_name"], $docroot."/modules/fattura_pa/upload/".$filename);
	array_push( $_SESSION['infos'], "Informazioni salvate correttamente!");

	$tmpName = $docroot."/modules/fattura_pa/upload/".$filename;
		

			
			
	// extensioni da controllare
	$extensions = array('XML', 'xml');



/* elementi xml */
	$fattura = new SimpleXMLElement($tmpName, null, true);            // load XML


	$denominazione = $fattura->FatturaElettronicaHeader->CedentePrestatore->DatiAnagrafici->Anagrafica->Denominazione;
	$idsecondario = $fattura->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento->Numero;
	$data_dattura = $fattura->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento->Data;
	$codice_ufficio = $fattura->FatturaElettronicaHeader->DatiTrasmissione->CodiceDestinatario;
	$ritenuta = $fattura->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento->DatiRitenuta->AliquotaRitenuta;
	$rivalsainps = $fattura->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento->DatiCassaPrevidenziale->AlCassa;
	$rivalsainps_iva = $fattura->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento->DatiCassaPrevidenziale->AliquotaIVA;
	$rivalsainps_ritenuta = $fattura->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento->DatiCassaPrevidenziale->Ritenuta;
	//$idsecondario="fattpa-01";

	//verifichiamo se esiste già il numeratore
	$query = "SELECT count(numero_esterno) as esiste FROM co_documenti co where co.numero_esterno='".$idsecondario."'";
	//echo "sql: ".$query;	
	$rs = $dbo->fetchArray($query);


	switch( $rs[0]['esiste']){
		case "1":
			echo "è un aggiornamento";
			array_push( $_SESSION['errors'], "Errore durante l'inserimento dell'intervento della fattura ".$idsecondario.". La fattura esiste già!" );
			break;
		case "0":
			//echo "è nuovo, si può proseguire, cerchiamo l'azienda per codice univoco ufficio'";
			
			$query = "SELECT idanagrafica  FROM an_anagrafiche an where an.codice_ufficio='".$codice_ufficio."'";
			//echo "sql: ".$query;	
			$rs = $dbo->fetchArray($query);
			$idanagrafica=$rs[0]['idanagrafica'];
			if($idanagrafica==""){
				array_push( $_SESSION['errors'], "Errore, non trovo un'anagrafica con il seguente codice_ufficio: ".$codice_ufficio."!" 	);				
			}else{

				
				//global $dir;
				$dir=save( $_POST['tipofattura'] );
				//echo $dir;
				if( $dir=='entrata' ){
					$idconto = get_var("Conto predefinito fatture di vendita");
				}
				else{
					$idconto = get_var("Conto predefinito fatture di acquisto");
				}

				//echo $idconto;
				//exit;
				$numero = get_new_numerofatturapa( $data );


				//Tipo di pagamento predefinito dall'anagrafica
				$query = "SELECT id FROM co_pagamenti WHERE id=(SELECT idpagamento FROM an_anagrafiche WHERE idanagrafica='".$idanagrafica."')";
				$rs = $dbo->fetchArray($query);
				//echo "sql: ".$query;	
				$idpagamento = $rs[0]['id'];

				//Da migliorare posizionamento nel piano dei conti
				if($dir=='entrata'){
				//Tipo di pagamento predefinito dall'anagrafica
					$query = "SELECT id FROM co_tipidocumento WHERE descrizione='Fattura PA vendita'";
					$idtipodocumento = 2;
				}else{
					$query = "SELECT id FROM co_tipidocumento WHERE descrizione='Fattura PA acquisto'";
					$idtipodocumento = 1;
				}
				//$rs = $dbo->fetchArray($query);
				//echo "sql: ".$query;	
				//$idtipodocumento = $rs[0]['id'];


				//Se la fattura è di vendita e non è stato associato un pagamento predefinito al cliente leggo il pagamento dalle impostazioni
				if( $dir=='entrata' && $idpagamento=='' )
					$idpagamento = get_var("Tipo di pagamento predefinito");

					$query = "INSERT INTO co_documenti ( numero, numero_esterno, idanagrafica, idconto, idtipodocumento, idpagamento, data, idstatodocumento, idsede ) VALUES ( \"".$numero."\", \"".$idsecondario."\", \"".$idanagrafica."\", \"".$idconto."\", \"".$idtipodocumento."\", \"".$idpagamento."\",\"".$data_dattura."\", (SELECT `id` FROM `co_statidocumento` WHERE `descrizione`='Bozza'), (SELECT idsede_fatturazione FROM an_anagrafiche WHERE idanagrafica=\"".$idanagrafica."\") )";
					//echo "sql: ".$query;	
					//exit;
					$dbo->query($query);
					
					$iddocumento = $dbo->last_inserted_id();
					//$id_record = $iddocumento;
					
					//a questo punto inseriamo le righe documento come righe generiche
					foreach($fattura->FatturaElettronicaBody->DatiBeniServizi->DettaglioLinee as $linea){
						//printf("%s<br />%s<br />%s<br />%s<br />%s<br />",(string)$linea->NumeroLinea,(string)$linea->Descrizione,(string)$linea->PrezzoUnitario,(string)$linea->PrezzoTotale,(string)$linea->AliquotaIVA );
						$descrizione = (string)$linea->Descrizione;
						$importo_manuale=(string)$linea->PrezzoUnitario;
						$qta=(string)$linea->Quantita;
						$aliva = (string)$linea->AliquotaIVA;
						$um = (string)$linea->UnitaMisura;
		
						
						addrigafatturapa($iddocumento, $descrizione, $importo_manuale, $qta, $aliva, $um, $ritenuta, $rivalsainps, $rivalsainps_iva, $rivalsainps_ritenuta);
					}
					


				array_push( $_SESSION['infos'], "Aggiunta fattura numero ".$numero.", ".$idsecondario."!" );
				
			}
			break;
		
	}

	   

} else {
	 
	array_push( $_SESSION['errors'], "Nessun file selezionato !" );

}

?>


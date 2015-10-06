<?php

	//numeratore generico
	function get_new_numerofatturapa( $data ){
		global $dbo;
		global $dir;
		$data = date(time());

		$query = "SELECT IFNULL(MAX(numero),'0') AS max_numerofattura FROM co_documenti WHERE DATE_FORMAT( data, '%Y' ) = '".date("Y", strtotime($data))."' AND idtipodocumento IN(SELECT id FROM co_tipidocumento WHERE dir='".$dir."') ORDER BY id DESC LIMIT 0,1";
		$rs = $dbo->fetchArray($query);
		
		$numero = $rs[0]['max_numerofattura']+1;

		return $numero;
	}
	
	//aggiunta riga documento come riga generica
	function addrigafatturapa($iddocumento, $descrizione, $importo_manuale, $qta, $aliva, $um, $ritenuta, $rivalsainps, $rivalsainps_iva, $rivalsainps_ritenuta){
		global $dbo;
		//echo "$iddocumento, $descrizione, $importo_manuale, $qta, $aliva, $um, $ritenuta, $rivalsainps, $rivalsainps_iva, $rivalsainps_ritenuta";

		if( $iddocumento != '' ){

			//Selezione costi da intervento
			$descrizione=save($descrizione);
			$importo_manuale = $importo_manuale;
			if($qta==''){
				$qta=1;
			}
			$qta = force_decimal($qta);
			
			if($um==''){
				$um="pz";
			}
			
			
			$aliva=force_decimal($aliva);

			$query="select id from co_iva where percentuale='$aliva' and id=(select valore from zz_impostazioni where nome='Iva predefinita')";
			$rs = $dbo->fetchArray($query);
			$idiva=$rs[0]['id'];

			
			$subtot = $importo_manuale*$qta;
			$sconto = 0;
			
			
			//Calcolo iva
			$query = "SELECT * FROM co_iva WHERE id='".$idiva."'";
			$rs = $dbo->fetchArray($query);
			$iva = ($subtot-$sconto)/100*$rs[0]['percentuale'];
			$iva_indetraibile = $iva/100*$rs[0]['indetraibile'];
			$desc_iva = $rs[0]['descrizione'];

			
			if($rivalsainps!=''){
				$query="select id from co_rivalsainps where percentuale='$rivalsainps' and id=(select valore from zz_impostazioni where nome='Percentuale rivalsa INPS')";
				$rs = $dbo->fetchArray($query);
				$idrivalsainps=$rs[0]['id'];
			}else{
				$idrivalsainps=0;
			}

			
			//Calcolo rivalsa inps
			$query = "SELECT * FROM co_rivalsainps WHERE id='".$idrivalsainps."'";
			$rs = $dbo->fetchArray($query);
			$rivalsainps = $importo_manuale * $qta / 100 * $rs[0]['percentuale'];
			

			if($ritenuta!=''){
				$query="select id from co_ritenutaacconto where percentuale='$ritenuta' and id=(select valore from zz_impostazioni where nome='Percentuale ritenuta d\'acconto')";
				$rs = $dbo->fetchArray($query);
				$idritenutaacconto=$rs[0]['id'];
			}else{
				$idritenutaacconto=0;
			}
			

			//Calcolo ritenuta d'acconto
			$query = "SELECT * FROM co_ritenutaacconto WHERE id='".$idritenutaacconto."'";
			$rs = $dbo->fetchArray($query);
			$ritenutaacconto = ($importo_manuale*$qta +$rivalsainps) / 100 * $rs[0]['percentuale'];


			//Aggiunta riga generica sul documento
			$query = "INSERT INTO co_righe_documenti( iddocumento, idiva, desc_iva, iva, iva_indetraibile, descrizione, subtotale, sconto, um, qta, idrivalsainps, rivalsainps, idritenutaacconto, ritenutaacconto ) VALUES( '$iddocumento', '$idiva', '$desc_iva', '$iva', '$iva_indetraibile', '$descrizione', '$subtot', '$sconto', '$um', '$qta', '$idrivalsainps', '$rivalsainps', '$idritenutaacconto', '$ritenutaacconto' )";

			if( $dbo->query($query) ){
				array_push( $_SESSION['infos'], "Riga aggiunta!" );
			
				//Ricalcolo inps, ritenuta e bollo
				if( $dir=='entrata' ){
					ricalcola_costiagg_fattura($iddocumento, $idrivalsainps, $idritenutaacconto );
				}else{
					ricalcola_costiagg_fattura($iddocumento, $idrivalsainps, $idritenutaacconto);
				}
			}
			

	}
	
	}
?>

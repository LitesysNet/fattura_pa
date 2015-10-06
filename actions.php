<?php

//echo 'permessi action prima: '.$permessi[$module_name];

	include_once("../../core.php");
	$module_name = "Importa xml Fattura PA"; //stesso nome di quello messo nel campo name di zz_modules
	include_once($docroot."/config.inc.php");
	include_once($docroot."/lib/user_check.php");
	include_once($docroot."/lib/permissions_check.php");
	include_once($docroot."/modules/fattura_pa/modutil.php");


	
	$id = $html->form('id', 'post');
	
	
	switch( $html->form('op', 'post') ){
		case "upload":
		
			if( $permessi[$module_name] == 'rw' ){
				include( $docroot."/modules/fattura_pa/upload_modules.php" );
			}else{
				array_push( $_SESSION['infos'], "Non hai i permessi" );
			}

			array_push( $_SESSION['infos'], "Inserimento effettuato corretamente." );
						
			break;

/*			
		case "uninstall":
			if( $permessi[$module_name] == 'rw' ){
				
				if( $id != '' ){
					//Leggo l'id del modulo
					$rs = $dbo->fetchArray("SELECT id, name, module_dir FROM zz_modules WHERE id=\"".$id."\" AND `default`=0");
					$module = $rs[0]['name'];
					$module_dir = $rs[0]['module_dir'];

					if( sizeof($rs)==1 ){
						//Elimino il modulo dal menu
						$dbo->query("DELETE FROM zz_modules WHERE id='".$id."' OR parent='".$id."'");

						$uninstall_script = $docroot."/modules/".$module_dir."/update/uninstall.php";
						
						if( file_exists($uninstall_script) ){
							include_once( $uninstall_script );
						}
						
						deltree( $docroot."/modules/".$module_dir."/" );
						array_push( $_SESSION['infos'], "Modulo &quot;".$module."&quot; disinstallato!" );
					}
				}
			}
			exit;


		case "disable":
			if( $permessi[$module_name] == 'rw' ){
				$dbo->query("UPDATE zz_modules SET enabled=0 WHERE id='".$id."'");
				$rs = $dbo->fetchArray("SELECT id, name FROM zz_modules WHERE id='".$id."'");
				$modulo = $rs[0]['name'];
				array_push( $_SESSION['infos'], "Modulo &quot;".$modulo."&quot; disabilitato!" );
			}
			
			exit;

		case "enable":
			if( $permessi[$module_name] == 'rw' ){
				$dbo->query("UPDATE zz_modules SET enabled=1 WHERE id='".$id."'");
				$rs = $dbo->fetchArray("SELECT id, name FROM zz_modules WHERE id='".$id."'");
				$modulo = $rs[0]['name'];
				array_push( $_SESSION['infos'], "Modulo &quot;".$modulo."&quot; abilitato!" );
			}
			
			exit;
		
		
		case "disable_widget":
			if( $permessi[$module_name] == 'rw' ){
				$dbo->query("UPDATE zz_widget_modules SET enabled=0 WHERE id='".$id."'");
				$rs = $dbo->fetchArray("SELECT id, name FROM zz_widget_modules WHERE id='".$id."'");
				$widget = $rs[0]['name'];
				array_push( $_SESSION['infos'], "Widget &quot;".$widget."&quot; disabilitato!" );
			}
			
			exit;

		case "enable_widget":
			if( $permessi[$module_name] == 'rw' ){
				$dbo->query("UPDATE zz_widget_modules SET enabled=1 WHERE id='".$id."'");
				$rs = $dbo->fetchArray("SELECT id, name FROM zz_widget_modules WHERE id='".$id."'");
				$widget = $rs[0]['name'];
				array_push( $_SESSION['infos'], "Widget &quot;".$widget."&quot; abilitato!" );
			}
			
			exit;
		
		case "change_position_widget_top":
			if( $permessi[$module_name] == 'rw' ){
				$dbo->query("UPDATE zz_widget_modules SET location='controller_top' WHERE id='".$id."'");
				$rs = $dbo->fetchArray("SELECT id, name FROM zz_widget_modules WHERE id='".$id."'");
				$widget = $rs[0]['name'];
				array_push( $_SESSION['infos'], "Posizione del widget &quot;".$widget."&quot; aggiornata!" );
			}
			
			exit;
			
		case "change_position_widget_right":
			if( $permessi[$module_name] == 'rw' ){
				$dbo->query("UPDATE zz_widget_modules SET location='controller_right' WHERE id='".$id."'");
				$rs = $dbo->fetchArray("SELECT id, name FROM zz_widget_modules WHERE id='".$id."'");
				$widget = $rs[0]['name'];
				array_push( $_SESSION['infos'], "Posizione del widget &quot;".$widget."&quot; aggiornata!" );
			}
			
			exit;
		
		
		//Ordinamento moduli di primo livello
		case "sortmodules":
			$ids = explode( ",", $html->form('ids', 'post') );

			for( $i=0; $i<sizeof($ids); $i++ ){
				$dbo->query("UPDATE zz_modules SET `order`='".$i."' WHERE id='".$ids[$i]."'");
			}
			
			exit;
		
		
		case "sortwidget":
			$id			= $html->form('id', 'post');
			$id_module	= $html->form('id_module', 'post');
			$location	= $html->form('location', 'post');
			$order		= $html->form('order', 'post');
			$class		= $html->form('class', 'post');
			
			
			$dbo->query("UPDATE zz_widget_modules SET `order`='".$order."', class='".$class."' WHERE id='".$id."' AND location='".$location."' AND id_module='".$id_module."'");
			
			exit;
		
		case "updatewidget":
			$id			= $html->form('id', 'post');
			$id_module	= $html->form('id_module', 'post');
			$location	= $html->form('location', 'post');

			$dbo->query("UPDATE zz_widget_modules SET location='".$location."', class='".$class."' WHERE id='".$id."' AND id_module='".$id_module."'");
			
			exit;
*/
	}
?>

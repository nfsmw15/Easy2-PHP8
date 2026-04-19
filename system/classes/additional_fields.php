<?php
/********************************************
* 
* System:   EASY 2.0 Loginsystem
* Author:   Marius Rasche (aka Marlight)
* Class:    additional_fields
* File:     additional_fields.php
* FVersion: 0.1.3.1 (this file)
* SVersion: 0.9.8.1 BETA (complete System)
* Date:     20.05.2018
*
* Created by www.marlight-systems.de
* Copyright by Marlight Systems (www.marlight-systems.de)
* All rights reserved.
* 
*********************************************/

class additional_fields extends loginsystem{
	protected \MysqliPDOWrapper $mysql;
	protected $tpl_field_dir = "./system/tpl/fields/";
	private	$types = array("text", "url", "email", "number", "radio", "checkbox", "textarea", "select");
	private	$type_options = array("radio", "checkbox", "select");
	private $required_text = '*';

	
	public function __construct(){
		parent::__construct();	
	}
	
	public function listFields(){
		$rtn = NULL;
		
		$sql = $this->mysql->query("Select * From `".Prefix."_fields` Order by pos ASC");
		while($row = $sql->fetch_assoc()){
			$btn = array();
			
			if(parent::auditRight('adf_show')) $btn[] = '<a href="?p=additional_fields&f=show&id='.$row['id'].'"><i class="fa fa-eye"></i></a>';
			if(parent::auditRight('adf_edit')) $btn[] = '<a href="?p=additional_fields&f=edit&id='.$row['id'].'"><i class="fa fa-pencil"></i></a>';
			if(parent::auditRight('adf_remove')) $btn[] = '<a href="?p=additional_fields&f=remove&id='.$row['id'].'"><i class="fa fa-trash"></i></a>';
			
			$required = ($row['required'] == 0) ? 'N' : 'J';
			$regist = ($row['regist'] == 0) ? 'N' : 'J';
			
			$btn = implode(' | ', $btn);
			$rtn .= '<tr>
				<td>'.$row['pos'].'</td>
				<td>'.$row['name'].'</td>
				<td>'.$row['title'].'</td>
				<td>'.$row['type'].'</td>
				<td>'.$required.'</td>
				<td>'.$regist.'</td>
				<td>'.$btn.'</td>
			</tr>';
		}
		
		if($sql->num_rows == 0){
			$rtn = '<tr><td colspan="7" class="text-center"><em>Keine zus&auml;tzlichen Felder angelegt...</em></td></tr>';
		}
		return $rtn;
	}
	
	private function lastFieldPosition(){
		return parent::getValue('fields', '!id', '0', 'pos', false, "Order by `pos` DESC");
	}
	
	private function autoPosition($start = 0, $direction = 0, $end = 'na'){
		/***********************
		 * $start => Startwert, ab welcher er die anderen Menuepunkte verschieben soll
		 * $direction => In welche Richtung verschoben werden soll (0 = +, 1 = -)
		 * $end => Bis zu welchem Wert er verschieben soll
		 ***********************/
		if($end == 'na'){
			if($direction == 0){
				$query = "Update `".Prefix."_fields` Set pos = pos + 1 Where pos >= '$start'";
			} else {
				$query = "Update `".Prefix."_fields` Set pos = pos - 1 Where pos >= '$start'";
			}
		} elseif(is_numeric($end)){
			if($direction == 0){
				$query = "Update `".Prefix."_fields` Set pos = pos + 1 Where pos >= '$start' AND pos <= '$end'";
			} else {
				$query = "Update `".Prefix."_fields` Set pos = pos - 1 Where pos >= '$start' AND pos <= '$end'";
			}
		}
		
		$sql = $this->mysql->query($query);
		if($sql === false){
			errormail('Fehler beim anpassen der Positionen der Zusatzfelder! Fehler in class '.__CLASS__.' => function '.__FUNCTION__.'()! MySQL-Fehler '.$this->mysql->errno.': '.$this->mysql->error);
			return false;
		}
		return true;
	}
	
	public function addField(){
		$error = NULL;
		$name = length(isset($_POST['name']) ? $_POST['name'] : '', 64, 0, "sql");
		$title = length(isset($_POST['title']) ? $_POST['title'] : '', 64);
		$type = length(isset($_POST['type']) ? $_POST['type'] : '', 64, 0, "sql");
		$placeholder = length(isset($_POST['placeholder']) ? $_POST['placeholder'] : '', 128);
		$value = length(isset($_POST['value']) ? $_POST['value'] : '', 1024, 0, "sql");
		$maxlength = length(isset($_POST['maxlength']) ? $_POST['maxlength'] : '', 11, 0, "sql");
		$description = length(isset($_POST['description']) ? $_POST['description'] : '', 9999999999999999, 0, "sql");
		$regex = length(isset($_POST['regex']) ? $_POST['regex'] : '', 9999999999999999, 0, "sql");
		$options = length(isset($_POST['options']) ? $_POST['options'] : '', 8192, 0, "sql");
		$position = length(isset($_POST['position']) ? $_POST['position'] : '', 3, 0, "sql");
		$required = length(isset($_POST['required']) ? $_POST['required'] : 0, 1);
		$regist = length(isset($_POST['regist']) ? $_POST['regist'] : 0, 1);
		$regex_options = length(isset($_POST['regex_options']) ? $_POST['regex_options'] : NULL, 8);
		
		
		if(parent::auditRight('fields_add')){
			if(!empty($name) && !empty($type) && $position != '' && is_numeric($position) && $position > -1 && $position < 1000){
				if(in_array($type, $this->types)){
					if(!in_array($type, $this->type_options) || (in_array($type, $this->type_options) && preg_match("/^(.){1,128}(\^){1}(.){1,128}/", $options))){
						if(preg_match('/[a-zA-Z0-9-_]{1,64}/', $name)){
							if(empty($maxlength) || preg_match('/^[\d]{1,16}$/', $maxlength)){
								if(parent::getAmount('fields', 'name', $name) == 0){
                                    if(DEMO_MODE){ return "In der DEMO nicht möglich!"; }
                                    
                                    $pos = $position;
									$autoPos = true;
									$next_pos = self::lastFieldPosition() + 1;
									if($next_pos < $pos){
										$pos = $next_pos;
									} else {
										$autoPos = self::autoPosition($pos);
									}
									
									if($autoPos == true){
										$sql = $this->mysql->query("Insert Into ".Prefix."_fields (`name`, `title`, `type`, `placeholder`, `value`, `maxlength`, `description`, `regex`, `options`, `pos`, `required`, `regist`, `regex_options`) Values ('$name', '$title', '$type', '$placeholder', '$value', '$maxlength', '$description', '$regex', '$options', '$position', '$required', '$regist', '$regex_options')");
										if($sql !== false){
											header('Location: ?p=additional_fields&h=additional_fields_add_successfully');
											exit();
										} else {
											$error = 'Fehler beim anlegen des Zusatzfeldes!';
											errormail($error.' Fehler in class '.__CLASS__.' => function '.__FUNCTION__.'()! MySQL-Fehler '.$this->mysql->errno.': '.$this->mysql->error);
										}
									} else {
										$error = 'Fehler beim positionieren des Feldes! Bitte versuche es sp&auml;ter erneut.';
									}
								} else {
									$error = 'Dieser Name wird bereits f&uuml;r ein anderes Feld verwendet! Bitte w&auml;hle einen anderen...';
								}
							} else {
								$error = 'Maximale L&auml;nge, muss eine Zahl zwischen 1 und 9.999.999.999.999.999 sein!';
							}
						} else {
							$error = 'Der Name darf nur Buchstaben a-Z, Zahlen 0-9, -, _ enthalten!';
						}
					} else {
						$error = 'Bei den Typen "Radio, Checkbox und Select" muss mindestens eine Option angegeben werden!';
					}
				} else {
					$error = 'Unbekannter Typ!';
				}
			} else {
				$error = 'Du musst mindestens die Felder "Name, Typ und Position ausf&uuml;llen"!';
			}
		} else {
			$error = 'Du hast keine Berechtigung um diese Aktion auszuf&uuml;hren!';
		}
		
		return $error;
	}
	
	public function editField(){
		global $id;
		$error = NULL;
		$name = length(isset($_POST['name']) ? $_POST['name'] : '', 64, 0, "sql");
		$title = length(isset($_POST['title']) ? $_POST['title'] : '', 64);
		$type = length(isset($_POST['type']) ? $_POST['type'] : '', 64, 0, "sql");
		$placeholder = length(isset($_POST['placeholder']) ? $_POST['placeholder'] : '', 128);
		$value = length(isset($_POST['value']) ? $_POST['value'] : '', 1024, 0, "sql");
		$maxlength = length(isset($_POST['maxlength']) ? $_POST['maxlength'] : '', 11, 0, "sql");
		$description = length(isset($_POST['description']) ? $_POST['description'] : '', 9999999999999999, 0, "sql");
		$regex = length(isset($_POST['regex']) ? $_POST['regex'] : '', 9999999999999999, 0, "sql");
		$options = length(isset($_POST['options']) ? $_POST['options'] : '', 8192, 0, "sql");
		$pos = length(isset($_POST['position']) ? $_POST['position'] : '', 3, 0, "sql");
		$required = length(isset($_POST['required']) ? $_POST['required'] : 0, 1);
		$regist = length(isset($_POST['regist']) ? $_POST['regist'] : 0, 1);
		$regex_options = length(isset($_POST['regex_options']) ? $_POST['regex_options'] : NULL, 8);
		
		$types = array("text", "url", "email", "number", "radio", "checkbox", "textarea", "select");
		$type_options = array("radio", "checkbox", "select");
		
		if(parent::auditRight('fields_edit')){
			if(!empty($id) && parent::getAmount('fields', 'id', $id) == 1){
				if(!empty($name) && !empty($type) && $pos != '' && is_numeric($pos) && $pos > -1 && $pos < 1000){
					if(preg_match('/[a-zA-Z0-9-_]{1,64}/', $name)){
						if(in_array($type, $this->types)){
							if(!in_array($type, $this->type_options) || (in_array($type, $this->type_options) && preg_match("/^(.){1,128}(\^){1}(.){1,128}/", $options))){
								if(empty($maxlength) || preg_match('/^[\d]{1,16}$/', $maxlength)){
                                    if(DEMO_MODE){ return "In der DEMO nicht möglich!"; }
                                    
                                    $old_pos = parent::getValue('fields', 'id', $id, 'pos');
									$next_pos = self::lastFieldPosition() + 1;
									$autoPos = true;
									
									if($next_pos < $pos){
										$pos = $next_pos;
									} elseif($old_pos != $pos){
										if($old_pos > $pos){
											$autoPos = self::autoPosition($pos, 0, $old_pos);
										} elseif($old_pos < $pos){
											$autoPos = self::autoPosition($old_pos, 1, $pos);
										}
									}
									
									if($autoPos == true){
										$sql = $this->mysql->query("Update `".Prefix."_fields` Set `name` = '$name', `title` = '$title', `type` = '$type', `placeholder` = '$placeholder', `value` = '$value', `maxlength` = '$maxlength', `description` = '$description', `regex` = '$regex', `options` = '$options', `required` = '$required', `regist` = '$regist', `pos` = '$pos', `regex_options` = '$regex_options' Where `id` = '$id'");
										if($sql !== false){
											header('Location: ?p=additional_fields&h=additional_fields_edit_successfully');
											exit();
										} else {
											$error = 'Fehler beim bearbeiten des Zusatzfeldes!';
											errormail($error.' Fehler in class '.__CLASS__.' => function '.__FUNCTION__.'()! MySQL-Fehler '.$this->mysql->errno.': '.$this->mysql->error);
										}
									} else {
										$error = 'Fehler beim positionieren des Feldes! Bitte versuche es sp&auml;ter erneut.';
									}
								} else {
									$error = 'Maximale L&auml;nge, muss eine Zahl zwischen 1 und 9.999.999.999.999.999 sein!';
								}
							} else {
								$error = 'Bei den Typen "Radio, Checkbox und Select" muss mindestens eine Option angegeben werden!';
							}
						} else {
							$error = 'Unbekannter Typ!';
						}
					} else {
						$error = 'Der Name darf nur Buchstaben a-Z, Zahlen 0-9, -, _ enthalten!';
					}
				} else {
					$error = 'Du musst mindestens die Felder "Name, Typ und Position ausf&uuml;llen"!';
				}
			} else {
				$error = 'Dieses Feld existiert nicht!';
			}
		} else {
			$error = 'Du hast keine Berechtigung um diese Aktion auszuf&uuml;hren!';
		}
		
		return $error;
	}
	
	public function removeField(){
		global $id;
		$error = NULL;
		// Does the user have the required authority?
		if(parent::auditRight('fields_remove')){
			// Exists this field id?
			if(!empty($id) && parent::getAmount('fields', 'id', $id) == 1){
				$passwd = length(isset($_POST['password']) ? $_POST['password'] : '', 64);
				// Check password
				if(parent::pwverify($passwd)){
                    if(DEMO_MODE){ return "In der DEMO nicht möglich!"; }
                    
                    $pos = parent::getValue('fields', 'id', $id, 'pos');
					
					// Delete Field
					$sql = $this->mysql->query("Delete From `".Prefix."_fields` Where `id` = '$id'");
					if($sql !== false){
						// Adjust positions
						$autoPos = self::autoPosition($pos, 1);
						
						// Delete all field data from users
						$sql = $this->mysql->query("Delete From `".Prefix."_additional_user_information` Where `field_id` = '$id'");
						if($sql !== false){
							header('Location: ?p=additional_fields&h=additional_fields_remove_successfully');
							exit();
						} else {
							$error = 'Fehler beim entfernen des Zusatzfeldes!';
							errormail($error.' (2) Fehler in class '.__CLASS__.' => function '.__FUNCTION__.'()! MySQL-Fehler '.$this->mysql->errno.': '.$this->mysql->error);
						}
					} else {
						$error = 'Fehler beim entfernen des Zusatzfeldes!';
						errormail($error.' Fehler in class '.__CLASS__.' => function '.__FUNCTION__.'()! MySQL-Fehler '.$this->mysql->errno.': '.$this->mysql->error);
					}
				} else {
					$error = 'Du hast ein falsches Passwort eingegeben!';
				}
			} else {
				$error = 'Dieser Eintrag existiert nicht!';
			}
		} else {
			$error = 'Du hast keine Berechtigung um diese Aktion auszuf&uuml;hren!';
		}
		
		return $error;
	}
	
	public function showFields($regist = 0, $prefix = '', $id = NULL, $tpl = 'default'){
		$rtn = array();
		$query = ($regist != 0) ? "Where `regist` = '$regist'" : '';
		
		$sql = $this->mysql->query("Select * From `".Prefix."_fields` $query Order by `pos` ASC");
		while($row = $sql->fetch_assoc()){
			$pre = !empty($id) ? parent::getValue('additional_user_information', array('user_id', 'field_id'), array($id, $row['id']), 'value') : NULL;
			if($row['type'] == 'checkbox'){
				$pre = explode('#', $pre);
			}
			$load_option_tpl = NULL;
			$options = array();
			$post_val = isset($_POST[$prefix.$row['name']]) ? $_POST[$prefix.$row['name']] : $pre;
			
			$load_tpl = file_get_contents($this->tpl_field_dir.$tpl.'/field_'.$row['type'].'.tpl');
			if(in_array($row['type'], $this->type_options)){
				$load_option_tpl = file_get_contents($this->tpl_field_dir.$tpl.'/field_'.$row['type'].'_options.tpl');
				$option_array = explode('#', $row['options']);
				foreach($option_array as $val){
					$select = NULL;
					$values = explode('^', $val);
					$field_type = ($row['type'] == 'select') ? 0 : 1;
					$select = checker($post_val, $values[0], $field_type, $prefix.$row['name']);
					$search = array("{OPTION_NAME}", "{OPTION_VALUE}", "{SELECTED}", "{NAME}", "{DESCRIPTION}");
					$replace = array($values[1], $values[0], $select, $prefix.$row['name'], $row['description']);
					$options[] = str_replace($search, $replace, $load_option_tpl);
				}
			}
			
			$required = ($row['required'] == 1) ? $this->required_text : '';
			$replace_value = empty($post_val) ? $row['value'] : $post_val;
			$option_glue = ($row['type'] == 'select') ? '' : '<br>';
			
			// Replace variables in tpl
			$load_tpl = str_replace('{ID}', $row['id'], $load_tpl);
			$load_tpl = str_replace('{NAME}', $prefix.$row['name'], $load_tpl);
			$load_tpl = str_replace('{TITLE}', $row['title'].$required, $load_tpl);
			$load_tpl = str_replace('{TYPE}', $row['type'], $load_tpl);
			$load_tpl = str_replace('{PLACEHOLDER}', $row['placeholder'], $load_tpl);
			$load_tpl = str_replace('{MAXLENGTH}', $row['maxlength'], $load_tpl);
			$load_tpl = str_replace('{DESCRIPTION}', $row['description'], $load_tpl);
			$load_tpl = str_replace('{VALUE}', $replace_value, $load_tpl);
			$load_tpl = str_replace('{OPTIONS}', implode($option_glue, $options), $load_tpl);
			
			$rtn[] = $load_tpl;
		}
		
		return implode("\n", $rtn);
	}
	
	public function setFieldValues($regist = 0, $prefix = NULL, $id = NULL, $checkOnly = false){
		$error = NULL;
		$query = ($regist != 0) ? "Where `regist` = '$regist'" : '';
        if(DEMO_MODE){ return "In der DEMO nicht möglich!"; }
        
        $sql = $this->mysql->query("Select * From `".Prefix."_fields` $query Order by `pos` ASC");
		while($row = $sql->fetch_assoc()){
			$val = isset($_POST[$prefix.$row['name']]) ? $_POST[$prefix.$row['name']] : NULL;
			if(is_array($val)){
				$val = implode('#', $val);
			} else {
				$val = length($val, $row['maxlength'], 0, "sql");
			}
			
			if(!empty($val) || $row['required'] == 0){
				if(empty($row['regex']) || $row['required'] == 0 || preg_match('#'.$row['regex'].'#'.$row['regex_options'], $val)){
					if($checkOnly == false){
						if(parent::getAmount('additional_user_information', array('field_id', 'user_id'), array($row['id'], $id)) == 0){
							$query = "Insert Into `".Prefix."_additional_user_information` (`field_id`, `user_id`, `value`) Values ('".$row['id']."', '$id', '$val')";
						} else {
							$query = "Update `".Prefix."_additional_user_information` Set `value` = '$val' Where `field_id` = '".$row['id']."' AND `user_id` = '$id'";
						}

						$sql_update = $this->mysql->query($query);
						if($sql_update === false){
							$error = 'Fehler beim speichern eines Feldes! Bitte versuche es zu einem sp&auml;teren Zeitpunkt erneut.';
							errormail('Fehler beim speichern des Feldes "'.$row['name'].'#'.$row['id'].'"! Fehler in class '.__CLASS__.' => function '.__FUNCTION__.'!<br> MySQL-Fehler '.$this->mysql->errno.': <br>'.$this->mysql->error.'<br><br>Query: '.$query);
							break;
						}
					}
				} else {
					$error = 'Das Feld "'.$row['title'].'" wurde nicht nach den Vorgaben ausgef&uuml;llt! Bitte beachte die Beschreibung!';
					break;
				}
			} else {
				$error = 'Bitte f&uuml;lle das Feld "'.$row['title'].'" aus!';
				break;
			}
		}
		
		return $error;
	}
	
	public function getFieldValues($id, $tpl = 'default'){
		$rtn = array();
		$tpl_text = file_get_contents($this->tpl_field_dir.$tpl.'/get_field_text.tpl');
		$tpl_multi = file_get_contents($this->tpl_field_dir.$tpl.'/get_field_multi.tpl');
		$query = "Select a.*, b.`type`, b.`title`, b.`options` From ".Prefix."_additional_user_information a INNER JOIN ".Prefix."_fields b ON a.field_id = b.id Where a.user_id = '$id' AND a.value != '' Order by b.pos ASC";
		$sql = $this->mysql->query($query);
		while($row = $sql->fetch_assoc()){
			$load_tpl = $tpl_text;
			$options = array();
			
			if(in_array($row['type'], $this->type_options)){
				$options = explode('#', $row['value']);
				$org_options = explode('#', $row['options']);
				for($i = 0; $i < count($org_options); $i++){
					$tmp = explode('^', $org_options[$i]);
					$org_options_array[$tmp[0]] = $tmp[1];
				}
				for($i = 0; $i < count($options); $i++){
					$options[$i] = $org_options_array[$options[$i]];
				}
			}
			
			if(count($options) < 2){
				if(!empty($options[0])){
					$val = $options[0];
				} else 
					$val = $row['value'];
				
				if($row['type'] == 'textarea')
					$val = nl2br($val);				
			} else {
				$org_options = explode('#', $row['options']);
				for($i = 0; $i < count($org_options); $i++){
					$tmp = explode('^', $org_options[$i]);
					$org_options_array[$tmp[0]] = $tmp[1];
				}

				$end_options = array();
				foreach($options as $opt){
					$load_multi_tpl = $tpl_multi;
					$end_options[] = str_replace('{OPTION_NAME}', $opt, $load_multi_tpl);
				}
				$val = implode(', ', $end_options);
			}
			
			$load_tpl = str_replace('{TITLE}', $row['title'], $load_tpl);
			$load_tpl = str_replace('{VALUE}', $val, $load_tpl);
			$rtn[] = $load_tpl;
		}
		
		return implode("\n", $rtn);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}

?>
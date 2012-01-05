<?php
/*
 * Tikapot Forms
 *
 */


require_once(home_dir . "framework/form_fields/init.php");

abstract class FormPrinter
{
	public abstract function run($form);
}

class HTMLFormPrinter extends FormPrinter
{
	public function run($form) {
		print $form->get_header();
		$formid = $form->get_form_id();
		foreach ($form->get_fieldsets() as $fieldset) {
			print '<fieldset>';
			if ($fieldset->get_legend() !== "")
				print '<legend>' . $fieldset->get_legend() . '</legend>';
			foreach ($fieldset->get_fields() as $name => $field) {
				print $field->get_label($fieldset->get_id($formid), $name);
				print $field->get_input($fieldset->get_id($formid), $name);
				print $field->get_error_html($fieldset->get_id($formid), $name);
			}
			print '</fieldset>';
		}
		print '<fieldset>';
		print '<input type="submit" name="submit" value="'.$GLOBALS["i18n"]["submit"].'" />';
		print '</fieldset>';
		print '</form>';
	}
}

class TableFormPrinter extends FormPrinter
{
	public function run($form) {
		print $form->get_header();
		$formid = $form->get_form_id();
		foreach ($form->get_fieldsets() as $fieldset) {
			print '<fieldset><table style="width: auto;">';
			if ($fieldset->get_legend() !== "")
				print '<legend>' . $fieldset->get_legend() . '</legend>';
			$fid = $fieldset->get_id($formid);
			foreach ($fieldset->get_fields() as $name => $field) {
				if ($field->get_type() == "hidden") {
					print $field->get_input($fid, $name);
				} else {
					print '<tr>';
					print '<td>'.$field->get_label($fid, $name).'</td>';
					print '<td>'.$field->get_input($fid, $name).'</td>';
					if (strlen($field->get_error()) > 0)
						print '<td>'.$field->get_error_html($fid, $name).'</td>';
					print '</tr>';
				}
			}
			print '</table></fieldset>';
		}
		print '<fieldset>';
		print '<input type="submit" name="submit" value="'.$GLOBALS["i18n"]["submit"].'" />';
		print '</fieldset>';
		print '</form>';
	}
}

class FormEmailer extends FormPrinter
{
	private $to, $from, $subject;
	
	public function __construct($to_address, $from_address, $subject) {
		$this->to = $to_address;
		$this->from = $from_address;
		$this->subject = $subject;
	}
	
	public function sanitize($str) {
		$injections = array(
			'/(\n+)/i',
			'/(\r+)/i',
			'/(\t+)/i',
			'/(%0A+)/i',
			'/(%0D+)/i',
			'/(%08+)/i',
			'/(%09+)/i'
		);
		return preg_replace($injections, '', $str);
	}
	
	private function add_attachment($name, $mime_boundary, $safe_name) {
		if(!isset($_FILES[$name]))
			return $GLOBALS["i18n"]["errorfile"]."<br />";
		$file_path = $_FILES[$name]["tmp_name"];
		if(!is_file($file_path)) // TODO - only fields with correct names are allowed!
			return "";
		
		$fp = @fopen($file_path, "rb");
		$data = @fread($fp, filesize($file_path));
		@fclose($fp);
		$data = chunk_split(base64_encode($data));
		
		$ret = '--'.$mime_boundary."\r\n";
		$ret .= 'Content-Type: application/octet-stream; name="' . basename($file_path) . "\"\r\n";
		$filename = "(" . $safe_name . ") " . $_FILES[$name]["name"];
		$ret .= 'Content-Description: ' . $filename . "\r\n";
		$ret .= 'Content-Disposition: attachment;' . "\r\n";
		$ret .= ' filename="' . $filename . '"; size="' . filesize($file_path) . '";' . "\r\n";
		$ret .= 'Content-Transfer-Encoding: base64' . "\n\n";
		$ret .= $data;
		$ret .= "\n\n";
		return $ret;
	}

	public function run($form) {
		$message = '<html>
						<head>
							<title>'.$this->subject.'</title>
						</head>
						<body>';
		
		foreach ($form->get_fieldsets() as $fieldsetname => $fieldset) {
			if ($fieldsetname === "control")
				continue;
			$message .= "\n";
			if (strlen($fieldset->get_legend()) > 0)
				$message .= '<h2>' . $this->sanitize($fieldset->get_legend()) . '</h2><br />';
			foreach ($fieldset->get_fields() as $name => $field) {
				if ($field->get_type() !== "file") {
					$message .= '<b>' . $this->sanitize($field->get_name()) . ':</b> ' . $this->sanitize($field->get_value()) . '<br />';
				}
			}
		}
		
		$message .= '</body></html>';
		
		$headers  = 'From: ' . $this->from . "\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		
		// Do we need to add attachments?
		if ($form->has_file()) {
    		$mime_boundary = "==Multipart_Boundary_x".md5(time())."x";
			$headers .= 'Content-Type: multipart/mixed;' . "\n";
			$headers .= ' boundary="' . $mime_boundary . '"' . "\n";
			
			// Prepare message
			$message_header = '--' . $mime_boundary . "\n";
			$message_header .= 'Content-Type: text/html; charset="iso-8859-1"' . "\n";
			$message_header .= 'Content-Transfer-Encoding: 7bit' . "\n\n";
			$message = $message_header . $message . "\n\n";
			
			// Add the files
			foreach ($form->get_fieldsets() as $fieldset) {
				foreach ($fieldset->get_fields() as $name => $field) {
					if ($field->get_type() == "file") {
						$base_id = $fieldset->get_id($form->get_form_id());
						$fname = $field->get_field_id($base_id, $name);
						$message .= $this->add_attachment($fname, $mime_boundary, $name);
					}
				}
			}
		} else {
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		}

		return @mail($this->to, $this->subject, $message, $headers);
	}
}
?>

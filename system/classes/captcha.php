<?php
/********************************************
* 
* System:   EASY 2.0 Loginsystem
* Author:   Marius Rasche (aka Marlight)
* Class:    captcha
* File:     captcha.php
* FVersion: 0.2 (this file)
* SVersion: 0.9.8.1 BETA (complete System)
* Date:     14.11.2017
*
* Created by www.marlight-systems.de
* Copyright by Marlight Systems (www.marlight-systems.de)
* All rights reserved.
*
* PHP 8 modifications: Copyright (C) 2026 Andreas P. <https://nfsmw15.de>
* SPDX-License-Identifier: AGPL-3.0-or-later
*********************************************/

class captcha{
	private $img_width  = 100; // in px
	private $img_height = 30; // in px
	private $background = "transparent"; // HEX-Code or "transparent"
	private $text_color = "000000"; // HEX-Code
    private $font       = __DIR__."/../fonts/Captureit.ttf";
	private $font_size  = 20;
	
	private $length       = 4;
	private $chars        = "ABCDEFGHKMNPRSTUVWZ2356789";
	private $name_session = "ml_captcha";
	
	public function __construct(){
		
	}
	
	public function generateCaptcha(){
		putenv('GDFONTPATH=' . realpath('.'));
		if(extension_loaded('gd') == false){
			die("The GD extension is required for CAPTCHA!");
		}
		if(function_exists('imagettftext') == false){
			die("The function 'imagettftext' is required for CAPTCHA!");
		}
		
		$img = imagecreatetruecolor($this->img_width, $this->img_height);
		imagesavealpha($img, true);
		
		if($this->background == "transparent"){
			$bkgr = imagecolorallocatealpha($img, 255, 255, 255, 127);
		} else {
			$color_red = hexdec(substr($this->background, 0, 2));
			$color_green = hexdec(substr($this->background, 2, 2));
			$color_blue = hexdec(substr($this->background, 4, 2));
			$bkgr = imagecolorallocate($img, $color_red, $color_green, $color_blue);
		}
		
		imagefill($img, 0, 0, $bkgr);
		imagefilledrectangle($img, 0, 0, $this->img_width, $this->img_height, $bkgr);
		$code = '';
		$chars = $this->chars;
		
		for($i = 0; $i < $this->length; $i++){
			$code .= $chr = $chars[random_int(0, strlen($chars)-1)];
			$r = hexdec(substr($this->text_color, 0, 2));
			$g = hexdec(substr($this->text_color, 2, 2));
			$b = hexdec(substr($this->text_color, 4, 2));
			$color = imagecolorallocate($img, $r, $g, $b);
			$rotation = random_int(-25, 25);
			$x = 5 + $i * (int)(4/3 * $this->font_size - 6);
			$minY = (int)round($this->font_size * 1.1);
			$maxY = max($minY, $this->img_height - 3);
			$y = random_int($minY, $maxY);
			imagettftext($img, $this->font_size, $rotation, $x, $y, $color, $this->font, $chr);
		}

		$_SESSION[$this->name_session] = $code;
		header("Content-type: image/png");
		header("Expires: Sun, 05 Nov 2017 23:35:00 GMT");
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		
		imagepng($img);
		$img = null;
		exit();
	}
	
	function check_captcha($code = NULL){
		$captcha_code = $_SESSION[$this->name_session] ?? '';
		$code = strtoupper((string)$code);
		if(!empty($captcha_code) && !empty($code)){
			if(hash_equals($captcha_code, $code)){
				unset($_SESSION[$this->name_session]);
				return true;
			}
		}
		return false;
	}
}



?>
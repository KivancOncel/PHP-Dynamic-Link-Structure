<?php

	/**

	* Plugin Name: Dynamic Link Structure

	* Plugin URI: https://github.com/KivancOncel/PHP-Dynamic-Link-Structure

	* Description: Automatically creating a clickable menu for h1 and h2 tags on the page..

	* Version: 0.1

	* Author: S. Kıvanç ÖNCEL

	* Author URI: https://github.com/KivancOncel

	**/

	function auto_id_headings2( $content ) {


		$pattern = '#(?P<full_tag><(?P<tag_name>h\d)(?P<tag_extra>[^>]*)>(?P<tag_contents>[^<]*)</h\d>)#i';
		if ( preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER ) ) {
			$find = array();
			$replace = array();
			foreach( $matches as $match ) {
				if ( strlen( $match['tag_extra'] ) && false !== stripos( $match['tag_extra'], 'id=' ) ) {
					continue;
				}
				$find[]    = $match['full_tag'];
				$id        = sanitize_title( $match['tag_contents'] );
				$id_attr   = sprintf( ' id="%s"', $id );
				$replace[] = sprintf( '<%1$s%2$s%3$s>%4$s</%1$s>', $match['tag_name'], $match['tag_extra'], $id_attr, $match['tag_contents']);
			}
			$content = str_replace( $find, $replace, $content );
		}
		return $content;



	}

	add_filter( 'the_content', 'auto_id_headings2' );

	

	function interBlock($content, $ad=null, $element="<h1", $altElement='<h3', $afterElement=2, $minElements=5) {		

		if (!$ad) $ad = createLink2($content);

		$parts = explode("<h2", $content);		

		$count = count($parts);

		if(($count-1)<$minElements) {

			$parts = explode($altElement, $content); 

			$count = count($parts);

			if(($count-1)<$minElements) return $content; 

			$element = $altElement;

		}

		

		$output='';

		for($i=1; $i<$count; $i++) {			

			$output .=  ($i==1 ? $parts[0]:'') . (($i==$afterElement) ? $ad : '')  . $element . $parts[$i]; 

		}

		return $output;

	}

	add_filter( 'the_content', 'interBlock' );



	function createLink2( $content ) {

		

		$htmlDom = new DOMDocument;



		@$htmlDom->loadHTML('<?xml encoding="utf-8"?>'.$content);



		$h1Tags = $htmlDom->getElementsByTagName('h1');

		

		$h2Tags = $htmlDom->getElementsByTagName('h2');



		$extractedH1Tags = [];

		$extractedH2Tags = [];



		foreach($h1Tags as $h1Tag){



			$h1Value = trim($h1Tag->nodeValue);



			$extractedH1Tags[] = $h1Value;

		}



		foreach($h2Tags as $h2Tag){



			$h2Value = trim($h2Tag->nodeValue);



			$extractedH2Tags[] = $h2Value;

		}



		$headingsArray = [

		  "h1" => $extractedH1Tags,

		  "h2" => $extractedH2Tags,

		];

		

		$i = 0;

		$text = "";

		foreach ($htmlDom->getElementsByTagName('h2') as $p) {

			$i++;

			

			if ($i == 2) {

				$text .= "<ul style=' float: right; margin-left: 30px;'>";

				$sayi = 1;

				foreach($headingsArray as $key => $value) {

					for($i = 0;$i<count($value);$i++) {

						if($value[$i] != "") {

							$text .= "<li><a href='#".sanitize_title(str_replace(' ', '-', $value[$i]))."'>".$value[$i]."</a></li>";

							$sayi++;

						}

					}

				}

				$text .= "</ul>";

			}

		}

		

		return $text;

		

	}

?>
<?php
	function auto_id_headings2( $content ) {

		$content = preg_replace_callback( '/(\<h[1-6](.*?))\>(.*)(<\/h[1-6]>)/i', function( $matches ) {
			if ( ! stripos( $matches[0], 'id=' ) ) :				
				$matches[0] = $matches[1] . $matches[2] . ' id="' . str_replace(" ","-",$matches[3]) . '">' . $matches[3] . $matches[4];
			endif;
			return $matches[0];
		}, $content );

		return $content;

	}
	add_filter( 'the_content', 'auto_id_headings2' );
	
	function interBlock($content, $ad=null, $element="<h2", $altElement='<h3', $afterElement=2, $minElements=5) {		
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
							$text .= "<li><a href='#".str_replace(' ', '-', $value[$i])."'>".$value[$i]."</a></li>";
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
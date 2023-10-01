<?php
	require("config_telugu_names.php");
	$html = "<html>
	<head>
	<style>
	body {
	    font-size: 8pt;
	}
	tbody td { vertical-align: top; line-height:25px;  }
	thead td { vertical-align: middle; }
	table.ddd td{ border:1px solid #cdcdcd; font-size:10pt; }	 
	</style>
	</head>
	<body>
	<center>
	<div style=\"font-family:Suravaram;\"> sdfsdfsdfs ".$config_telugu_names['101']['telugu']."  &#40706; &#40742; &#40772; &#40784; &#40802; &#40809;
    &#x23289; &#x2328a; sdfsdfdsfsd</div>
    <div style=\"font-family:pmingliu;\">
    &#40706; &#40742; &#40772; &#40784; &#40802; &#40809;
	    &#x23289; &#x2328a;
	</div>
	</center>
	</body>
	</html>";

	//error_reporting(0);
	//echo $html;exit;
	require('vendor/autoload.php');
	$defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
	$fontDirs = $defaultConfig['fontDir'];
	// print_r($fontDirs);
	// echo __DIR__;
	// exit;

	$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
	$fontData = $defaultFontConfig['fontdata'];	
//	echo "<pre>";print_r( $fontData );exit;

	$mpdf = new \Mpdf\Mpdf(['mode'=>'s',
		'fontDir' => array_merge($fontDirs, [
	        __DIR__ . '/fonts',
	    ]),
	    'fontdata' => $fontData + [ // lowercase letters only in font key
	        'Suravaram' => [
	            'R' => 'Suravaram.ttf',
	            'B' => 'Suravaram.ttf',
	            'I' => 'Suravaram.ttf',
	            'BI' => 'Suravaram.ttf',
	            'TTCfontID' => [
	                'R' => 1,
	            ],
	            'useOTL' => 0xFF,
	        ]
	    ],
	]);

	//$fontname = $mpdf->addTTFfont('./Suravaram.ttf', 'TrueTypeUnicode');
	$mpdf->WriteHTML($html);
	$mpdf->autoScriptToLang = true;
	$mpdf->autoLangToFont = true;
	$mpdf->baseScript = 1;
	$mpdf->SetDisplayMode('fullpage');
	$mpdf->Output("temp.pdf", "I");
	exit;

?>
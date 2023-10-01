<?php

	$url = explode("/",($_GET['module']??'') );
	$module = $url[0];
	$param1 = (sizeof($url)>1?$url[1]:"");
	$param2 = (sizeof($url)>2?$url[2]:"");

<?php

ini_set( "default_charset", "utf-8" );

if( $_SERVER['HTTP_X_FORWARDED_FOR'] ){
    $d = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR'] );
    $_SERVER['REMOTE_ADDR'] = trim($d[0]);
    $_SERVER['HTTP_X_REAL_IP'] = trim($d[0]);
}else{
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP']?$_SERVER['HTTP_X_REAL_IP']:$_SERVER['REMOTE_ADDR'];
}

// if( $_SERVER['REMOTE_ADDR'] != "43.241.66.118"){
// 	header("http/1.1 403 too many requests");
// 	echo "Under Construction!";exit;
// }

$config_categories  = array(
"101"=> array(
	"sno"=> "101", 
	"name"=> "Playlets", 
	"english"=> "Short Plays", 
	"details"=>"Minimum 15 min. Choose a topic suitable for age group", 
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [1,15], [1,15], [1,15] ),
	"group"=>true,
	),
"102"=>array(
	"sno"=> "102", 
	"name"=> "Classical Dance - Group",
	"english"=> "Classical Dance - Group", 
 	"details"=>"Minimum 5 should be in group. Please bring song in pendrive", 
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [5,15], [5,15], [5,15] ),
	"group"=>true,
	 ), 
"103"=> array( 
	"sno"=> "103", 
	"name"=> "Songs - Group",
	"english"=> "Songs - Group",
	"details"=>"Minimum 4 should be in group. Light/Folk music" , 
	"enabled"=> array( 0, 1, 1), 
	"max"=> array( 0, [4,15], [4,15] ),
	"group"=>1,
	 ), 
"104"=> array( 
	"sno"=> "104", 
	"name"=>"Instrument Music",
	"english"=> "Instrument Music",
	"details"=>"Should bring your own instrument",
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [1,3], [1,3], [1,3] ),
	),
"105"=> array(
	 "sno"=> "105", 
	"name"=> "Mono Play", 
	"english"=> "Solo Act", 
	"details"=>"Settings and background music allowed", 
	"enabled"=> array( 0, 1, 1), 
	"max"=> array( 0, [1,2], [1,2] ),
	), 
"106"=> array( 
	"sno"=> "106", 
	"name"=>"Mime, Magic, Mimicry", 
	"english"=> "Mime, Magic, Mimicry", 
	"details"=>"Can present any talent suitable for stage", 
	 "enabled"=> array( 0, 1, 1), 
	 "max"=> array( 0, [1,2], [1,2] ),
	 ), 
"107"=> array( 
	"sno"=> "107", 
	"name"=>"Poster Presentation", 
	"english"=> "Poster Presentation", 
	"details"=>"Topics: Jr: Chandrayaan or Rivers of India, Sr: Gender Descrimination or G20", 
	 "enabled"=> array( 0, 1, 1), 
	 "max"=> array( 0, [1,2], [1,2] ),
	 ), 
"108"=> array( 
	"sno"=> "108", 
	"name"=>"Science Experiments", 
	"english"=> "Science Experiments ", 
	"details"=>"Should bring your apparatus and demonstrate", 
	"enabled"=> array( 0, 1, 1), 
	"max"=> array( 0, [1,2], [1,2] ),
	), 
"109"=> array(
	"sno"=> "109",
	"name"=>"Kolattam", 
	"english"=> "Kolattam", 
	"details"=>"Minimum 16 persons and 10 minutes", 
	"enabled"=> array( 0, 1, 1), 
	"max"=> array( 0, [16,50], [16,50] ),
	"group"=>1,
	), 
"110"=> array(
	"sno"=> "110",
	"name"=>"Essay Writing",
	"english"=> "Essay Writing",
  	"details"=>"List of topics will be announced on the spot",
	"enabled"=> array( 0, 0, 1 ),
	"max"=> array( 0, 0, [1,5] ),
   ),
"111"=> array(
	"sno"=> "111",
	"name"=>"Quiz - Group (Two)", 
	"english"=> "Quiz", 
	"details"=>"First round written test, further rounds oral", 
	"enabled"=> array( 0, 2, 2), 
	"max"=> array( 0, [2,2], [2,2] ),
	"group"=>true,
	), 
"112"=> array(
	"sno"=> "112",
	"name"=> "Debate - Group (Three)", 
	"english"=> "Debate", 
	"details"=>"List of topics are in website", 
	"enabled"=> array( 0, 0, 2), 
	"max"=> array( 0, 0, [3,3] ),
	"group"=>true,
	), 

"113"=> array(
	"sno"=> "113",
	"name"=>"Short Film Review",
	"english"=> "Short Film Review",
  	"details"=>"Should write review on short film shown",
	"enabled"=> array( 0, 1, 1 ), 
	"max"=> array( 0, [1,5], [1,5] ),
   ), 
"114"=> array(
	 "sno"=> "114",
	"name"=>"Story Writing",
	"english"=> "Story Writing",
	"details"=>"Should write a story in telugu on given topic",
	"enabled"=>array( 0, 1, 1),
	"max"=> array( 0, [1,5], [1,5] ),
	),
"115"=> array(
	 "sno"=> "115", 
	"name"=>"Story Analysis",
	"english"=> "Story Analysis", 
	"details"=>"List of stories are in website",
	 "enabled"=> array( 0, 1, 1), 
	 "max"=> array( 0,  [1,5], [1,5] ),
	 ),
"116"=> array(
	 "sno"=> "116",
	"name"=>"Search in Internet", 
	"english"=> "Internet Search", 
	"details"=> "Should write answers using search in your smartphone", 
	"enabled"=> array( 0, 1, 1), 
	"max"=> array( 0, [1,4], [1,4] ),
	),  
"117"=> array(
	 "sno"=> "117",
	"name"=>"Map Pointing", 
	"english"=> "Map", 
	"details"=>"Topics: Jr:World, Sr:India, Map will be supplied", 
	"enabled"=> array( 0, 1, 1), 
	"max"=> array( 0, [1,4], [1,4] ),
	), 

"118"=>array(
	"sno"=> "118",
	"name"=> "Folk Dance - Group",
	"english"=> "Folk Dance - Group", 
 	"details"=>"Minimum 5 in group, Please bring song in pendrive", 
	"enabled"=> array( 1, 1, 1 ), 
	"max"=> array( [5,15], [5,15], [5,15] ),
	"group"=> true,
	 ), 
"119"=> array(
	 "sno"=> "119",
	"name"=> "Songs Solo",
	"english"=> "Songs - Singles",
	"details"=> "Light/Folk Music", 
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [1,2], [1,2], [1,2] ),
	  ), 
"120"=> array(
	 "sno"=> "120",
	"name"=> "Fancy Dress",
	 "english"=> "Fancy Dress", 
	 "details"=> "Settings, Background Music, and Dialogues allowed", 
	 "enabled"=> array(1, 0, 0), 
	  "max"=> array( [1,3], 0, 0 ),
	  ), 
"121"=> array(
	 "sno"=> "121",
	"name"=> "Story Telling", 
	"english"=> "Story Telling", 
	"details"=>"The way of narration would be given importance", 
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [1,2], [1,2], [1,2] ),
	),
"122"=> array(
	 "sno"=> "122",
	"name"=>"Project Work - Solo", 
	"english"=> "Project Work", 
	"details"=>"Should display and demonstrate", 
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [1,2], [1,2], [1,2] ),
	), 
"123"=> array(
	"sno"=> "123",
	"name"=> "Burrakatha - Group", 
	"english"=> "Burrakatha - Group", 
	"details"=>"Minimum 10 minutes. Choose a topic suitable for age group", 
	"enabled"=> array( 1, 1, 1), 
	"group"=>1,
	"max"=>array( [3,3], [3,3], [3,3] ),
),

"124"=> array(
	"sno"=> "124",
	"name"=>"Spelling", 
	"english"=> "Spelling", 
	"details"=>"List of words and other details are in website", 
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [1,3], [1,3], [1,3] ),
	), 
	"125"=> array(
	 "sno"=> "125",
	"name"=> "Drawing", 
	"english"=> "Painting", 
	"details"=>"Drawing sheet will be given. Topic will be announced on the spot",
	 "enabled"=> array( 5, 5, 5), 
	 "max"=> array( [1,5], [1,5], [1,5] ),
	 ), 
	"126"=> array(
	 "sno"=> "126",
	"name"=>"Craft Creative Making",
	 "english"=> "Creative Making", 
	 "details"=>"Should bring and prepare with your Low-cost/No-cost material", 
	 "enabled"=> array( 1, 1, 1), 
	 "max"=> array( [1,4], [1,4], [1,4] ),
	 ), 
	"127"=> array(
	 "sno"=> "127",
	 "name"=>"Clay Modelling - Solo",
	 "english"=> "Clay Modelling - Solo", 
	 "details"=>"Clay will be provided. Topic will be announced on the spot",
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [1,4], [1,4], [1,4] ),
	),
	"128"=> array(
	 "sno"=> "128",
	 "name"=>"Clay Modelling - Group",
	 "english"=> "Clay Modelling - Group", 
	 "details"=>"Clay will be provided. Topis are in website",
	"enabled"=> array( 0, 0, 1), 
	"max"=> array( 0, 0, [3,3] ),
	"group"=>1,
	)
);

$school_details = array(
	"school_name" => "School Name",
	"village_name"=> "Village Name",
	"district_name" => "Mandal/District",
	"teacher_name"=>"Teacher Name",
	"pno"=>"Mobile",
	"pno2"=>"Phone 2",
	"email"=>"Email",
	"school_category"=>"School Category",
	"school_type"=>"School Type",
	"medium"=> "Medium"
);



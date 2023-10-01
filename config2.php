<?php

$config_categories  = array(

"101"=> array(
	"sno"=> "101", 
	"name"=> "Playlets", 
	"english"=> "Short Plays", 
	"telugu"=>"లఘు నాటికలు ",
	"details"=>"Minimum 15 minits. choose a topic suitable for age group", 
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [1,15], [1,15], [1,15] ),
	"group"=>true,
	"old"=>101,
	),
"102"=>array(
	"sno"=> "102", 
	"name"=> "Classical Dance - Group",
	"telugu"=> "శాస్త్రీయ నృత్యం - గ్రూఫు ",
	"english"=> "Classical Dance - Group", 
 	"details"=>"Minimum 3 in group. CD/Pendrive/Orchestra Allowed", 
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [3,15], [3,15], [3,15] ),
	"group"=>true,
	"old"=>102,
	 ), 
"103"=> array( 
	"sno"=> "103", 
	"name"=> "Songs - Group",
	"english"=> "Songs - Groups",
	"telugu"=>"పాటల గ్రూఫు ",
	"details"=>"Minimum 4 should be in group" , 
	"enabled"=> array( 0, 1, 1), 
	"max"=> array( 0, [4,15], [4,15] ),
	"group"=>1,
	"old"=>103,
	 ), 
"104"=> array( 
	"sno"=> "104", 
	"name"=>"Instrument Music",
	"english"=> "Instrument Music",
	"telugu"=>"వాద్య సంగీతం ",
	"details"=>"Should bring your own instrument",
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [1,3], [1,3], [1,3] ),
	"old"=>104,
	),
"105"=> array(
	 "sno"=> "105", 
	"name"=> "Mono Play", 
	"english"=> "Solo Act", 
	"telugu"=>"ఏక పాత్రాభినయం ",
	"details"=>"Settings and background music allowed", 
	"enabled"=> array( 0, 1, 1), 
	"max"=> array( 0, [1,2], [1,2] ),
	"old"=>105,
	), 
"106"=> array( 
	"sno"=> "106", 
	"name"=>"Poster Presentaion", 
	"english"=> "Poster Presentation", 
	"telugu"=> "పోస్టర్ ప్రెజెంటేషన్ ",
	"details"=>"Topics: Jr: Plastic effects on pollution, Sr: Covid-19", 
	 "enabled"=> array( 0, 1, 1), 
	 "max"=> array( 0, [1,2], [1,2] ),
	 "old"=>106,
	 ), 
"107"=> array( 
	"sno"=> "107", 
	"name"=>"Science Experiments", 
	"english"=> "Science Experiments ", 
	"telugu"=> "సైన్స్ ప్రయోగాలు - వ్యక్తిగత ",
	"details"=>"Should bring your apparatus and demonstrate", 
	"enabled"=> array( 0, 1, 1), 
	"max"=> array( 0, [1,2], [1,2] ),
	"old"=>107,
	), 
"108"=> array(
	"sno"=> "108",
	"name"=>"Spelling", 
	"english"=> "Spelling", 
	"telugu"=>"స్పెల్లింగ్ ",
	"details"=>"list of words and other details are in website", 
	"enabled"=> array( 0, 1, 1), 
	"max"=> array( 0, [1,3], [1,3] ),
	"old"=>109,
	), 
"109"=> array(
	"sno"=> "109",
	"name"=>"Quiz - Group (Two)", 
	"english"=> "Quiz", 
	"telugu"=>"క్విజ్ - గ్రూప్ (ఇద్దరు)",
	"details"=>"First round written, further rounds oral" , 
	"enabled"=> array( 0, 2, 2), 
	"max"=> array( 0, [2,2], [2,2] ),
	"group"=>true,
	"old"=>110,
	), 
"110"=> array(
	"sno"=> "110",
	"name"=> "Debate - Group (Three)", 
	"english"=> "Debate", 
	"telugu"=>"డిబేట్ - గ్రూప్ (ముగ్గురు)",
	"details"=>"List of topics are in website", 
	"enabled"=> array( 0, 0, 2), 
	"max"=> array( 0, 0, [3,3] ),
	"group"=>true,
	"old"=>111,
	), 
"111"=> array(
	"sno"=> "111",
	"name"=>"Essay Writing",
	"telugu"=>"వ్యాస రచన ",
	"english"=> "Essay Writing",
  	"details"=>"Topic will be anounced on the spot",
	"enabled"=> array( 0, 0, 1 ),
	"max"=> array( 0, [1,5], [1,5] ),
	"old"=>113,
   ),
"112"=> array(
	"sno"=> "112",
	"name"=>"Short Film Review",
	"english"=> "Short Film Review",
	"telugu"=>"లఘు చిత్ర సమీక్ష ",
  	"details"=>"Should write review on short film shown",
	"enabled"=> array( 0, 1, 1 ), 
	"max"=> array( 0, [1,3], [1,3] ),
	"old"=>113,
   ), 
"113"=> array(
	 "sno"=> "113",
	"name"=>"Story Writing",
	"english"=> "Story Writing",
	"telugu"=>"కథా రచన ",
	"details"=>"Topic will be announced on the spot",
	"enabled"=>array( 0, 1, 1),
	"max"=> array( 0, [1,5], [1,5] ),
	"old"=>112,
	),
"114"=> array(
	 "sno"=> "114", 
	"name"=>"Story analysis",
	"english"=> "Story Analysis", 
	"telugu"=>"కథా విశ్లేషణ ",
	"details"=>"Should write analysis on given telugu story",
	 "enabled"=> array( 0, 0, 1), 
	 "max"=> array( 0, 0, [1,5] ),
	 "old"=>114,
	 ),
"115"=> array(
	 "sno"=> "115",
	"name"=>"Search in Internet", 
	"english"=> "Internet Search", 
	"telugu"=>"అంతర్జాలంలో అన్వేషణ ",
	"details"=> "Should write answers using search in your smartphone", 
	"enabled"=> array( 0, 1, 1), 
	"max"=> array( 0, [1,3], [1,3] ),
	"old"=>116,
	),  
"116"=> array(
	 "sno"=> "116",
	"name"=>"Map Pointing", 
	"english"=> "Map", 
	"telugu"=>"మ్యాప్ ",
	"details"=>"Topics: Jr:India, Sr:World, Map will be supplied", 
	"enabled"=> array( 0, 1, 1), 
	"max"=> array( 0, [1,3], [1,3] ),
	"old"=>117,
	), 

"117"=>array(
	"sno"=> "117",
	"name"=> "Folk Dance - Group",
	"english"=> "Folk dance - Group", 
	"telugu"=>"జానపద నృత్యం - గ్రూపు",
 	"details"=>"Minimum 3 in group, CD/Pendrive/Orchestra Allowed", 
	"enabled"=> array( 1, 1, 1 ), 
	"max"=> array( [3,15], [3,15], [3,15] ),
	"group"=> true,
	"old"=>119,
	 ), 
"118"=> array(
	 "sno"=> "118",
	"name"=> "Songs Solo",
	"english"=> "Songs - Singles",
	"telugu"=>"పాటలు వ్యక్తిగత ",
	"details"=> "Light and Folk Music", 
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [1,2], [1,2], [1,2] ),
	"old"=>120,
	  ), 
"119"=> array(
	 "sno"=> "119",
	"name"=> "Fancy Dress",
	 "english"=> "Fancy Dress", 
	 "telugu"=>"విచిత్ర వేషధారణ (ఫ్యాన్సీ డ్రెస్)",
	 "details"=> "Background music and Dialogues Allowed. Settings allowed", 
	 "enabled"=> array(1, 0, 0), 
	  "max"=> array( [1,2], 0, 0 ),
	  "old"=>121,
	  ), 
"120"=> array(
	 "sno"=> "120",
	"name"=> "Story Telling", 
	"english"=> "Story Telling", 
	"telugu"=>"కథ చెప్పడం ",
	"details"=>"The way of narration would be given importance", 
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [1,2], [1,2], [1,2] ),
	"old"=>122,
	),
"121"=> array(
	 "sno"=> "121",
	"name"=>"Project Work - Solo", 
	"english"=> "Project Work", 
	"telugu"=>"ప్రాజెక్టు పని - వ్యక్తిగత",
	"details"=>"Should display and explain", 
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [1,2], [1,2], [1,2] ),
	"old"=>123,
	), 
"122"=> array(
	"sno"=> "122",
	"name"=> "Burrakatha/Magic/Mime/Mimicry", 
	"english"=> "Burrakatha/Magic/Mime/Mimicry", 
	"telugu"=> "బుర్రకథ, మ్యాజిక్, మిమిక్రీ,  మైము",
	"details"=>"Choose a topic suitable for age group", 
	"enabled"=> array( 1, 1, 1), 
	"max"=>array( [1,1], [1,1], [1,1] ),
	"old"=>124,
	), 

"123"=> array(
	 "sno"=> "123",
	"name"=> "Drawing", 
	"english"=> "Painting", 
	"telugu" => "చిత్ర లేఖనం ",
	"details"=>"Drawing sheet will be given. Topic will be announced on the spot",
	 "enabled"=> array( 5, 5, 5), 
	 "max"=> array( [1,5], [1,5], [1,5] ),
	 "old"=>127,
	 ), 
"124"=> array(
	 "sno"=> "124",
	"name"=>"Craft",
	 "english"=> "Creative Making", 
	 "telugu"=> "సృజనాత్మక వస్తువుల తయారీ ",
	 "details"=>"Should bring and prepare with your Low-cost/No-cost material", 
	 "enabled"=> array( 1, 1, 1), 
	 "max"=> array( [1,3], [1,3], [1,3] ),
	 "old"=>128,
	 ), 
"125"=> array(
	 "sno"=> "125",
	 "name"=>"Clay Modeling",
	 "english"=> "Clay Toys", 
	 "telugu"=>"మట్టితో బొమ్మలు చేయుట ",
	 "details"=>"Clay will be provided. Topic will be announced on the spot",
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [1,3], [1,3], [1,3] ),
	"old"=>129,
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

?>
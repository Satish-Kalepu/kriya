<?php

$config_categories  = array(


"1"=> array(
	"sno"=> "1", 
	"name"=> "లఘు నాటికలు", 
	"english"=> "Short Plays", 
	"details"=>"కనీసం 15 ని. వుండాలి. పిల్లల వయసుకు తగిన ఇతివృత్తమై వుండాలి.", 
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [1,15], [1,15], [1,15] ),
	"group"=>true,
	),
"2"=>array(
	"sno"=> "2", 
	"name"=> "శాస్త్రీయ నృత్యం- గ్రూపు",
	"english"=> "Classical Dance - Group", 
 	"details"=>"కనీసం ముగ్గురు పాల్గొనాలి. ఆర్కెస్ఱ్రా/ సిడి/పెన్ డ్రైవ్ తెచ్చుకోవాలి.", 
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [3,15], [3,15], [3,15] ),
	"group"=>true,
	 ), 
"3"=> array( 
	"sno"=> "3", 
	"name"=> "పాటలు - గ్రూపు",
	"english"=> "Songs - Groups",
	"details"=>"కనీసం నలుగురు పాల్గొనాలి .లలిత/ జానపద   గేయాలు.", 
	"enabled"=> array( 0, 1, 1), 
	"max"=> array( 0, [4,15], [4,15] ),
	"group"=>1,
	 ), 
"4"=> array( 
	"sno"=> "4", 
	"name"=> "వాద్య సంగీతం", 
	"english"=> "Music Instruments", 
	"details"=>"వాద్య పరికరం తెచ్చుకోవాలి.",
	"enabled"=> array( 0, 3, 3), 
	"max"=> array( 0, [1,3], [1,3] )
	),
"5"=> array(
	 "sno"=> "5", 
	"name"=> "ఏకపాత్రాభినయం", 
	"english"=> "Solo Act", 
	"details"=>"నేపథ్య  సంగీతం,సెట్టింగులు ఉండవచ్చు.", 
	"enabled"=> array( 0, 2, 2), 
	"max"=> array( 0, [1,2], [1,2] )
	), 
"6"=> array( 
	"sno"=> "6", 
	"name"=> "పోస్టర్ ప్రజంటేషన్", 
	"english"=> "Poster Presentation", 
	"details"=>"జూః అంతరిక్షం , సీః జీవవైవిధ్యం . ముందుగా తయారుచేసి తెచ్చుకోవాలి.", 
	 "enabled"=> array( 0, 2, 2), 
	 "max"=> array( 0, [1,2], [1,2] )
	 ), 
"7"=> array( 
	"sno"=> "7", 
	"name"=> "సైన్సు ప్రయోగాలు - వ్యక్తిగత ", 
	"english"=> "Science Experiments ", 
	"details"=>"సామాగ్రి తెచ్చుకోవాలి. ప్రయోగం చేసి చూపి, వివరించాలి.", 
	"enabled"=> array( 0, 2, 2), 
	"max"=> array( 0, [1,2], [1,2] )
	), 
"8"=> array( 
	"sno"=> "8", 
	"name"=> "తెలుగులో మాట్లాడటం", 
	"english"=> "Telugu Speaking", 
	"details"=>"అప్పటికప్పుడు ఇచ్చిన అంశంపై పూర్తిగా తెలుగులో మాట్లాడాలి.",
	 "enabled"=> array( 0, 2, 2), 
	 "max"=> array( 0, [1,2], [1,2] )
	 ),
"9"=> array( 
	"sno"=> "9", 
	"name"=> "నాటకీకరణ (కనీసం ముగ్గురు)", 
	"english"=> "Instant Acting", 
	"details"=>"అంశం అప్పటికప్పుడు ఇవ్వబడుతుంది. మేకప్ ఉండరాదు.", 
	"enabled"=> array( 0, 0, 1), 
	"max"=> array( 0, 0, [3,15] )
	), 
"10"=> array( 
	"sno"=> "10", 
	"name"=> "స్పెల్లింగ్ ", 
	"english"=> "Spelling", 
	"details"=>"పదాలు జాబితా ,ఇతర వివరాలు వెబ్ సైటులో ఉంచడమైనది.", 
	"enabled"=> array( 0, 1, 1), 
	"max"=> array( 0, [1,3], [1,3] )
	), 
"11"=> array( 
	"sno"=> "11", 
	"name"=> "క్విజ్ - గ్రూప్ (ఇద్దరు)", 
	"english"=> "Quiz", 
	"details"=>"మొదటి రౌండు వ్రాత పరీక్ష. తదుపరి రౌండ్లు మౌఖికం   .", 
	"enabled"=> array( 0, 2, 2), 
	"max"=> array( 0, [2,2], [2,2] ),
	"group"=>true
	), 
"12"=> array( 
	"sno"=> "12", 
	"name"=> "డిబేట్ - గ్రూప్ (ముగ్గురు)", 
	"english"=> "Debate", 
	"details"=>"మొదటి రౌండు వ్రాత పరీక్ష. తదుపరి రౌండ్లు మౌఖికం   .", 
	"enabled"=> array( 0, 0, 2), 
	"max"=> array( 0, 0, [3,3] ),
	"group"=>true
	), 
"13"=> array( 
	"sno"=> "13", 
	"name"=> "లఘు చిత్ర సమీక్ష",
	"english"=> "Short Film Review",
  	"details"=>"చూపించిన లఘు చిత్రం పై సమీక్ష వ్రాయాలి.",
	"enabled"=> array( 0, 3, 3), 
	"max"=> array( 0, [1,3], [1,3] )
   ), 
"14"=> array(
	 "sno"=> "14", 
	"name"=> "కథా రచన (తెలుగు)", 
	"english"=> "Story Writing", 
	"details"=>"అంశం అప్పటికప్పుడు ప్రకటించబడుతుంది.", 
	"enabled"=>array( 0, 5, 5), 
	"max"=> array( 0, [1,5], [1,5] )
	), 
"15"=> array(
	 "sno"=> "15", 
	"name"=> "కథా విశ్లేషణ (తెలుగు)",  
	"english"=> "Story Analysis", 
	"details"=>"ఇచ్చిన కథ చదివి విశ్లేషణ వ్రాయాలి.",
	 "enabled"=> array( 0, 0, 5), 
	 "max"=> array( 0, 0, [1,5] )
	 ),
"16"=> array(
	 "sno"=> "16", 
	"name"=> "దినపత్రికా పఠనం (వ్రాత పరీక్ష)", 
	"english"=> "writing test", 
	"details"=>"ఫిబ్రవరి 9 నుండి 15వ తేదీ  దినపత్రికల  నుండి ప్రశ్నలుంటాయి",
	 "enabled"=> array( 0, 0, 5), 
	 "max"=> array( 0, 0, [1,5] )
	 ),
"17"=> array(
	 "sno"=> "17", 
	"name"=> "అంతర్జాలంలో అన్వేషణ", 
	"english"=> "Internet Search", 
	"details"=>"ప్రశ్నలకు జవాబులు మీ స్మార్ట్ ఫోనులో వెతికి రాయాలి.", 
	"enabled"=> array( 0, 3, 3), 
	"max"=> array( 0, [1,3], [1,3] )
	),  
"18"=> array(
	 "sno"=> "18", 
	"name"=> "మ్యాప్", 
	"english"=> "Map", 
	"details"=>"జూః  భారతదేశం సీః ఆంధ్రప్రదేశ్ మ్యాప్ ఇవ్వబడుతుంది.", 
	"enabled"=> array( 0, 3, 3), 
	"max"=> array( 0, [1,3], [1,3] )
	), 

"19"=>array(
	"sno"=> "19", 
	"name"=> "జానపద నృత్యం - గ్రూపు ",
	"english"=> "Folk dance - Group", 
 	"details"=>"కనీసం ముగ్గురు పాల్గొనాలి. ఆర్కెస్ఱ్రా/ సిడి/పెన్ డ్రైవ్ తెచ్చుకోవాలి.", 
	"enabled"=> array( 1, 1, 1 ), 
	"max"=> array( [3,15], [3,15], [3,15] ),
	"group"=> true
	 ), 
"20"=> array(
	 "sno"=> "20", 
	"name"=> "పాటలు - వ్యక్తిగతం",
	"english"=> "Songs - Singles",
	"details"=>"లలిత /  జానపద గెేయాలు.", 
	"enabled"=> array( 2, 2, 2), 
	"max"=> array( [1,2], [1,2], [1,2] )
	  ), 
"21"=> array(
	 "sno"=> "21", 
	"name"=> "విచిత్ర వేషధారణ(ఫ్యాస్యీ డ్రస్)",
	 "english"=> "Fancy Dress", 
	 "details"=>"నేపధ్యం సంగీతం, మాటలు ఉండవచ్చు. సెట్టింగులు ఉండవచ్చు.", 
	 "enabled"=> array(2, 0, 0), 
	  "max"=> array( [1,2], 0, 0 )
	  ), 
"22"=> array(
	 "sno"=> "22", 
	"name"=> "కథ చెప్పడం", 
	"english"=> "Story Telling", 
	"details"=>"కథ చెప్పే విధానానికి ప్రాధాన్యత ఇవ్వబడుతుంది.", 
	"enabled"=> array( 2, 2, 2), 
	"max"=> array( [1,2], [1,2], [1,2] )
	),
"23"=> array(
	 "sno"=> "23", 
	"name"=> "ప్రాజెక్టు పని - వ్యక్తిగతం ", 
	"english"=> "Project Work", 
	"details"=>"ముందుగా తయారు చేసుకుని తెచ్చి ప్రదర్శించి, వివరించాలి.", 
	"enabled"=> array( 2, 2, 2), 
	"max"=> array( [1,2], [1,2], [1,2] )
	), 
"24"=> array(
	 "sno"=> "24", 
	"name"=> "క్షేత్ర పర్యటన - గ్రూపు (ముగ్గురు)", 
	"english"=> "Field tour - Group ", 
	"details"=>"ఇటీీవల  చేసిన  క్షేత్ర పర్యటన  అనుభవాలు చెప్పాలి", 
	"enabled"=> array( 0, 1, 1), 
	"max"=>array( 0, [3,3], [3,3])
	), 
"25"=> array(
	 "sno"=> "25", 
	"name"=> "చిత్రలేఖనం", 
	"english"=> "Painting", 
	"details"=>"షీట్ ఇవ్వబడుతుంది. అంశం అప్పటికప్పుడు ప్రకటించబడుతుంది.",
	 "enabled"=> array( 5, 5, 5), 
	 "max"=> array( [1,5], [1,5], [1,5] )
	 ), 
"26"=> array(
	 "sno"=> "26", 
	"name"=> "సృజనాత్మక వస్తువుల తయారి(క్రాప్ట్)",
	 "english"=> "Creative Making", 
	 "details"=>"తక్కువ ఖర్చు/ఖర్చు లేని సామాగ్రి తెచ్చుకుని, 
	 అక్కడే తయారు చేయాలి.", 
	 "enabled"=> array( 2, 2, 2), 
	 "max"=> array( [1,3], [1,3], [1,3] )
	 ), 
"27"=> array(
	 "sno"=> "27", 
	 "name"=> "మట్టితో బొమ్మలు చేయుట",
	 "english"=> "Clay Toys", 
	 "details"=>"మట్టి ఇవ్వబడుతుంది. అంశం అప్పటికప్పుడు ప్రకటించబడుతుంది.",
	"enabled"=> array( 3, 3, 3), 
	"max"=> array( [1,3], [1,3], [1,3] )
	  )
);
$school_details = array(
	"school_name" => "పాఠశాల పేరు",
	"village_name"=>"గ్రామం/పట్టణం",
	"district_name" => "మండలం, జిల్లా",
	"teacher_name"=>"ఉపాధ్యాయుని పేరు",
	"pno"=>"ఫోన్ నెంబరు",
	"pno2"=>"ఫోన్ నెంబరు 2",
	"email"=>"ఇ-మెయిల్ ",
	"school_category"=>"స్కూల్ వర్గం",
	"school_type"=>"స్కూల్ రకం",
	"medium"=>"మీడియం"
);
?>
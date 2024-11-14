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

$config_categories2  = array(
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

$config_categories  = array(
"101"=> array(
	"sno"=> "101", 
	"name"=> "Playlets", 
	"english"=> "Short Plays", 
	"details"=>"Send video on Whatsapp to 9063924369 for Scrutiny", 
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [1,15], [1,15], [1,15] ),
	"group"=>true,
	),
"102"=>array(
	"sno"=> "102", 
	"name"=> "Classical Dance - Group",
	"english"=> "Classical Dance - Group", 
 	"details"=>"Send video on Whatsapp to 8332993993 for Scrutiny", 
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [5,15], [5,15], [5,15] ),
	"group"=>true,
	 ), 
"103"=> array( 
	 "sno"=> "103", 
	"name"=> "Mono Play", 
	"english"=> "Solo Act", 
	"details"=>"Settings and background music allowed", 
	"enabled"=> array( 0, 1, 1), 
	"max"=> array( 0, [1,2], [1,2] ),
	 ), 
"104"=> array( 
	"sno"=> "104",
	"name"=>"Kolatam", 
	"english"=> "Kolatam", 
	"details"=>"Minimum 16 persons and 10 minutes", 
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [16,50], [16,50], [16,50] ),
	"group"=>1,
	),
"105"=> array(
	"sno"=> "105", 
	"name"=> "Songs - Group",
	"english"=> "Songs - Group",
	"details"=>"Minimum 4 should be in group. Light/Folk music" , 
	"enabled"=> array( 0, 1, 1), 
	"max"=> array( 0, [4,8], [4,8] ),
	"group"=>1,
	), 
"106"=> array( 
	"sno"=> "106", 
	"name"=>"Poster Presentation - Solo", 
	"english"=> "Poster Presentation - Solo", 
	"details"=>"Create and bring a poster of your choice and explain", 
	 "enabled"=> array( 0, 1, 1), 
	 "max"=> array( 0, [1,2], [1,2] ),
	 ), 
"107"=> array( 
	"sno"=> "107", 
	"name"=>"Science Experiments - Solo", 
	"english"=> "Science Experiments - Solo", 
	"details"=>"Jr: Magnetism or Force & Pressure. Sr: Sound or Light", 
	"enabled"=> array( 0, 1, 1), 
	"max"=> array( 0, [1,2], [1,2] ),
	 ), 
"108"=> array( 
	"sno"=> "108",
	"name"=> "Debate - Group (Three)", 
	"english"=> "Debate", 
	"details"=>"List of topics are in website", 
	"enabled"=> array( 0, 0, 2), 
	"max"=> array( 0, 0, [3,3] ),
	"group"=>true,
	), 
"109"=> array(
	"sno"=> "109",
	"name"=>"Map", 
	"english"=> "Map", 
	"details"=>"Topics: Jr:India, Sr:World, Map will be supplied", 
	"enabled"=> array( 0, 1, 1), 
	"max"=> array( 0, [1,4], [1,4] ),
	), 
"110"=> array(
	"sno"=> "110",
	"name"=>"Maths & Reasoning", 
	"english"=> "Maths & Reasoning", 
	"details"=>"First round written test, second round oral", 
	"enabled"=> array( 0, 1, 1), 
	"max"=> array( 0, [1,4], [1,4] ),
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
	"name"=>"Essay Writing",
	"english"=> "Essay Writing",
  	"details"=>"List of topics will be announced on the spot, 2nd round oral",
	"enabled"=> array( 0, 0, 1 ),
	"max"=> array( 0, 0, [1,5] ),
	), 

"113"=> array(
	"sno"=> "113",
	"name"=>"Story Writing",
	"english"=> "Story Writing",
	"details"=>"Should write a story in telugu on given topic on spot",
	"enabled"=>array( 0, 1, 1),
	"max"=> array( 0, [1,5], [1,5] ),
   ), 
"114"=> array(
	"sno"=> "114", 
	"name"=>"Story Analysis",
	"english"=> "Story Analysis", 
	"details"=>"Write the analysis of the story given on the spot",
	"enabled"=> array( 0, 0, 1), 
	"max"=> array( 0,  0, [1,5] ),
	),
"115"=> array(
	"sno"=> "115",
	"name"=>"Search in Internet", 
	"english"=> "Internet Search", 
	"details"=> "Should write answers using search in your smartphone", 
	"enabled"=> array( 0, 1, 1), 
	"max"=> array( 0, [1,4], [1,4] ),
	 ),
"116"=> array(
	"sno"=> "116",
	"name"=> "Folk Dance - Group",
	"english"=> "Folk Dance - Group", 
 	"details"=>"Send video on Whatsapp to 9494585588 for Scrutiny", 
	"enabled"=> array( 1, 1, 1 ), 
	"max"=> array( [5,15], [5,15], [5,15] ),
	"group"=> true,
	),  
"117"=> array(
	"sno"=> "117",
	"name"=> "Fancy Dress",
	"english"=> "Fancy Dress", 
	"details"=> "Settings, Background Music, and Dialogues allowed", 
	"enabled"=> array(1, 0, 0), 
	"max"=> array( [1,3], 0, 0 ),
	), 

"118"=>array(
	"sno"=> "118",
	"name"=> "Burrakatha", 
	"english"=> "Burrakatha", 
	"details"=>"Minimum 10 minutes. Choose a topic suitable for age group", 
	"enabled"=> array( 1, 1, 1), 
	"group"=>1,
	"max"=>array( [1,3], [1,3], [1,3] ),
	 ), 
"119"=> array(
	"sno"=> "119",
	"name"=>"Project Work - Solo", 
	"english"=> "Project Work - Solo", 
	"details"=>"Should display and demonstrate", 
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [1,2], [1,2], [1,2] ),
	  ), 
"120"=> array(
	"sno"=> "120", 
	"name"=>"Instrument Music - Solo",
	"english"=> "Instrument Music - Solo",
	"details"=>"String & rythm categories. bring your own instrument",
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [1,3], [1,3], [1,3] ),
	  ), 
"121"=> array(
	"sno"=> "121",
	"name"=> "Songs Solo",
	"english"=> "Songs - Singles",
	"details"=> "Light/Folk Music", 
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [1,2], [1,2], [1,2] ),
	),
"122"=> array(
	"sno"=> "122",
	"name"=> "Story Telling", 
	"english"=> "Story Telling", 
	"details"=>"The way of narration would be given importance", 
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [1,2], [1,2], [1,2] ),
	), 
"123"=> array(
	"sno"=> "123",
	"name"=>"Spelling", 
	"english"=> "Spelling", 
	"details"=>"List of words and other details are in website", 
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [1,3], [1,3], [1,3] ),
	),
"124"=> array(
	"sno"=> "124",
	"name"=> "Drawing", 
	"english"=> "Painting", 
	"details"=>"Drawing sheet will be given. Topic will be announced on the spot",
	 "enabled"=> array( 5, 5, 5), 
	 "max"=> array( [1,5], [1,5], [1,5] ),
	), 
"125"=> array(
	 "sno"=> "125",
	"name"=>"Craft",
	 "english"=> "Craft", 
	 "details"=>"Should bring and prepare with your Low-cost/No-cost material", 
	 "enabled"=> array( 1, 1, 1), 
	 "max"=> array( [1,4], [1,4], [1,4] ),
	 ), 
"126"=> array(
	 "sno"=> "126", 
	"name"=>"Mime", 
	"english"=> "Mime", 
	"details"=>"min 10mins. choose topic suitable for age group", 
	 "enabled"=> array( 0, 1, 1), 
	 "max"=> array( 0, [1,2], [1,2] ),
	 ), 
"127"=> array(
	 "sno"=> "127",
	 "name"=>"Clay Modelling",
	 "english"=> "Clay Modelling", 
	 "details"=>"Clay will be provided. Topic will be announced on the spot",
	"enabled"=> array( 1, 1, 1), 
	"max"=> array( [1,4], [1,4], [1,4] ),
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




	$config_school_types = array(
		'sub_jrs'=>[
			"Pr. Up Pr. and Secondary Only"=>1,
			"Pr. with Up.Pr. sec. and H.Sec."=>1,
			"primary"=>1,
			"Primary"=>1,
			"Primary with Upper Primary"=>1,
			"-"=>1,
		],
		'jrs'=>[
			"Pr. Up Pr. and Secondary Only"=>1,
			"Pr. with Up.Pr. sec. and H.Sec."=>1,
			"Primary with Upper Primary"=>1,
			"Secondary"=>1,
			"secondary"=>1,
			"Secondary Only"=>1,
			"Secondary School"=>1,
			"Secondary with Higher Secondary"=>1,
			"Senior Secondary"=>1,
			"Up. Pr. Secondary and Higher Sec"=>1,
			"Upper Pr. and Secondary"=>1,
			"Upper Primary only"=>1,
			"Upper Primary"=>1,
			"-"=>1,		
		],
		'srs'=>[
			"Pr. Up Pr. and Secondary Only"=>1,
			"Pr. with Up.Pr. sec. and H.Sec."=>1,
			"Primary with Upper Primary"=>1,
			"Secondary"=>1,
			"secondary"=>1,
			"Secondary Only"=>1,
			"Secondary School"=>1,
			"Secondary with Higher Secondary"=>1,
			"Senior Secondary"=>1,
			"Up. Pr. Secondary and Higher Sec"=>1,
			"Upper Pr. and Secondary"=>1,
			"Upper Primary only"=>1,
			"-"=>1,
		],		
	);

$state_data = array(
	array(
        'name' => 'Andhra Pradesh',
	    'districts' => array(
	        'Srikakulam', 'Parvathipuram Manyam', 'Visakhapatnam', 'Vizianagaram', 
	        'Anakapalli', 'Kakinada', 'Konaseema', 'Alluri Sitaramaraju', 'East Godavari', 
	        'Eluru', 'NTR District', 'Guntur', 'West Godavari', 'Bapatla', 'Palnadu', 
	        'Sri Potti Sriramulu Nellore', 'Prakasam', 'Tirupati', 'Annamayya', 'YSR Kadapa', 
	        'Chittoor', 'Anantpur', 'Kurnool', 'Sri Satyasai', 'Nandyal', 'Sri. Balaji Dist'
	    )
    ),
    array(
        'name' => 'Arunachal Pradesh',
        'districts' => array(
            'Tawang', 'West Kameng', 'East Kameng', 'Papum Pare', 'Kurung Kumey',
            'Kra Daadi', 'Lower Subansiri', 'Upper Subansiri', 'West Siang',
            'East Siang', 'Siang', 'Upper Siang', 'Lower Siang', 'Lower Dibang Valley',
            'Dibang Valley', 'Anjaw', 'Lohit', 'Namsai', 'Changlang', 'Tirap', 'Longding'
        )
    ),
    array(
        'name' => 'Assam',
        'districts' => array(
            'Baksa', 'Barpeta', 'Biswanath', 'Bongaigaon', 'Cachar', 'Charaideo',
            'Chirang', 'Darrang', 'Dhemaji', 'Dhubri', 'Dibrugarh', 'Goalpara',
            'Golaghat', 'Hailakandi', 'Hojai', 'Jorhat', 'Kamrup Metropolitan',
            'Kamrup', 'Karbi Anglong', 'Karimganj', 'Kokrajhar', 'Lakhimpur',
            'Majuli', 'Morigaon', 'Nagaon', 'Nalbari', 'Dima Hasao', 'Sivasagar',
            'Sonitpur', 'South Salmara-Mankachar', 'Tinsukia', 'Udalguri', 'West Karbi Anglong'
        )
    ),
    array(
        'name' => 'Bihar',
        'districts' => array(
            'Araria', 'Arwal', 'Aurangabad', 'Banka', 'Begusarai', 'Bhagalpur',
            'Bhojpur', 'Buxar', 'Darbhanga', 'East Champaran (Motihari)', 'Gaya',
            'Gopalganj', 'Jamui', 'Jehanabad', 'Kaimur (Bhabua)', 'Katihar',
            'Khagaria', 'Kishanganj', 'Lakhisarai', 'Madhepura', 'Madhubani',
            'Munger (Monghyr)', 'Muzaffarpur', 'Nalanda', 'Nawada', 'Patna',
            'Purnia (Purnea)', 'Rohtas', 'Saharsa', 'Samastipur', 'Saran', 'Sheikhpura',
            'Sheohar', 'Sitamarhi', 'Siwan', 'Supaul', 'Vaishali', 'West Champaran'
        )
    ),
    array(
        'name' => 'Chandigarh (UT)',
        'districts' => array('Chandigarh')
    ),
    array(
        'name' => 'Chhattisgarh',
        'districts' => array(
            'Balod', 'Baloda Bazar', 'Balrampur', 'Bastar', 'Bemetara',
            'Bijapur', 'Bilaspur', 'Dantewada (South Bastar)', 'Dhamtari', 'Durg',
            'Gariyaband', 'Janjgir-Champa', 'Jashpur', 'Kabirdham (Kawardha)',
            'Kanker (North Bastar)', 'Kondagaon', 'Korba', 'Korea (Koriya)',
            'Mahasamund', 'Mungeli', 'Narayanpur', 'Raigarh', 'Raipur',
            'Rajnandgaon', 'Sukma', 'Surajpur', 'Surguja'
        )
    ),
    array(
        'name' => 'Dadra and Nagar Haveli (UT)',
        'districts' => array('Dadra & Nagar Haveli')
    ),
    array(
        'name' => 'Daman and Diu (UT)',
        'districts' => array('Daman', 'Diu')
    ),
    array(
        'name' => 'Delhi (NCT)',
        'districts' => array(
            'Central Delhi', 'East Delhi', 'New Delhi', 'North Delhi', 
            'North East Delhi', 'North West Delhi', 'Shahdara', 
            'South Delhi', 'South East Delhi', 'South West Delhi', 'West Delhi'
        )
    ),
    array(
        'name' => 'Goa',
        'districts' => array('North Goa', 'South Goa')
    ),
    array(
        'name' => 'Gujarat',
        'districts' => array(
            'Ahmedabad', 'Amreli', 'Anand', 'Aravalli', 'Banaskantha (Palanpur)',
            'Bharuch', 'Bhavnagar', 'Botad', 'Chhota Udepur', 'Dahod', 
            'Dangs (Ahwa)', 'Devbhoomi Dwarka', 'Gandhinagar', 'Gir Somnath',
            'Jamnagar', 'Junagadh', 'Kachchh', 'Kheda (Nadiad)', 'Mahisagar',
            'Mehsana', 'Morbi', 'Narmada (Rajpipla)', 'Navsari', 
            'Panchmahal (Godhra)', 'Patan', 'Porbandar', 'Rajkot', 
            'Sabarkantha (Himmatnagar)', 'Surat', 'Surendranagar', 
            'Tapi (Vyara)', 'Vadodara', 'Valsad'
        )
    ),
    array(
        'name' => 'Haryana',
        'districts' => array(
            'Ambala', 'Bhiwani', 'Charkhi Dadri', 'Faridabad', 'Fatehabad',
            'Gurgaon', 'Hisar', 'Jhajjar', 'Jind', 'Kaithal', 'Karnal',
            'Kurukshetra', 'Mahendragarh', 'Mewat', 'Palwal', 'Panchkula',
            'Panipat', 'Rewari', 'Rohtak', 'Sirsa', 'Sonipat', 'Yamunanagar'
        )
    ),
    array(
        'name' => 'Himachal Pradesh',
        'districts' => array(
            'Bilaspur', 'Chamba', 'Hamirpur', 'Kangra', 'Kinnaur', 'Kullu',
            'Lahaul & Spiti', 'Mandi', 'Shimla', 'Sirmaur (Sirmour)', 
            'Solan', 'Una'
        )
    ),
    array(
        'name' => 'Jammu and Kashmir',
        'districts' => array(
            'Anantnag', 'Bandipore', 'Baramulla', 'Budgam', 'Doda',
            'Ganderbal', 'Jammu', 'Kargil', 'Kathua', 'Kishtwar', 
            'Kulgam', 'Kupwara', 'Leh', 'Poonch', 'Pulwama', 'Rajouri',
            'Ramban', 'Reasi', 'Samba', 'Shopian', 'Srinagar', 'Udhampur'
        )
    ),
    array(
        'name' => 'Jharkhand',
        'districts' => array(
            'Bokaro', 'Chatra', 'Deoghar', 'Dhanbad', 'Dumka', 
            'East Singhbhum', 'Garhwa', 'Giridih', 'Godda', 'Gumla', 
            'Hazaribag', 'Jamtara', 'Khunti', 'Koderma', 'Latehar', 
            'Lohardaga', 'Pakur', 'Palamu', 'Ramgarh', 'Ranchi', 
            'Sahibganj', 'Seraikela-Kharsawan', 'Simdega', 'West Singhbhum'
        )
    ),
    array(
        'name' => 'Karnataka',
        'districts' => array(
            'Bagalkot', 'Ballari (Bellary)', 'Belagavi (Belgaum)', 
            'Bengaluru (Bangalore) Rural', 'Bengaluru (Bangalore) Urban',
            'Bidar', 'Chamarajanagar', 'Chikballapur', 
            'Chikkamagaluru (Chikmagalur)', 'Chitradurga', 'Dakshina Kannada', 
            'Davangere', 'Dharwad', 'Gadag', 'Hassan', 'Haveri', 
            'Kalaburagi (Gulbarga)', 'Kodagu', 'Kolar', 'Koppal', 
            'Mandya', 'Mysuru (Mysore)', 'Raichur', 'Ramanagara', 
            'Shivamogga (Shimoga)', 'Tumakuru (Tumkur)', 'Udupi', 
            'Uttara Kannada (Karwar)', 'Vijayapura (Bijapur)', 'Yadgir'
        )
    ),
    array(
        'name' => 'Kerala',
        'districts' => array(
            'Alappuzha', 'Ernakulam', 'Idukki', 'Kannur', 'Kasaragod', 
            'Kollam', 'Kottayam', 'Kozhikode', 'Malappuram', 'Palakkad', 
            'Pathanamthitta', 'Thiruvananthapuram', 'Thrissur', 'Wayanad'
        )
    ),
    array(
        'name' => 'Lakshadweep (UT)',
        'districts' => array('Lakshadweep')
    ),
    array(
        'name' => 'Madhya Pradesh',
        'districts' => array(
            'Agar Malwa', 'Alirajpur', 'Anuppur', 'Ashoknagar', 'Balaghat',
            'Barwani', 'Betul', 'Bhopal', 'Burhanpur', 'Chhindwara', 
            'Damoh', 'Datia', 'Dewas', 'Dhar', 'Dindori', 'Guna', 
            'Gwalior', 'Harda', 'Hoshangabad', 'Indore', 'Jabalpur', 
            'Jhabua', 'Katni', 'Khandwa', 'Khargone', 'Mandla', 
            'Mandsaur', 'Morena', 'Narmada', 'Neemuch', 'Panna', 
            'Raisen', 'Rajaudhan', 'Ratlam', 'Rewa', 'Sagar', 
            'Satna', 'Sehore', 'Seoni', 'Shahdol', 'Shajapur', 
            'Sheopur', 'Sidhi', 'Singrauli', 'Tikamgarh', 'Ujjain', 
            'Umaria', 'Vidisha'
        )
    ),
    array(
        'name' => 'Maharashtra',
        'districts' => array(
            'Ahmednagar', 'Akola', 'Amravati', 'Aurangabad', 'Bhandara',
            'Buldhana', 'Chandrapur', 'Dhule', 'Gadchiroli', 'Jalna', 
            'Jalgaon', 'Kolhapur', 'Latur', 'Mumbai City',
            'Mumbai Suburban', 'Nagpur', 'Nanded', 'Nasik', 'Osmanabad',
            'Parbhani', 'Pune', 'Raigad', 'Ratnagiri', 'Sindhudurg', 
            'Solapur', 'Thane', 'Wardha', 'Washim', 'Yavatmal'
        )
    ),
    array(
        'name' => 'Manipur',
        'districts' => array(
            'Bishnupur', 'Chandel', 'Churachandpur', 'Imphal East', 
            'Imphal West', 'Senapati', 'Tamenglong', 'Thoubal', 
            'Ukhrul'
        )
    ),
    array(
        'name' => 'Meghalaya',
        'districts' => array('East Garo Hills', 'East Khasi Hills', 'North Garo Hills', 'South Garo Hills', 'West Garo Hills', 'West Khasi Hills')
    ),
    array(
        'name' => 'Mizoram',
        'districts' => array('Aizawl', 'Champhai', 'Lunglei', 'Mamit', 'Saiha', 'Serchhip')
    ),
    array(
        'name' => 'Nagaland',
        'districts' => array('Dimapur', 'Kiphire', 'Mokokchung', 'Mon', 'Peren', 'Phek', 'Tuensang', 'Wokha', 'Zunheboto')
    ),
    array(
        'name' => 'Odisha',
        'districts' => array(
            'Angul', 'Balangir', 'Balasore', 'Bargarh', 'Bhadrak',
            'Bolangir', 'Dhenkanal', 'Ganjam', 'Gajapati', 
            'Jagatsinghpur', 'Jajpur', 'Jharsuguda', 'Kalahandi', 
            'Kandhamal', 'Keonjhar', 'Khurda', 'Koraput', 'Nabarangpur', 
            'Nayagarh', 'Nuapada', 'Rayagada', 'Sambalpur', 
            'Subarnapur', 'Sundergarh'
        )
    ),
    array(
        'name' => 'Puducherry (UT)',
        'districts' => array('Karaikal', 'Mahe', 'Puducherry', 'Yanam')
    ),
    array(
        'name' => 'Punjab',
        'districts' => array(
            'Amritsar', 'Barnala', 'Bathinda', 'Faridkot', 'Fatehgarh Sahib',
            'Fazilka', 'Hoshiarpur', 'Jalandhar', 'Kapurthala', 
            'Ludhiana', 'Mansa', 'Moga', 'Patiala', 'Rupnagar', 
            'SAS Nagar (Mohali)', 'Sangrur', 'Tarn Taran'
        )
    ),
    array(
        'name' => 'Rajasthan',
        'districts' => array(
            'Ajmer', 'Alwar', 'Bikaner', 'Bundi', 'Chittorgarh',
            'Churu', 'Dausa', 'Dholpur', 'Dungarpur', 'Jaipur', 
            'Jaisalmer', 'Jalor', 'Jhunjhunu', 'Nagaur', 
            'Pali', 'Rajsamand', 'Sikar', 'Sirohi', 
            'Tonk', 'Udaipur'
        )
    ),
    array(
        'name' => 'Sikkim',
        'districts' => array('East Sikkim', 'North Sikkim', 'South Sikkim', 'West Sikkim')
    ),
    array(
        'name' => 'Tamil Nadu',
        'districts' => array(
            'Ariyalur', 'Chennai', 'Coimbatore', 'Cuddalore', 
            'Dharmapuri', 'Dindigul', 'Erode', 'Kanchipuram', 
            'Kanyakumari', 'Karur', 'Madurai', 'Nagapattinam', 
            'Namakkal', 'Nilgiris', 'Perambalur', 'Pudukkottai', 
            'Ramanathapuram', 'Salem', 'Sivagangai', 'Thanjavur', 
            'Theni', 'Tiruchirappalli', 'Tirunelveli', 
            'Tiruppur', 'Vellore', 'Virudhunagar'
        )
    ),
    array(
        'name' => 'Telangana',
        'districts' => array(
            'Adilabad', 'Hyderabad', 'Jagtial', 'Jangaon', 
            'Karimnagar', 'Khammam', 'Mahabubnagar', 'Mancherial', 
            'Medak', 'Nalgonda', 'Nizamabad', 'Peddapalli', 
            'Rajanna Sircilla', 'Rangareddy', 'Sangareddy', 
            'Siddipet', 'Vikarabad', 'Wanaparthy', 'Warangal', 
            'Yadadri Bhuvanagiri', 'Mahabubnagar'
        )
    ),
    array(
        'name' => 'Tripura',
        'districts' => array(
            'Dhalai', 'Gomati', 'Khowai', 'North Tripura', 
            'Sepahijala', 'South Tripura', 'Unakoti', 'West Tripura'
        )
    ),
    array(
        'name' => 'Uttar Pradesh',
        'districts' => array(
            'Agra', 'Aligarh', 'Allahabad', 'Ambedkar Nagar', 
            'Amethi', 'Amroha', 'Auraiya', 'Ayodhya', 'Azamgarh', 
            'Badaun', 'Baghpat', 'Bahraich', 'Ballia', 'Balrampur', 
            'Banda', 'Barabanki', 'Bareilly', 'Basti', 'Bijnor', 
            'Bulandshahr', 'Chandauli', 'Chhatrapati Shahuji Maharaj Nagar', 
            'Chitrakoot', 'Deoria', 'Etah', 'Etawah', 'Faizabad', 
            'Farrukhabad', 'Fatehpur', 'Firozabad', 'Gautam Buddha Nagar', 
            'Ghaziabad', 'Ghazipur', 'Gonda', 'Hamirpur', 'Hardoi', 
            'Hathras', 'Jalaun', 'Jaunpur', 'Jhansi', 'Kannauj', 
            'Kanpur Dehat', 'Kanpur Nagar', 'Kanshiram Nagar', 'Kheri', 
            'Lucknow', 'Maharajganj', 'Mahoba', 'Mainpuri', 
            'Mathura', 'Mau', 'Mirzapur', 'Mohammedabad', 'Moradabad', 
            'Muzaffarnagar', 'Pilibhit', 'Pratapgarh', 'Rae Bareli', 
            'Rampur', 'Saharanpur', 'Sambhal', 'Sant Ravidas Nagar', 
            'Shahjahanpur', 'Shrawasti', 'Siddharth Nagar', 'Sitapur', 
            'Sonbhadra', 'Sultanpur', 'Unnao', 'Varanasi', 
            'Shamli', 'Bijnor', 'Hapur'
        )
    ),
    array(
        'name' => 'Uttarakhand',
        'districts' => array(
            'Almora', 'Bageshwar', 'Champawat', 'Dehradun', 
            'Haridwar', 'Nainital', 'Pauri Garhwal', 
            'Pithoragarh', 'Rudraprayag', 'Tehri Garhwal', 
            'Udham Singh Nagar', 'Uttarkashi'
        )
    ),
    array(
        'name' => 'West Bengal',
        'districts' => array(
            'Alipurduar', 'Bankura', 'Birbhum', 'Cooch Behar', 
            'Dakshin Dinajpur', 'Darjeeling', 'Hooghly', 'Howrah', 
            'Jalpaiguri', 'Jhargram', 'Malda', 'Medinipur', 
            'Murshidabad', 'Nadia', 'North 24 Parganas', 
            'Purulia', 'South 24 Parganas', 'Uttar Dinajpur'
        )
    )
);


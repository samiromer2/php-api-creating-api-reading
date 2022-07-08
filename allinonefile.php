<?php 
//conneting to needed database
include('config.php');

//exmaple open data api
$api_url = 'https://api.open.undp.org/api/project_list/?year=2021&operating_unit=SDN';

$json_data = file_get_contents($api_url);
$response_data = json_decode($json_data);
$array = json_decode(json_encode($response_data), True);
//print_r($response_data);
//echo $response_data->project_id;
//echo $response_data->title;
//print_r($array);
$challenge = array();
$solution = array();
$comment = array();

$changed = array();
foreach ($array['data'] as $key => $value) {
	//print_r($key);
	// print_r($value);
	if (is_array($value) || is_object($value)) {
		foreach ($value as $key2 => $value2) {
			if (!empty($value2)) {
				//print_r($value2);
				$projectid = $value2['project_id'];

				$projecttitle = $value2['title'];

				$Sql = "SELECT `idfortable`, `projectid`, `date`, `status`, `challenge`, `solution`, `comment` FROM `challenge` WHERE  `projectid` = '$projectid'";
				$Check   = mysqli_query($link, $Sql);
				while ($row = mysqli_fetch_array($Check)) {
					$challenge[$projectid][] = $row['challenge'];
					$solution[$projectid][] = $row['solution'];
					$comment[$projectid][] = $row['comment'];
				}

				//here!


				//$changed[] = $projectid;
				// $changed[] = $projecttitle;

				if (empty($challenge[$projectid])) {
					$challenge[$projectid] = "";
				}
				if (empty($solution[$projectid])) {
					$solution[$projectid] = "";
				}
				if (empty($comment[$projectid])) {
					$comment[$projectid] = "";
				}

				$changed[] = ['projectid' => $projectid, 'projecttitle' => $projecttitle, 'challenge' => $challenge[$projectid], 'solution' => $solution[$projectid], 'comment' => $comment[$projectid]];
			}
		}
	}
}


//print_r($changed);

//echo print_r($changed['projectid']);
$changedata = ['data' => $changed];
echo json_encode($changedata);

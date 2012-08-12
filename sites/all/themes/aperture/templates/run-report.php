<table class="gen-report" align="center" border="0">
	<tr id="head-report">
		<td class="report-nums-head">#</td>
		<td class="report-name-col-left">Name</td>
		<td>Username</td>
		<td  class="report-name-col-right">Total Time</td>
	</tr>
	<?php
	// get name and user id from the users table
		$calUsers = db_query('SELECT u.name, u.uid, first.field_first_name_value, first.entity_id, last.field_last_name_value, last.entity_id FROM {users} u INNER JOIN {field_data_field_first_name} first INNER JOIN {field_data_field_last_name} last  ON u.uid = last.entity_id AND u.uid = first.entity_id');
	$numOfUsers = 0;
	// for each user get node_id, node_type and user_id
	foreach ($calUsers as $user) {
		++$numOfUsers;
		//display table for every user
		echo "<tr><td class=\"report-nums\">$numOfUsers</td><td class=\"report-name-col-left\">$user->field_first_name_value $user->field_last_name_value</td><td>$user->name</td>";

		$totalTime = 0;
		// store data from the sql request
		$calEvents = db_query('SELECT nid, type, uid FROM {node} n WHERE n.uid = :uid AND n.type = :eventType', array(':uid' => $user -> uid, ':eventType' => "event"));

		// for every event get the time
		foreach ($calEvents as $singleEvent) {
			$volunteeringTime = db_query('SELECT entity_id, field_event_date_value, field_event_date_value2 FROM {field_data_field_event_date} fd WHERE fd.entity_id = :nid', array(':nid' => $singleEvent -> nid));

			//get start and end time for every event
			foreach ($volunteeringTime as $volTime) {
				//get start and end time
				$end_time = strtotime($volTime -> field_event_date_value2);
				$start_time = strtotime($volTime -> field_event_date_value);
				$totalTime = $totalTime + abs($end_time - $start_time);
			}
		}
		
		$hours = floor($totalTime / 3600);
		$minutes = floor(($totalTime / 60) % 60);
		
		//convert number of seconds into hours/minutes
		echo "<td>$hours hrs $minutes mins</td></tr>";
	}

?>
</table>
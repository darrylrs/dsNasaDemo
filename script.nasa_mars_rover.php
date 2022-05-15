<?php
/*
 * Nasa Mars Rover Script
 * Sends a request to the Nasa Mars Rover API, checks if the media returned is a new image and the forwards to all subscribed recipients.
 * This file should be executed once daily in a cron job.
 */

require_once('api.nasa_mars_rover.php');

$result = MarsRover::callApi();

if ($result && $first_result = $result['photos'][0]) {
	$url = $first_result['img_src'];
	$remoteId = $first_result['id'];

	if ($remoteId != MarsRover::getCurrentId()) {
		//We have a new image and must process accordingly.

		//Send Email
		$mailSubject = "New Mars Rover Image";

		$date = DateTime::createFromFormat('Y-m-d', $first_result['earth_date']);

		$mailBaseBody = "The Mars Rover has taken a new photo. This shot was taken at " . $date->format('d-m-Y') . ".<br /><br /><img src='" . $url . "' />";
		$subscribedUsers = MarsRover::getSubscribedUsers();
		if (is_array($subscribedUsers)) {
			foreach($subscribedUsers as $user) {
				$mailBody = "Hi, ". $user['first_name'] . ". <br /><br />" . $mailBaseBody;
				//The mail function is commented out for testing.
				//mail($user['email'], $mailSubject, $mailBody);

				//For the purposes of this demonstration, we're also going to echo this content
				echo $mailBody;
			}
		}

		//Store id for future comparisons.
		MarsRover::storeId($remoteId);
	}
}
else {
	echo "There was an error fetching data from the NASA API";
}

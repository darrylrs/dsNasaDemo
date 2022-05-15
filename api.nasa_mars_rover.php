<?php
/*
 * Nasa Mars Rover API Class
 * Used to handle all interactions with the Nasa API.
 * 
 * We store the id of the image that we've seen so that if the script executes additional times, we don't send multiple emails.
 * Currently, this data is stored in a file. I would usually use something else, such as Redis for this, but this is quicker for the demo.
 * 
 * Additionally, the getSubscribedUsers is using dummy data. This method would be expanded out using the SQL shown in the comments.
 * These decisions were made so that the code would function on any system it's executed on without worrying about what databases or libraries are available.
 */

class MarsRover {
	const baseUrl = "https://api.nasa.gov/mars-photos/api/v1/rovers/curiosity/photos";
	const apiKey = "DEMO_KEY";
	
	/*
	 * callApi
	 * Handle sending a curl request, verifying the return value, and processing the json into a useful array.
	 */
	public static function callApi():?array {
		//Using the MAST camera filter to reduce the size of the query.
		//It is likely that in order to get this working properly, we'll need to update the timezone to match the NASA API.

		$date = new DateTime('NOW');
		// $date = DateTime::createFromFormat('Y-m-d', "2022-05-13"); // Known working date for testing
		$apiUrl = self::baseUrl . '?earth_date='.$date->format('Y-m-d').'&camera=MAST&api_key=' . self::apiKey;
		
		$curl = curl_init($apiUrl);
		curl_setopt($curl, CURLOPT_URL, $apiUrl);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$curl_response = curl_exec($curl);
		curl_close($curl);

		//Make sure the JSON we got back is valid before using it.
		$response = json_decode($curl_response, true);
		if (json_last_error() === JSON_ERROR_NONE) {
			return $response;
		}
		else {
			return null;
		}
	}

	/*
	 * getCurrentID
	 * Get the ID of the image that we have stored to compare it to the newly retrieved one, or to use in other locations.
	 */
	public static function getCurrentID():?string {
		if (file_exists('nasa_id.txt')) {
			return file_get_contents('nasa_id.txt');
		}
		else {
			return null;
		}
	}

	/*
	 * storeId
	 * Store the new ID for future comparisons as to whether or not this is a new image.
	 */
	public static function storeID(string $imageId):void {
		file_put_contents('nasa_id.txt', $imageId);
	}

	/*
	 * getSubscribedUsers
	 * Returns an array with the name and email of all users in the system with the Mars Rover flag selected.
	 */
	public static function getSubscribedUsers():array {
		// I would expect that this function would usually be created using SQL, such as the following:
		// SELECT first_name, email FROM users WHERE active=1 AND okay_to_email=1 AND mars_rover_notifications=1

		// For the purposes of this demonstration, I am simply using an array
		$subscribedUsers = [];
		$subscribedUsers[] = ['first_name'=>"Darryl", 'email'=>"Darryl.R.Smith@gmail.com"];
		return $subscribedUsers;
	}
}
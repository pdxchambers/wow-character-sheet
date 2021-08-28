<?php
    /**
     * Blizz Engine
     * 
     * This file contains all of the calls to the Blizzard World of Warcraft API, creates a data structure/dictionary object containing the resulting data,
     * and holds the functions needed to manipulate the resulting data set. 
     */

    /**
    *	@function pdxc_wow_client_authenticate()
    *  	@param string $clientID - the client ID provided by the Blizzard API
    *	@param string $clientSecret - the client secret provided by the Blizzard API
    *
    *	Requests an oAuth token from Blizzard to be used when making API Calls and parses the 
    *	resulting data.
    */
    define('CLIENT_ID', get_option('wow-clientKey'));
    define('CLIENT_SECRET', get_option('wow-clientSecret'));
    define('OAUTH_ENDPOINT', 'https://us.battle.net/oauth/token');
    define('CHARACTER_NAME', get_option('wow-character'));
    define('CHARACTER_REALM', get_option('wow-realm'));
    define('AVATAR_URL', 'https://render-us.worldofwarcraft.com/character/');


    function pdxc_wow_client_authenticate($clientID, $clientSecret){
        $url = OAUTH_ENDPOINT;
        $grant_type = 'grant_type=client_credentials';
        $client_auth_string = CLIENT_ID . ':' . CLIENT_SECRET;
        $headers = array(
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'grant_type=client_credentials'
        );
        $curl_resource = curl_init($url);
        curl_setopt($curl_resource, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl_resource, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl_resource, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl_resource, CURLOPT_POST, TRUE);
        curl_setopt($curl_resource, CURLOPT_POSTFIELDS, $grant_type);
        curl_setopt($curl_resource, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl_resource, CURLOPT_USERPWD, $client_auth_string);
        $result = json_decode(curl_exec($curl_resource), TRUE);
        //var_dump($result);
        return $result;
    }

    /**
    *	@function pdxc_wow_fetch_character_profile()
    *	@param string $realm  - the World of Warcraft realm the character exists on.
    *	@param string $name   - the name of the World of Warcraft character.
    * 	@param string $token  - the oAuth token received from the Blizzard API
    *
    * 	Queries the Community Profile API and returns character data. The exact datasaet returned depends on 
    *	the value passed in through $fields.
    */
    function pdxc_wow_fetch_character_profile( $realm, $name, $token ){
        $url = 'https://us.api.blizzard.com/profile/wow/character/' . $realm . '/' . $name . '?namespace=profile-us&locale=en_US&access_token=' . $token;
        $headers = array(
            'Accept' => 'application/json',
        );
        $rs = curl_init($url);
        curl_setopt($rs, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($rs, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($rs, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($rs, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($rs, CURLOPT_HTTPGET, TRUE);
        $character_Profile = json_decode(curl_exec($rs), true);
        //var_dump($character_Profile);
        return $character_Profile;
    }

    /**
    *	@function pdxc_wow_get_character_data()
    *	@param int $urlD       - API endpoint being queried.
    *	@param string $token   - the oAuth token received from the Blizzard API
    *
    *	Helper function that uses a URL returned in the character profile to obtain further data about a character.
    */
    function pdxc_wow_get_character_data($url, $token){
        $headers = array(
            'Accept' => 'application/json',
        );
        $rs = curl_init($url . '&access_token=' . $token);
        curl_setopt($rs, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($rs, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($rs, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($rs, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($rs, CURLOPT_HTTPGET, TRUE);
        $character_data = json_decode(curl_exec($rs), true);
        return $character_data;
    }

    function pdxc_wow_apiError($errorCode, $errorDescription){
        $html = '<div id="errorBlock">';
            $html .= '<h2>' . $errorCode . ': </h2>';
            $html .= '<h3>' . $errorDescription . '</h3>';
            $html .= '<p style="Color:black;">Either your Client ID or Client Secret is invalid.</p>';
        $html .= '</div>';

        return $html;
    }

    function pdxc_wow_apiCode($code, $description){
        $html = '<div id="errorBlock">';
            $html .= '<h2>' . $code . ': </h2>';
            $html .= '<h3>' . $description . '</h3>';
            $html .= '<p style="Color:black;">Something went wrong while retreiving the character profile.</p>';
        $html .= '</div>';

        return $html;
    }

    /*
		Fetch and parse authentication token from Blizzard
	*/
	$tokenResult = (pdxc_wow_client_authenticate(CLIENT_ID, CLIENT_SECRET));
	if(!array_key_exists('error', $tokenResult)){
		$access_token = $tokenResult['access_token'];
		$token_type = $tokenResult['token_type'];
		$token_expires = $tokenResult['expires_in'];

	/*
		Fetch Character Profile
	*/
		$profile = pdxc_wow_fetch_character_profile( CHARACTER_REALM, CHARACTER_NAME, $access_token );
		if(!array_key_exists('code', $profile)){
			$character_media = pdxc_wow_get_character_data( $profile['media']['href'], $access_token );
			$character_equip = pdxc_wow_get_character_data( $profile['equipment']['href'], $access_token );
			$pvp_summary = pdxc_wow_get_character_data($profile['pvp_summary']['href'], $access_token);
			$character_stats = pdxc_wow_get_character_data($profile['statistics']['href'], $access_token);
			$character_realm = pdxc_wow_get_character_data('https://us.api.blizzard.com/data/wow/connected-realm/' . $profile['realm']['id'] . '?namespace=dynamic-us&locale=en_US', $access_token);
		}
	}
?>
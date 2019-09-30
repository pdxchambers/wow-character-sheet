<?php
	/* Template Name: WoW Page */
	 get_header();

	 define('CLIENT_ID', get_field('client_id'));
	 define('CLIENT_SECRET', get_field('client_secret'));
	 define('OAUTH_ENDPOINT', 'https://us.battle.net/oauth/token');
	 define('CHARACTER_NAME', get_field('character_name'));
	 define('CHARACTER_REALM', get_field('realm'));
	 define('AVATAR_URL', 'https://render-us.worldofwarcraft.com/character/');

	 /**
		@function client_authenicate()
		@param string $clientID - the client ID provided by the Blizzard API
		@paream string $clientSecret - the client secret provided by the Blizzard API

		Requests an oAuth token from Blizzard to be used when making API Calls and parses the 
		resulting data.
	 **/
	 function client_authenicate($clientID, $clientSecret){
		$url = OAUTH_ENDPOINT;
		$grant_type = 'grant_type=client_credentials';
		$client_auth_string = CLIENT_ID . ':' . CLIENT_SECRET;
		$headers = array(
			'Accept' => 'application/json',
			'Accept-Language' => 'en_US',
			"grant_type=client_credentials"
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
		@function fetch_character_profile()
		@param string $realm  - the World of Warcraft realm the character exists on.
		@param string $name   - the name of the World of Warcraft character.
		@param string $token  - the oAuth token received from the Blizzard API

		Queries the Community Profile API and returns character data. The exact datasaet returned depends on 
		the value passed in through $fields.
	 **/
	 function fetch_character_profile( $realm, $name, $token ){
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
		@function get_character_data()
		@param int $urlD       - API endpoint being queried.
		@param string $token   - the oAuth token received from the Blizzard API

		Helper function that uses a URL returned in the character profile to obtain further data about a character.
	**/
	function get_character_data($url, $token){
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

	/*
		Fetch and parse authentication token from Blizzard
	*/
	$tokenResult = (client_authenicate(CLIENT_ID, CLIENT_SECRET));
	if(!array_key_exists('error', $tokenResult)){
		$access_token = $tokenResult['access_token'];
		$token_type = $tokenResult['token_type'];
		$token_expires = $tokenResult['expires_in'];

	/*
		Fetch Character Profile
	*/
		$profile = fetch_character_profile( CHARACTER_REALM, CHARACTER_NAME, $access_token );
		if(!array_key_exists('code', $profile)){
			$character_media = get_character_data( $profile['media']['href'], $access_token );
			$character_equip = get_character_data( $profile['equipment']['href'], $access_token );
			$pvp_summary = get_character_data($profile['pvp_summary']['href'], $access_token);
			$character_stats = get_character_data($profile['statistics']['href'], $access_token);
			$character_realm = get_character_data('https://us.api.blizzard.com/data/wow/connected-realm/' . $profile['realm']['id'] . '?namespace=dynamic-us&locale=en_US', $access_token);
		}
	}
?>
	<?php
		if(array_key_exists('error', $tokenResult) || array_key_exists('code', $profile)){
	?>
	<div id="errorBlock">
	<?php
		if(array_key_exists('error', $tokenResult)){
			echo '<h2>' . $tokenResult['error'] . ':</h2>';
			echo '<h3>' . $tokenResult['error_description'] . '</h3>';
			echo '<p style="Color:black;">Either your Client ID or Client Secret is invalid.</p>';
		} else if(array_key_exists('code', $profile)){
			echo '<h2>' . $profile['code'] . ':</h2>';
			echo '<h3>' . $profile['detail'] . '</h3>';
			echo '<p style="Color:black;">Something went wrong while retreiving the character profile.</p>';
		}
	?>
	</div>
	<?php
		} else {
	?>
 <div id="wowMainBlock">
	<div id="profileHeader">
		<div id="wowCharImgBlock">
			<a href="<?php  echo $character_media['render_url']; ?>"><img src="<?php echo $character_media['render_url']; ?>" alt="Avatar of <?php echo $profile['name']; ?>"></a>
		</div>
		<div id="wowCharInfoBlock">
			<div id="charName">
				<h2><?php echo preg_replace('/{name}/', $profile['name'],  $profile['active_title']['display_string']); ?></h2>
				<h3 id="charClass"><?php echo '<span class="level"><span class=label>Level</span><span class="value">' . $profile['level'] . '</span></span> ' . $profile['race']['name'] . ' ' . $profile['character_class']['name']; ?></h3>
				<h3 class="specialization">(<?php echo $profile['active_spec']['name']; ?>)</h3>
				<span class="statHeader">Health:</span><span class="statVal" style="background-color: #790000;"><?php echo $character_stats['health']; ?></span>
				<?php
					$pwrColor = '';
					$pwrType = $character_stats['power_type']['name']['en_US'];
					switch (strtolower($pwrType)){
						case 'mana':
							$pwrColor = 'blue';
							$valColor = '#BFBFBF';
							break;
						case 'rage':
							$pwrColor = '#790000';
							$valColor = '#BFBFBF';
							break;
						case 'energy':
							$pwrColor = '#CCB278';
							$valColor = '#0D0D0D';
							break;
						case 'focus':
							$pwrColor = '#73280D';
							$valcolor = '#BFBFBF';
						default:
							break;
					}
					?>
				<span class="statHeader"><?php echo $pwrType; ?>:</span><span class="statVal" style="background-color: <?php echo $pwrColor; ?>; color: <?php echo $valColor; ?>;"><?php echo $character_stats['power']; ?></span>
				<?php
					if($pvp_summary['honorable_kills'] > 0){ 
				?>
						<span class="honor"><?php echo $pvp_summary['honorable_kills']; ?> honorable kills.</span>
				<?php } ?>
			</div>
		</div>
		<div id="charFac">
			<div id="faction">
			<?php
				if ($profile['faction']['name'] == 'Horde'){
					$faction_crest = 'horde_crest.png'; 
					} else if($profile['faction']['name'] == 'Alliance') {
					$faction_crest = 'alliance_crest.png';
					} else if ($profile['faction']['name'] = 'Neutral'){
					/**
						Since Pandaran start out as a neutral faction, we'll use the pandaran crest for characters that 
						have not yet chosen a faction.
					**/
					$faction_crest = 'pandaren_crest.png';
					}
				echo '<img src="' . get_stylesheet_directory_uri() . '/img/' . $faction_crest . '" alt="' . $profile['faction']['name'] . '">';
			?>
			</div>
			<h3 id="realm"><?php echo $profile['realm']['name']; ?><span class="fa <?php echo $character_realm['status']['type'] == 'UP'? 'fa-arrow-circle-up' : 'fa-arrow-circle-down'; ?>"  style="color:<?php echo $character_realm['status']['type'] == 'UP'? 'green' : 'red'; ?>;"></span></h3>
		</div>
	</div>
	<div id="wowCharStatsBlock">
		<div id="charStats">
			<table cellspacing="10">
				<tbody>
					<tr>
						<th>Strength</th>
						<th>Agility</th>
						<th>Intellect</th>
						<th>Stamina</th>
					</tr>
					<tr>
						<td><?php echo $character_stats['strength']['effective'] ?> / <?php echo $character_stats['strength']['base'] ?></td>
						<td><?php echo $character_stats['agility']['effective'] ?> / <?php echo $character_stats['agility']['base'] ?></td>
						<td><?php echo $character_stats['intellect']['effective'] ?> / <?php echo $character_stats['intellect']['base'] ?></td>
						<td><?php echo $character_stats['stamina']['effective'] ?> / <?php echo $character_stats['stamina']['base'] ?></td>
					</tr>
				</tbody>
			</table>
			<table cellspacing="10">
				<tbody>
					<tr>
						<th>Attack</th>
						<th>Damage</th>
						<th>DPS</th>
						<th>Spell Power</th>
						<th>Armor</th>
					</tr>
					<tr>
						<td><?php echo $character_stats['attack_power']; ?></td>
						<td><?php echo round($character_stats['main_hand_damage_max']); ?></td>
						<td><?php echo round($character_stats['main_hand_dps']); ?></td>
						<td><?php echo round($character_stats['spell_power']); ?></td>
						<td><?php echo round($character_stats['armor']['effective']); ?></td>
					</tr>
				</tbody>
			</table>
	</div>
	<div id="wowCharEquipBlock">
		<table>
		<caption>Equipped Items</caption>
			<tbody>
			<?php
			$equipped = '';
			foreach ( $character_equip['equipped_items'] as $item ){
				$equipped .= '<tr><th>' . $item['slot']['name']['en_US'] . '</th><td>' . $item['name']['en_US'] . '</td></tr>';		
			}
			echo $equipped;
			?>
			</tbody>
		</table>
	</div>
		<div id="professionBlock">
			<div id="professionTable">
			<table cellspacing="10">
				<caption>Primary Professions</caption>
				<tbody>
				<?php
				$tableRow = '';
				$profList = get_character_data('https://us.api.blizzard.com/wow/character/' . $profile['realm']['name'] . '/' . $profile['name'] . '?fields=professions&locale=en_US', $access_token);
				foreach( $profList['professions']['primary'] as $profession){
					if($profession['rank'] > 0){
						$tableRow .= '<tr><th>' . $profession['name'] . '</th><td>' . $profession['rank'] . '</td></tr>';
					}
				}
				echo $tableRow;
			?>
			</tbody>
			</table>
			<table cellspacing="10">
				<caption>Secondary Professions</caption>
				<tbody>
			<?php
				$tableRow = '';
				foreach( $profList['professions']['secondary'] as $profession){
					if($profession['rank'] > 0){
						$tableRow .= '<tr><th>' . $profession['name'] . '</th><td>' . $profession['rank'] . '</td></tr>';
					}
				}
				echo $tableRow;
				?>
				</tbody>
			</table>
			</div>
		</div>
	</div>
 </div>
 	 <?php } //end else ?>
 <?php
	get_footer();
 ?>
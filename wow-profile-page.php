<?php
 /**
  * WoW Character Sheet
  *
  * @author            Julien Chambers
  * @copyright         2021 Julien Chambers
  * @license           GPL-3.0-or-later
  *
  * Plugin Name:       WoW Character Sheet
  * Plugin URI:        https://pdxchambers.com/wow-character-sheet/
  * Description:       This plugin queries the Blizzard API to display information about a character in World of Warcraft.
  * Version:           2.0.1
  * Requires at least: 5.6
  * Requires PHP:      7.2
  * Author:            Julien Chambers
  * Author URI:        https://www.pdxchambers.com
  * License:           GPL v3 or later
  * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
  * Text Domain:       wow-character-sheet
  *
  *	WoW Character Sheet is free software: you can redistribute it and/or modify
  *	it under the terms of the GNU General Public License as published by
  *	the Free Software Foundation, either version 2 of the License, or
  *	any later version.
  *	
  *	WoW Character Sheet is distributed in the hope that it will be useful,
  *	but WITHOUT ANY WARRANTY; without even the implied warranty of
  *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  *	GNU General Public License for more details.
  *	
  *	You should have received a copy of the GNU General Public License
  *	along with WoW Character Sheet. If not, see https://www.gnu.org/licenses/gpl-3.0.html.
 */
	include_once 'src/wow-character-sheet-functions.php';
	include_once 'src/wow-character-sheet-settings.php';	
 	include_once 'src/wow-character-sheet-blizz-engine.php';
	
	

	/**
	 *  @function pdxc_wow_generate()
	 * 
	 *  Callback for the shortcode to display the data to a page.
	 */
	function pdxc_wow_generate(){}


?>
	<?php
		if(array_key_exists('error', $tokenResult) || array_key_exists('code', $profile)){
	?>
	<div id="errorBlock">
	<?php
		if(array_key_exists('error', $tokenResult)){
			echo pdxc_wow_apiError($tokenResult['error'], $tokenResult['error_description']);
		} else if(array_key_exists('code', $profile)){
			echo pdxc_wow_apiError($tokenResult['code'], $tokenResult['detail']);
		}
	?>
	</div>
	<?php
		} else {
	?>
<div id="wowMainBlock">
	<div id="profileHeader">
			pdxc_wow_render_charImgBlock( $character_media['render_url'], $profile['name']);
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
					/*
						Since Pandaran start out as a neutral faction, we'll use the pandaran crest for characters that 
						have not yet chosen a faction.
					*/
					$faction_crest = 'pandaren_crest.png';
					}
				echo '<img src="' . __FILE__ . '/img/' . $faction_crest . '" alt="' . $profile['faction']['name'] . '">';
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
				$profList = pdxc_wow_get_character_data('https://us.api.blizzard.com/wow/character/' . $profile['realm']['name'] . '/' . $profile['name'] . '?fields=professions&locale=en_US', $access_token);
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
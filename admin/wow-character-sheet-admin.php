<?php
function wow_character_sheet_settings_menu(){
		add_menu_page(
			'WoW Character Sheet',
			'WoW Character Sheet Settings',
			'manage_options',
			'wow_character_sheet',
			'wow_character_sheet_options_page_html',
			20
		);
	}

	function wow_character_sheet_options_page_html() {
		?>
		<div class="wrap">
		  <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		  <hr>
		  <p>Enter the API settings to retrieve your character data. If you don't already have one, you'll need to obtain an 
		  API key and client secret from <a href="https://develop.battle.net/">Blizzard</a>.

		  <form action="options.php" method="post">
		  	<table>
			<?php
			// output security fields for the registered setting "wow_character_sheet_options"
			settings_fields( 'wow_character_sheet_options' );
			wow_character_sheet_get_api();
			wow_character_sheet_get_secret();
			wow_character_sheet_get_char_name();
			wow_character_sheet_get_char_realm();
			// output save settings button
			submit_button( __( 'Save Settings', 'textdomain' ) );
			?>
			</table>
		  </form>
		</div>
		<?php
	}

	function wow_character_sheet_get_api(){
		echo '<tr><td><label for="BlizzApiKey">Blizzard API Key: <span class="helpText">Your API key provided by Blizzard.</span></label></td>'
			. '<td><input id="BlizzApiKey name="BlizzApiKey" type="text"</td></tr>';
	}

	function wow_character_sheet_get_secret(){
			echo '<tr><td><label for="ClientSecret">Blizzard API Secret: <span class="helpText">Your client secret provided by Blizzard.</span></label></td>'
			. '<td><input name="ClientSecret" type="text"</td></tr>';
	}

	function wow_character_sheet_get_char_name(){
			echo '<tr><td><label for="CharacterName">Name of Character to Display: <span class="helpText">The name of your character.</span></label></td>'
			. '<td><input name="CharacterName" type="text"</td></tr>';
	}

	function wow_character_sheet_get_char_realm(){
			echo '<tr><td><label for="CharacterRealm">Realm Character Resides on:<span class="helpText">The name slug for the realm your character resides on.</span></label></td>'
			. '<td><input name="CharacterRealm" type="text"</td></tr>';
	}
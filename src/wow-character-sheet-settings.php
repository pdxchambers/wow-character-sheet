<?php
	function pdxc_wow_settings_init(){
		register_setting('pdxc_wow', 'wow-clientKey');
		register_setting('pdxc_wow', 'wow-clientSecret');
		register_setting('pdxc_wow', 'wow-character');
		register_setting('pdxc_wow', 'wow-realm');

		add_settings_section(
			'pdxc_wow',
			'PDXChambers WoW Character Sheet Settings',
			'pdxc_wow_settings_page_html',
			'WoW Character Sheet'
		);

		add_settings_field('wow-clientKey', 'API Client Key', 'pdxc_wow_settings_fields', 'pdxc_wow');
		add_settings_field('wow-clientSecret', 'API Client Secret', 'pdxc_wow_settings_fields', 'pdxc_wow');
		add_settings_field('wow-character', 'Character Name', 'pdxc_wow_settings_fields', 'pdxc_wow');
		add_settings_field('wow-realm', 'Realm Name', 'pdxc_wow_settings_fields', 'pdxc_wow');
	}

	add_action('admin_init', 'pdxc_wow_settings_init');

	function pdxc_wow_settings_page_html() {
		if ( !current_user_can( 'manage_options' ) ){
			return;
		}
		if( isset( $_GET['settings-updated'] ) ) {
			add_settings_error( 'pdxc_wow', 'pdxc_wow_message', __( 'Settings Saved', 'pdxc_wow'), 'updated' );
		}
		settings_errors( 'pdxc_wow_messages' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<p>Enter the API settings to retrieve your character data. If you don't already have one, you'll need to obtain an
			API key and client secret from <a href="https://develop.battle.net/">Blizzard</a>.
			<form action="options.php" method="post">
				<?php
					settings_fields( 'pdxc_wow');
					do_settings_sections( 'pdxc_wow' );
					submit_button( 'Save Settings' );
				?>
			</form>
		</div>
<?php
	}
?>
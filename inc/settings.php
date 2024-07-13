<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

/**
 * Class EH_Email_Notifier_Settings
 */
class EH_Email_Notifier_Settings {

	/**
	 * EH_Email_Notifier_Settings constructor.
	 */
	public function __construct() {

		add_action( 'admin_init', [ $this, 'register_settings' ] );

		add_action( 'admin_menu', [ $this, 'add_settings_page' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

	}

	/**
	 * Register the settings.
	 */
	public function register_settings() {

		register_setting( 'eh_email_notifier_settings_group', 'eh_email_notifier_post_types', array( 'type' => 'array' ) );
		register_setting( 'eh_email_notifier_settings_group', 'eh_email_notifier_user_1_email' );
		register_setting( 'eh_email_notifier_settings_group', 'eh_email_notifier_user_1_status' );
		register_setting( 'eh_email_notifier_settings_group', 'eh_email_notifier_user_2_email' );
		register_setting( 'eh_email_notifier_settings_group', 'eh_email_notifier_user_2_status' );

	}

	/**
	 * Add the settings page.
	 */
	public function add_settings_page() {

		add_options_page(
			__( 'Post Status Emailer', 'post-status-emailer' ),
			__( 'Post Status Emailer', 'post-status-emailer' ),
			'manage_options',
			'eh-email-notifier',
			array( $this, 'render_settings_page' )
		);

	}

	/**
	 * Render the settings page.
	 */
	public function render_settings_page() {

		$post_stati = get_post_stati();

		?>

		<div class="wrap">
			<h1><?php esc_html_e( 'Post Status Emailer', 'post-status-emailer' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'eh_email_notifier_settings_group' );
				do_settings_sections( 'eh-email-notifier' );
				?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Post Types', 'post-status-emailer' ); ?></th>
						<td>
							<?php 
							$post_types = get_post_types( array( 'public' => true ), 'objects' );
							$selected_post_types = (array) get_option( 'eh_email_notifier_post_types', array() );
							?>
							<select id="eh_email_notifier_post_types" class="widefat" name="eh_email_notifier_post_types[]" multiple>
								<?php foreach ( $post_types as $post_type ) { ?>
									<option value="<?php echo esc_attr( $post_type->name ); ?>" <?php echo in_array( $post_type->name, $selected_post_types ) ? 'selected' : ''; ?>>
										<?php echo esc_html( $post_type->label ); ?>
									</option>
								<?php } ?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the post types to monitor for changes.', 'post-status-emailer' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'User 1 Email', 'post-status-emailer' ); ?></th>
						<td>
							<input type="email" class="widefat" name="eh_email_notifier_user_1_email" value="<?php echo esc_attr( get_option( 'eh_email_notifier_user_1_email' ) ); ?>" />
							<p class="description"><?php esc_html_e( 'Enter the email address for User 1, who will be notified when your post type transitions to the status below.', 'post-status-emailer' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'User 1 Status', 'post-status-emailer' ); ?></th>
						<td>
							<?php $user_1_status = get_option( 'eh_email_notifier_user_1_status' ); ?>
							<select name="eh_email_notifier_user_1_status" class="widefat" >
								<?php foreach ( $post_stati as $status ) { ?>
									<option value="<?php echo esc_attr( $status ); ?>" <?php selected( $user_1_status, $status ); ?>>
										<?php echo esc_html( $status ); ?>
									</option>
								<?php } ?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the post status to determine when to alert User 1.', 'post-status-emailer' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'User 2 Email', 'post-status-emailer' ); ?></th>
						<td>
							<input type="email" class="widefat" name="eh_email_notifier_user_2_email" value="<?php echo esc_attr( get_option( 'eh_email_notifier_user_2_email' ) ); ?>" />
							<p class="description"><?php esc_html_e( 'Enter the email address for User 2, who will be notified when your post type transitions to the status below.', 'post-status-emailer' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'User 2 Status', 'post-status-emailer' ); ?></th>
						<td>
							<?php $user_2_status = get_option( 'eh_email_notifier_user_2_status' ); ?>
							<select name="eh_email_notifier_user_2_status" class="widefat" >
								<?php foreach ( $post_stati as $status ) { ?>
									<option value="<?php echo esc_attr( $status ); ?>" <?php selected( $user_2_status, $status ); ?>>
										<?php echo esc_html( $status ); ?>
									</option>
								<?php } ?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the post status to dermine when to alert User 2.', 'post-status-emailer' ); ?></p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Enqueue the settings page scripts.
	 *
	 * @param string $hook
	 */
	public function enqueue_scripts( $hook ) {

		if ( 'settings_page_eh-email-notifier' !== $hook ) {

			return;

		}
		
		wp_enqueue_style( 'select2-css', plugin_dir_url( __FILE__ ) . '../src/css/select2.min.css' );
		wp_enqueue_script( 'select2-js', plugin_dir_url( __FILE__ ) . '../src/js/select2.min.js', array( 'jquery' ), null, true );

		wp_enqueue_script( 'eh-email-notifier-js', plugin_dir_url( __FILE__ ) . '../src/js/eh-email-notifier.js', array( 'jquery', 'select2-js' ), null, true );

	}

}

new EH_Email_Notifier_Settings();

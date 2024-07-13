<?php
/*
Plugin Name: Post Status Emailer
Plugin URI: https://www.wordpress.org
Description: Email users when a post type status changes to a set status. Made for Amber Hinds.
Version: 1.0.0
Author: Evan Herman
Author URI: https://www.wordpress.org
License: GPLv3 or later
Text-Domain: post-status-emailer
*/

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class EH_Email_Notifier_Settings
 */
final class EH_Email_Notifier {

	/**
	 * EH_Email_Notifier constructor.
	 */
	public function __construct() {

		add_action( 'transition_post_status', array( $this, 'notify_on_post_status_change' ), 10, 3 );

		// Settings.
		require plugin_dir_path( __FILE__ ) . 'inc/settings.php';

	}

	/**
	 * Notify users when a post status changes.
	 *
	 * @param string $new_status New post status.
	 * @param string $old_status Old post status.
	 * @param WP_Post $post Post object.
	 */
	public function notify_on_post_status_change( $new_status, $old_status, $post ) {

		$selected_post_types = (array) get_option('eh_email_notifier_post_types', array() );

		if ( is_array( $selected_post_types ) && ! in_array( $post->post_type, $selected_post_types, true ) ) {
			return;
		}

		$user1_email = (string) get_option( 'eh_email_notifier_user_1_email', '' );
		$user2_email = (string) get_option( 'eh_email_notifier_user_2_email', '' );

		$user1_status = (string) get_option( 'eh_email_notifier_user_1_status', '' );
		$user2_status = (string) get_option( 'eh_email_notifier_user_2_status', '' );

		$subject = '';
		$message = '';

		// Check for first post status.
		if ( ! empty( $user1_email ) && $new_status === $user1_status && $old_status !== $user1_status ) {

			$to      = $user1_email;
			$subject = ucwords( $post->post_type ) . ' Saved as ' . $new_status;
			$message = 'A post titled "' . $post->post_title . '" has been saved as ' . $new_status . ".<br /><br />";

			if ( 'draft' === $new_status ) {

				$message .= 'Edit the ' . $post->post_type . ': <a href="' . get_edit_post_link( $post->ID ) . '">"' . $post->post_title . '"</a>';

			}

		}

		// Check for second status.
		if ( ! empty( $user2_email ) && $new_status === $user2_status && $old_status !== $user2_status ) {

			$to      = $user2_email;
			$subject = ucwords( $post->post_type ) . ' Updated: ' . $new_status;
			$message = 'A post titled "' . $post->post_title . '" has been set to ' . $new_status . ".<br /><br />";

			if ( 'publish' === $new_status ) {

				$subject  = $post->post_title . ' Has Been Published';
				$message = 'Your ' . $post->post_type . ' has been published and is now live.<br /><br />' . '<a href="' . get_permalink( $post->ID ) . '">View "' . $post->post_title . '"</a>';

			}

		}

		if ( $subject && $message ) {

			wp_mail(
				$to,
				$subject,
				$message,
				array( 'Content-Type: text/html; charset=UTF-8' )
			);

		}

	}

}

new EH_Email_Notifier();

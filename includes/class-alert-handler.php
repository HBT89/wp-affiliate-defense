<?php
class Affiliate_Link_Monitor_Alert_Handler {

    public function init() {
        add_action('save_post', array($this, 'check_affiliate_links_on_save'), 10, 3);
        add_action('admin_notices', array($this, 'show_affiliate_link_change_notice'));
    }

	public function get_affiliate_links($content) {
		$links = [];
		$pattern = '/href=["\'](https?:\/\/[^"\']*affiliate[^"\']*)["\']/i';
		preg_match_all($pattern, $content, $matches);

		if (!empty($matches[1])) {
			$links = $matches[1];
		}

		return $links;
	}

	public function check_affiliate_links_on_save($post_ID, $post, $update) {
    error_log('check_affiliate_links_on_save triggered for post ID: ' . $post_ID);

    if (!in_array($post->post_type, ['post', 'page'])) {
        error_log('Post type is not post or page. Exiting.');
        return;
    }

    $content = $post->post_content;
    $previous_content = get_post_meta($post_ID, '_previous_content', true);

    error_log('Previous content: ' . $previous_content);
    error_log('Current content: ' . $content);

    if ($previous_content && $previous_content !== $content) {
        $affiliate_links = $this->get_affiliate_links($content);
        $previous_affiliate_links = $this->get_affiliate_links($previous_content);

        error_log('Current affiliate links: ' . implode(', ', $affiliate_links));
        error_log('Previous affiliate links: ' . implode(', ', $previous_affiliate_links));

        $changed_links = array_diff($affiliate_links, $previous_affiliate_links);

        if (!empty($changed_links)) {
            error_log('Changed links detected: ' . implode(', ', $changed_links));
            $this->log_changed_links($post_ID, $changed_links);
        } else {
            error_log('No changes detected in affiliate links.');
        }
    } else {
        error_log('No previous content or content has not changed.');
    }

    update_post_meta($post_ID, '_previous_content', $content);
	}


    public function log_changed_links($post_ID, $changed_links) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'affiliate_link_changes';

		// Insert the new log entry into the database
		$wpdb->insert(
			$table_name,
			array(
				'post_id' => $post_ID,
				'changed_links' => implode(', ', $changed_links),
				'change_time' => current_time('mysql'),
			)
		);
	}





    public function show_affiliate_link_change_notice() {
        $notice = get_option('affiliate_link_change_notice');
        if ($notice) {
            echo "<div class='notice notice-warning is-dismissible'><p>$notice</p></div>";
            delete_option('affiliate_link_change_notice');
        }
    }
}

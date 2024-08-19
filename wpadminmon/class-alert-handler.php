<?php
class Affiliate_Link_Monitor_Alert_Handler {

    public function init() {
        add_action('save_post', array($this, 'check_affiliate_links_on_save'), 10, 3);
        add_action('admin_notices', array($this, 'show_affiliate_link_change_notice'));
    }

    public function check_affiliate_links_on_save($post_ID, $post, $update) {
        if ($post->post_type !== 'post' && $post->post_type !== 'page') {
            return;
        }

        $content = $post->post_content;
        $previous_content = get_post_meta($post_ID, '_previous_content', true);

        if ($previous_content && $previous_content !== $content) {
            $affiliate_links = $this->get_affiliate_links($content);
            $previous_affiliate_links = $this->get_affiliate_links($previous_content);

            $changed_links = array_diff($affiliate_links, $previous_affiliate_links);

            if (!empty($changed_links)) {
                $this->log_changed_links($post_ID, $changed_links);
            }
        }

        update_post_meta($post_ID, '_previous_content', $content);
    }

    public function get_affiliate_links($content) {
        $links = [];
        $pattern = '/href=["\'](https?:\/\/[^"\']+?)["\']/i';
        preg_match_all($pattern, $content, $matches);

        foreach ($matches[1] as $url) {
            if (strpos($url, 'affiliate') !== false) {
                $links[] = $url;
            }
        }

        return $links;
    }

    public function log_changed_links($post_ID, $changed_links) {
        $log_message = "Changes detected in affiliate links on post ID $post_ID: " . implode(', ', $changed_links);
        update_option('affiliate_link_change_notice', $log_message);
    }

    public function show_affiliate_link_change_notice() {
        $notice = get_option('affiliate_link_change_notice');
        if ($notice) {
            echo "<div class='notice notice-warning is-dismissible'><p>$notice</p></div>";
            delete_option('affiliate_link_change_notice');
        }
    }
}

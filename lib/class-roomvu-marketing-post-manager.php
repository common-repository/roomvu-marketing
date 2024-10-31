<?php

class Roomvu_Marketing_PostManager
{

    protected $layout;

    public function __construct($layout)
    {
        $this->layout = $layout;
    }

    /**
     * process books array
     */
    public function savePosts($posts)
    {
        $savedPost = 0;
        foreach ($posts as $post) {
            $title = sanitize_text_field($post['title']);
            if ($this->check_duplicate($title)) {
                continue;
            }
            $savedPost++;
            $template_data = [
				'post_type' => sanitize_text_field($post['post_type']),
                'description' => isset($post['video_description']) ? sanitize_textarea_field($post['video_description']) : '',
				'article_body' => isset($post['article_body']) ? sanitize_textarea_field($post['article_body']) : '',
				'article_url' => isset($post['article_url']) ? sanitize_textarea_field($post['article_url']) : '',
                'video_final_url' => isset($post['video_final_url']) ? sanitize_url($post['video_final_url']) : '',
            ];
            $post_content = $this->layout->render('post.php', $template_data);
            $post_category = $this->settings['default_category'] ?? '';
            $post_status = (isset($this->settings['default_status']) && $this->settings['default_status']) ? $this->settings['default_status'] : 'publish';
            $wp_post = array(
                'post_title' => $title,
                'post_status' => $post_status,
                'post_content' => $post_content,
                'post_category' => [$post_category],
                'post_date' => $post['post_datetime'],
            );
            $post_id = wp_insert_post($wp_post);
            $this->download_attachment($post_id, sanitize_url($post['image_preview']));
        }

        return $savedPost;
    }


    /**
     * check duplicate post
     *
     * @param string $title
     *
     * @return bool
     */
    protected function check_duplicate($title = '')
    {
        return !!get_page_by_title($title, OBJECT, 'post');
    }

    protected function download_attachment($post_id, $image_url)
    {
        try {
            // Add Featured Image to Post
            $upload_dir = wp_upload_dir(); // Set upload folder
            $arrContextOptions = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );
            $image_name = $this->resolve_image_name($image_url);
            $requestImage = wp_remote_get($image_url);

            if (wp_remote_retrieve_response_code($requestImage) == 200) {
                $image_data = wp_remote_retrieve_body($requestImage);
                $unique_file_name = wp_unique_filename($upload_dir['path'], $image_name); // Generate unique name
                $filename = wp_basename($unique_file_name); // Create image file name
                if (wp_mkdir_p($upload_dir['path'])) {
                    $file = $upload_dir['path'] . '/' . $filename;
                } else {
                    $file = $upload_dir['basedir'] . '/' . $filename;
                }

                file_put_contents($file, $image_data);
                $wp_filetype = wp_check_filetype($filename, null);
                $attachment = array(
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title' => sanitize_file_name($filename),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );

                $attach_id = wp_insert_attachment($attachment, $file, $post_id);
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attach_data = wp_generate_attachment_metadata($attach_id, $file);
                wp_update_attachment_metadata($attach_id, $attach_data);
                set_post_thumbnail($post_id, $attach_id);

            }

        } catch (Exception $exception) {

        }
    }

    protected function resolve_image_name($image_url)
    {
        $imageData = explode('/', $image_url);

        return $imageData[count($imageData) - 1];
    }
}
<?php

class Roomvu_Marketing_Roomvu
{
    public $plugin_name = "roomvu-marketing";
    public $plugin_title = "Roomvu Marketing";

    public $base_name = '';
    public $plugin_url = '';
    public $assets_url = '';
    public $plugin_path = '';
    public $admin_url = '';
    public $key = 'rvm_';


    protected $layout;

    protected $apiService;

    protected $cronjobService;

    public function __construct($bootPath)
    {
        $this->base_name = plugin_basename($bootPath);
        $this->plugin_url = WP_PLUGIN_URL . '/' . dirname($this->base_name) . '/';
        $this->plugin_path = WP_PLUGIN_DIR . '/' . dirname($this->base_name) . '/';

        // define layout
        $this->layout = new Roomvu_Marketing_Layout($this->plugin_path);

        // define cronjob
        $this->cronjobService = new Roomvu_Marketing_Cronjob($this);
        $this->cronjobService->initCronjob();

        // get saved settings
        $settings = $this->getSettings();

        // define Api
        $this->apiService = new Roomvu_Marketing_Rest_Api(isset($settings['email']) ? $settings['email'] : '', isset($settings['api_key']) ? $settings['api_key'] : '');

        $this->admin_url = get_bloginfo('url') . "/wp-admin/admin.php";
        $this->assets_url = $this->plugin_url . '/assets/';
        //add admin menu
        add_action('admin_menu', array($this, 'roomvuMarketingInitializeMenu'));

        //load javascripts
        add_action(is_admin() ? 'admin_enqueue_scripts' : 'wp_enqueue_scripts', array(&$this, 'roomvuMarketingLoadScripts'));
        //load styles
        $this->roomvuMarketingLoadStyle();
    }

    public function settingPage()
    {
        // Import manually
        if ('import' == $this->getQuery('action')) //TODO sanitize and validate
        {
            $this->importPage();
            return;
        }
        // Save Settings
        $data = [];
        if (count($_POST) > 0) {
            $settings = [
                'profile_name' => sanitize_text_field($this->postQuery('profile_name') ?? ''),
                'name' => sanitize_text_field($this->postQuery('name') ?? ''),
                'email' => sanitize_email($this->postQuery('email') ?? ''),
                'api_key' => sanitize_text_field($this->postQuery('api_key') ?? ''),
                'default_category' => sanitize_text_field($this->postQuery('default_category') ?? ''),
                'default_status' => sanitize_text_field($this->postQuery('default_status') ?? ''),
                'auto_post' => 1, // (int) sanitize_text_field($postData['auto_post']??0)
            ];
            $success = "Settings  has been successfully updated ";
            update_option($this->key . 'posts_settings', $settings);

            $data['success'] = $success;

            // update Api
            $this->apiService = new Roomvu_Marketing_Rest_Api($settings['email'], $settings['api_key']);

        } else {
            $settings = $this->getSettings();
        }
        $data = array_merge($data, [
            'settings' => $settings,
            'activeCron' => $this->cronjobService->cronHasActive(),
            'correctApi' => $this->apiService->isActive(),
            'plugin_name' => $this->plugin_name,
            'plugin_title' => $this->plugin_title,
        ]);

        echo $this->layout->render('settings.php', $data);
    }

    function roomvuMarketingInitializeMenu()
    {
        add_submenu_page('edit.php', $this->plugin_title, $this->plugin_title, 'administrator', $this->plugin_name, [
            $this,
            'settingPage'
        ], null);
    }

    /**
     * load js scripts for plugin backed
     * @global string $pagenow
     */
    function roomvuMarketingLoadScripts()
    {
        global $pagenow;
    }

    /**
     * load css for plugin backed
     * @global $pagenow
     */
    function roomvuMarketingLoadStyle()
    {
        //load admin css
        global $pagenow;
        if (is_admin()) {
            if ($wpadmin_page = $this->getQuery('page')) {
                $ifTthSubs = strpos($wpadmin_page, $this->plugin_name);
                if ($ifTthSubs !== false) {
                    wp_enqueue_style($this->key . 'admin_css', $this->assets_url . 'css/admin-style.css');
                }
            }
        }
    }

    public function postQuery($name, $default = null)
    {
        return isset($_POST[$name]) ? sanitize_text_field($_POST[$name]) : $default;
    }

    public function getQuery($name, $default = null)
    {
        return (isset($_GET[$name])) ? sanitize_text_field($_GET[$name]) : $default;
    }


    protected function getSettings()
    {
        return get_option($this->key . 'posts_settings', $this->getDefaultSettings());
    }

    protected function getDefaultSettings()
    {
        return [
            'import' => [
                'email' => '',
                'api_key' => '',
                'profile_name' => '',
                'name' => '',
                'auto_post' => 1,
                'default_category' => '',
                'default_status' => 'publish',
            ],
        ];
    }

    public function importPage()
    {
        $data = $this->import();
        echo $this->layout->render('success.php', $data);

    }

    public function import()
    {
        $data = $this->apiService->getCalendarContent();
        $data['count'] = 0;

        if ($data['status'] == 'success') {
            $postManager = new Roomvu_Marketing_PostManager($this->layout);
            $data['count'] = $postManager->savePosts($data['data']);
        }
        return $data;

    }
}
<?php

namespace YourVendor\SageThemeOptions;

use Illuminate\Support\ServiceProvider;
use Roots\Acorn\Sage\SageFeatures;

class ThemeOptionsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('sage.theme-options', function () {
            return new ThemeOptions();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'sage-theme-options');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'sage-theme-options');

        $this->publishes([
            __DIR__ . '/../config/theme-options.php' => $this->app->configPath('theme-options.php'),
            __DIR__ . '/../resources/assets' => $this->app->resourcePath('assets/theme-options'),
            __DIR__ . '/../resources/views' => $this->app->resourcePath('views/theme-options'),
        ], 'sage-theme-options');

        if ($this->app->bound('sage')) {
            add_action('admin_menu', [$this, 'registerOptionsPage']);
            add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
            add_action('wp_ajax_save_theme_options', [$this, 'saveThemeOptions']);
            add_action('wp_head', [$this, 'renderHeaderScripts']);
            add_action('wp_footer', [$this, 'renderFooterScripts']);
        }
    }

    /**
     * Register the options page
     */
    public function registerOptionsPage()
    {
        add_menu_page(
            __('Theme Options', 'sage-theme-options'),
            __('Theme Options', 'sage-theme-options'),
            'manage_options',
            'sage-theme-options',
            [$this, 'renderOptionsPage'],
            'dashicons-admin-generic',
            99
        );
    }

    /**
     * Render the options page
     */
    public function renderOptionsPage()
    {
        echo view('sage-theme-options::admin.options-page')->render();
    }

    /**
     * Enqueue admin assets
     */
    public function enqueueAssets($hook)
    {
        if ($hook !== 'toplevel_page_sage-theme-options') {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        wp_enqueue_style(
            'sage-theme-options-styles',
            $this->app['sage.assets']->get('styles/theme-options.css')->uri(),
            [],
            null
        );

        wp_enqueue_script(
            'sage-theme-options-scripts',
            $this->app['sage.assets']->get('scripts/theme-options.js')->uri(),
            ['jquery', 'wp-color-picker'],
            null,
            true
        );

        wp_localize_script('sage-theme-options-scripts', 'themeOptionsData', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sage_theme_options_nonce'),
        ]);
    }

    /**
     * Save theme options via AJAX
     */
    public function saveThemeOptions()
    {
        if (!check_ajax_referer('sage_theme_options_nonce', 'nonce', false)) {
            wp_send_json_error(['message' => __('Security check failed', 'sage-theme-options')]);
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Unauthorized access', 'sage-theme-options')]);
        }

        $options = [];
        parse_str($_POST['form_data'], $form_data);

        $options_data = isset($form_data['theme_options']) ? $form_data['theme_options'] : [];

        // Logo handling
        if (isset($_POST['logo_id']) && !empty($_POST['logo_id'])) {
            $options['logo'] = [
                'id' => absint($_POST['logo_id']),
                'url' => esc_url($_POST['logo_url'])
            ];
        }

        // Favicon handling
        if (isset($_POST['favicon_id']) && !empty($_POST['favicon_id'])) {
            $options['favicon'] = [
                'id' => absint($_POST['favicon_id']),
                'url' => esc_url($_POST['favicon_url'])
            ];
        }

        // Process other options
        $allowed_html = [
            'script' => [
                'type' => true,
                'src' => true,
                'async' => true,
                'defer' => true,
            ],
            'div' => ['class' => true, 'id' => true],
            'span' => ['class' => true, 'id' => true],
            'a' => ['href' => true, 'class' => true, 'id' => true, 'target' => true],
            'p' => ['class' => true],
            'h1' => ['class' => true],
            'h2' => ['class' => true],
            'h3' => ['class' => true],
            'h4' => ['class' => true],
            'h5' => ['class' => true],
            'h6' => ['class' => true],
            'br' => [],
            'em' => [],
            'strong' => [],
            'ul' => ['class' => true],
            'ol' => ['class' => true],
            'li' => ['class' => true],
            'img' => ['src' => true, 'alt' => true, 'width' => true, 'height' => true, 'class' => true],
        ];

        $text_fields = [
            'heading_one', 'seo_content', 'header_ads', 'underplay_ads',
            'footer_ads', 'google_verification', 'other_scripts', 'footer_content'
        ];

        foreach ($text_fields as $field) {
            if (isset($options_data[$field])) {
                $options[$field] = wp_kses($options_data[$field], $allowed_html);
            }
        }

        update_option('sage_theme_options', $options);
        wp_send_json_success(['message' => __('Options saved successfully', 'sage-theme-options')]);
    }

    /**
     * Render header scripts
     */
    public function renderHeaderScripts()
    {
        $options = get_option('sage_theme_options', []);

        // Output favicon
        if (!empty($options['favicon']['url'])) {
            echo '<link rel="shortcut icon" href="' . esc_url($options['favicon']['url']) . '" />' . PHP_EOL;
        }

        // Output Google site verification
        if (!empty($options['google_verification'])) {
            echo $options['google_verification'] . PHP_EOL;
        }

        // Output header ads
        if (!empty($options['header_ads'])) {
            echo '<div class="header-ads">' . $options['header_ads'] . '</div>' . PHP_EOL;
        }

        // Output other scripts
        if (!empty($options['other_scripts'])) {
            echo $options['other_scripts'] . PHP_EOL;
        }
    }

    /**
     * Render footer scripts
     */
    public function renderFooterScripts()
    {
        $options = get_option('sage_theme_options', []);

        // Output footer ads
        if (!empty($options['footer_ads'])) {
            echo '<div class="footer-ads">' . $options['footer_ads'] . '</div>' . PHP_EOL;
        }
    }
}
?>

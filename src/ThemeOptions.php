<?php

namespace YourVendor\SageThemeOptions;

class ThemeOptions
{
    protected $options;

    public function __construct()
    {
        $this->options = get_option('sage_theme_options', []);
    }

    /**
     * Get all options
     */
    public function all()
    {
        return $this->options;
    }

    /**
     * Get specific option
     */
    public function get($key, $default = null)
    {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }

    /**
     * Get logo URL
     */
    public function getLogo()
    {
        return isset($this->options['logo']) ? $this->options['logo']['url'] : '';
    }

    /**
     * Get favicon URL
     */
    public function getFavicon()
    {
        return isset($this->options['favicon']) ? $this->options['favicon']['url'] : '';
    }

    /**
     * Get heading one text
     */
    public function getHeadingOne()
    {
        return isset($this->options['heading_one']) ? $this->options['heading_one'] : '';
    }

    /**
     * Get SEO content
     */
    public function getSeoContent()
    {
        return isset($this->options['seo_content']) ? $this->options['seo_content'] : '';
    }

    /**
     * Get footer content
     */
    public function getFooterContent()
    {
        return isset($this->options['footer_content']) ? $this->options['footer_content'] : '';
    }
}
?>

<?php

/**
 * Plugin Name:       Genealabs CDN Rewriter
 * Description:       Rewrite any URLs pointing to assets in the wp-uploads folder.
 * Plugin URI:        n/a
 * Version:           0.1.0
 * Author:            Mike Bronner
 * Requires at least: 5.0.0
 * Tested up to:      4.4.2
 *
 * @package Genealabs CDN Rewriter
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

final class GenealabsCdnRewriter
{
    /**
     * Set up the plugin
     */
    public function __construct()
    {
        add_action('init', [$this, 'setup'], -1);
        require_once('functions.php');
    }

    /**
     * Setup all the things
     */
    public function setup()
    {
        add_action('wp_enqueue_scripts', array($this, 'css'), 999);
        add_action('wp_enqueue_scripts', array($this, 'js'));
        add_filter('template_include', [$this, 'template']);
        add_filter('wc_get_template', [$this, 'wcGetTemplate'], 10, 5);
    }

    /**
     * Enqueue the CSS
     *
     * @return void
     */
    public function css()
    {
        // wp_enqueue_style('genealabs-cdn-rewriter-css', plugins_url('/style.css', __FILE__));
    }

    /**
     * Enqueue the Javascript
     *
     * @return void
     */
    public function woocommerceCustomizationsJs()
    {
        // wp_enqueue_script('genealabs-cdn-reqriter-js', plugins_url('/custom.js', __FILE__), array('jquery'));
    }

    /**
     * Look in this plugin for template files first.
     * This works for the top level templates (IE single.php, page.php etc). However, it doesn't work for
     * template parts yet (content.php, header.php etc).
     *
     * Relevant trac ticket; https://core.trac.wordpress.org/ticket/13239
     *
     * @param  string $template template string.
     * @return string $template new template string.
     */
    public function template($template)
    {
        $templatePath = untrailingslashit(plugin_dir_path(__FILE__))
            . '/templates/' . basename($template);

        if (! file_exists($templatePath)) {
            return $template;
        }

        return $templatePath;
    }

    /**
     * Look in this plugin for WooCommerce template overrides.
     *
     * For example, if you want to override woocommerce/templates/cart/cart.php, you
     * can place the modified template in <plugindir>/custom/templates/woocommerce/cart/cart.php
     *
     * @param string $located is the currently located template, if any was found so far.
     * @param string $template_name is the name of the template (ex: cart/cart.php).
     * @return string $located is the newly located template if one was found, otherwise
     *                         it is the previously found template.
     */
    public function wcGetTemplate($located, $templateName)
    {
        $pluginTemplatePath = untrailingslashit(plugin_dir_path(__FILE__))
            . '/templates/woocommerce/' . $templateName;

        if (! file_exists($pluginTemplatePath)) {
            return $located;
        }

        return $pluginTemplatePath;
    }
}

add_action('plugins_loaded', function () {
    new GenealabsCdnRewriter;
});

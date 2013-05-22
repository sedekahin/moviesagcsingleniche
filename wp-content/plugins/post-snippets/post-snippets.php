<?php
/*
Plugin Name: Post Snippets
Plugin URI: http://johansteen.se/code/post-snippets/
Description: Build a library with snippets of HTML, PHP code or reoccurring text that you often use in your posts. Variables to replace parts of the snippet on insert can be used. The snippets can be inserted as-is or as shortcodes.
Author: Johan Steen
Author URI: http://johansteen.se/
Version: 2.2.1
License: GPLv2 or later
Text Domain: post-snippets 

Copyright 2009-2013 Johan Steen  (email : artstorm [at] gmail [dot] com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/** Load all of the necessary class files for the plugin */
spl_autoload_register('PostSnippets::autoload');

/**
 * Init Singleton Class.
 *
 * @author  Johan Steen <artstorm at gmail dot com>
 * @link    http://johansteen.se/
 */
class PostSnippets
{
    private static $instance = false;

    const MIN_PHP_VERSION     = '5.2.4';
    const MIN_WP_VERSION      = '3.3';
    const OPTION_KEY          = 'post_snippets_options';
    const USER_META_KEY       = 'post_snippets';
    const TINYMCE_PLUGIN_NAME = 'post_snippets';
    const TEXT_DOMAIN         = 'post-snippets';
    const FILE                = __FILE__;

    /**
     * Singleton class
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initializes the plugin.
     */
    private function __construct()
    {
        if (!$this->testHost()) {
            return;
        }

        add_action('init', array($this, 'textDomain'));
        register_uninstall_hook(__FILE__, array(__CLASS__, 'uninstall'));

        // Add TinyMCE button
        add_action('init', array(&$this, 'addTinymceButton'));

        $this->createShortcodes();

        // Adds the JS and HTML code in the header and footer for the jQuery
        // insert UI dialog in the editor
        add_action('admin_init', array(&$this,'enqueueAssets'));
        add_action('admin_head', array(&$this,'jqueryUiDialog'));
        add_action('admin_footer', array(&$this,'addJqueryUiDialog'));
        
        // Add Editor QuickTag button:
        add_action(
            'admin_print_footer_scripts',
            array(&$this,'addQuicktagButton'),
            100
        );

        new PostSnippets_Admin;
    }

    /**
     * PSR-0 compliant autoloader to load classes as needed.
     *
     * @param  string  $classname  The name of the class
     * @return null    Return early if the class name does not start with the
     *                 correct prefix
     */
    public static function autoload($className)
    {
        if (__CLASS__ !== mb_substr($className, 0, strlen(__CLASS__))) {
            return;
        }
        $className = ltrim($className, '\\');
        $fileName  = '';
        $namespace = '';
        if ($lastNsPos = strrpos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
            $fileName .= DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, 'lib_'.$className);
        $fileName .='.php';

        require $fileName;
    }

    /**
     * Loads the plugin text domain for translation
     */
    public function textDomain()
    {
        $domain = self::TEXT_DOMAIN;
        $locale = apply_filters('plugin_locale', get_locale(), $domain);
        load_textdomain(
            $domain,
            WP_LANG_DIR.'/'.$domain.'/'.$domain.'-'.$locale.'.mo'
        );
        load_plugin_textdomain(
            $domain,
            false,
            dirname(plugin_basename(__FILE__)).'/lang/'
        );
    }

    /**
     * Fired when the plugin is uninstalled.
     */
    public function uninstall()
    {
        // Delete all snippets
        delete_option('post_snippets_options');

        // Delete any per user settings
        global $wpdb;
        $wpdb->query(
            "
            DELETE FROM $wpdb->usermeta 
            WHERE meta_key = 'post_snippets'
            "
        );
    }


    // -------------------------------------------------------------------------
    // WordPress Editor Buttons
    // -------------------------------------------------------------------------

    /**
     * Add TinyMCE button.
     *
     * Adds filters to add custom buttons to the TinyMCE editor (Visual Editor)
     * in WordPress.
     *
     * @since   Post Snippets 1.8.7
     */
    public function addTinymceButton()
    {
        // Don't bother doing this stuff if the current user lacks permissions
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return;
        }

        // Add only in Rich Editor mode
        if (get_user_option('rich_editing') == 'true') {
            add_filter(
                'mce_external_plugins',
                array(&$this, 'registerTinymcePlugin')
            );
            add_filter(
                'mce_buttons',
                array(&$this, 'registerTinymceButton')
            );
        }
    }

    /**
     * Register TinyMCE button.
     *
     * Pushes the custom TinyMCE button into the array of with button names.
     * 'separator' or '|' can be pushed to the array as well. See the link
     * for all available TinyMCE controls.
     *
     * @see     wp-includes/class-wp-editor.php
     * @link    http://www.tinymce.com/wiki.php/Buttons/controls
     * @since   Post Snippets 1.8.7
     *
     * @param   array   $buttons    Filter supplied array of buttons to modify
     * @return  array               The modified array with buttons
     */
    public function registerTinymceButton($buttons)
    {
        array_push($buttons, 'separator', self::TINYMCE_PLUGIN_NAME);
        return $buttons;
    }

    /**
     * Register TinyMCE plugin.
     *
     * Adds the absolute URL for the TinyMCE plugin to the associative array of
     * plugins. Array structure: 'plugin_name' => 'plugin_url'
     *
     * @see     wp-includes/class-wp-editor.php
     * @since   Post Snippets 1.8.7
     *
     * @param   array   $plugins    Filter supplied array of plugins to modify
     * @return  array               The modified array with plugins
     */
    public function registerTinymcePlugin($plugins)
    {
        // Load the TinyMCE plugin, editor_plugin.js, into the array
        $plugins[self::TINYMCE_PLUGIN_NAME] =
            plugins_url('/tinymce/editor_plugin.js?ver=1.9', __FILE__);

        return $plugins;
    }

    /**
     * Adds a QuickTag button to the HTML editor.
     *
     * Compatible with WordPress 3.3 and newer.
     *
     * @see         wp-includes/js/quicktags.dev.js -> qt.addButton()
     * @since       Post Snippets 1.8.6
     */
    public function addQuicktagButton()
    {
        // Only run the function on post edit screens
        if (function_exists('get_current_screen')) {
            $screen = get_current_screen();
            if ($screen->base != 'post') {
                return;
            }
        }

        echo "\n<!-- START: Add QuickTag button for Post Snippets -->\n";
        ?>
        <script type="text/javascript" charset="utf-8">
            QTags.addButton( 'post_snippets_id', 'Post Snippets', qt_post_snippets );
            function qt_post_snippets() {
                post_snippets_caller = 'html';
                jQuery( "#post-snippets-dialog" ).dialog( "open" );
            }
        </script>
        <?php
        echo "\n<!-- END: Add QuickTag button for Post Snippets -->\n";
    }

    // -------------------------------------------------------------------------
    // JavaScript / jQuery handling for the post editor
    // -------------------------------------------------------------------------

    /**
     * jQuery control for the dialog and Javascript needed to insert snippets into the editor
     *
     * @since       Post Snippets 1.7
     */
    public function jqueryUiDialog()
    {
        // Only run the function on post edit screens
        if (function_exists('get_current_screen')) {
            $screen = get_current_screen();
            if ($screen->base != 'post') {
                return;
            }
        }

        echo "\n<!-- START: Post Snippets jQuery UI and related functions -->\n";
        echo "<script type='text/javascript'>\n";
        
        # Prepare the snippets and shortcodes into javascript variables
        # so they can be inserted into the editor, and get the variables replaced
        # with user defined strings.
        $snippets = get_option(self::OPTION_KEY, array());
        foreach ($snippets as $key => $snippet) {
            if ($snippet['shortcode']) {
                # Build a long string of the variables, ie: varname1={varname1} varname2={varname2}
                # so {varnameX} can be replaced at runtime.
                $var_arr = explode(",", $snippet['vars']);
                $variables = '';
                if (!empty($var_arr[0])) {
                    foreach ($var_arr as $var) {
                        // '[test2 yet="{yet}" mupp=per="{mupp=per}" content="{content}"]';
                        $var = $this->stripDefaultVal($var);

                        $variables .= ' ' . $var . '="{' . $var . '}"';
                    }
                }
                $shortcode = $snippet['title'] . $variables;
                echo "var postsnippet_{$key} = '[" . $shortcode . "]';\n";
            } else {
                // To use $snippet is probably not a good naming convention here.
                // rename to js_snippet or something?
                $snippet = $snippet['snippet'];
                # Fixes for potential collisions:
                /* Replace <> with char codes, otherwise </script> in a snippet will break it */
                $snippet = str_replace('<', '\x3C', str_replace('>', '\x3E', $snippet));
                /* Escape " with \" */
                $snippet = str_replace('"', '\"', $snippet);
                /* Remove CR and replace LF with \n to keep formatting */
                $snippet = str_replace(chr(13), '', str_replace(chr(10), '\n', $snippet));
                # Print out the variable containing the snippet
                echo "var postsnippet_{$key} = \"" . $snippet . "\";\n";
            }
        }
        ?>
        
        jQuery(document).ready(function($){
        <?php
        # Create js variables for all form fields
        foreach ($snippets as $key => $snippet) {
            $var_arr = explode(",", $snippet['vars']);
            if (!empty($var_arr[0])) {
                foreach ($var_arr as $key_2 => $var) {
                    $varname = "var_" . $key . "_" . $key_2;
                    echo "var {$varname} = $( \"#{$varname}\" );\n";
                }
            }
        }
        ?>
            
            var $tabs = $("#post-snippets-tabs").tabs();
            
            $(function() {
                $( "#post-snippets-dialog" ).dialog({
                    autoOpen: false,
                    modal: true,
                    dialogClass: 'wp-dialog',
                    buttons: {
                        Cancel: function() {
                            $( this ).dialog( "close" );
                        },
                        "Insert": function() {
                            $( this ).dialog( "close" );
                            var selected = $tabs.tabs('option', 'selected');
                        <?php
        foreach ($snippets as $key => $snippet) {
                        ?>
                                if (selected == <?php echo $key; ?>) {
                                    insert_snippet = postsnippet_<?php echo $key; ?>;
                                    <?php
                                    $var_arr = explode(",", $snippet['vars']);
            if (!empty($var_arr[0])) {
                foreach ($var_arr as $key_2 => $var) {
                    $varname = "var_" . $key . "_" . $key_2; ?>
                                            insert_snippet = insert_snippet.replace(/\{<?php
                                            echo $this->stripDefaultVal($var);
                                            ?>\}/g, <?php echo $varname; ?>.val());
            <?php
                    echo "\n";
                }
            }
            ?>
                                }
        <?php
        }
        ?>

                            // Decide what method to use to insert the snippet depending
                            // from what editor the window was opened from
                            if (post_snippets_caller == 'html') {
                                // HTML editor in WordPress 3.3 and greater
                                QTags.insertContent(insert_snippet);
                            } else if (post_snippets_caller == 'html_pre33') {
                                // HTML editor in WordPress below 3.3.
                                edInsertContent(post_snippets_canvas, insert_snippet);
                            } else {
                                // Visual Editor
                                post_snippets_canvas.execCommand('mceInsertContent', false, insert_snippet);
                            }

                        }
                    },
                    width: 500,
                });
            });
        });

        // Global variables to keep track on the canvas instance and from what editor
        // that opened the Post Snippets popup.
        var post_snippets_canvas;
        var post_snippets_caller = '';

        <?php
        echo "</script>\n";
        echo "\n<!-- END: Post Snippets jQuery UI and related functions -->\n";
    }

    /**
     * Build jQuery UI Window.
     *
     * Creates the jQuery for Post Editor popup window, its snippet tabs and the
     * form fields to enter variables.
     *
     * @since       Post Snippets 1.7
     */
    public function addJqueryUiDialog()
    {
        // Only run the function on post edit screens
        if (function_exists('get_current_screen')) {
            $screen = get_current_screen();
            if ($screen->base != 'post') {
                return;
            }
        }

        $data = array('snippets' => get_option(self::OPTION_KEY, array()));
        echo PostSnippets_View::render('jquery-ui-dialog', $data);
    }

    /**
     * Strip Default Value.
     *
     * Checks if a variable string contains a default value, and if it does it 
     * will strip it away and return the string with only the variable name
     * kept.
     *
     * @since   Post Snippets 1.9.3
     * @param   string  $variable   The variable to check for default value
     * @return  string              The variable without any default value
     */
    public function stripDefaultVal($variable)
    {
        // Check if variable contains a default defintion
        $def_pos = strpos($variable, '=');

        if ($def_pos !== false) {
            $split = str_split($variable, $def_pos);
            $variable = $split[0];
        }
        return $variable;
    }

    // -------------------------------------------------------------------------
    // Shortcode
    // -------------------------------------------------------------------------

    /**
     * Create the functions for shortcodes dynamically and register them
     */
    public function createShortcodes()
    {
        $snippets = get_option(self::OPTION_KEY);
        if (!empty($snippets)) {
            foreach ($snippets as $snippet) {
                // If shortcode is enabled for the snippet, and a snippet has been entered, register it as a shortcode.
                if ($snippet['shortcode'] && !empty($snippet['snippet'])) {
                    
                    $vars = explode(",", $snippet['vars']);
                    $vars_str = "";
                    foreach ($vars as $var) {
                        $attribute = explode('=', $var);
                        $default_value = (count($attribute) > 1) ? $attribute[1] : '';
                        $vars_str .= "\"{$attribute[0]}\" => \"{$default_value}\",";
                    }

                    // Get the wptexturize setting
                    $texturize = isset( $snippet["wptexturize"] ) ? $snippet["wptexturize"] : false;

                    add_shortcode(
                        $snippet['title'],
                        create_function(
                            '$atts,$content=null',
                            '$shortcode_symbols = array('.$vars_str.');
                            extract(shortcode_atts($shortcode_symbols, $atts));
                            
                            $attributes = compact( array_keys($shortcode_symbols) );
                            
                            // Add enclosed content if available to the attributes array
                            if ( $content != null )
                                $attributes["content"] = $content;
                            

                            $snippet = \''. addslashes($snippet["snippet"]) .'\';
                            // Disables auto conversion from & to &amp; as that should be done in snippet, not code (destroys php etc).
                            // $snippet = str_replace("&", "&amp;", $snippet);

                            foreach ($attributes as $key => $val) {
                                $snippet = str_replace("{".$key."}", $val, $snippet);
                            }

                            // Handle PHP shortcodes
                            $php = "'. $snippet["php"] .'";
                            if ($php == true) {
                                $snippet = PostSnippets::phpEval( $snippet );
                            }

                            // Strip escaping and execute nested shortcodes
                            $snippet = do_shortcode(stripslashes($snippet));

                            // WPTexturize the Snippet
                            $texturize = "'. $texturize .'";
                            if ($texturize == true) {
                                $snippet = wptexturize( $snippet );
                            }

                            return $snippet;'
                        )
                    );
                }
            }
        }
    }

    /**
     * Evaluate a snippet as PHP code.
     *
     * @since   Post Snippets 1.9
     * @param   string  $content    The snippet to evaluate
     * @return  string              The result of the evaluation
     */
    public static function phpEval($content)
    {
        if (!self::canExecutePHP()) {
            return $content;
        }

        $content = stripslashes($content);

        ob_start();
        eval ($content);
        $content = ob_get_clean();

        return addslashes($content);
    }

    /**
     * Enqueues the necessary scripts and styles for the plugins
     *
     * @since       Post Snippets 1.7
     */
    public function enqueueAssets()
    {
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_style('wp-jquery-ui-dialog');

        # Adds the CSS stylesheet for the jQuery UI dialog
        $style_url = plugins_url('/assets/post-snippets.css', __FILE__);
        wp_register_style('post-snippets', $style_url, false, '2.0');
        wp_enqueue_style('post-snippets');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Allow snippets to be retrieved directly from PHP.
     *
     * @since   Post Snippets 1.8.9.1
     *
     * @param   string      $snippet_name
     *          The name of the snippet to retrieve
     * @param   string      $snippet_vars
     *          The variables to pass to the snippet, formatted as a query string.
     * @return  string
     *          The Snippet
     */
    public static function getSnippet($snippet_name, $snippet_vars = '')
    {
        $snippets = get_option(self::OPTION_KEY, array());
        for ($i = 0; $i < count($snippets); $i++) {
            if ($snippets[$i]['title'] == $snippet_name) {
                parse_str(htmlspecialchars_decode($snippet_vars), $snippet_output);
                $snippet = $snippets[$i]['snippet'];
                $var_arr = explode(",", $snippets[$i]['vars']);

                if (!empty($var_arr[0])) {
                    for ($j = 0; $j < count($var_arr); $j++) {
                        $snippet = str_replace("{".$var_arr[$j]."}", $snippet_output[$var_arr[$j]], $snippet);
                    }
                }
            }
        }
        return do_shortcode($snippet);
    }

    /**
     * Allow other plugins to disable the PHP Code execution feature.
     *
     * @see   http://wordpress.org/extend/plugins/post-snippets/faq/
     * @since 2.1
     */
    public static function canExecutePHP()
    {
        return apply_filters('post_snippets_php_execution_enabled', true);
    }


    // -------------------------------------------------------------------------
    // Environment Checks
    // -------------------------------------------------------------------------

    /**
     * Checks PHP and WordPress versions.
     */
    private function testHost()
    {
        // Check if PHP is too old
        if (version_compare(PHP_VERSION, self::MIN_PHP_VERSION, '<')) {
            // Display notice
            add_action('admin_notices', array(&$this, 'phpVersionError'));
            return false;
        }

        // Check if WordPress is too old
        global $wp_version;
        if (version_compare($wp_version, self::MIN_WP_VERSION, '<')) {
            add_action('admin_notices', array(&$this, 'wpVersionError'));
            return false;
        }
        return true;
    }

    /**
     * Displays a warning when installed on an old PHP version.
     */
    public function phpVersionError()
    {
        echo '<div class="error"><p><strong>';
        printf(
            'Error: %3$s requires PHP version %1$s or greater.<br/>'.
            'Your installed PHP version: %2$s',
            self::MIN_PHP_VERSION,
            PHP_VERSION,
            $this->getPluginName()
        );
        echo '</strong></p></div>';
    }

    /**
     * Displays a warning when installed in an old Wordpress version.
     */
    public function wpVersionError()
    {
        echo '<div class="error"><p><strong>';
        printf(
            'Error: %2$s requires WordPress version %1$s or greater.',
            self::MIN_WP_VERSION,
            $this->getPluginName()
        );
        echo '</strong></p></div>';
    }

    /**
     * Get the name of this plugin.
     *
     * @return string The plugin name.
     */
    private function getPluginName()
    {
        $data = get_plugin_data(self::FILE);
        return $data['Name'];
    }
}

add_action('plugins_loaded', array('PostSnippets', 'getInstance'));

// -----------------------------------------------------------------------------
// Helper functions
// -----------------------------------------------------------------------------

/**
 * Allow snippets to be retrieved directly from PHP.
 * This function is a wrapper for Post_Snippets::get_snippet().
 *
 * @since   Post Snippets 1.6
 * @deprecated Post Snippets 2.1
 *
 * @param   string      $snippet_name
 *          The name of the snippet to retrieve
 * @param   string      $snippet_vars
 *          The variables to pass to the snippet, formatted as a query string.
 * @return  string
 *          The Snippet
 */
function get_post_snippet($snippet_name, $snippet_vars = '')
{
    _deprecated_function(__FUNCTION__, '2.1', 'PostSnippets::getSnippet()');
    return PostSnippets::getSnippet($snippet_name, $snippet_vars);
}

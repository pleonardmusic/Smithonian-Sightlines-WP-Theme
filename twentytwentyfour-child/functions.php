<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array(  ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );
// END ENQUEUE PARENT ACTION

// ALLOW SVG ////////////////////////
// Allow SVG file uploads
function allow_svg_upload( $mimes ) {
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
}
add_filter( 'upload_mimes', 'allow_svg_upload' );
// END SVG

/*
function make_wordpress_site_private(){
    global $wp;
    if (!is_user_logged_in() && $GLOBALS['pagenow'] !== 'wp-login.php'){
      wp_redirect(wp_login_url($wp -> request));
      exit;
    }
  }
  add_action('wp', 'make_wordpress_site_private');
  */

// ACF Display Custom Fields
add_filter('acf/settings/remove_wp_meta_box', '__return_false');

function acf_content_shortcode($atts) {
    $atts = shortcode_atts(array(
        'field' => '', // Default field name
        'post_id' => false, // Default post ID, false means the current post
        'default' => '*** TO EDIT THIS FEATURED TOP SECTION OF PAGE: Click Edit Page at top of screen.  First, set the Featured Image on the right.  Then scroll to the bottom of the editor page and replace this text by editing the Custom Field -Value- for the custom field named featured_story_top_right_text', // Default text
    ), $atts, 'acf_content');

    // Get the field value
    $field_value = get_field($atts['field'], $atts['post_id']);

    // Check if the field value is empty and set the output accordingly
    if (empty($field_value)) {
        $output = $atts['default'];
    } else {
        $output = $field_value;
    }

    return $output;
}

// Register the shortcode
add_shortcode('acf_content', 'acf_content_shortcode');

// RegiFunction to allow caption PHOTO CREDITS to be rendered correctly
function modify_image_captions($content) {
    // Load the DOM extension
    $dom = new DOMDocument();
    @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

    // Get all figcaption elements
    $xpath = new DOMXPath($dom);
    $captions = $xpath->query('//figcaption');

    foreach ($captions as $caption) {
        $captionText = $caption->textContent;

        // Check if the caption contains the word "credit:"
        $creditIndex = stripos($captionText, 'credit:');

        if ($creditIndex !== false) {
            // Split the text into main caption and credit part
            $mainCaption = trim(substr($captionText, 0, $creditIndex));
            $credit = trim(substr($captionText, $creditIndex + 7)); // 7 to skip "credit:"

            // Create new HTML content for the caption
            $caption->nodeValue = ''; // Clear the original caption content
            $caption->appendChild($dom->createElement('span', htmlspecialchars($mainCaption, ENT_QUOTES | ENT_XML1, 'UTF-8')));
            $creditSpan = $dom->createElement('span', $credit);
            $creditSpan->setAttribute('style', 'display: block; font-style: italic; font-weight:300;');
            $caption->appendChild($creditSpan);
        }
    }

    // Return the modified content
    return $dom->saveHTML();
}

function add_custom_caption_css() {
    ?>
    <style>
        figcaption span {
            display: block; /* Ensures each part is on its own line */
        }
    </style>
    <?php
}

// Add filters to modify the content and add custom CSS
add_filter('the_content', 'modify_image_captions');
add_action('wp_head', 'add_custom_caption_css');

// Function to modify text inside <p> elements in .block-captions
function modify_block_captions($content) {
    // Load the DOM extension
    $dom = new DOMDocument();
    @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

    // Get all <p> elements inside .block-captions
    $xpath = new DOMXPath($dom);
    $captions = $xpath->query('//div[contains(@class, "block-captions")]//p');

    foreach ($captions as $caption) {
        $captionText = $caption->textContent;

        // Check if the caption contains the word "credit:"
        $creditIndex = stripos($captionText, 'credit:');

        if ($creditIndex !== false) {
            // Split the text into main caption and credit part
            $mainCaption = trim(substr($captionText, 0, $creditIndex));
            $credit = trim(substr($captionText, $creditIndex + 7)); // 7 to skip "credit:"

            // Create new HTML content for the caption
            $caption->nodeValue = ''; // Clear the original caption content
            $caption->appendChild($dom->createElement('span', htmlspecialchars($mainCaption, ENT_QUOTES | ENT_XML1, 'UTF-8')));
            $creditSpan = $dom->createElement('span', $credit);
            $creditSpan->setAttribute('style', 'display: block; font-style: italic; font-weight: 300;');
            $caption->appendChild($creditSpan);
        }
    }

    // Return the modified content
    return $dom->saveHTML();
}

function add_custom_block_caption_css() {
    ?>
    <style>
        .block-captions p span {
            display: block; /* Ensures each part is on its own line */
        }
    </style>
    <?php
}

add_filter('the_content', 'modify_block_captions');
add_action('wp_head', 'add_custom_block_caption_css');


// HOMEPAGE VIDEO SCRIPT: This script listens for the end of the video playback on both left and right column videos
// and sets the z-index of their respective columns to -1 when the video ends.
function enqueue_custom_script() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const leftVideo = document.querySelector('.homepage-left-column video');
            const rightVideo = document.querySelector('.homepage-right-column video');
            const homepageContainer = document.querySelector('.homepage-fixed-for-video');
            const homepageLeftColumn = document.querySelector('.homepage-left-column');
            const homepageRightColumn = document.querySelector('.homepage-right-column');
            const homepageLeftVideo = document.querySelector('.homepage-left-column video');
            const homepageRightVideo = document.querySelector('.homepage-right-column video');

            let leftVideoEnded = false;
            let rightVideoEnded = false;

            function stopVideos() {
                leftVideo.pause();
                rightVideo.pause();
                leftVideo.currentTime = 0;
                rightVideo.currentTime = 0;
            }

            function setZIndexAndStopVideos() {
                homepageContainer.style.zIndex = '-99';
                homepageLeftColumn.style.zIndex = '-99';
                homepageRightColumn.style.zIndex = '-99';
                homepageLeftColumn.style.pointerEvents = 'none';
                homepageRightColumn.style.pointerEvents = 'none';
                homepageContainer.style.pointerEvents = 'none';
                stopVideos();
            }

            // Set the z-index to 1 when the videos start loading
            leftVideo.addEventListener('loadeddata', function() {
                homepageContainer.style.zIndex = '1';
            });

            rightVideo.addEventListener('loadeddata', function() {
                homepageContainer.style.zIndex = '1';
            });

            // Set the z-index back to -99 when the videos end
            leftVideo.addEventListener('ended', function() {
                console.log('Left video ended');
                homepageLeftColumn.style.zIndex = '-1';
                leftVideoEnded = true;
                checkBothVideosEnded();
            });

            rightVideo.addEventListener('ended', function() {
                console.log('Right video ended');
                homepageRightColumn.style.zIndex = '-1';
                rightVideoEnded = true;
                checkBothVideosEnded();
            });

            function checkBothVideosEnded() {
                console.log('Checking both videos ended:', leftVideoEnded, rightVideoEnded);
                if (leftVideoEnded || rightVideoEnded) {
                    console.log('Both videos ended, setting z-index to -99');
                    setZIndexAndStopVideos();
                }
            }

            // Set a timer as a backup
            setTimeout(function() {
                if (!leftVideoEnded || !rightVideoEnded) {
                    console.log('Timer triggered, setting z-index to -99');
                    setZIndexAndStopVideos();
                }
            }, 6500); // 6.5 seconds

        });
    </script>
    <?php
}
add_action('wp_footer', 'enqueue_custom_script');




function render_custom_animations_on_scroll() {
    ?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Select all elements with the 'animate-on-scroll' class, all <p>, .wp-block-image, <h1>-<h6>, div p, and .anchor-background elements except those with the 'no-animation' class
        const elements = document.querySelectorAll('.animate-on-scroll:not(.no-animation), .wp-block-image:not(.no-animation), figcaption:not(.no-animation) ,p:not(.no-animation), div p:not(.no-animation), h1:not(.no-animation), h2:not(.no-animation), h3:not(.no-animation), h4:not(.no-animation), h5:not(.no-animation), h6:not(.no-animation), .anchor-background:not(.no-animation)');
        
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                    observer.unobserve(entry.target); // Stop observing once the animation is triggered
                }
            });
        }, { threshold: 0.2 }); // Adjust the threshold as needed

        elements.forEach(element => {
            observer.observe(element);
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'render_custom_animations_on_scroll');



// // ADD CATEGORIES AND TAGS TO PAGES
function add_categories_tags_to_pages() {
    register_taxonomy_for_object_type('category', 'page');
    register_taxonomy_for_object_type('post_tag', 'page');
}
add_action('init', 'add_categories_tags_to_pages');

// Include pages in category and tag archive pages
function include_pages_in_archives($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }
    if ($query->is_category() || $query->is_tag()) {
        $query->set('post_type', array('post', 'page'));
    }
}
add_action('pre_get_posts', 'include_pages_in_archives');

// LOAD CUSTOM TEMPLATE FOR CATEGORY AND TAG TEMPLATES
function custom_archive_template($template) {
    if (is_category() || is_tag()) {
        $new_template = locate_template(array('custom-archive.php'));
        if ($new_template) {
            return $new_template;
        }
    }
    return $template;
}
add_filter('template_include', 'custom_archive_template');
// END PAGE CATEGORY FUNCTIONS ---- - - - - - - - //


// Function to load the videos for homepage based on screen size
function add_dynamic_video_sources_script() {
    if (is_front_page()) { // Only include on the homepage
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            function updateVideoSources() {
                var leftVideo = document.querySelector('.homepage-left-column video');
                var rightVideo = document.querySelector('.homepage-right-column video');

                // Define video sources for different screen sizes
                var desktopLeftSource = '/wp-content/uploads/2024/08/Logo-5sec-desktop-left.mp4';
                var desktopRightSource = '/wp-content/uploads/2024/08/Logo-5sec-desktop-right.mp4';
                var tabletLeftSource = '/wp-content/uploads/2024/08/Logo-5sec-tablet-left.mp4'; // Update with your tablet source
                var tabletRightSource = '/wp-content/uploads/2024/08/Logo-5sec-tablet-right.mp4'; // Update with your tablet source
                var phoneLeftSource = '/wp-content/uploads/2024/08/Logo-5sec-Mobile-2-left.mp4'; // Update with your phone source
                var phoneRightSource = '/wp-content/uploads/2024/08/Logo-5sec-Mobile-2-right.mp4'; // Update with your phone source

                // Detect screen width
                if (window.innerWidth <= 480) { // Phone breakpoint
                    if (leftVideo.src !== phoneLeftSource || rightVideo.src !== phoneRightSource) {
                        leftVideo.src = phoneLeftSource;
                        rightVideo.src = phoneRightSource;
                        leftVideo.load();
                        rightVideo.load();
                    }
                } else if (window.innerWidth <= 820) { // Tablet breakpoint
                    if (leftVideo.src !== tabletLeftSource || rightVideo.src !== tabletRightSource) {
                        leftVideo.src = tabletLeftSource;
                        rightVideo.src = tabletRightSource;
                        leftVideo.load();
                        rightVideo.load();
                    }
                } else { // Desktop
                    if (leftVideo.src !== desktopLeftSource || rightVideo.src !== desktopRightSource) {
                        leftVideo.src = desktopLeftSource;
                        rightVideo.src = desktopRightSource;
                        leftVideo.load();
                        rightVideo.load();
                    }
                }
            }

            // Run the function on page load
            updateVideoSources();

            // Also run the function on window resize
            window.addEventListener('resize', updateVideoSources);
        });
        </script>
        <?php
    }
}
add_action('wp_footer', 'add_dynamic_video_sources_script');



//Enqueue CSS and JS for Custom Assets
function enqueue_custom_assets() {
    //wp_enqueue_style('popup-styles', get_stylesheet_directory_uri() . '/theme-assets/css/popup-styles.css');
    //wp_enqueue_script('popup-scripts', get_stylesheet_directory_uri() . '/theme-assets/js/popup-scripts.js', array(), null, true);

    //wp_enqueue_style('multi-story-styles', get_stylesheet_directory_uri() . '/theme-assets/css/multi-story-styles.css');
    //wp_enqueue_script('multi-story-scripts', get_stylesheet_directory_uri() . '/theme-assets/js/multi-story-scripts.js', array('jquery'), null, true);

    wp_enqueue_style('custom-audio-player-styles', get_stylesheet_directory_uri() . '/theme-assets/css/custom-audio-player.css');
    wp_enqueue_script('custom-audio-player-scripts', get_stylesheet_directory_uri() . '/theme-assets/js/custom-audio-player.js', array(), null, true);

    wp_enqueue_script(
        'focus-management', // Handle for the script
        get_stylesheet_directory_uri() . '/theme-assets/js/focus-management.js', // Path to the script (for child theme)
        array('jquery'), // Dependencies (make sure jQuery is loaded first)
        null, // Version (set to null to avoid version conflicts)
        true // Load in footer
    );
}
add_action('wp_enqueue_scripts', 'enqueue_custom_assets');


// Add tabindex 0 to images with class img.has-tab-index-0 to make screen focusable
// functions.php

/*
function custom_transcript_dropdown_script() {
    ?>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            // Function to handle Enter key for toggling dropdown
            function handleEnterKey(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    var checkbox = e.target.previousElementSibling;
                    checkbox.checked = !checkbox.checked;
                    checkbox.dispatchEvent(new Event('change'));
                }
            }

            // Attach keydown event handler to transcript headers
            document.addEventListener('keydown', function(e) {
                if (e.target.classList.contains('transcript-header')) {
                    handleEnterKey(e);
                }
            });
        });
    </script>
    <?php
}
add_action('wp_footer', 'custom_transcript_dropdown_script');
*/


function custom_transcript_dropdown_script() {
    ?>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            var transcriptCount = 0;

            // Function to create an aria-live div with a unique ID
            function createAriaLiveDiv() {
                transcriptCount++; // Increment to ensure unique IDs
                var statusDiv = document.createElement('div');
                var uniqueId = 'transcript-status-' + transcriptCount;
                statusDiv.id = uniqueId;
                statusDiv.setAttribute('aria-live', 'assertive');
                statusDiv.style.position = 'absolute';
                statusDiv.style.left = '-9999px';
                
                // Append the div to the body (or near the transcript, if preferred)
                document.body.appendChild(statusDiv);
                return uniqueId;
            }

            // Function to update aria-live region based on checkbox state
            function updateAriaLive(transcriptToggle, uniqueStatusId) {
                var statusText = transcriptToggle.checked ? 'Transcript opened.' : 'Transcript closed.';
                document.getElementById(uniqueStatusId).textContent = statusText;
            }

            // Function to handle toggling of transcripts
            function handleTranscriptToggle(transcriptToggle, uniqueStatusId) {
                transcriptToggle.addEventListener('change', function() {
                    updateAriaLive(transcriptToggle, uniqueStatusId);
                });
            }

            // Function to observe DOM changes and check for transcript elements
            function observeDOMChanges() {
                var observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === 1) { // Check if the node is an element
                                var transcriptToggles = node.querySelectorAll('.transcript-toggle');
                                transcriptToggles.forEach(function(transcriptToggle) {
                                    var uniqueStatusId = createAriaLiveDiv();
                                    handleTranscriptToggle(transcriptToggle, uniqueStatusId);
                                });
                            }
                        });
                    });
                });

                // Start observing the entire document for changes (or a specific container)
                observer.observe(document.body, { childList: true, subtree: true });
            }

            // Event delegation to handle Enter key press for opening/closing transcripts
            document.body.addEventListener('keydown', function(e) {
                if (e.target.classList.contains('transcript-header') && e.key === 'Enter') {
                    e.preventDefault();
                    var checkbox = e.target.previousElementSibling;
                    checkbox.checked = !checkbox.checked;
                    checkbox.dispatchEvent(new Event('change'));
                }
            });

            // Call the function to observe DOM changes
            observeDOMChanges();

            // Check if transcripts are already present on page load and handle them
            document.querySelectorAll('.transcript-toggle').forEach(function(transcriptToggle) {
                var uniqueStatusId = createAriaLiveDiv();
                handleTranscriptToggle(transcriptToggle, uniqueStatusId);
            });
        });
    </script>
    <?php
}
add_action('wp_footer', 'custom_transcript_dropdown_script');




/**
 * ===============================
 * FUNCTIONS FROM OLD PLUGIN
 * ===============================
 *
 * The following functions were previously part of a custom plugin.
 * They have been moved here to the theme's functions.php file.
 * 
 * Please ensure that these functions do not conflict with existing
 * theme functions and are tested thoroughly after the move.
 * 
 * Note: Maintain proper order of execution and avoid duplications.
 * 
 * Below this comment, you will find the functions originally
 * defined in the custom plugin.
 */

 // HOMEPAGE Shortcode to display random pages by category
function wpb_rand_pages_by_category() {
    // Get the page IDs by their slugs
    $about_page_id = get_page_by_path('about')->ID;
    $resources_page_id = get_page_by_path('resources')->ID;
    $homepage_id = get_option('page_on_front');

    // Array of page IDs to exclude
    $excluded_pages = array($homepage_id, $about_page_id, $resources_page_id);

    // Categories slugs
    $categories = array('cat-1', 'cat-2', 'cat-3');

    // Initialize the string variable
    $string = '<div class="wpb-rand-posts-grid">'; // 3 column grid

    // Define the fade-in classes
    $fade_in_classes = array('fade-in-4_5-sec', 'fade-in-5-sec', 'fade-in-5_5-sec');

    // Loop through each category and fetch one random page from each
    foreach ($categories as $index => $category_slug) {
        $args = array(
            'post_type' => 'page', // Query pages
            'orderby' => 'rand',
            'posts_per_page' => 1, // Only one page per category
            'post__not_in' => $excluded_pages, // Exclude specified pages
            'tax_query' => array(
                array(
                    'taxonomy' => 'category', // Category taxonomy
                    'field'    => 'slug',
                    'terms'    => $category_slug, // Category slug
                ),
            ),
            'post_status' => 'publish', // Ensures only published (not private) pages are included
        );

        $the_query = new WP_Query($args);

        if ($the_query->have_posts()) {
            while ($the_query->have_posts()) {
                $the_query->the_post();
                $featured_image = get_the_post_thumbnail(get_the_ID(), 'medium', array('class' => 'circle-image'));
                // Get the attachment ID for the featured image
                $thumbnail_id = get_post_thumbnail_id(get_the_ID());
                // Get the caption of the featured image
                $caption = wp_get_attachment_caption($thumbnail_id);
                // Use the caption as the title if it exists, otherwise use the post title
                $title = !empty($caption) ? $caption : get_the_title();

                // Assign the fade-in class based on the index
                $fade_in_class = $fade_in_classes[$index];

                $string .= '<div class="wpb-rand-post-item ' . $fade_in_class . '">'; // Add fade-in class
                $string .= '<a href="' . get_permalink() . '" class="wpb-rand-post-link">';
                $string .= '<div class="circle-image-container" tabindex="0">'; // Add container for circle image
                $string .= $featured_image; // Add featured image
                $string .= '</div>'; // End circle image container
                $string .= '<span class="wpb-rand-post-title" tabindex="0">' . $title . '</span>';
                $string .= '</a>'; // End link
                $string .= '</div>'; // End grid item
            }
            wp_reset_postdata();
        } else {
            $string .= '<p>No pages found in category: ' . $category_slug . '</p>';
        }
    }

    $string .= '</div>'; // End grid container

    return $string;
}

add_shortcode('wpb-random-pages', 'wpb_rand_pages_by_category');
add_filter('widget_text', 'do_shortcode');


    
// ADD MULTI STORY SHORTCODE ---------------------------------------- //
function multi_story_posts($atts) {
    wp_enqueue_style('multi-story-styles', get_stylesheet_directory_uri() . '/theme-assets/css/multi-story-styles.css');
    wp_enqueue_script('multi-story-scripts', get_stylesheet_directory_uri() . '/theme-assets/js/multi-story-scripts.js', array('jquery'), null, true);

    $atts = shortcode_atts(array(
        'category' => '', // Default category slug
    ), $atts, 'multi_story_posts');

    $args = array(
        'post_type' => 'post',
        'category_name' => $atts['category'], // Use the provided category slug
        'posts_per_page' => -1, // Display all posts in the category
    );

    $the_query = new WP_Query($args);

    $string = ''; // Initialize the string variable

    if ($the_query->have_posts()) {
        $post_count = $the_query->found_posts; // Get the number of posts

        // Determine the grid template based on the number of posts
        if ($post_count == 4) {
            $grid_template = 'repeat(2, 1fr)'; // 2 columns for 4 posts
        } else {
            $grid_template = 'repeat(auto-fit, minmax(180px, 1fr))'; // Default 3 columns
        }

        $string .= '<h2 style="font-size: clamp(24px, 4vw, 32px); text-align: center; margin-top: 40px; margin-bottom: 10px; color: var(--wp--preset--color--custom-blue-2);">Select a story to learn more</h2>';

        $string .= '<div class="multi-story-posts-grid" style="display: grid; grid-template-columns: ' . $grid_template . '; gap: 10px; position: relative; margin-top: 20px;">'; // Add position: relative and margin-top
        while ($the_query->have_posts()) {
            $the_query->the_post();
            $featured_image = get_the_post_thumbnail(get_the_ID(), 'medium', array('class' => 'circle-image'));

            $title = get_the_title();

            $string .= '<div class="multi-story-post-item" style="padding: 10px;">'; // Start grid item
            $string .= '<div class="multi-story-post-link" style="display: flex; flex-direction: column; align-items: center; text-align: center;">'; // Start link box
            $string .= '<a href="#" class="post-popup-link" data-post-id="'. get_the_ID() .'" aria-expanded="false" aria-controls="post-popup-content" tabindex="0">';
            $string .= '<div class="circle-image-container" style="width: 150px; height: 150px; margin: 0 auto; overflow: hidden;" tabindex="0">';
            $string .= $featured_image;
            $string .= '</div>';
            $string .= '<span class="post-title" style="color: var(--wp--preset--color--custom-blue-2)" tabindex="0">' . $title . '</span>';
            $string .= '</a>';
            $string .= '</div>'; // End link box
            $string .= '</div>'; // End grid item
        }
        $string .= '</div>'; // End grid container

        // Popup structure
        $string .= '<div id="post-popup" class="post-popup alignfull" role="dialog" aria-labelledby="post-popup-title" aria-hidden="true">';
        $string .= '<button id="close-post-popup" class="close-post-popup" aria-label="Close popup" tabindex="0"><img src="/wp-content/uploads/2024/06/x-button-orange.svg" alt="Close"></button>';
        $string .= '<div id="post-popup-content" class="post-popup-content" tabindex="-1"></div>';
        $string .= '</div>'; // End post-popup

        /* Restore original Post Data */
        wp_reset_postdata();

        // Enqueue CSS and JS files for multi-story popup
        add_action('wp_enqueue_scripts', function() {
            wp_enqueue_style('multi-story-styles', get_stylesheet_directory_uri() . '/multi-story-styles.css');
            wp_enqueue_script('multi-story-scripts', get_stylesheet_directory_uri() . '/multi-story-scripts.js', array('jquery'), null, true);
        });
    }

    return $string;
}
add_shortcode('multi_story_posts', 'multi_story_posts');




// POPUP Function to retrieve posts and generate HTML for thumbnail grid
function custom_popup_menu_shortcode($atts, $content = null) {
    // Enqueue the custom stylesheet for the popup
    wp_enqueue_style('popup-styles', get_stylesheet_directory_uri() . '/theme-assets/css/popup-styles.css');

    // Enqueue custom JavaScript for the popup
    wp_enqueue_script('popup-scripts', get_stylesheet_directory_uri() . '/theme-assets/js/popup-scripts.js', array(), null, true);

    // Set default attributes and merge with user-supplied attributes
    $atts = shortcode_atts(
        array(
            'button_id' => 'custom-nav-button', // Default button ID
        ), 
        $atts, 
        'custom_popup_menu'
    );

    // Get the page IDs by their slugs
    $about_page_id = get_page_by_path('about')->ID;
    $resources_page_id = get_page_by_path('resources')->ID;
    $homepage_id = get_option('page_on_front');
    
    // Array of page IDs to exclude
    $excluded_pages = array($homepage_id, $about_page_id, $resources_page_id);

    // Start building the output
    $output = '';

    // Add HTML markup for the custom navigation button
    $output .= '<button id="' . esc_attr($atts['button_id']) . '" class="custom-nav-button" aria-label="Open menu" aria-expanded="false">';
    $output .= '<div class="compass-container no-animation-div"><div class="compass no-animation-div"><img src="/wp-content/uploads/2024/07/compas-needle.svg" class="needle" alt="Compass needle" /></div></div>';
    $output .= '</button>';

    // Add HTML markup for the popup
    $output .= '<div id="popup" class="popup no-animation-div" role="dialog" aria-labelledby="popup-title" aria-hidden="true">';
    $output .= '<h2 id="popup-title" class="sr-only">Navigation Menu</h2>';
    $output .= '<button id="close-popup-button" class="close-popup-button" aria-label="Close menu"><img src="/wp-content/uploads/2024/05/x-button-white.svg" alt="Close" /></button>';
    $output .= '<a href="/"><div class="popup-logo-container" tabindex="0"><img src="/wp-content/uploads/2024/07/SightlinesLogo-white-NEW.svg" alt="The Sightlines Atlas" class="popup-logo"></div></a>';
    $output .= '<div class="thumbnail-grid constrained">';

    // Query pages, excluding the specified pages
    $args = array(
        'post_type' => 'page', // Query pages
        'posts_per_page' => -1, // Display all pages
        'post__not_in' => $excluded_pages // Exclude the homepage and other specified pages
    );
    
    $the_query = new WP_Query($args);

    // Check if pages were found
    if ($the_query->have_posts()) {
        // Loop through each page
        while ($the_query->have_posts()) {
            $the_query->the_post();
            $featured_image = get_the_post_thumbnail(get_the_ID(), 'medium', array('class' => 'circle-image'));
            // Get the attachment ID for the featured image
            $thumbnail_id = get_post_thumbnail_id(get_the_ID());
            // Get the caption of the featured image
            $caption = wp_get_attachment_caption($thumbnail_id);
            // Use the caption as the title attribute and link text if it exists, otherwise use the post title
            $alt_text = $caption ? $caption : get_the_title();
            $title_text = $caption ? $caption : get_the_title();
            // Append each thumbnail and title to the output
            $output .= '<div class="thumbnail-wrapper">';
            $output .= '<a href="' . get_permalink() . '" class="thumbnail-link" aria-label="' . esc_attr($alt_text) . '" tabindex="0">';
            $output .= '<div class="circle-image-container">' . $featured_image . '</div>';
            $output .= '<span class="post-title">' . esc_html($title_text) . '</span>';
            $output .= '</a>';
            $output .= '</div>';
        }
        // Reset post data
        wp_reset_postdata();
    } else {
        $output .= '<p>No pages found.</p>';
    }
    
    $output .= '</div></div>';
    // Return the final output
    return $output;
}
add_shortcode('custom_popup_menu', 'custom_popup_menu_shortcode');




// Function to retrieve and display the featured image
function insert_featured_image_shortcode($atts) {
    global $post;
    // Check if the post has a featured image
    if (has_post_thumbnail($post->ID)) {
        // Get the featured image
        $image = get_the_post_thumbnail($post->ID, 'large');
        // Check if the image was retrieved successfully
        if ($image) {
            return $image;
        } else {
            // If 'large' size is not available, return 'full' size
            return get_the_post_thumbnail($post->ID, 'full');
        }
    }
    // Return an empty string if no featured image is found
    return '';
}
// Register the shortcode
add_shortcode('featured_image', 'insert_featured_image_shortcode');


// ADD ANCHOR OBJECT shortcode
function anchor_object_shortcode($atts) {
    // Attributes
    $atts = shortcode_atts(array(
        'slug' => '', // Default value
    ), $atts, 'anchor_object_shortcode');
    
    // Get post by slug
    $post = get_page_by_path($atts['slug'], OBJECT, 'post');
    if (!$post) {
        return '<p>No post found with the given slug.</p>';
    }
    
    // Get post data
    $post_id = $post->ID;
    
    // Get the featured image and its caption
    $featured_image = get_the_post_thumbnail($post_id, 'medium', array('class' => 'anchor-featured-image'));
    $thumbnail_id = get_post_thumbnail_id($post_id);
    $caption = wp_get_attachment_caption($thumbnail_id);
    
    // Use the caption as the title if it exists, otherwise use the post title
    if (!empty($caption)) {
        $title = $caption;
    } else {
        $title = get_the_title($post_id);
    }
    
    $content = apply_filters('the_content', $post->post_content);
    
    // Start building the output
    $output = '';
    
    // Top content area
    $output .= '<div class="anchor-object-container" style="width: 100%; margin: 0 auto;">';
    $output .= '<div class="anchor-top-content multi-story-posts-grid" style="display: grid; gap: 10px; position: relative; margin-top: 20px;">'; // Add position: relative and margin-top
    
    // Left column: Featured image
    $output .= '<div class="multi-story-post-item" style="padding: 10px;">'; // Start grid item
    $output .= '<div class="multi-story-post-link">'; // Start link box
    $output .= '<div class="wide-image-container" style="width: 100%; overflow: hidden;">'; // Adjusted width and overflow
    $output .= $featured_image;
    $output .= '</div>';
    $output .= '</div>'; // End link box
    $output .= '</div>'; // End grid item
    
    // Right column: Title and content
    $output .= '<div class="multi-story-post-item" style="padding: 10px;">'; // Start 2nd grid item
    $output .= '<div class="multi-story-post-link">'; // Start 2nd link box
    $output .= '<div class="anchor-text" style="color: var(--wp--preset--color--custom-white);">';
    $output .= '<h4 style="color: var(--wp--preset--color--custom-white);">' . esc_html($title) . '</h4>';
    $output .= '<div>' . $content . '</div>';
    $output .= '</div>'; // Close anchor-text
    $output .= '</div>'; // End 2nd link box
    $output .= '</div>'; // End 2nd grid item
    
    $output .= '</div>'; // End top content area

    //********* BOTTOM READ MORE SECTION REMOVED HERE.......................... */

    $output .= '</div>'; // Close anchor-object-container
    
    return $output;
}
add_shortcode('anchor_object', 'anchor_object_shortcode');


// ADD READ MORE   shortcode ///////////////////
function read_more_shortcode($atts) {
    // Attributes
    $atts = shortcode_atts(
        array(
            'page_slug1' => '',
            'page_slug2' => ''
        ), 
        $atts, 
        'read_more_shortcode'
    );

    // Get posts by slug
    $pages = array();
    if (!empty($atts['page_slug1'])) {
        $page1 = get_page_by_path($atts['page_slug1'], OBJECT, 'page');
        if ($page1) {
            $pages[] = $page1;
        }
    }

    if (!empty($atts['page_slug2'])) {
        $page2 = get_page_by_path($atts['page_slug2'], OBJECT, 'page');
        if ($page2) {
            $pages[] = $page2;
        }
    }

    if (empty($pages)) {
        return '<p>No pages found with the given slugs.</p>';
    }

    // Start building the output
    $output = '';
    $output .= '<h2 style="font-size: 36px; text-align: center; margin-top: 40px; margin-bottom: 10px; color: var(--wp--preset--color--custom-blue-1);">More Stories</h2>';

    // Bottom content area
    $output .= '<div class="anchor-bottom-content read-more-grid">';

    foreach ($pages as $page) {
        // Get the featured image and its caption
        $featured_image = get_the_post_thumbnail($page->ID, array(125, 125)); // Adjusted size for smaller circle
        $thumbnail_id = get_post_thumbnail_id($page->ID);
        $caption = wp_get_attachment_caption($thumbnail_id);

        // Use the caption as the title if it exists, otherwise use the post title
        if (!empty($caption)) {
            $title = $caption;
        } else {
            $title = get_the_title($page->ID);
        }

        $output .= '<div class="read-more-item">';

// Image section with its own div and tabindex
$output .= '<a href="' . get_permalink($page->ID) . '" style="display: block;">';
$output .= '<div class="read-more-image" tabindex="0">';
$output .= $featured_image;
$output .= '</div>';
$output .= '</a>';

// Title and arrow sections with their own divs and tabindex
$output .= '<a href="' . get_permalink($page->ID) . '" style="display: block; color: inherit; text-decoration: none;">';
$output .= '<div class="read-more-content" tabindex="0">';
$output .= '<h5>' . $title . '</h5>';
$output .= '<div class="read-more-arrow-container">';
$output .= '<img src="/wp-content/uploads/2024/08/read-more-arrow.svg" alt="Read more arrow">';
$output .= '</div>';
$output .= '</div>';
$output .= '</a>';

$output .= '</div>'; // End grid item

    }

    $output .= '</div>'; // End grid container

    // Add the custom button below the content and center it
    $output .= '<div style="text-align: center; margin-top: 0px;">';
    $output .= do_shortcode('[custom_popup_menu button_id="custom-nav-button-2"]');
    $output .= '</div>';

    return $output;
}
add_shortcode('read_more', 'read_more_shortcode');




// ADD FEATURED SECTION  shortcode ///////////////////
///////////////////
function featured_section_shortcode($atts) {
    // Define the default color
    $default_color = 'blue-1';

    // Extract the color attribute
    $atts = shortcode_atts(
        array(
            'color' => $default_color, // Default color
        ),
        $atts,
        'featured_section'
    );

    // Map color names to rgba values
    $color_map = array(
        'blue-1' => 'rgba(45, 56, 95, 0.85)',
        'blue-2' => 'rgba(69, 85, 136, 0.85)',
        'blue-3' => 'rgba(111, 130, 165, 0.85)',
        'blue-4' => 'rgba(189, 203, 221, 0.85)',
        'orange' => 'rgba(222, 109, 64, 0.85)',
        // Add more color mappings as needed
    );

    // Get the featured image of the page
    $post_id = get_the_ID();
    $featured_image_url = get_the_post_thumbnail_url($post_id, 'full');

    // Get the site logo URL
    $site_logo_id = get_theme_mod('custom_logo');
    $site_logo_url = '/wp-content/uploads/2024/07/SightlinesLogo-white.svg';

    // Get the page title and determine subtitle
    $page_title = get_the_title($post_id);
    $subtitle = '';
    if (strpos($page_title, ':') !== false) {
        list($page_title, $subtitle) = explode(':', $page_title, 2);
    }

    // Start building the output
    $output = '<div class="background-wrapper">';
    $output .= '<div class="image-container">';
    $output .= '<img src="' . esc_url($featured_image_url) . '" alt="Background Image">';
    $output .= '</div>';
    $output .= '<div class="overlay-content">';
    $output .= '<div class="columns">';
    
    // Left column with site logo and page title
    $output .= '<div class="headline-column">';
    $output .= '<div class="background-extension"></div>';  // New div for extending the background color
    $output .= '<a href="' . esc_url(home_url('/')) . '" class="custom-logo-link" rel="home"><div class="article-image-left-site-title wp-block-site-logo" tabindex="0"><img src="' . esc_url($site_logo_url) . '" class="custom-logo no-animation" alt="Sightlines Atlas Logo" decoding="async" /></div></a>';
    $output .= '<div class="article-image-left-page-title"><h1>' . esc_html($page_title) . '</h1>';
    if (!empty($subtitle)) {
        $output .= '<h2>' . esc_html(trim($subtitle)) . '</h2>';
    }
    $output .= '<hr class="title-separator">';
    $output .= '</div></div>';
    
    // Right column with ACF content
    $output .= '<div class="text-column">';
    $output .= do_shortcode('[acf_content field="featured_story_top_right_text"]');
    $output .= '</div>';
    
    $output .= '</div>'; // End of columns
    $output .= '</div>'; // End of overlay-content
    $output .= '</div>'; // End of background-wrapper
    
    // Add inline styles for the wrapper's background image
    $output .= '<style>
        .background-wrapper {
            position: relative;
            width: 100%;
            min-height: 95vh;
            overflow: hidden;
        }
        .image-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }
        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: grayscale(100%);
            mix-blend-mode: multiply;
        }
        .overlay-content {
            position: relative;
            background-color: ' . $color_map[$atts['color']] . ';
            min-height: 95vh;
            z-index: 2;
            display: flex;
            align-items: flex-end; /* Align items to the bottom of the container */
            justify-content: center; /* Center the content horizontally */
            box-sizing: border-box;
            width: 100%;
            padding-bottom: 20px; /* Adjust this to create a margin at the bottom */
        }
        .columns {
            display: flex;
            width: 100%;
            height: 100%;
            max-width: 1280px;
        }
        .headline-column, .text-column {
            flex: 1;
            padding: 20px;
            color: white;
            box-sizing: border-box;
        }
        .headline-column h1, .headline-column h2 {
            color: ' . ($atts['color'] === 'blue-4' ? '#2D385F' : 'white') . ';
        }
        .headline-column .title-separator {
            width: 118px;
            border: 0;
            height: 4px;
            background: var(--wp--preset--color--custom-orange);
            margin: 10px 0; /* Adjust as necessary */
        }
        .text-column {
            font-size: 20px;
            text-align: left;
            overflow: auto;
            display: flex;
            flex-direction: column;
            justify-content: flex-end; /* Aligns content to the bottom */
            margin-bottom: 18px;
            color: ' . ($atts['color'] === 'blue-4' ? '#272835' : 'white') . ';
        }
        .text-column * {
            text-align: left !important;
            overflow: auto;
        }
        .wp-block-site-logo img {
            height: 45px;
            max-width: 100%;
            padding: 15px;
            padding-bottom: 10px;
            padding-top: 20px;
        }

        /* Media query for max-width 1399.98px */
        @media (max-width: 1399.98px) {
            .article-image-left-page-title, .article-image-left-site-title {
                padding-left: 40px;
                max-width: 430px;
            }    
            .text-column {
                padding-right: 60px;
                padding-left: 60px;
            }
        }

        /* Media query for max-width: 991.98px */
        @media (max-width: 991.98px) {
            .article-image-left-page-title, .article-image-left-site-title {
                padding-left: 0px;
            }   
            .text-column {
                margin-bottom: 0px;
                padding-right: 20px;
            }  
              .headline-column h1 {
                font-size: 3.7rem;
                max-width: 380px;
            }      
        }

        @media (max-width: 768px) {
            .columns {
                flex-direction: column;
            }
            .headline-column, .text-column {
                width: 100%; /* Ensures full width */
                box-sizing: border-box;
            }
            .headline-column {
                /* min-height: 95vh;  Ensures the column takes up 95vh */
            }
            .article-image-left-page-title {
                position: relative;
                top: 80px;
            }
            .text-column {
                margin-top: 40px; /* Adds spacing between stacked columns */
                min-height: auto; /* Resets the min-height */
                padding-left: 40px;
                padding-right: 40px;        
                font-size: 1.05rem;
                overflow: unset !important;
            }
        }
    </style>';
    
    return $output;
}
add_shortcode('featured_section', 'featured_section_shortcode');

// Add a custom JavaScript snippet to target Chrome specifically
function add_chrome_specific_styles() {
    ?>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
            if (isChrome) {
                document.querySelectorAll('.custom-nav-button').forEach(function(el) {
                    el.style.marginTop = '-22px';
                });
            }
        });
    </script>
    <?php
}

// Hook the function to the wp_head action to include the script in the header
add_action('wp_head', 'add_chrome_specific_styles');

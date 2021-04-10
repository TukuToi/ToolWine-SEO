<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tw_Seo
 * @subpackage Tw_Seo/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Tw_Seo
 * @subpackage Tw_Seo/public
 * @author     TukuToi <hello@tukutoi.com>
 */
class Tw_Seo_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * The Option Settings of this plugin
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $options    All Settings stored as options for this plugin.
     */
    private $options;

    /**
     * The ID of the currnet post, page, or custom post
     *
     * @since    1.0.0
     * @access   private
     * @var      int    $ID    Current ID of Post used for SEO Content markup.
     */
    private $ID;

    /**
     * The ID of the currnet term
     *
     * @since    1.0.0
     * @access   private
     * @var      int    $term_ID    Current ID of Term used for SEO Content markup.
     */
    private $term_ID;

    /**
     * The Title, Name of the currnet post, page, or else
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $title    The Title or name of the currnet post, page, or else
     */
    private $title;

    /**
     * The Permalink of the currnet post, page, or else
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $url    The permalink of the currnet post, page, or else
     */
    private $url;

    /**
     * The Image of the currnet post, page, or as defined in settings
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $image    The Image of the currnet post, page, or as defined in settings
     */
    private $image;

    /**
     * The Excerpt of the currnet post, page, or as defined in settings
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $excerpt    The Excerpt of the currnet post, page, or as defined in settings
     */
    private $excerpt;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name   The name of the plugin.
     * @param      string    $version       The version of this plugin.
     * @param      array     $options       The Options of this plugin
     * @param      string    $title         The title of the current object.
     * @param      string    $ID            The id of the current object.
     * @param      string    $term_ID       The term ID of the current term.
     * @param      string    $url           The url of the current object.
     * @param      string    $image         The image if the current object.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name  = $plugin_name;
        $this->version      = $version;
        $this->options      = get_option( $this->plugin_name );
        $this->title        = '';
        $this->excerpt      = '';
        $this->ID           = '';
        $this->term_ID      = '';
        $this->url          = '';
        $this->image        = '';

    }

    /**
     * Build Current Post/Archive/Document data
     * @since 1.0.0
     * @access public
     */
    private function get_current_object_data(){

        global $wp_query;

        if( is_singular() ){
            $this->ID       = get_the_ID();
            $this->title    = get_the_title();
            $this->url      = get_permalink();
            $this->excerpt  = get_the_excerpt();
            if( !has_post_thumbnail( $this->ID )) { //the post does not have featured image, use a default image
                $this->image = $this->options[$this->plugin_name .'_logo']; //replace this with a default image on your server or an image in your media library
            }
            else{
                $this->image = esc_attr(wp_get_attachment_image_src( get_post_thumbnail_id( $this->ID, 'medium' ))[0]);
            }
            if( is_front_page() ) 
                $this->title = get_bloginfo('name') . ' | ' . get_bloginfo('description');
        }
        elseif( !is_home() && !is_date() && !is_search() ){
            $this->ID       = 0;
            $this->title    = get_queried_object()->name;
            $this->url      = home_url( $_SERVER['REQUEST_URI'] );
        }
        elseif( is_tax() || is_category() || is_tag() ){
            $this->term_ID = $wp_query->get_queried_object()->term_id;
        }
        else{
            if( is_home() )
                $this->title = get_bloginfo('name') . ' ' . get_bloginfo('description');
            if( is_search() )
                $this->title = get_bloginfo('name') . ' | Search';
            if( is_date() )
                $this->title = get_bloginfo('name') . ' | Date Archives';
            $this->url = home_url( $_SERVER['REQUEST_URI'] );
        }

    }

    public function remove_default_html_tags(){
        remove_filter ('term_description', 'wpautop');
    }

    /**
     * Add Meta Tags title, description, keywords, author
     * @since 1.0.0
     * @access public
     */
    public function add_metatags( $output ) {

        $title = !empty( get_post_meta( $this->ID, $this->plugin_name .'-title', true) ) ? get_post_meta( $this->ID, $this->plugin_name .'-title', true) : $this->title;
        //<meta name="Description" content="Written by A.N. Author, 
        //Illustrated by V. Gogh, Price: $17.99, 
        //Length: 784 pages">
        $description = !empty( get_post_meta( $this->ID, $this->plugin_name .'-description', true) ) ? get_post_meta( $this->ID, $this->plugin_name .'-description', true) : $this->excerpt;
        $description = !empty( $description ) ? $description : get_bloginfo('description');
        $keywords = get_post_meta( $this->ID, $this->plugin_name .'-keywords', true);
        $author = get_the_author_meta( 'display_name', get_post_field( 'post_author', $this->ID ) );
        
        if(is_archive()){
            if(is_tax() || is_category() || is_tag()){
                $title = get_the_archive_title();
                //<meta name="Description" content="Written by A.N. Author, 
                //Illustrated by V. Gogh, Price: $17.99, 
                //Length: 784 pages">
                $description = term_description( $this->term_ID );
                $description = !empty( $description ) ? $description : get_bloginfo('description');
                /**
                 *@todo do not hardcode this
                 */
                $keywords = get_term_meta($this->term_ID, 'wpcf-'.$this->fields['keywords'].'-term', true);
                $author = get_the_author_meta( 'display_name', get_post_field( 'post_author', $this->ID ) );
            }
            if(is_post_type_archive()){
                $title = get_the_archive_title();
                //<meta name="Description" content="Written by A.N. Author, 
                //Illustrated by V. Gogh, Price: $17.99, 
                //Length: 784 pages">
                $description = get_queried_object()->description;
                $description = !empty( $description ) ? $description : get_bloginfo('description');
                $keywords   = '';
                $posts = get_posts(array('post_type' => get_queried_object()->name));
                foreach($posts  as $post){
                    $keywords_all   .= $post->post_title . ', ';
                }
                $keywords = $keywords_all;
                $author = get_the_author_meta( 'display_name', get_post_field( 'post_author', $this->ID ) );
            }
        }
		elseif(is_home()){
			$title 	= get_bloginfo('name') . ' | ' .get_bloginfo('description');
			/**
			 * @todo do not hardcode this
			 */
			$author = get_the_author_meta( 'display_name', 1 );
		}

        $title = '<meta name="title" content="'. $title .'">';
        $description = '<meta name="description" content="'. $description .'">';
        $keywords = '<meta name="keywords" content="'. $keywords .'">';
        $author = '<meta name="author" content="'. $author .'">';

        $output = '<!-- Start Meta Tags -->'.$title.$description.$keywords.$author.'<!-- End Meta Tags -->';

        echo $output;

    }

    /**
     * Add Opengraph language attributes
     * @since 1.0.0
     * @access public
     */
    public function opengraph_doctype( $output ) {
        return $output . ' xmlns:og="https://opengraphprotocol.org/schema/" xmlns:fb="https://www.facebook.com/2008/fbml"';
    }

    /**
     * Build FaceBook OG Tags
     * @since 1.0.0
     * @access public
     */
    public function opengraph_fb(){

        $html  = '<!-- OpenGraph Tags -->';
        $html .= '<meta property="twitter:card" content="summary_large_image">';
        $html .= '<meta property="twitter:url" content="'. $this->url .'">';
        $html .= '<meta property="twitter:title" content="'. $this->title .'">';
        $html .= '<meta property="twitter:description" content="'. $this->options[$this->plugin_name .'_main_description'] .'">';
        $html .= '<meta property="twitter:image" content="'. $this->options[$this->plugin_name .'_logo'] .'">';
        $html .= '<meta property="og:url" content="' . $this->url . '"/>';
        $html .= '<meta property="og:type" content="article" />';
        $html .= '<meta property="og:title" content="' . $this->title . '"/>';
        $html .= '<meta property="og:description" content="' . $this->options[$this->plugin_name.'_main_description'] . '">';
        $html .= '<meta property="og:image" content="' . $this->image . '"/>';
        $html .= '<meta property="fb:app_id" content="'. $this->options[$this->plugin_name .'_fbappid'] .'" />';
        $html .= '<!-- End OpenGraph Tags -->';

        echo $html;

    }

    /**
     * Add Header HTML
     * @since 1.0.0
     * @access public
     */
    public function add_header_html() {

        $html = $this->options[$this->plugin_name .'_header'];
        
        echo $html;

    }

    /**
     * Add Gooogle Gtag JS
     * @since 1.0.0
     * @access public
     */
    public function add_google_gtag_js(){
        
        $html  = '<!-- Global site tag (gtag.js) - Google Analytics -->';
        $html .= '<script async src="https://www.googletagmanager.com/gtag/js?id='. get_option( $this->plugin_name )[$this->plugin_name .'_gtag'] .'"></script>';
        $html .= '<script>';
        $html .= 'window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag(\'js\', new Date());gtag(\'config\', \''. get_option( $this->plugin_name )[$this->plugin_name .'_gtag'] .'\');';
        $html .= '</script>';
        $html .= '<!-- End Global site tag (gtag.js) - Google Analytics -->';

        echo $html;

    }
    
    public function add_wp_missing_canonical_archives() {

        if( is_archive() ) {

            echo '<link rel="canonical" href="'. $this->url .'">';

        }

    }

    /**
     * Rebuild the Document title if Custom SEO Title Field value was passed
     *
     * @since    1.0.0
     * @access   public
     */
    public function custom_document_title( $title_parts ) {

        $tw_seo_title = get_post_meta( $this->ID, $this->plugin_name .'seo-title', true );

        if ( is_archive() ) {

            if( is_tax() ){
                
                $title_parts['title'] = esc_html( term_description( $this->term_ID ) );
            }
            elseif( is_author() ){

                $title_parts['title'] = get_the_author();

            }
            else{

                $title_parts['title'] = get_queried_object()->description;

            }
            
            $title_parts['site'] = get_bloginfo('name');

        }
        else {

            $title_parts['title'] = $tw_seo_title != '' ? $tw_seo_title : esc_html( $this->title );
            $title_parts['site'] = $tw_seo_title != '' ? '' : get_bloginfo('name');

        }

        return $title_parts;

    }

    
    /**
     * Build Custom Schema Markup
     *
     * @since    1.0.0
     * @access   public
     */
    public function add_schema_org(){

        $schema_type    = $this->options[$this->plugin_name .'_schema_maps'];
        $logo           = $this->options[$this->plugin_name .'_logo'];
        $image          = has_post_thumbnail( $this->ID ) ? wp_get_attachment_url( get_post_thumbnail_id($this->ID), 'thumbnail' ) : $logo;
        $description    = $this->options[$this->plugin_name .'_main_description'];
        $social         = explode( ',', $this->options[$this->plugin_name .'_social_media'] );
        $language       = TKT_WPML_IS_ACTIVE != false ? ICL_LANGUAGE_CODE : get_locale();
        $schema         = array();
        $currency       = 'USD';


        $main = array(
            '@context'      => "https://schema.org",
            '@type'         => "WebSite",
            'dateModified'  => get_the_modified_date('Y-m-d', $this->ID),//2021-04-09
            'description'   => $description,
            'headline'      => get_bloginfo( 'description' ),
            'inLanguage'    => $language,
            'name'          => get_bloginfo('name'),
            'publisher'     => array (
                '@type'     => "Organization",
                'name'      => get_bloginfo('name'),
                'sameAs'    => $social,
                'logo'      => array(
                    '@type' => "ImageObject",
                    'url'   => $logo,
                ),
            ),
            'url'           => get_home_url(),

        );

        array_push($schema, $main);

        if( is_singular() )
            $has_manual_schema = get_post_meta($this->ID, 'wpcf-schema-dot-org', true) ? get_post_meta($this->ID, 'wpcf-schema-dot-org', true) : false;

        if( is_singular() && $has_manual_schema != false ){
            array_push($schema, $has_manual_schema);
        }
        elseif ( !empty( array_keys($schema_type, 'softwareapplication') ) && is_singular( array_keys($schema_type, 'softwareapplication') ) ) {//current post is mapped to softwareapplication

            /**
             *@todo the field slugs are currently hardcoded
             */
            $download_url   = get_post_meta($this->ID,'wpcf-github-repo-url', true);
            $screenshot     = get_post_meta($this->ID,'wpcf-software-logo', true);
            $requirements   = get_post_meta($this->ID,'wpcf-requires', true);
            $sof_version    = get_post_meta($this->ID,'wpcf-version', true);
            $rating         = get_post_meta($this->ID,'wpcf-rating', true);
            $rating_count   = get_post_meta($this->ID,'wpcf-rating-count', true);
            $price          = get_post_meta($this->ID,'wpcf-price', true);
            $op_system      = get_post_meta($this->ID,'wpcf-operating-system', true);

            $program = array(
                '@context'              => 'https://schema.org',
                '@type'                 => 'SoftwareApplication',
                'name'                  => $this->title,
                'operatingSystem'       => $op_system,

                'downloadUrl'           => $download_url,
                'screenshot'            => $screenshot,
                'softwareRequirements'  => $requirements,
                'softwareVersion'       => $sof_version,
                'author'                => array(
                                                '@type'         => 'Organization',
                                                'name'          => get_bloginfo('name')
                                            ),

                'applicationCategory'   =>  'DeveloperApplication',
                'aggregateRating'       =>  array(
                                                '@type'         =>  'AggregateRating',
                                                'ratingValue'   =>  $rating,
                                                'ratingCount'   =>  $rating_count
                                            ),
                'offers'                => array(
                                                '@type'         =>  'Offer',
                                                'price'         =>  $price,
                                                'priceCurrency' =>  $currency,
                                            ),

            );

            array_push( $schema, $program );

        }
        elseif( !empty( array_keys($schema_type, 'product') ) && is_singular( array_keys($schema_type, 'product') ) ) {//current post is mapped to product
            
            $brand          = get_post_meta($this->ID,'wpcf-tkt-seo-brand', true);
            $rating         = get_post_meta($this->ID,'wpcf-rating', true);
            $rating_count   = get_post_meta($this->ID,'wpcf-rating-count', true);
            $review_count   = get_post_meta($this->ID,'wpcf-review-count', true);
            $price          = get_post_meta($this->ID,'wpcf-service-full-price', true);
            $stock          = get_post_meta($this->ID,'wpcf-stock', true);//https://schema.org/InStock
            $cond           = get_post_meta($this->ID,'wpcf-condition', true);//'https://schema.org/NewCondition'

            /**
             *@todo the reviews are hardcoded
             */
            $promotion = array(
                '@context'          => 'https://schema.org',
                '@type'             => 'Product',
                'name'              => $this->title,
                'image'             => $image,
                'description'       => wp_strip_all_tags( get_the_excerpt($this->ID), true ),
                'brand'             => $brand,
                'offers'            => array(
                    '@type'         => 'Offer',
                    'url'           => $this->url,
                    'priceCurrency' => $currency,
                    'price'         => $price,
                    'availability'  => $stock,
                    'itemCondition' => $cond,
                ),
                'aggregateRating'   => array(
                    '@type'         => 'AggregateRating',
                    'ratingValue'   => $rating,
                    'bestRating'    => '5',
                    'worstRating'   => '0',
                    'ratingCount'   => $rating_count,
                    'reviewCount'   => $review_count
                ),
                'review'            => array(
                    array(
                        '@type'         => 'Review',    
                        'name'          => 'Some title',
                        'reviewBody'    => 'Excellent product',
                        'reviewRating'  => array(
                            '@type'         => 'Rating',
                            'ratingValue'   => '4.5',
                            'bestRating'    => '5',
                            'worstRating'   => '0'
                        ),
                        'datePublished' => '2020-07-09',
                        'author'        => array(
                            '@type'         => 'Person', 
                            'name'          => 'Dane',
                        ),
                        'publisher'     =>  array(
                            '@type'         => 'Organization', 
                            'name'          => 'Dane'
                        ),
                    ),
                    array(
                        '@type'         => 'Review',
                        'name'          => 'Some title',
                        'reviewBody'    => 'Excellent product',
                        'reviewRating'  => array(
                            '@type'         => 'Rating',
                            'ratingValue'   => '4.5',
                            'bestRating'    => '5',
                            'worstRating'   => '0'
                        ),
                        'datePublished' => '2020-07-09',
                        'author'        => array(
                            '@type'         => 'Person', 
                            'name'          => 'Dane',
                        ),
                        'publisher'     =>  array(
                            '@type'         => 'Organization', 
                            'name'          => 'Dane'
                        ),
                    ),
                ),
            );

            array_push( $schema, $promotion );

        }
        elseif( !empty( array_keys($schema_type, 'article') ) && is_singular( array_keys($schema_type, 'article') ) ){//current post is mapped to article

            $single = array(
                
                '@context'          => "https://schema.org",
                '@type'             => "Article",
                'author'            => array(
                    '@type'     => "Person",
                    'name'      => get_the_author_meta('display_name', get_post($this->ID)->post_author) ? get_the_author_meta('display_name', get_post($this->ID)->post_author) : get_bloginfo('name'),
                    'url'       => get_the_author_meta('display_name', get_post($this->ID)->post_author) ? get_author_posts_url(get_the_author_meta( 'ID', get_post($this->ID)->post_author)) : get_home_url(),
                    ),
                'commentCount'      => get_comments_number(),
                'dateModified'      => get_the_modified_date('Y-m-d', $this->ID),
                'datePublished'     => get_the_date('Y-m-d', $this->ID),
                'description'       => get_the_excerpt(get_post($this->ID)),
                'headline'          => get_the_title(get_post($this->ID)) .' | '. get_bloginfo('name'),
                'mainEntityOfPage'  => get_permalink(get_post($this->ID)),
                'name'              => get_the_title(get_post($this->ID)),
                'publisher'         => array(
                    '@type'     => "Organization",
                    'name'      => get_bloginfo('name'),
                    'sameAs'    => $social,
                    'logo'      => array(
                        '@type' => "ImageObject",
                        'url'   => $logo,
                    ),
                ),
                'url'               => get_permalink($this->ID),
                'image'             => $image,
            );

            array_push( $schema, $single );

        }
        elseif( is_singular( array_keys($schema_type, 'offer') ) ){//current post is mapped as an offer

        }
        elseif( is_singular( array_keys($schema_type, 'rating') ) ){//current post is mapped as a rating

        }
        elseif( is_singular( array_keys($schema_type, 'person') ) ){//current post is mapped as a person

        }
        else{//the current post is neither mapped nor has manual schema
        }

        echo '<script type="application/ld+json">' . json_encode( $schema ) . '</script>';

    }

    public function remove_archive_titles( $title ) {
        if ( is_category() ) {
            $title = single_cat_title( '', false );
        } elseif ( is_tag() ) {
            $title = single_tag_title( '', false );
        } elseif ( is_author() ) {
            $title = '<span class="vcard">' . get_the_author() . '</span>';
        } elseif ( is_year() ) {
            $title = get_the_date( _x( 'Y', 'yearly archives date format' ) );
        } elseif ( is_month() ) {
            $title = get_the_date( _x( 'F Y', 'monthly archives date format' ) );
        } elseif ( is_day() ) {
            $title = get_the_date( _x( 'F j, Y', 'daily archives date format' ) );
        } elseif ( is_tax( 'post_format' ) ) {
            if ( is_tax( 'post_format', 'post-format-aside' ) ) {
                $title = _x( 'Asides', 'post format archive title' );
            } elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
                $title = _x( 'Galleries', 'post format archive title' );
            } elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
                $title = _x( 'Images', 'post format archive title' );
            } elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
                $title = _x( 'Videos', 'post format archive title' );
            } elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
                $title = _x( 'Quotes', 'post format archive title' );
            } elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
                $title = _x( 'Links', 'post format archive title' );
            } elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
                $title = _x( 'Statuses', 'post format archive title' );
            } elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
                $title = _x( 'Audio', 'post format archive title' );
            } elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
                $title = _x( 'Chats', 'post format archive title' );
            }
        } elseif ( is_post_type_archive() ) {
            $title = post_type_archive_title( '', false );
        } elseif ( is_tax() ) {
            $title = single_term_title( '', false );
        } else {
            $title = __( 'Archives' );
        }
        return $title;
    }

    public function setup_current_object_data(){
        $this->get_current_object_data();
    }


}

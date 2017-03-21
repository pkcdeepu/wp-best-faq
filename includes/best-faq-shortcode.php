<?php 
 /**
 * Class to handle the output of the FAQ items
 *
 * @since   1.0.0
 */
class Best_FAQ_Display {

    /**
     * Array of query defaults
     *
     * @since   1.0.0
     * @access  protected
     * @var     array       $defaults    Plugin query defaults
     */
    protected $defaults;

    /**
     * Initialize the class and set its properties.
     *
     * @since   1.0.0
     */
    public function __construct() {
        $this->defaults = array(
            'p'                 => '',
            'order'             => 'ASC',
            'orderby'           => 'title',
            'skip_group'        => false,
            'style'             => 'toggle',
            'posts_per_page'    => -1,
            'nopaging'          => true,
            'group'             => ''
        );
    }

    /**
     * Get plugin query defaults
     *
     * @since   1.6.0
     * @return  array       filterable query defaults
     */
    public function getdefaults() {
        return apply_filters( 'best_faq_defaults', $this->defaults );
    }

    /**
     * Get our FAQ data
     *
     * @since   1.2.0
     * @version 1.6.1
     *
     * @param   array   $args
     * @param   bool    $echo   Echo or Return the data
     *
     * @return  string          FAQ information for display
     */
    public function loop( $args, $echo = false ) {
        // Merge incoming args with the class defaults
        $args = wp_parse_args( $args, $this->getdefaults() );
	    // Container
        $html = '';

        // Do we have an accordion?
        $args['style'] == 'accordion' ? $accordion = true : $accordion = false;


            // Set up our standard query args.
            $q = new WP_Query( array(
                'post_type'         => 'faq',
                'p'                 => $args['p'],
                'order'             => $args['order'],
                'orderby'           => $args['orderby'],
                'posts_per_page'    => $args['posts_per_page']
            ) );


            if ( $q->have_posts() ) {

                if ( $accordion )
                    $html .= '<div class="best-faq-accordion-wrap">';

                while ( $q->have_posts() ) : $q->the_post();

                    if ( $accordion )
                        $html .= $this->wp_best_accordion_output();
                    else
                        $html .= $this->wp_best_toggle_output();

                endwhile;

                if ( $accordion )
                    $html .= '</div>';
            } // end have_posts()

            wp_reset_postdata();
        

        // Allow complete override of the FAQ content
        $html = apply_filters( 'best_faq_return', $html, $args );

        if ( $echo === true )
            echo $html;
        else
            return $html;
    }

    /**
     * Output the FAQs in an accordion style
     *
     * @since   1.0.0
     * @version 1.0.0
     * @param   bool    $echo       echo or return the results
     * @return  string  $html     FAQs in an accordion configuration
     */
    private function wp_best_accordion_output( $echo = false ) {
        $html = '';

        // Set up our anchor link
        $link = 'faq-' . sanitize_html_class( get_the_title() );

        $html .= '<div id="faq-' . get_the_id() . '" class="best-faq-accordion-title">';
        $html .= get_the_title() . '</div>';
        $html .= '<div id="' . $link . '" class="best-faq-accordion-content">' . apply_filters( 'the_content', get_the_content() );
        $html .= $this->wp_best_return_to_top( $link );
        $html .= '</div>';

        // Allows a user to completely overwrite the output
        $html = apply_filters( 'best_faq_accordion_output', $html );

        if ( $echo === true )
            echo $html;
        else
            return $html;
    }

    /**
     * Output the FAQs in a toggle style
     *
     * @since   1.5.0
     * @param   bool    $echo       echo or return the results
     * @return  string  $html     FAQs in a toggle configuration
     */
    private function wp_best_toggle_output( $echo = false ) {
        $html = '';

        // Grab our metadata
        $lo = get_post_meta( get_the_id(), '_acf_open', true );

        // If Open on Load checkbox is true
        $lo == true ? $lo = ' faq-open' : $lo = ' faq-closed';

        // Set up our anchor link
        $link = 'faq-' . sanitize_html_class( get_the_title() );

        $html .= '<div id="faq-' . get_the_id() . '" class="best-faq-wrap">';
        $html .= '<div id="' . $link . '" class="best-faq-title' . $lo . '">' . get_the_title() . '</div>';
        $html .= '<div class="best-faq-content' . $lo . '">' . apply_filters( 'the_content', get_the_content() );

        $html .= $this->wp_best_return_to_top( $link );

        $html .= '</div>'; // faq-content
        $html .= '</div>'; // faq-wrap

        // Allows a user to completely overwrite the output
        $html = apply_filters( 'best_faq_toggle_output', $html );

        if ( $echo === true )
            echo $html;
        else
            return $html;
    }

    /**
     * Provide a hyperlinked url to return to the top of the current FAQ
     *
     * @since   1.5.0
     * @param   string  $link       The faq link to be hyperlinked
     * @param   bool    $echo       Echo or return the results
     * @return  string  $html     Hyperlinked "Return to Top" link
     */
    private function wp_best_return_to_top( $link, $echo = false ) {
        $html = '';

        // Grab our metadata
        $rtt = get_post_meta( get_the_id(), '_acf_rtt', true );

        // If Return to Top checkbox is true
        if ( $rtt && $link ) {
            $rtt_text = __( 'Return to Top', 'best-faq' );
            $rtt_text = apply_filters( 'best_faq_return_to_top_text', $rtt_text );

            $html .= '<div class="best-faq-to-top"><a href="#' . $link . '">' . $rtt_text . '</a></div>';
        }

        if ( $echo === true )
            echo $html;
        else
            return $html;
    }

}

	
	

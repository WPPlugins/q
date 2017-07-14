<?php

/*
 * Filter CPT by Taxonomy
 * 
 * @link        http://en.bainternet.info/2013/add-taxonomy-filter-to-custom-post-type
 * @since       0.1
 */

if (!class_exists('Tax_CPT_Filter'))
{

    /**
    * Tax CTP Filter Class 
    * Simple class to add custom taxonomy dropdown to a custom post type admin edit list
    * @author Ohad Raz <admin@bainternet.info>
    * @version 0.1
    */
    class Tax_CPT_Filter
    {
        
        /**
         * __construct 
         * @author Ohad Raz <admin@bainternet.info>
         * @since 0.1
         * @param array $cpt [description]
         */
        function __construct( $cpt = array() )
        {
            
            // assign CPT to property ##
            $this->cpt = $cpt;
            
            // Adding a Taxonomy Filter to Admin List for a Custom Post Type
            add_action( 'restrict_manage_posts', array( $this, 'my_restrict_manage_posts' ));
            
        }
  
        /**
         * my_restrict_manage_posts  add the slelect dropdown per taxonomy
         * @author Ohad Raz <admin@bainternet.info>
         * @since 0.1
         * @return void
         */
        public function my_restrict_manage_posts() 
        {
            // only display these taxonomy filters on desired custom post_type listings
            global $typenow;
            $types = array_keys( $this->cpt );
            if ( in_array( $typenow, $types ) ) 
            {
                // create an array of taxonomy slugs you want to filter by - if you want to retrieve all taxonomies, could use get_taxonomies() to build the list
                $filters = $this->cpt[$typenow];
                foreach ( $filters as $tax_slug ) 
                {
                    
                    // retrieve the taxonomy object
                    $tax_obj = get_taxonomy( $tax_slug );
                    $tax_name = $tax_obj->labels->name; // get the tax name ##
  
                    // output html for taxonomy dropdown filter
                    echo "<select name='".strtolower($tax_slug)."' id='".strtolower($tax_slug)."' class='postform'>";
                    
                    // defalut options ##
                    echo "<option value=''>Show All $tax_name</option>";
                    
                    #$this->generate_taxonomy_options( $tax_slug, 0, 0, ( isset($_GET[strtolower($tax_slug)]) ? $_GET[strtolower($tax_slug)] : null ) );
                    
                    // build the select options ##
                    $this->generate_taxonomy_options( 
                        $tax_slug // tax_slug ##
                        , 0 // parent ID ##
                        , 0 // level ##
                        , ( isset( $_GET[strtolower($tax_slug)] ) ? $_GET[strtolower($tax_slug)] : null ) // $selected tax_slug OR null ##
                    );
                    
                    // close the select ##
                    echo "</select>";
                    
                }
            }
        }
         
        
        
        /**
         * generate_taxonomy_options generate dropdown
         * 
         * @author Ohad Raz <admin@bainternet.info>
         * @since 0.1
         * @param  string   $tax_slug s
         * @param  string   $parent   
         * @param  integer  $level    
         * @param  string   $selected 
         * @return void            
         */
        public function generate_taxonomy_options( $tax_slug, $parent = null, $level = 0, $selected = null ) 
        {
            
            $args = array( 
                'hide_empty'    => 0 // show empty terms ##
                #,'get'          => 'all'
            ); 
            
            // add the parent to the args list ##
            if( ! is_null( $parent ) ) {
                $args['parent'] = $parent;
            }

            #echo '$args'; var_dump( $args );
            $terms = get_terms( $tax_slug, $args );
            #echo '$terms'; var_dump( $terms );
            
            // tabs for hierarchial display ##
            $tab = '';
            for( $i=0; $i < $level; $i++ ) {
                $tab.='--';
            }
            
            // loop over each term ##
            foreach ( $terms as $term ) {
                
                // output each select option line, check against the last $_GET to show the current option selected
                echo '<option value='. $term->slug, $selected == $term->slug ? ' selected="selected"' : '','>' .$tab. $term->name .' (' . $term->count .')</option>';
                
                // go back for more options ##
                $this->generate_taxonomy_options( 
                    $tax_slug // tax_slug ##
                    , $term->term_id // parent ID ##
                    , $level+1 // level + 1 ##
                    , $selected // select
                );
                
            }
  
        }
        
        
    }//end class
    
}//end if
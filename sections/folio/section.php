<?php
/*
Section: Folio
Author: Aleksander Hansson
Author URI: http://ahansson.com
Workswith: main, templates
Class Name: Folio
Cloning: true
Demo: http://folio.ahansson.com
v3: true
*/

class Folio extends PageLinesSection {

	var $ptID = 'folio';
	var $taxID = 'folio-cat';

	function section_persistent(){

		$this->post_type_setup();
		$this->post_meta_setup();

	}

	function section_head() {
		$clone_id = $this->get_the_id();

		$prefix = ($clone_id != '') ? 'clone'.$clone_id : '';

		?>

			<script type="text/javascript">
				jQuery(document).ready(function(){
					jQuery('.folio-modal').appendTo(jQuery('body'))
				})
			</script>

		<?php
	}

	function section_template() {

		$category = ( ploption( 'folio_tax_select', $this->oset ) ) ? ploption( 'folio_tax_select', $this->oset ) : null;
//		$orderby = ( ploption( 'ap_playlist_orderby', $this->oset ) ) ? ploption( 'ap_playlist_orderby', $this->oset ) : 'menu_order';
//		$order = ( ploption( 'ap_playlist_order', $this->oset ) ) ? ploption( 'ap_playlist_order', $this->oset ) : 'ASC';

		$args = array(
			'post_type'	=> $this->ptID,
			'post_status'   => 'publish',
			'nopaging' => true,
			'post_per_page' => 99999, //needs to be something unreal
//			'orderby' => $orderby,
//			'order'=> $order,
			$this->taxID => $category,
		);

		$loop = new WP_Query( $args );

		$clone_id = $this->get_the_id();

		$prefix = ($clone_id != '') ? 'clone'.$clone_id : '';

		if ( $loop->have_posts() ) {

			?>

				<div class="folio-wrap folio-<?php echo $prefix;?>">
					<ul class="folio-container row">
						<?php

							while ( $loop->have_posts() ) : $loop->the_post();

								global $post;

								$link = ( get_post_meta( $post->ID,'single_folio_link', $this->oset ) );

								$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large' );

								$height = ( ploption( 'folio_height', $this->oset ) ) ? ploption( 'folio_height', $this->oset ) : '250';

								?>
									<li class="span4 folio-<?php the_ID(); ?>">
										<div class="folio-screenshot" style="height:<?php echo $height; ?>px;">
											<img class="center" src="<?php echo $thumb['0'] ?>" width="500" height="<?php echo $height; ?>">
											<div class="folio-overlay span4">
												<div class="folio-overlay-content">
													<div class="folio-title">
														<h4><?php echo get_the_title(); ?></h4>
													</div>
													<div class="folio-buttons">
														<?php
															if ($link) {
																?>
																	<a href="<?php echo $link; ?>" class="btn btn-primary"><?php echo __( 'Link', 'folio' ); ?></a>
																<?php
															}

															if ( get_the_content() ) {
																?>
																	<!-- Button to trigger modal -->
																	<a href="#folio-modal-<?php the_ID(); ?>" role="button" class="btn btn-primary" data-toggle="modal"><?php echo __( 'Details', 'folio' ); ?></a>
																<?php
															}
														?>
													</div>
												</div>
											</div>
											<?php
												if ( get_the_content() ) {
													?>
														<!-- Modal -->
														<div id="folio-modal-<?php the_ID(); ?>" class="modal hide fade folio-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
															<div class="modal-header">
																<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
																<h3 id="myModalLabel"><?php echo get_the_title(); ?></h3>
															</div>
														  	<div class="modal-body">
														    	<?php echo do_shortcode( get_the_content() ); ?>
														  	</div>
														  	<div class="modal-footer">
														    	<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __( 'Close', 'folio' ); ?></button>
														  	</div>
														</div>
													<?php
												}
											?>
										</div>
									</li>
								<?php

							endwhile;

							wp_reset_query();

						?>
					</ul>
				</div>
			<?php

		} else {
			?>
				<p class="no-posts"><?php __('There is no Folios to show!', 'folio'); ?></p>
			<?php
		}

	}

	function section_optionator($settings) {
		$settings = wp_parse_args($settings, $this->optionator_default);

		$tab = array(

			'folio_help'  => array(
				'title'  => __( 'How To Use', 'folio' ),
				'type'   => 'help',
				'exp'   => __( '1. Go to Wordpress backend and create a new Folio. </br></br>2. Input Title, Content (Optional), and a Link to the Folio (Optional). You also have to set a Thumbnail for the Folio. </br></br>3. Choose Categories for your Folio . </br></br>4. Go back to Folio\'s Section options and choose which category to show. Here you can also set the thumbnail height.', 'folio' ),
			),

			'folio_tax_select' => array(
				'type' 			=> 'select_taxonomy',
				'taxonomy_id'	=> $this->taxID,
				'inputlabel'	=> __( 'Category To Show', 'folio' ),
				'title'	=> __( 'Category', 'folio' )
			),

			'folio_height'  => array(
				'inputlabel'  => __( 'Folio Thumbnail Height In px', 'folio' ),
				'type'   => 'text',
				'title'   => __( 'Image Dimension', 'folio' ),
			),

		);

		$tab_settings = array(
			'id'		=> 'folio_meta',
			'name'	=> 'Folio',
			'icon'	=> $this->icon,
			'clone_id'  => $settings['clone_id'],
			'active'	=> $settings['active']
		);

		register_metatab( $tab_settings, $tab);
	}

	function post_type_setup(){

		$args = array(
			'label'			=> __('Folios', 'folio'),
			'singular_label'	=> __('Folio', 'folio'),
			'description'	=> __('For creating Folios', 'folio'),
			'menu_icon'		=> $this->icon,
			'supports'		=> array('title', 'editor', 'thumbnail'),
		);
		$taxonomies = array(
			$this->taxID => array(
				"label" => __('Categories', 'folio'),
				"singular_label" => __('Category', 'folio'),
			)
		);

		$columns = array(
			"cb"			=> "<input type=\"checkbox\" />",
			"title"		=> __('Title', 'folio'),
			"description"   => __('Text', 'folio'),
			"event-categories"	=> __('Categories', 'folio'),
		);

		$this->post_type = new PageLinesPostType( $this->ptID, $args, $taxonomies,$columns,array(&$this, 'column_display'));

	}


	function post_meta_setup(){

		$type_meta_array = array(

			'single_folio_help'  => array(
				'title'  => __( 'How To Use', 'folio' ),
				'type'   => 'help',
				'exp'   => __( '1. Go to Wordpress backend and create a new Folio. </br></br>2. Input Title, Content (Optional), and a Link to the Folio (Optional). You also have to set a Thumbnail for the Folio. </br></br>3. Choose Categories for your Folio . </br></br>4. Go back to Folio\'s Section options and choose which category to show. Here you can also set the thumbnail height.', 'folio' ),
			),

			'single_folio_options' => array(
				'type' => 'multi_option',
				'title' => __('Folio settings', 'folio'),
				'selectvalues' => array(

					'single_folio_link'  => array(
						'inputlabel'  => __( 'Link to project', 'folio' ),
						'type'   => 'text',
					),
				),
			),

//			'single_ap_directions'	=> array(
//				'type'		=> '',
//				'title'	=> __('<strong style="display:block;font-size:16px;color:#eaeaea;text-shadow:0 1px 0 black;padding:7px 7px 5px;background:#333;margin-top:5px;border-radius:3px;border:1px solid white;letter-spacing:0.1em;box-shadow:inset 0 0 3px black;">HOW TO USE:</strong>', 'folio'),
//				'shortexp'   => __('', 'folio'),
//			),
		);

		$post_types = array($this->id);

		$type_metapanel_settings = array(
			'id'		=> 'folio-metapanel',
			'name'	=> 'Folio Options',
			'posttype'  => $post_types,
		);

		global $p_meta_panel;

		$p_meta_panel =  new PageLinesMetaPanel( $type_metapanel_settings );

		$type_metatab_settings = array(
			'id'		=> 'folio-type-metatab',
			'name'	=> 'Folio Options',
			'icon'	=> $this->icon
		);

		$p_meta_panel->register_tab( $type_metatab_settings, $type_meta_array );

	}

	function column_display($column){
        global $post;

        switch ($column){
            case "description":
                the_excerpt();
                break;
            case "event-categories":
                $this->get_tags();
                break;
        }
    }

    // fetch the tags for the columns in admin
    function get_tags() {
        global $post;

        $terms = wp_get_object_terms($post->ID, $this->taxID);
        $terms = array_values($terms);

        for($term_count=0; $term_count<count($terms); $term_count++) {

            echo $terms[$term_count]->slug;

            if ($term_count<count($terms)-1){
                echo ', ';
            }
        }
    }

}

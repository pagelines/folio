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
Filter: format
*/

class Folio extends PageLinesSection {

	var $ptID = 'folio';
	var $taxID = 'folio-cat';

	function section_persistent(){

		$this->post_type_setup();

		if(! class_exists( 'CMB_Meta_Box' ) ) {
			require_once( 'custom-meta/custom-meta-boxes.php' );
		}

		add_filter( 'cmb_meta_boxes', array( &$this, 'custom_meta' ) );

	}

	function section_head() {
		$clone_id = $this->get_the_id();

		$prefix = ($clone_id != '') ? 'clone'.$clone_id : '';

		$open_in_new = ( $this->opt( 'single_open_in_new', $this->oset ) );

		?>

			<script type="text/javascript">
				jQuery(document).ready(function(){
					jQuery('.folio-modal').appendTo(jQuery('body'));
					<?php
						if($open_in_new) {
							?>
								jQuery('a.btn-folio-link-<?php echo $prefix; ?>').click(function(){
							    	window.open(this.href);
								    return false;
								});
							<?php
						}
					?>
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

							$i = 1;

							while ( $loop->have_posts() ) : $loop->the_post();

								global $post;

								$link = ( get_post_meta( $post->ID,'single_folio_link', $this->oset ) );

								$button = ( $this->opt( 'folio_button', $this->oset ) ) ? $this->opt( 'folio_button', $this->oset ) : 'btn-primary';

								$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large' );

								$height = ( $this->opt( 'folio_height', $this->oset ) ) ? $this->opt( 'folio_height', $this->oset ) : '250';

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
																	<a href="<?php echo $link; ?>" class="btn <?php echo $button; ?> btn-folio-link-<?php echo $prefix; ?>"><?php echo __( 'Link', 'folio' ); ?></a>
																<?php
															}

															if ( get_the_content() ) {
																?>
																	<a href="#folio-modal-<?php the_ID(); ?>" role="button" class="btn <?php echo $button; ?> btn-folio-details" data-toggle="modal"><?php echo __( 'Details', 'folio' ); ?></a>
																<?php
															}
														?>
													</div>
												</div>
											</div>
											<?php
												if ( get_the_content() ) {
													?>
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

								if($i % 3 == 0) {echo '</ul><ul class="folio-container row">';}

							$i++;

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

	function section_opts() {

		$options = array();

		$how_to_use = __( '
			<strong>Read the instructions below before asking for additional help:</strong>
			</br></br>
			<strong>1.</strong> Go to Wordpress backend and create a new Folio. </br></br>
			<strong>2.</strong> Input Title, Content (Optional), and a Link to the Folio (Optional). You also have to set a Thumbnail for the Folio. </br></br>
			<strong>3.</strong> Choose Categories for your Folio . </br></br>
			<strong>4.</strong> Go back to Folio\'s Section options and choose which category to show. Here you can also set the thumbnail height.
			</br></br>
			<div class="row zmb">
				<div class="span6 tac zmb">
					<a class="btn btn-info" href="http://forum.pagelines.com/71-products-by-aleksander-hansson/" target="_blank" style="padding:4px 0 4px;width:100%"><i class="icon-ambulance"></i>          Forum</a>
				</div>
				<div class="span6 tac zmb">
					<a class="btn btn-info" href="http://betterdms.com" target="_blank" style="padding:4px 0 4px;width:100%"><i class="icon-align-justify"></i>          Better DMS</a>
				</div>
			</div>
			<div class="row zmb" style="margin-top:4px;">
				<div class="span12 tac zmb">
					<a class="btn btn-success" href="http://shop.ahansson.com" target="_blank" style="padding:4px 0 4px;width:100%"><i class="icon-shopping-cart" ></i>          My Shop</a>
				</div>
			</div>
		', 'folio' );

		$options[] = array(
			'key' => 'folio_help',
			'type'     => 'template',
			'template'      => do_shortcode( $how_to_use ),
			'title' =>__( 'How to use:', 'folio' ) ,
		);

		$options[] = array(
			'key' => 'folio_settings',
			'title' => __( 'Folio Settings', 'folio' ),
			'type'	=> 'multi',
			'opts'	=> array(

				array(
					'key' => 'folio_tax_select',
					'type' 			=> 'select_taxonomy',
					'post_type'	=> 'folio',
					'label'	=> __( 'Category To Show', 'folio' ),
				),

				array(
					'key' => 'folio_height',
					'label'  => __( 'Folio Thumbnail Height In px', 'folio' ),
					'type'   => 'text',
				),

				array(
					'key' => 'folio_button',
					'label'  => __( 'Choose button type', 'folio' ),
					'type'   => 'select_button',
				),
				array(
					'key' => 'single_open_in_new',
					'label'  => __( 'Open link in new window?', 'folio' ),
					'type'   => 'check',
				),
			)

		);

		return $options;
	}

	function post_type_setup(){

		$args = array(
			'label'			=> __('Folios', 'folio'),
			'singular_label'	=> __('Folio', 'folio'),
			'description'	=> __('For creating Folios', 'folio'),
			'menu_icon'		=> $this->base_url.'/icon.png',
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

	function custom_meta( array $meta_boxes ) {

	    $meta_boxes[] = array(
	        'title' => 'Folio Setup',
	        'pages' => 'folio',
	        'fields' => array(
	        	array(
				    'id'   => 'single_folio_link',
				    'name' => __( 'Link to project', 'folio' ),
				    'type' => 'text_url',
				    'cols' => 12,
				),
			),
	    );

	    return $meta_boxes;

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

<?php // About Ashe

// Add About Ashe Page
function ashe_about_page() {
	add_theme_page( esc_html__( 'About Ashe', 'ashe' ), esc_html__( 'About Ashe', 'ashe' ), 'edit_theme_options', 'about-ashe', 'ashe_about_page_output' );
}
add_action( 'admin_menu', 'ashe_about_page' );

// Render About Ashe HTML
function ashe_about_page_output() {
?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Welcome to Ashe!', 'ashe' ); ?></h1>
		<p class="welcome-text">
			<?php esc_html_e( 'Ashe is free personal and multi-author WordPress Blog theme. It\'s perfect for any kind of blog: personal, multi-author, food, lifestyle, etc... Is fully Responsive and Retina Display ready, clean, modern and minimal. Ashe is WooCommerce compatible, also has RTL support and for sure it\'s SEO friendly. Coded with latest WordPress\' standards.', 'ashe' ); ?>
		</p>

		<!-- Tabs -->
		<?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'ashe_tab_1'; ?>  

		<div class="nav-tab-wrapper">
			<a href="?page=about-ashe&tab=ashe_tab_1" class="nav-tab <?php echo $active_tab == 'ashe_tab_1' ? 'nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Getting Started', 'ashe' ); ?>
			</a>
			<a href="?page=about-ashe&tab=ashe_tab_2" class="nav-tab <?php echo $active_tab == 'ashe_tab_2' ? 'nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Recommended Plugins', 'ashe' ); ?>
			</a>
			<a href="?page=about-ashe&tab=ashe_tab_3" class="nav-tab <?php echo $active_tab == 'ashe_tab_3' ? 'nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Support', 'ashe' ); ?>
			</a>
			<a href="?page=about-ashe&tab=ashe_tab_4" class="nav-tab <?php echo $active_tab == 'ashe_tab_4' ? 'nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Free vs Pro', 'ashe' ); ?>
			</a>
		</div>

		<!-- Tab Content -->
		<?php if ( $active_tab == 'ashe_tab_1' ) : ?>

			<div class="three-columns-wrap">

				<br>

				<div class="column-wdith-3">
					<h3><?php esc_html_e( 'Theme Documentation', 'ashe' ); ?></h3>
					<p>
						<?php esc_html_e( 'Highly recommended to begin with this, read the full theme documentation to understand the basics and even more details about how to use Ashe. It is worth to spend 10 minutes and know almost everything about the theme.', 'ashe' ); ?>
					</p>
					<a target="_blank" href="<?php echo esc_url('https://wp-royal.com/themes/ashe/docs/?ref=ashe-free-backend-about-docs/'); ?>" class="button button-primary"><?php esc_html_e( 'Read Documentation', 'ashe' ); ?></a>
				</div>

				<div class="column-wdith-3">
					<h3><?php esc_html_e( 'Demo Content', 'ashe' ); ?></h3>
					<p>
						<?php esc_html_e( 'If you are a WordPress beginner it\'s highly recomended to install the Demo Content. This file includes: Menus, Posts, Pages, Widgets, etc. Read More about demo import in the ', 'ashe' ); ?>
						<a href="<?php echo esc_url('https://wp-royal.com/themes/ashe/docs/?ref=ashe-free-backend-about-docs/#demo'); ?>" target="_blank"><?php esc_html_e( 'Theme Documentation.', 'ashe' ); ?></a>
					</p>
					<a target="_blank" target="_blank" href="<?php echo esc_url('https://wp-royal.com/themes/ashe/democontent/ashe_free_demo_content.html?ref=ashe-free-backend-about-demoxml-btn'); ?>" class="button button-primary"><?php esc_html_e( 'Download Import File', 'ashe' ); ?></a>
				</div>

				<div class="column-wdith-3">
					<h3><?php esc_html_e( 'Theme Customizer', 'ashe' ); ?></h3>
					<p>
					<?php esc_html_e( 'All theme options are located here. After reading the Theme Documentation we recommend you to open the Theme Customizer and play with some options. You will enjoy it.', 'ashe' ); ?>
					</p>
					<a target="_blank" href="<?php echo esc_url( wp_customize_url() );?>" class="button button-primary"><?php esc_html_e( 'Customize Your Site', 'ashe' ); ?></a>
				</div>

			</div>

			<div class="four-columns-wrap">
			
				<h2 id="ashe-predefined-styles"><?php esc_html_e( 'Ashe Pro - Predefined Styles', 'ashe' ); ?></h2>
				<p>
					<?php esc_html_e( 'Ashe Pro\'s powerful setup allows you to easily create unique looking sites. Here are a few included examples that can be installed with one click in the Pro Version.', 'ashe' ); ?>
					<a target="_blank" href="http://wp-royal.com/themes/ashe/docs/?ref=ashe-free-backend-about-predefined-styles#predefined"><?php esc_html_e( 'Ashe Pro Theme.', 'ashe' ); ?></a>
					<?php esc_html_e( 'More details in the theme Documentation.', 'ashe' ); ?>
				</p>

				<div class="column-wdith-4">
					<div class="active-style"><?php esc_html_e( 'Active', 'ashe' ); ?></div>
					<img src="<?php echo get_template_directory_uri() . '/assets/images/img1.jpg'; ?>" alt="">
					<div>
						<h2><?php esc_html_e( 'Main', 'ashe' ); ?></h2>
						<a href="<?php echo esc_url('http://wp-royal.com/themes/ashe-pro/demo/?ref=ashe-free-backend-about-predefined-styles'); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'Live Preview', 'ashe' ); ?></a>
					</div>
				</div>
				<div class="column-wdith-4">
					<img src="<?php echo get_template_directory_uri() . '/assets/images/food.jpg'; ?>" alt="">
					<div>
						<h2><?php esc_html_e( 'Food', 'ashe' ); ?></h2>
						<a href="<?php echo esc_url('http://wp-royal.com/themes/ashe-pro/food/?ref=ashe-free-backend-about-predefined-styles'); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'Live Preview', 'ashe' ); ?></a>
					</div>
				</div>
				<div class="column-wdith-4">
					<img src="<?php echo get_template_directory_uri() . '/assets/images/lifestyle.jpg'; ?>" alt="">
					<div>
						<h2><?php esc_html_e( 'Lifestyle', 'ashe' ); ?></h2>
						<a href="<?php echo esc_url('http://wp-royal.com/themes/ashe-pro/lifestyle/?ref=ashe-free-backend-about-predefined-styles'); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'Live Preview', 'ashe' ); ?></a>
					</div>
				</div>
				<div class="column-wdith-4">
					<img src="<?php echo get_template_directory_uri() . '/assets/images/img2.jpg'; ?>" alt="">
					<div>
						<h2><?php esc_html_e( 'Dark', 'ashe' ); ?></h2>
						<a href="<?php echo esc_url('http://wp-royal.com/themes/ashe-pro/color-black/?ref=ashe-free-backend-about-predefined-styles'); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'Live Preview', 'ashe' ); ?></a>
					</div>
				</div>
				<div class="column-wdith-4">
					<img src="<?php echo get_template_directory_uri() . '/assets/images/img7.jpg'; ?>" alt="">
					<div>
						<h2><?php esc_html_e( 'Style 1', 'ashe' ); ?></h2>
						<a href="<?php echo esc_url('http://wp-royal.com/themes/ashe-pro/typography-v2/?ref=ashe-free-backend-about-predefined-styles'); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'Live Preview', 'ashe' ); ?></a>
					</div>
				</div>
				<div class="column-wdith-4">
					<img src="<?php echo get_template_directory_uri() . '/assets/images/img12.jpg'; ?>" alt="">
					<div>
						<h2><?php esc_html_e( 'Style 2', 'ashe' ); ?></h2>
						<a href="<?php echo esc_url('http://wp-royal.com/themes/ashe-pro/sample-v3/?ref=ashe-free-backend-about-predefined-styles'); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'Live Preview', 'ashe' ); ?></a>
					</div>
				</div>
				<div class="column-wdith-4">
					<img src="<?php echo get_template_directory_uri() . '/assets/images/img5.jpg'; ?>" alt="">
					<div>
						<h2><?php esc_html_e( 'Style 3', 'ashe' ); ?></h2>
						<a href="<?php echo esc_url('http://wp-royal.com/themes/ashe-pro/columns2-sidebar/?ref=ashe-free-backend-about-predefined-styles'); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'Live Preview', 'ashe' ); ?></a>
					</div>
				</div>
				<div class="column-wdith-4">
					<img src="<?php echo get_template_directory_uri() . '/assets/images/img3.jpg'; ?>" alt="">
					<div>
						<h2><?php esc_html_e( 'Style 4', 'ashe' ); ?></h2>
						<a href="<?php echo esc_url('http://wp-royal.com/themes/ashe-pro/sample-v5/?ref=ashe-free-backend-about-predefined-styles'); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'Live Preview', 'ashe' ); ?></a>
					</div>
				</div>
				<div class="column-wdith-4">
					<img src="<?php echo get_template_directory_uri() . '/assets/images/img4.jpg'; ?>" alt="">
					<div>
						<h2><?php esc_html_e( 'Style 5', 'ashe' ); ?></h2>
						<a href="<?php echo esc_url('http://wp-royal.com/themes/ashe-pro/color-colorful/?ref=ashe-free-backend-about-predefined-styles'); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'Live Preview', 'ashe' ); ?></a>
					</div>
				</div>
				<div class="column-wdith-4">
					<img src="<?php echo get_template_directory_uri() . '/assets/images/img6.jpg'; ?>" alt="">
					<div>
						<h2><?php esc_html_e( 'Style 6', 'ashe' ); ?></h2>
						<a href="<?php echo esc_url('http://wp-royal.com/themes/ashe-pro/columns4/?ref=ashe-free-backend-about-predefined-styles'); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'Live Preview', 'ashe' ); ?></a>
					</div>
				</div>
				<div class="column-wdith-4">
					<img src="<?php echo get_template_directory_uri() . '/assets/images/img8.jpg'; ?>" alt="">
					<div>
						<h2><?php esc_html_e( 'Style 7', 'ashe' ); ?></h2>
						<a href="<?php echo esc_url('http://wp-royal.com/themes/ashe-pro/columns3-sidebar/?ref=ashe-free-backend-about-predefined-styles'); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'Live Preview', 'ashe' ); ?></a>
					</div>
				</div>
				<div class="column-wdith-4">
					<img src="<?php echo get_template_directory_uri() . '/assets/images/img9.jpg'; ?>" alt="">
					<div>
						<h2><?php esc_html_e( 'Style 8', 'ashe' ); ?></h2>
						<a href="<?php echo esc_url('http://wp-royal.com/themes/ashe-pro/color-black-white/?ref=ashe-free-backend-about-predefined-styles'); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'Live Preview', 'ashe' ); ?></a>
					</div>
				</div>
				<div class="column-wdith-4">
					<img src="<?php echo get_template_directory_uri() . '/assets/images/img10.jpg'; ?>" alt="">
					<div>
						<h2><?php esc_html_e( 'Style 9', 'ashe' ); ?></h2>
						<a href="<?php echo esc_url('http://wp-royal.com/themes/ashe-pro/columns3-nsidebar/?ref=ashe-free-backend-about-predefined-styles'); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'Live Preview', 'ashe' ); ?></a>
					</div>
				</div>
				<div class="column-wdith-4">
					<img src="<?php echo get_template_directory_uri() . '/assets/images/img11.jpg'; ?>" alt="">
					<div>
						<h2><?php esc_html_e( 'Style 10', 'ashe' ); ?></h2>
						<a href="<?php echo esc_url('http://wp-royal.com/themes/ashe-pro/columns2-nsidebar/?ref=ashe-free-backend-about-predefined-styles'); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'Live Preview', 'ashe' ); ?></a>
					</div>
				</div>

			</div>

		<?php elseif ( $active_tab == 'ashe_tab_2' ) : ?>
			
			<div class="three-columns-wrap">
				
				<br>
				<p><?php esc_html_e( 'Recommended Plugins are fully supported by Ashe theme, they are styled to fit the theme design and performing well. Not mandatory, but may be usefl.', 'ashe' ); ?></p>
				<br>

				<?php

				// WooCommerce
				ashe_recommended_plugin( 'woocommerce', 'woocommerce', esc_html__( 'WooCommerce', 'ashe' ), esc_html__( 'WooCommerce is a powerful, extendable eCommerce plugin that helps you sell anything. Beautifully.', 'ashe' ) );

				// MailPoet 2
				ashe_recommended_plugin( 'wysija-newsletters', 'index', esc_html__( 'MailPoet 2', 'ashe' ), esc_html__( 'Create and send newsletters or automated emails. Capture subscribers with a widget. Import and manage your lists. MailPoet is made with love.', 'ashe' ) );

				// Contact Form 7
				ashe_recommended_plugin( 'contact-form-7', 'wp-contact-form-7', esc_html__( 'Contact Form 7', 'ashe' ), esc_html__( 'Just another contact form plugin. Simple but flexible.', 'ashe' ) );

				// Recent Posts Widget
				ashe_recommended_plugin( 'recent-posts-widget-with-thumbnails', 'recent-posts-widget-with-thumbnails', esc_html__( 'Recent Posts Widget With Thumbnails', 'ashe' ), esc_html__( 'Small and fast plugin to display in the sidebar a list of linked titles and thumbnails of the most recent postings.', 'ashe' ) );

				// Instagram Widget
				ashe_recommended_plugin( 'wp-instagram-widget', 'wp-instagram-widget', esc_html__( 'WP Instagram Widget', 'ashe' ), esc_html__( 'A WordPress widget for showing your latest Instagram photos.', 'ashe' ) );

				// Ajax Thumbnail Rebuild
				ashe_recommended_plugin( 'ajax-thumbnail-rebuild', 'ajax-thumbnail-rebuild', 'Ajax Thumbnail Rebuild', 'AJAX Thumbnail Rebuild allows you to rebuild all thumbnails at once without script timeouts on your server.' );

				// Facebook Widget
				ashe_recommended_plugin( 'facebook-pagelike-widget', 'facebook_widget', esc_html__( 'Facebook Widget', 'ashe' ), esc_html__( 'This widget adds a Simple Facebook Page Like Widget into your WordPress website sidebar within few minutes.', 'ashe' ) );

				?>


			</div>

		<?php elseif ( $active_tab == 'ashe_tab_3' ) : ?>

			<div class="three-columns-wrap">

				<br>

				<div class="column-wdith-3">
					<h3>
						<span class="dashicons dashicons-sos"></span>
						<?php esc_html_e( 'Forums', 'ashe' ); ?>
					</h3>
					<p>
						<?php esc_html_e( 'Before asking a questions it\'s highly recommended to search on forums, but if you can\'t find the solution feel free to create a new topic.', 'ashe' ); ?>
						<hr>
						<a target="_blank" href="<?php echo esc_url('https://wp-royal.com/support-ashe-free/?ref=ashe-free-backend-about-support-forum/'); ?>"><?php esc_html_e( 'Go to Support Forums', 'ashe' ); ?></a>
					</p>
				</div>

				<div class="column-wdith-3">
					<h3>
						<span class="dashicons dashicons-book"></span>
						<?php esc_html_e( 'Documentation', 'ashe' ); ?>
					</h3>
					<p>
						<?php esc_html_e( 'Need more details? Please check out Ashe Theme Documentation for detailed information.', 'ashe' ); ?>
						<hr>
						<a target="_blank" href="<?php echo esc_url('https://wp-royal.com/themes/ashe/docs/?ref=ashe-free-backend-about-docs/'); ?>"><?php esc_html_e( 'Read Full Documentation', 'ashe' ); ?></a>
					</p>
				</div>

				<div class="column-wdith-3">
					<h3>
						<span class="dashicons dashicons-admin-tools"></span>
						<?php esc_html_e( 'Changelog', 'ashe' ); ?>
					</h3>
					<p>
						<?php esc_html_e( 'Stay always up to date, check for fixes, updates and some new feauters you should not miss.', 'ashe' ); ?>
						<hr>
						<a target="_blank" href="<?php echo esc_url('https://wp-royal.com/ashe-free-changelog/?ref=ashe-free-backend-about-changelog/'); ?>"><?php esc_html_e( 'See Changelog', 'ashe' ); ?></a>
					</p>
				</div>

				<div class="column-wdith-3">
					<h3>
						<span class="dashicons dashicons-smiley"></span>
						<?php esc_html_e( 'Donation', 'ashe' ); ?>
					</h3>
					<p>
						<?php esc_html_e( 'Even a small sum can help us a lot with theme development. If the Ashe theme is useful and our support is helpful, please don\'t hesitate to donate a little bit, at least buy us a Coffee or a Beer :)', 'ashe' ); ?>
						<hr>
						<a target="_blank" href="<?php echo esc_url('https://wp-royal.com/themes/ashe/ashe-donate.html'); ?>"><?php esc_html_e( 'Donate with PayPal', 'ashe' ); ?></a>
					</p>
				</div>

			</div>

		<?php elseif ( $active_tab == 'ashe_tab_4' ) : ?>

			<br><br>

			<table class="free-vs-pro form-table">
				<thead>
					<tr>
						<th>
							<a href="<?php echo esc_url('https://wp-royal.com/themes/item-ashe-pro/?ref=ashe-free-backend-about-section-getpro-btn'); ?>" target="_blank" class="button button-primary button-hero">
								<?php esc_html_e( 'Get Ashe Pro', 'ashe' ); ?>
							</a>
						</th>
						<th><?php esc_html_e( 'Ashe', 'ashe' ); ?></th>
						<th><?php esc_html_e( 'Ashe Pro', 'ashe' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<h3><?php esc_html_e( '100% Responsive and Retina Ready', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Theme adapts to any kind of device screen, from mobile phones to high resolution Retina displays.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Translation Ready', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Each hard-coded string is ready for translation, means you can translate everything. Language "ashe.pot" file included.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'RTL Support', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'RTL stylesheet for languages that are read from right to left like Arabic, Hebrew, etc... Your content will adapt to RTL direction.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'WooCommerce Integration', 'ashe' ); ?></h3>
							<p>
								<?php esc_html_e( 'The best eCommerce solution for WordPress websites. Add your own Shop and sell anything from digital Goods to Coconuts.', 'ashe' ); ?>
								<br>
								<strong class="only-pro"><?php esc_html_e( 'Pro Version:', 'ashe' ); ?></strong> <?php esc_html_e( 'Left &amp; Right WooCommerce widgetised areas. Perfectly styled to fit the theme design.', 'ashe' ); ?>
							</p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Contact Form 7 Support', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'The most popular contact form plugin. You can build almost any kind of contact form. Very simple but super flexible.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Image &amp; Text Logos', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Upload your logo image(set the size) or simply type your text logo.', 'ashe' ); ?><br><strong class="only-pro"><?php esc_html_e( 'Pro Version:', 'ashe' ); ?></strong> <?php esc_html_e( 'Adjust Logo position to fit your custom header design.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Featured Posts Slider', 'ashe' ); ?></h3>
							<p>
								<?php esc_html_e( 'Showcase up to 5 most recent Blog Posts in header area.', 'ashe' ); ?>
								<br>
								<strong class="only-pro"><?php esc_html_e( 'Pro Version:', 'ashe' ); ?></strong> <?php esc_html_e( 'Unlimited number of Slides. Feature specific(custom) posts and order them by date, comments or even random. Change Slider columns from 1 up to 4, set Autoplay and enable/disable any element.', 'ashe' ); ?>  
							</p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Featured Links (Promo Boxes)', 'ashe' ); ?></h3>
							<p>
								<?php esc_html_e( 'Display up to 3 eye-catching linked images under header area, which could be a Custom Page Links or Banners(ads).', 'ashe' ); ?> 
								<br>
								<strong class="only-pro"><?php esc_html_e( 'Pro Version:', 'ashe' ); ?></strong> <?php esc_html_e( 'You can have 5 Featured Links.', 'ashe' ); ?>
							</p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'List Layout', 'ashe' ); ?></h3>
							<p>
								<?php esc_html_e( 'Nice list style layout, perfect for the Food based websites.', 'ashe' ); ?> 
								<br>
								<strong class="only-pro"><?php esc_html_e( 'Pro Version:', 'ashe' ); ?></strong> <?php esc_html_e( 'Adjust Featured Image crop width and height sizes.', 'ashe' ); ?>
							</p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Background Image/Color', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Set the custom body Background image or Color.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Header Background Image/Color', 'ashe' ); ?></h3>
							<p>
								<?php esc_html_e( 'Set the custom header Background image or Color.', 'ashe' ); ?>
								<br>
								<strong class="only-pro"><?php esc_html_e( 'Pro Version:', 'ashe' ); ?></strong> <?php esc_html_e( 'Adjust Header size &amp; enable ', 'ashe' ); ?><strong><?php esc_html_e( 'Parallax Scrolling', 'ashe' ); ?></strong> <?php esc_html_e( 'to fit your custom website design.', 'ashe' ); ?>
							</p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Classic Layout', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Standard one column Blog Feed layout.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Multi-level Sub Menu Support', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Unlimited level of sub menus. Add as much as you need.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Left &amp; Right Sidebars', 'ashe' ); ?></h3>
							<p>
								<?php esc_html_e( 'Left and Right Widgetised areas. Could be globally Enabled/Disabled.', 'ashe' ); ?>
								<br>
								<strong class="only-pro"><?php esc_html_e( 'Pro Version:', 'ashe' ); ?></strong> <?php esc_html_e( 'Full controll - Enable/Disable on specific Posts &amp; Pages.', 'ashe' ); ?>
							</p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Alternative Sidebar', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Stylish and modern Alternative Widgetised area, which is hidden by default and pops up on click.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					
					<!-- Only Pro -->
					<tr>
						<td>
							<h3><?php esc_html_e( 'One Click Demo Import', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Just a Single Click and you will get the same content as shown on our Demo website. Menus, Posts, Pages, Widgets, etc... will be imported.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-no"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Unlimited Colors', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Tons of color options. You can customize your Header, Content and Footer separately as much as possible.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-no"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( '800+ Google Fonts', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Rich Typography options. Choose from more than 800 Google Fonts, adjust Size, Line Height, Font Weight, etc...', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-no"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Advanced Header Background', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Upload custom header background Video or display images as a Slider.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-no"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Advanced WooCommerce Support', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Set 2, 3 or 4 Columns on WooCommerce Product Grid. Enable/Disable Left & Right WooCommerce widgetized areas.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-no"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Grid Layout', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Choose from 1 up to 4 columns grid layout for your Blog Feed.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-no"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Post Formats Support', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Create Audio, Video, Gallery, Link &amp; Quote Blog Posts with unique, modern and minimal styling. Full control over your Blog Posts.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-no"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Post Sharing Icons', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Ability to share your Blog Posts on the most popular social media: Facebook, Twitter, Pinterest, Google Plus, Linkedin, Reddit, Tumblr.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-no"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Advanced Post Options', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Custom Post Header image upload, Full-width Post option, ability to display current post in the Featured Slider.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-no"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Advanced Page Options', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Custom Page Header image, Full-width page option, enable/disable Featured Slider & Featured Links on current page, Show/hide page Title & Featured Image.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-no"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Different Blog Feed Pagination', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Choose from 4 Diffenet pagination styles: Default, Numeric, Load More Button and Infinite Page Scrolling.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-no"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Sticky Navigation', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Fix the main navigation to the page, it will be always visible at the top.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-no"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Instagram Widget Area', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Showcase your Instagram photos in your website Footer or Header.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-no"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Integration with MailChimp', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'This plugin helps you add more subscribers to your MailChimp lists using various methods.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-no"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Integration with JetPack', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Jetpack is the ultimate toolkit for WordPress. It gives you everything you need to design, secure, and grow your site in one bundle.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-no"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Ashe Pro Widgets', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Ashe Author, Ads &amp; Social Icons widgets included.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-no"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Advanced Footer Options', 'ashe' ); ?></h3>
							<p><?php esc_html_e( 'Theme and Author credit links in the footer are automatically removed. You can add social icons to the footer.', 'ashe' ); ?></p>
						</td>
						<td class="compare-icon"><span class="dashicons-before dashicons-no"></span></td>
						<td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
					</tr>


					<tr>
						<td></td>
						<td colspan="2">
							<a href="<?php echo esc_url('https://wp-royal.com/themes/item-ashe-pro/?ref=ashe-free-backend-about-section-getpro-btn'); ?>" target="_blank" class="button button-primary button-hero">
								<?php esc_html_e( 'Get Ashe Pro', 'ashe' ); ?>
							</a>
							<br><br>
						</td>
					</tr>
				</tbody>
			</table>

	    <?php endif; ?>

	</div><!-- /.wrap -->
<?php
} // end ashe_about_page_output

// Check if plugin is installed
function ashe_check_installed_plugin( $slug, $filename ) {
	return file_exists( ABSPATH . 'wp-content/plugins/' . $slug . '/' . $filename . '.php' ) ? true : false;
}

// Generate Recommended Plugin HTML
function ashe_recommended_plugin( $slug, $filename, $name, $description) {

	if ( $slug === 'facebook-pagelike-widget' ) {
		$size = '128x128';
	} else {
		$size = '256x256';
	}

?>

	<div class="plugin-card">
		<div class="name column-name">
			<h3>
				<?php echo esc_html( $name ); ?>

				<?php if ( $slug === 'ajax-thumbnail-rebuild' ) : ?>
					<img src="<?php echo get_template_directory_uri() . '/assets/images/ajax-thumbnail-rebuild.jpeg'; ?>" class="plugin-icon" alt="">
				<?php else: ?>
					<img src="<?php echo esc_url('https://ps.w.org/'. $slug .'/assets/icon-'. $size .'.png') ?>" class="plugin-icon" alt="">
				<?php endif; ?>
			</h3>
		</div>
		<div class="action-links">
			<?php if ( ashe_check_installed_plugin( $slug, $filename ) ) : ?>
			<button type="button" class="button button-disabled" disabled="disabled"><?php esc_html_e( 'Installed', 'ashe' ); ?></button>
			<?php else : ?>
			<a class="install-now button-primary" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin='. $slug ), 'install-plugin_'. $slug ) ); ?>" >
				<?php esc_html_e( 'Install Now', 'ashe' ); ?>
			</a>							
			<?php endif; ?>
		</div>
		<div class="desc column-description">
			<p><?php echo esc_html( $description ); ?></p>
		</div>
	</div>

<?php
}

// enqueue ui CSS/JS
function ashe_enqueue_about_page_scripts($hook) {

	if ( 'appearance_page_about-ashe' != $hook ) {
		return;
	}

	// enqueue CSS
	wp_enqueue_style( 'ashe-about-page-css', get_theme_file_uri( '/inc/about/css/about-ashe-page.css' ) );

}
add_action( 'admin_enqueue_scripts', 'ashe_enqueue_about_page_scripts' );
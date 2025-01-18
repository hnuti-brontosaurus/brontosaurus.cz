<?php

namespace HnutiBrontosaurus\Theme;

function banner_settings_page() {
	add_options_page(
		'Homepage banner',
		'Homepage banner',
		'publish_pages', // basically editor+ role
		'homepage-banner',
		'HnutiBrontosaurus\Theme\render_homepage_banner_settings',
	);
}
add_action('admin_menu', 'HnutiBrontosaurus\Theme\banner_settings_page');

function enqueue_banner_settings_scripts($hook) {
    if ($hook !== 'settings_page_homepage-banner') {
        return;
    }

    // Enqueue WordPress media uploader script and styles.
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'HnutiBrontosaurus\Theme\enqueue_banner_settings_scripts');



function render_homepage_banner_settings() {
	// Check if the user has permission to save options.
	if (!current_user_can('publish_pages')) {
		return;
	}

	// Handle form submission.
	if (isset($_POST['homepage-banner_save-settings'])) {
		check_admin_referer('homepage-banner_nonce'); // Validate nonce.

		update_option('homepage-banner_heading', sanitize_text_field($_POST['homepage-banner_heading']));
		update_option('homepage-banner_subheading', sanitize_text_field($_POST['homepage-banner_subheading']));
		update_option('homepage-banner_image', esc_url_raw($_POST['homepage-banner_image']));
		update_option('homepage-banner_link', esc_url_raw($_POST['homepage-banner_link']));

		echo '<div class="updated"><p>Úspěšně uloženo!</p></div>';
	}

	// Get saved values.
	$heading = get_option('homepage-banner_heading', '');
	$subheading = get_option('homepage-banner_subheading', '');
	$image = get_option('homepage-banner_image', '');
	$link = get_option('homepage-banner_link', '');
	?>

	<div class="wrap">
		<h1>Homepage banner</h1>
		<form method="post">
			<?php wp_nonce_field('homepage-banner_nonce'); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="homepage-banner_heading">Hlavní text:*</label></th>
					<td><input type="text" id="homepage-banner_heading" name="homepage-banner_heading" value="<?php echo esc_attr($heading); ?>" class="regular-text" required></td>
				</tr>
				<tr>
					<th scope="row"><label for="homepage-banner_subheading">Podtext:</label></th>
					<td><input type="text" id="homepage-banner_subheading" name="homepage-banner_subheading" value="<?php echo esc_attr($subheading); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="homepage-banner_image">Banner:*</label></th>
					<td>
						<input type="text" id="homepage-banner_image" name="homepage-banner_image" value="<?php echo esc_url($image); ?>" class="regular-text" required>
						<button type="button" class="button" id="homepage-banner_upload-image-button">Vybrat obrázek</button>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="homepage-banner_link">Odkaz:</label></th>
					<td><input type="text" id="homepage-banner_link" name="homepage-banner_link" value="<?php echo esc_attr($link); ?>" class="regular-text"></td>
				</tr>
			</table>
			<?php submit_button('Uložit', 'primary', 'homepage-banner_save-settings'); ?>
		</form>
	</div>

	<script>
		document.addEventListener("DOMContentLoaded", () => {
			const uploadButton = document.getElementById("homepage-banner_upload-image-button");
			const bannerImageField = document.getElementById("homepage-banner_image");

			let mediaUploader;

			uploadButton.addEventListener("click", (e) => {
				e.preventDefault();

				// If the media uploader already exists, open it.
				if (mediaUploader) {
					mediaUploader.open();
					return;
				}

				// Create the media uploader.
				mediaUploader = wp.media({
					title: "Select Banner Image",
					button: {
						text: "Use this image",
					},
					multiple: false, // Only allow a single image.
				});

				// When an image is selected, update the input field.
				mediaUploader.on("select", () => {
					const attachment = mediaUploader.state().get("selection").first().toJSON();
					bannerImageField.value = attachment.url;
				});

				// Open the media uploader.
				mediaUploader.open();
			});
		});
	</script>

	<?php
}

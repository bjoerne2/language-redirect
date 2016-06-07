<?php
function language_redirect_show_settings_page() {
?>

<div class="wrap">
	<?php screen_icon(); ?>
	<h2>Language Redirect Options</h2>
	<form method="post" action="options.php">
<?php 
	settings_fields( 'language_redirect_group' );
	do_settings_sections( 'language_redirect_group' );
?>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">Default redirect location</th>
					<td>
						<input id="language_redirect_default_redirect_location" class="regular-text" type="text" value="<?php echo get_option( 'language_redirect_default_redirect_location' ) ?>" name="language_redirect_default_redirect_location" />
						<p class="description">Used if no mapping for the user's language is found, e.g. /en/</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Language redirect mapping</th>
					<td>
						<textarea id="language_redirect_redirect_mapping" class="all-options" name="language_redirect_redirect_mapping"><?php echo get_option( 'language_redirect_redirect_mapping' ) ?></textarea>
						<p class="description">Every line should have the format <i>&lt;language&gt;=&lt;redirect location&gt;</i>, e.g. <i>en=/en/</i></p>
					</td>
				</tr>
			</tbody>
		</table>
		<?php submit_button(); ?>
	</form>
</div>

<?php
}
?>
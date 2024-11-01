<div class="wrap">
<h2>WP Places</h2>
<style>
.form-table label {
	display: inline-block;
	width: 100px; 
}
</style>
<form method="post" action="options.php">
    <?php settings_fields('wp-places'); ?>
    <table class="form-table">
    
        <tr valign="top">
        <th scope="row">Google Maps API Key</th>
        <td>
			<input type="text" name="places-apikey" value="<?=get_option('places-apikey');?>" class="regular-text" />
		</td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Disable Auto Linking</th>
        <td>
			<input type="checkbox" name="autolinking" value="yes"<?=get_option('autolinking') == 'yes' ? ' checked' : '';?> />
		</td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Category Thumbnail Dimensions</th>
        <td>
			Width <input type="text" name="cat-width" value="<?=get_option('cat-width');?>" style="width:50px;" /> 
			Height <input type="text" name="cat-height" value="<?=get_option('cat-height');?>" style="width:50px;" />
		</td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Map Dimensions</th>
        <td>
			Width <input type="text" name="map-width" value="<?=get_option('map-width');?>" style="width:50px;" /> 
			Height <input type="text" name="map-height" value="<?=get_option('map-height');?>" style="width:50px;" />
		</td>
        </tr>
         
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>
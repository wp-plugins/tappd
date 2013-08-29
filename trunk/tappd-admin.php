<?php 
	if($_POST['druntpd_hidden'] == 'Y') {
		//Form data sent
		$drclientid = $_POST['druntpd_drclientid'];
		update_option('druntpd_drclientid', $drclientid);
		
		$drsecret = $_POST['druntpd_drsecret'];
		update_option('druntpd_drsecret', $drsecret);

		$drredirecturi = $_POST['druntpd_drredirecturi'];
		update_option('druntpd_drredirecturi', $drredirecturi);
		
		$druntappdusername = $_POST['druntpd_druntappdusername'];
		update_option('druntpd_druntappdusername', $druntappdusername);
		
		$drbreweryid = $_POST['druntpd_drbreweryid'];
		update_option('druntpd_drbreweryid', $drbreweryid);

		$drvenueid = $_POST['druntpd_drvenueid'];
		update_option('druntpd_drvenueid', $drvenueid);
		
		$drbeerid = $_POST['druntpd_drbeerid'];
		update_option('druntpd_drbeerid', $drbeerid);
		
?>

		<div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>

<?php

	} else {
		//Normal page display
		$drclientid = get_option('druntpd_drclientid');
		$drsecret = get_option('druntpd_drsecret');
		$drredirecturi = get_option('druntpd_drredirecturi');
		$druntappdusername = get_option('druntpd_druntappdusername');
		$drbreweryid = get_option('druntpd_drbreweryid');
		$drvenueid = get_option('druntpd_drvenueid');
		$drbeerid = get_option('druntpd_drbeerid');
	}
	
?>

<div class="wrap tappd">
    <?php    echo "<div id='icon-tools' class='icon32'></div><h2>" . __( 'Tappd Plugin Settings', 'druntpd_trdom' ) . "</h2>"; ?>
<p>To get an Untappd API Key, you must have an Untappd account registered and visit <a href="https://untappd.com/api/register">https://untappd.com/api/register</a>.</p>
    <form name="druntpd_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
        <input type="hidden" name="druntpd_hidden" value="Y">
        <table class="form-table">
            <tr valign="top"><th scope="row">
                <?php    echo "<h3 class='title'>" . __( 'Tappd API Settings', 'druntpd_trdom' ) . "</h3>"; ?>
            </th></tr>
            <tr valign="top"><th scope="row">
                <label for="druntpd_drclientid"><?php _e("Client ID: " ); ?></label>
            </th><td>
                <input type="text" name="druntpd_drclientid" id="druntpd_drclientid" value="<?php echo $drclientid; ?>" size="50">
            </td></tr>
            <tr valign="top"><th scope="row">
                <label for="druntpd_drsecret"><?php _e("Client Secret: " ); ?></label>
            </th><td>
                <input type="text" name="druntpd_drsecret" id="druntpd_drsecret" value="<?php echo $drsecret; ?>" size="50">
            </td></tr>
            <tr valign="top"><th scope="row">
                <?php    echo "<h3 class='title'>" . __( 'Tappd Feed Settings', 'druntpd_trdom' ) . "</h3>"; ?>
            </th></tr>
            <tr valign="top"><th scope="row">
                <label for="druntpd_druntappdusername"><?php _e("Untappd Username: " ); ?></label>
            </th><td>
                <input type="text" name="druntpd_druntappdusername" id="druntpd_druntappdusername" value="<?php echo $druntappdusername; ?>" size="50">
                <p class="description">(ex https://untappd.com/user/<strong style="color:#DB5A18;">patstrader</strong>)</p>
            </td></tr>
            <tr valign="top"><th scope="row">
                <label for="druntpd_drbreweryid"><?php _e("Default Brewery ID: " ); ?></label>
            </th><td>
                <input type="text" name="druntpd_drbreweryid" id="druntpd_drbreweryid" value="<?php echo $drbreweryid; ?>" size="50">
                <p class="description">(ex https://untappd.com/brewery/<strong style="color: #DB5A18;">94</strong>)</p>
            </td></tr>
            <tr valign="top"><th scope="row">
                <label for="druntpd_drvenueid"><?php _e("Default Venue ID: " ); ?></label>
            </th><td>
                <input type="text" name="druntpd_drvenueid" id="druntpd_drvenueid" value="<?php echo $drvenueid; ?>" size="50">
                <p class="description">(ex https://untappd.com/venue/<strong style="color: #DB5A18;">2009</strong>)</p>
            </td></tr>
            <tr valign="top"><th scope="row">
                <label for="druntpd_drbeerid"><?php _e("Default Beer ID " ); ?></label>
            </th><td>
                <input type="text" name="druntpd_drbeerid" id="druntpd_drbeerid" value="<?php echo $drbeerid; ?>" size="50">
                <p class="description">(ex https://untappd.com/beer/<strong style="color: #DB5A18;">30257</strong>)</p>
            </td></tr>
        </table>
        <p class="submit"><input class="button-primary" type="submit" name="Submit" value="<?php _e('Update Options', 'druntpd_trdom' ) ?>" /></p>
    </form>
</div>
<div id="digitalrelativity">
    <ul class="social">
        <li class="facebook"><a href="https://www.facebook.com/digitalrelativity"></a></li>
        <li class="twitter"><a href="https://twitter.com/digirelativity"></a></li>
        <li class="googleplus"><a href="https://plus.google.com/105117490952923823346/posts"></a></li>
        <li class="pinterest"><a href="http://pinterest.com/digirelativity/"></a></li>
        <li class="instagram"><a href="http://instagram.com/digitalrelativity"></a></li>
    </ul>
    <p><a href="http://www.digitalrelativity.com/tag/craft-beer/">Craft beer digital marketing problem solving</a>. Mobile marketing, app design, social media training, website design & development.</p>
</div>



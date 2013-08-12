<?php 
/*
Plugin Name: Tappd
Description: Plugin to utilize Untappd's API
Version: 1.0.2
Author: Digital Relativity
Author URI: http://digitalrelativity.com/
Plugin URI: http://digitalrelativity.com/untappd-wordpress-plugin/
*/

class DR_Tappd
{
    const URI_BASE = 'http://api.untappd.com/v4';
    protected $_clientId = '';
    protected $_clientSecret = '';
    protected $_redirectUri = '';
    protected $_lastParsedResponse = null;
    protected $_lastRawResponse = null;
    protected $_lastRequestUri = null;

    public function __construct(array $connectArgs = array())
    {
        if (!isset($connectArgs['clientId']) || empty($connectArgs['clientId'])) {
            echo 'clientId not set and is required';
        }

        if (!isset($connectArgs['clientSecret']) || empty($connectArgs['clientSecret'])) {
            echo 'clientSecret not set and is required';
        }

        $this->_clientId = $connectArgs['clientId'];
        $this->_clientSecret = $connectArgs['clientSecret'];
    }

    public function userFeed($username = '', $limit = '', $offset = '')
    {
        if ($username == '') {

            echo 'username parameter or Untappd authentication parameters must be set.';
        }
        
        $args = array(
            'limit'  => $limit,
            'offset' => $offset
        );

        return $this->TappdRequest('user/checkins/' . $username, $args);
    }

    public function beerFeed($beerId, $since = '', $offset = '')
    {
        if (empty($beerId)) {

            echo 'beerId parameter must be set and not empty';
        }

        $args = array(
            'since'  => $since,
            'offset' => $offset,
        );

        return $this->TappdRequest('beer/checkins/' . $beerId, $args);
    }

    public function venueFeed($venueId, $since = '', $offset = '', $limit = '')
    {
        if (empty($venueId)) {

            echo 'venueId parameter must be set and not empty';
        }
        $args = array(
            'since'    => $since,
            'offset'   => $offset,
            'limit'    => $limit,
        );

        return $this->TappdRequest('venue/checkins/' . $venueId, $args);
    }

    public function breweryFeed($breweryId, $since = '', $offset = '', $limit = '')
    {
        if (empty($breweryId)) {

            echo 'breweryId parameter must be set and not empty';
        }

        $args = array(
            'since'  => $since,
            'offset' => $offset,
            'limit'  => $limit,
        );

        return $this->TappdRequest('brewery/checkins/' . $breweryId, $args);
    }

    protected function TappdRequest($method, $args, $requireAuth = false)
    {
        $this->_lastRequestUri = null;
        $this->_lastRawResponse = null;
        $this->_lastParsedResponse = null;

        // Append the API key to the args passed in the query string
        $args['client_id'] = $this->_clientId;
        $args['client_secret'] = $this->_clientSecret;

        // remove any unnecessary args from the query string
        foreach ($args as $key => $a) {
            if ($a == '') {
                unset($args[$key]);
            }
        }

        if (preg_match('/^http/i', $method)) {
            $this->_lastRequestUri = $method;
        } else {
            $this->_lastRequestUri = self::URI_BASE . '/' . $method;
        }

        $this->_lastRequestUri .= '?' . http_build_query($args);

        // Set curl options and execute the request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_lastRequestUri);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $this->_lastRawResponse = curl_exec($ch);

        if ($this->_lastRawResponse === false) {

            $this->_lastRawResponse = curl_error($ch);

            echo 'CURL Error: ' . curl_error($ch);
        }

        curl_close($ch);

        // Response comes back as JSON, so we decode it into a stdClass object
        $this->_lastParsedResponse = json_decode($this->_lastRawResponse);

        // If the http_code var is not found, the response from the server was unparsable
        if (!isset($this->_lastParsedResponse->meta->code)) {

            echo 'Error parsing response from server.';
        }

        // Server provides error messages in http_code and error vars.  If not 200, we have an error.
        if ($this->_lastParsedResponse->meta->code != '200') {

            echo 'Untappd Service Error ' .
                $this->_lastParsedResponse->meta->code . ': ' .  $this->_lastParsedResponse->meta->error;
        }

        return $this->getLastParsedResponse();
    }

    public function getLastParsedResponse()
    {
        return $this->_lastParsedResponse;
    }

    public function getLastRawResponse()
    {
        return $this->_lastRawResponse;
    }

    public function getLastRequestUri()
    {
        return $this->_lastRequestUri;
    }
 
}

//********Get API settings for  ***********

function dr_get_api_settings()
{
    $drclientid = get_option('druntpd_drclientid');
    $drsecret = get_option('druntpd_drsecret');
    
    $config = array(
        'clientId'     => $drclientid,
        'clientSecret' => $drsecret,
        'redirectUri'  => $drredirecturi
    );
    return $config;
}

//******************* Widget Functions ************
//Beer
function widget_DRTappd_Beer() {
    $options = get_option("widget_DRTappd_Beer");
    $id = $options['id'];
    $limit = $options['limit'];
    $feedtype = 'beerFeed';
    Tappd_output($id,$feedtype,$limit);
}
function widget_myBeerFeed($args) {
    extract($args);
    $options = get_option("widget_DRUntapped_Beer");
     
    echo $before_widget;
    echo $before_title;
    echo $options['title'];
    echo $after_title;
    widget_DRTappd_Beer();
    echo $after_widget;
}
function myBeerFeed_control()
{
    build_control('widget_DRTappd_Beer');
}

//Brewery
function widget_DRTappd_Brewery() {
    $options = get_option("widget_DRTappd_Brewery");
    $id = $options['id'];
    $limit = $options['limit'];
    $feedtype = 'breweryFeed';
    Tappd_output($id,$feedtype,$limit);
}
function widget_myBreweryFeed($args) {
    extract($args);
    $options = get_option("widget_DRUntapped_Brewery");
     
    echo $before_widget;
    echo $before_title;
    echo $options['title'];
    echo $after_title;
    widget_DRTappd_Brewery();
    echo $after_widget;
}
function myBreweryFeed_control()
{
    build_control('widget_DRTappd_Brewery');
}

//Venue
function widget_DRTappd_Venue() {
    $options = get_option("widget_DRTappd_Venue");
    $id = $options['id'];
    $limit = $options['limit'];
    $feedtype = 'venueFeed';
    Tappd_output($id,$feedtype,$limit);
}
function widget_myVenueFeed($args) {
    extract($args);
    $options = get_option("widget_DRUntapped_Venue");
     
    echo $before_widget;
    echo $before_title;
    echo $options['title'];
    echo $after_title;
    widget_DRTappd_Venue();
    echo $after_widget;
}
function myVenueFeed_control()
{
    build_control('widget_DRTappd_Venue');
}


//User
function widget_DRTappd_User() {
    $options = get_option("widget_DRTappd_User");
    $id = $options['id'];
    $limit = $options['limit'];
    $feedtype = 'userFeed';
    Tappd_output($id,$feedtype,$limit);
}
function widget_myUserFeed($args) {
    extract($args);
    $options = get_option("widget_DRUntapped_User");
     
    echo $before_widget;
    echo $before_title;
    echo $options['title'];
    echo $after_title;
    widget_DRTappd_User();
    echo $after_widget;
}
function myUserFeed_control()
{
    build_control('widget_DRTappd_User');
}

//******************* Shortcode Functions *****************\\
//Beer
function beer_shortcode($atts) {
    extract(shortcode_atts(array(
      "id" => '',
      "limit" => ''
    ), $atts));
    
    $feedtype = 'beerFeed';
    Tappd_output($id,$feedtype,$limit);
}

//Brewery
function brewery_shortcode($atts) {
    extract(shortcode_atts(array(
      "id" => '',
      "limit" => ''
    ), $atts));
    
    $feedtype = 'breweryFeed';
    Tappd_output($id,$feedtype,$limit);
}

//Venue
function venue_shortcode($atts) {
    extract(shortcode_atts(array(
      "id" => '',
      "limit" => ''
    ), $atts));
    
    $feedtype = 'venueFeed';
    Tappd_output($id,$feedtype,$limit);
}

//User
function user_shortcode($atts) {
    extract(shortcode_atts(array(
      "id" => '',
      "limit" => ''
    ), $atts));
    
    $feedtype = 'userFeed';
    Tappd_output($id,$feedtype,$limit);
    
}

//Widget Controls
function build_control($feedoptions)
{
    $options = get_option($feedoptions);
     
    if (!is_array( $options ))
    {
        $options = array(
            'title' => 'Untappd Feed',
            'id' => '',
            'limit' => ''
        );
    }
     
    if ($_POST['sideDRTappdSubmit']) {
        $options['title'] = htmlspecialchars($_POST['sideDRTappdTitle']);
        $options['id'] = htmlspecialchars($_POST['sideDRTappdID']);
        $options['limit'] = htmlspecialchars($_POST['sideDRTappdLimit']);
        update_option($feedoptions, $options);
    }
     
    ?>
    <p>
    <label for="sideDRTappdTitle">Title: </label><br />
    <input class="widefat" type="text" id="sideDRTappdTitle" name="sideDRTappdTitle" value="<?php echo $options['title'];?>" />
    <br /><br />
    <label for="sideDRTappdID">Untappd ID:</label>
    <input type="text" id="sideDRTappdID" name="sideDRTappdID" value="<?php echo $options['id'];?>" /><br />
    <br /><br />
    <label for="sideDRTappdLimit">Limit: </label>
    <input type="text" id="sideDRTappdLimit" name="sideDRTappdLimit" value="<?php echo $options['limit'];?>" /><br />
    <input type="hidden" id="sideDRTappdSubmit" name="sideDRTappdSubmit" value="1" />
    </p>
    <?php
} 


//  ******     OUTPUT      *****  //


function Tappd_output($id, $feedtype, $limit){
  
    $id = preg_replace('~&#x0*([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $id);
    $id = preg_replace('~&#0*([0-9]+);~e', 'chr(\\1)', $id); 

    $config = dr_get_api_settings();
    $Tappd = new DR_Tappd($config);
    $feed ='';
    $transientName = $feedtype.$id;
    try {
        if ( false === ( $feed = get_transient($transientName) ) ) {
            if($feedtype == 'beerFeed') {
                $feed = $Tappd->beerFeed($id);
            }
            if($feedtype == 'venueFeed'){
                $feed = $Tappd->venueFeed($id);
            }
            if($feedtype == 'breweryFeed'){
                $feed = $Tappd->breweryFeed($id);
            }
            if($feedtype == 'userFeed'){
                $feed = $Tappd->userFeed($id);
            }
        	set_transient( $transientName, $feed, 60*60*0.25);
        }
    } catch (Exception $e) {
        die();
    }
    
    if ($limit == ''){
        $limit = 10;
    }
    
    $counter = 1;
    
    if($feedtype == 'userFeed' && $id != ''){
?>
        <div class="untappduserfeed">
            <div class="untappduserheading">
                <div class="untappduserpic" >
                    <a href="http://untappd.com/user/<?php echo $feed->response->checkins->items[0]->user->user_name; ?>" >
                        <img src="<?php echo htmlentities($feed->response->checkins->items[0]->user->user_avatar); ?>" alt="<?php echo $feed->response->checkins->items[0]->user->user_name; ?>" />
                    </a>
                </div>
                <div class="untappdusername" >
                    <a href="http://untappd.com/user/<?php echo $feed->response->checkins->items[0]->user->user_name; ?>" ><span class="untappdrealname"><?php echo $feed->response->checkins->items[0]->user->first_name; ?> <?php echo $feed->response->checkins->items[0]->user->last_name; ?></span></a>
                    <?php if ($feed->response->checkins->items[0]->user->bio != '') { ?>
                        <br><span class="untappdbio"><?php echo $feed->response->checkins->items[0]->user->bio; ?></span>
                    <?php } ?>
                </div>
            </div>
            <div class="checkincontainer">
<?php       
        foreach ($feed->response->checkins->items as $i) { 
            if($counter <= $limit){      
?> 
                <div class="usercheckin">
                     <div class="userbeerlabel">
                          <img src="<?php echo $i->beer->beer_label; ?>" alt="<?php echo $i->beer->beer_name; ?>" />
                     </div> 
                     <div class="userbeername">
                         <a href="https://untappd.com/beer/<?php echo $i->beer->bid; ?>"><?php echo $i->beer->beer_name; ?></a><br>
                         <span>by <a href="<?php echo $i->brewery->contact->url; ?>"><?php echo $i->brewery->brewery_name; ?></a></span>         
                     </div>  
                </div>
<?php
            }
            else{
                break;
            }
            $counter++; 
        }
?>
            </div>
            <div class="branding">
                <span>Data provided by <a href="http://untappd.com">Untappd</a></span>
            </div>
        </div>
<?php
    }
    if($feedtype == 'venueFeed' && $id != ''){
    ?>
        <div class="untappdvenuefeed" >
            <div class="untappdvenueheading">
                <div class="untappdvenuepic" >
                    <a href="https://untappd.com/venue/<?php echo $feed->response->checkins->items[0]->venue->venue_id; ?>" >
                          <img src="https://maps.googleapis.com/maps/api/staticmap?size=100x100&amp;center=<?php echo $feed->response->checkins->items[0]->venue->location->lat; ?>,<?php echo $feed->response->checkins->items[0]->venue->location->lng; ?>&amp;sensor=false&amp;zoom=13&amp;markers=color:yellow|label:Venue|<?php echo $feed->response->checkins->items[0]->venue->location->lat; ?>,<?php echo $feed->response->checkins->items[0]->venue->location->lng; ?>" alt="<?php echo $feed->response->checkins->items[0]->venue->venue_name; ?>" />
                    </a>
                </div>
                <div class="untappdvenuename">
                    Checkins at <a href="https://untappd.com/venue/<?php echo $feed->response->checkins->items[0]->venue->venue_id; ?>" ><?php echo $feed->response->checkins->items[0]->venue->venue_name; ?></a>
                </div>
            </div>
            <div class="checkincontainer">
<?php
            foreach ($feed->response->checkins->items as $i) {
                if($counter <= $limit){ 
?>      
                <div class="venuecheckin">
                    <div class="venueuserpic" >
                        <a href="http://untappd.com/user/<?php echo $i->user->user_name; ?>">
                            <img src="<?php echo htmlentities($i->user->user_avatar); ?>" alt="<?php echo $i->user->user_name; ?>"/>
                        </a>
                    </div>
                    <div class="venueusername">
                        <a href="http://untappd.com/user/<?php echo $i->user->user_name; ?>" ><?php echo $i->user->user_name; ?></a> is drinking <a href="https://untappd.com/beer/<?php echo $i->beer->bid; ?>"><?php echo $i->beer->beer_name; ?></a> <span>by <a href="<?php echo $i->brewery->contact->url; ?>"><?php echo $i->brewery->brewery_name; ?></a></span> 
                    </div>  
                </div>       
<?php 
                }
                else{
                    break;
                }
                $counter++; 
            }     
?>
            </div>
            <div class="branding">
                <span>Data provided by <a href="http://untappd.com">Untappd</a></span>
            </div>
        </div>
<?php 
    }     
    if($feedtype == 'breweryFeed' && $id != ''){  ?>
        <div class="untappdbreweryfeed" >
            <div class="untappdbreweryheading">
                <div class="untappdbrewerypic">
                    <a href="<?php echo $feed->response->checkins->items[0]->brewery->contact->url; ?>">
                        <img src="<?php echo $feed->response->checkins->items[0]->brewery->brewery_label; ?>" alt="<?php echo $feed->response->checkins->items[0]->brewery->brewery_name; ?>" />
                    </a>
                </div>
                <div class="untappdbreweryname">
                    Checkins from 
                    <a href="<?php echo $feed->response->checkins->items[0]->brewery->contact->url; ?>">
                        <?php echo $feed->response->checkins->items[0]->brewery->brewery_name; ?>
                    </a>
                </div>
            </div>
            <div class="checkincontainer">
<?php    
        foreach ($feed->response->checkins->items as $i) {
            if($counter <= $limit){ 
?>
                <div class="brewerycheckin">
                    <div class="breweryuserpic" >
                        <a href="http://untappd.com/user/<?php echo $i->user->user_name; ?>" >
                           <img src="<?php echo htmlentities($i->user->user_avatar); ?>" alt="<?php echo $i->user->user_name; ?>" />
                        </a>
                    </div>
                    <div class="breweryusername" >
                        <a href="http://untappd.com/user/<?php echo $i->user->user_name; ?>" ><?php echo $i->user->user_name; ?></a> is drinking <a href="https://untappd.com/beer/<?php echo $i->beer->bid; ?>"><?php echo $i->beer->beer_name; ?></a>
                    </div>    
                </div>  
<?php
            }
            else{
                break;
            }
            $counter++; 
        }
?>
            </div>
            <div class="branding">
                <span>Data provided by <a href="http://untappd.com">Untappd</a></span>
            </div>
        </div>
<?php
    }
    
    if($feedtype == 'beerFeed' && $id != ''){
?>
        <div class="untappdbeerfeed" >
            <div class="untappdbeerheading">
                <div class="untappdbeerpic">
                    <a href="https://untappd.com/beer/<?php echo $feed->response->checkins->items[0]->beer->bid ?>">
                        <img src="<?php echo $feed->response->checkins->items[0]->beer->beer_label; ?>" alt="<?php echo $feed->response->checkins->items[0]->beer->beer_name; ?>" />
                    </a>
                </div>
                <div class="untappdbeername">
                    Checkins for <a href="https://untappd.com/beer/<?php echo $feed->response->checkins->items[0]->beer->bid ?>"><?php echo $feed->response->checkins->items[0]->beer->beer_name; ?></a> by                   <a href="<?php echo $feed->response->checkins->items[0]->brewery->contact->url; ?>"><?php echo $feed->response->checkins->items[0]->brewery->brewery_name; ?></a>
                </div>
            </div>
            <div class="checkincontainer">
<?php
        foreach ($feed->response->checkins->items as $i) {  
            if($counter <= $limit){       
?>
                <div class="beercheckin">
                    <div class="beeruserpic">
                        <a href="http://untappd.com/user/<?php echo $i->user->user_name; ?>" >
                            <img src="<?php echo htmlentities($i->user->user_avatar); ?>" alt="<?php echo $i->user->user_name; ?>" />
                        </a>
                    </div>
                    <div class="beerusername" >
                        <a href="http://untappd.com/user/<?php echo $i->user->user_name; ?>" ><?php echo $i->user->user_name; ?></a> is drinking <?php echo $i->beer->beer_name; ?>
                    </div>
                </div>
<?php
            }
            else{
                break;
            }
            $counter++; 
            $beerentries = '';
        }
?>
            </div>
            <div class="branding">
                <span>Data provided by <a href="http://untappd.com">Untappd</a></span>
            </div>
        </div>
<?php
    }
}

function DR_add_stylesheet() {
    // Respects SSL, Style.css is relative to the current file
    wp_register_style( 'DR-style', plugins_url('css/tappd-style.css', __FILE__) );
    wp_enqueue_style( 'DR-style' );
}
function DR_add_admin_stylesheet() {
    // Respects SSL, Style.css is relative to the current file
    wp_register_style( 'DR-admin-style', plugins_url('css/tappd-admin-style.css', __FILE__) );
    wp_enqueue_style( 'DR-admin-style' );
}

//*************** Admin functions ***************

function DRTappd_admin() {
	include('tappd-admin.php');
}
function DRTappd_admin_actions() {
    add_menu_page("Tappd", "Tappd", 1, "Tappd", "DRTappd_admin", plugins_url('img/dradminlogo.png', __FILE__));
}

//  ******     HOOKS      *****  //

add_shortcode("beer", "beer_shortcode");
add_shortcode("venue", "venue_shortcode");
add_shortcode("brewery", "brewery_shortcode");
add_shortcode("untappduser", "user_shortcode");

register_widget_control( 'Untappd beerFeed', 'myBeerFeed_control');   
register_widget_control( 'Untappd breweryFeed', 'myBreweryFeed_control');   
register_widget_control( 'Untappd venueFeed', 'myVenueFeed_control');   
register_widget_control( 'Untappd userFeed', 'myUserFeed_control'); 

register_sidebar_widget( 'Untappd beerFeed', 'widget_myBeerFeed');  
register_sidebar_widget( 'Untappd breweryFeed', 'widget_myBreweryFeed');
register_sidebar_widget( 'Untappd venueFeed', 'widget_myVenueFeed');
register_sidebar_widget( 'Untappd userFeed', 'widget_myUserFeed');

add_action( 'wp_enqueue_scripts', 'DR_add_stylesheet' );

global $user_ID; if( $user_ID ) :
if( current_user_can('level_9') ) :
 
	add_action( 'admin_init', 'DR_add_admin_stylesheet' );
	add_action('admin_menu', 'DRTappd_admin_actions');

else :
endif;
endif;

?>

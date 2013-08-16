<?php 
/*
Plugin Name: Tappd
Description: Plugin to utilize Untappd's API
Version: 1.0.4
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
    
    $output = '';
    $counter = 1;
    
    if($feedtype == 'userFeed' && $id != ''){
    
        $output .= '<div class="untappduserfeed">';
            $output .= '<div class="untappduserheading">';
                $output .= '<div class="untappduserpic" >';
                    $output .= '<a href="http://untappd.com/user/' . $feed->response->checkins->items[0]->user->user_name . '" >';
                        $output .= '<img src="' . htmlentities($feed->response->checkins->items[0]->user->user_avatar). '" alt="' . $feed->response->checkins->items[0]->user->user_name . '" />';
                    $output .= '</a>';
                $output .= '</div>';
                $output .= '<div class="untappdusername" >';
                    $output .= '<a href="http://untappd.com/user/' . $feed->response->checkins->items[0]->user->user_name . '" ><span class="untappdrealname">' . $feed->response->checkins->items[0]->user->first_name . ' ' . $feed->response->checkins->items[0]->user->last_name . '</span></a>';
                    	if ($feed->response->checkins->items[0]->user->bio != '') {
                        	$output .= '<br><span class="untappdbio">' . $feed->response->checkins->items[0]->user->bio . '</span>';
                        }
                $output .= '</div>';
            $output .= '</div>';
            $output .= '<div class="checkincontainer">';
       
        foreach ($feed->response->checkins->items as $i) { 
            if($counter <= $limit){      
                $output .= '<div class="usercheckin">';
                     $output .= '<div class="userbeerlabel">';
                          $output .= '<img src="' . $i->beer->beer_label . '" alt="' . $i->beer->beer_name . '" />';
                     $output .= '</div>';
                     $output .= '<div class="userbeername">';
                         $output .= '<a href="https://untappd.com/beer/' . $i->beer->bid . '">' . $i->beer->beer_name . '</a><br>';
                         $output .= '<span>by <a href="' . $i->brewery->contact->url . '">' . $i->brewery->brewery_name . '</a></span>';         
                     $output .= '</div>';
                $output .= '</div>';
            }
            else{
                break;
            }
            $counter++; 
        }
            $output .= '</div>';
            $output .= '<div class="branding">';
                $output .= '<span>Data provided by <a href="http://untappd.com">Untappd</a></span>';
            $output .= '</div>';
        $output .= '</div>';
    }
    if($feedtype == 'venueFeed' && $id != ''){
        $output .= '<div class="untappdvenuefeed" >';
            $output .= '<div class="untappdvenueheading">';
                $output .= '<div class="untappdvenuepic" >';
                    $output .= '<a href="https://untappd.com/venue/' . $feed->response->checkins->items[0]->venue->venue_id . '" >';
                          $output .= '<img src="https://maps.googleapis.com/maps/api/staticmap?size=100x100&amp;center=' . $feed->response->checkins->items[0]->venue->location->lat . ',' . $feed->response->checkins->items[0]->venue->location->lng . '&amp;sensor=false&amp;zoom=13&amp;markers=color:yellow|label:Venue|' . $feed->response->checkins->items[0]->venue->location->lat . ',' . $feed->response->checkins->items[0]->venue->location->lng . '" alt="' . $feed->response->checkins->items[0]->venue->venue_name . '" />';
                    $output .= '</a>';
                $output .= '</div>';
                $output .= '<div class="untappdvenuename">';
                    $output .= 'Checkins at <a href="https://untappd.com/venue/' . $feed->response->checkins->items[0]->venue->venue_id . '" >' . $feed->response->checkins->items[0]->venue->venue_name . '</a>';
                $output .= '</div>';
            $output .= '</div>';
            $output .= '<div class="checkincontainer">';

            foreach ($feed->response->checkins->items as $i) {
                if($counter <= $limit){ 
                $output .= '<div class="venuecheckin">';
                    $output .= '<div class="venueuserpic" >';
                        $output .= '<a href="http://untappd.com/user/' . $i->user->user_name . '">';
                            $output .= '<img src="' . htmlentities($i->user->user_avatar) . '" alt="' . $i->user->user_name . '"/>';
                        $output .= '</a>';
                    $output .= '</div>';
                    $output .= '<div class="venueusername">';
                        $output .= '<a href="http://untappd.com/user/' . $i->user->user_name . '" >' . $i->user->user_name . '</a> is drinking <a href="https://untappd.com/beer/' . $i->beer->bid . '">' . $i->beer->beer_name . '</a> <span>by <a href="' . $i->brewery->contact->url . '">' . $i->brewery->brewery_name . '</a></span>';
                    $output .= '</div>';
                $output .= '</div>';      
                }
                else{
                    break;
                }
                $counter++; 
            }     
            $output .= '</div>';
            $output .= '<div class="branding">';
                $output .= '<span>Data provided by <a href="http://untappd.com">Untappd</a></span>';
            $output .= '</div>';
        $output .= '</div>';
    }     
    if($feedtype == 'breweryFeed' && $id != ''){
        $output .= '<div class="untappdbreweryfeed" >';
            $output .= '<div class="untappdbreweryheading">';
                $output .= '<div class="untappdbrewerypic">';
                    $output .= '<a href="' . $feed->response->checkins->items[0]->brewery->contact->url . '">';
                        $output .= '<img src="' . $feed->response->checkins->items[0]->brewery->brewery_label . '" alt="' . $feed->response->checkins->items[0]->brewery->brewery_name . '" />';
                    $output .= '</a>';
                $output .= '</div>';
                $output .= '<div class="untappdbreweryname">';
                    $output .= 'Checkins from ';
                    $output .= '<a href="' . $feed->response->checkins->items[0]->brewery->contact->url . '">';
                        $output .= $feed->response->checkins->items[0]->brewery->brewery_name;
                    $output .= '</a>';
                $output .= '</div>';
            $output .= '</div>';
            $output .= '<div class="checkincontainer">';
   
        foreach ($feed->response->checkins->items as $i) {
            if($counter <= $limit){ 
                $output .= '<div class="brewerycheckin">';
                    $output .= '<div class="breweryuserpic" >';
                        $output .= '<a href="http://untappd.com/user/' . $i->user->user_name . '" >';
                           $output .= '<img src="' . htmlentities($i->user->user_avatar) . '" alt="' . $i->user->user_name . '" />';
                        $output .= '</a>';
                    $output .= '</div>';
                    $output .= '<div class="breweryusername" >';
                        $output .= '<a href="http://untappd.com/user/' . $i->user->user_name . '" >' . $i->user->user_name . '</a> is drinking <a href="https://untappd.com/beer/' . $i->beer->bid . '">' . $i->beer->beer_name . '</a>';
                    $output .= '</div>';
                $output .= '</div>';
            }
            else{
                break;
            }
            $counter++; 
        }
            $output .= '</div>';
            $output .= '<div class="branding">';
                $output .= '<span>Data provided by <a href="http://untappd.com">Untappd</a></span>';
            $output .= '</div>';
        $output .= '</div>';
    }
    
    if($feedtype == 'beerFeed' && $id != ''){
        $output .= '<div class="untappdbeerfeed" >';
            $output .= '<div class="untappdbeerheading">';
                $output .= '<div class="untappdbeerpic">';
                    $output .= '<a href="https://untappd.com/beer/' . $feed->response->checkins->items[0]->beer->bid . '">';
                        $output .= '<img src="' . $feed->response->checkins->items[0]->beer->beer_label . '" alt="' . $feed->response->checkins->items[0]->beer->beer_name . '" />';
                    $output .= '</a>';
                $output .= '</div>';
                $output .= '<div class="untappdbeername">';
                    $output .= 'Checkins for <a href="https://untappd.com/beer/' . $feed->response->checkins->items[0]->beer->bid . '">' . $feed->response->checkins->items[0]->beer->beer_name . '</a> by <a href="' . $feed->response->checkins->items[0]->brewery->contact->url . '">' . $feed->response->checkins->items[0]->brewery->brewery_name . '</a>';
                $output .= '</div>';
            $output .= '</div>';
            $output .= '<div class="checkincontainer">';

        foreach ($feed->response->checkins->items as $i) {  
            if($counter <= $limit){       
                $output .= '<div class="beercheckin">';
                    $output .= '<div class="beeruserpic">';
                        $output .= '<a href="http://untappd.com/user/' . $i->user->user_name . '" >';
                            $output .= '<img src="' . htmlentities($i->user->user_avatar)' . " alt="' . $i->user->user_name . '" />';
                        $output .= '</a>';
                    $output .= '</div>';
                    $output .= '<div class="beerusername" >';
                        $output .= '<a href="http://untappd.com/user/' . $i->user->user_name . '" >' . $i->user->user_name . '</a> is drinking ' . $i->beer->beer_name;
                    $output .= '</div>';
                $output .= '</div>';
            }
            else{
                break;
            }
            $counter++; 
            $beerentries = '';
        }

            $output .= '</div>';
            $output .= '<div class="branding">';
                $output .= '<span>Data provided by <a href="http://untappd.com">Untappd</a></span>';
            $output .= '</div>';
        $output .= '</div>';
    }
    
    return $output; //Thanks Seth!
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
    add_menu_page("Tappd", "Tappd", "moderate_comments", "Tappd", "DRTappd_admin", plugins_url('img/dradminlogo.png', __FILE__));
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
add_action( 'admin_init', 'DR_add_admin_stylesheet' );
add_action('admin_menu', 'DRTappd_admin_actions');
?>

<?php

include SITE_FILE . 'lib/template.php';

class index extends template {

    function __construct() {
        $func = new functions();
        parent::setTemplate('home');
    }

    public function handlePage() {
        echo $this->showHomePage();
    }

    public function showHomePage() {
        $head = new header('home');
        echo $head->getHeader();
        $index_temp = parent::getTemplate();
        $foot = new footer();
        echo $foot->getFooter();
    }
	
	public function featured_artist(){
		$fun = new DatabaseFunctions();
        $func = new functions();
		$query_artist = "select 
						ad.name,
						ad.twitter_screen_name,
						ad.title_clean,
						ud.profile_id,
						ud.facebook_id,
						ud.twitter_id,
						ud.gplus_id
						from artist_details as ad
						join user_details as ud
							on ad.user_id = ud.user_id
						where
							ud.status = ? and
							ad.featured = ?
						order by rand()";
		$query_val = array('1', '1');
		$result = $fun->SelectFromTable($query_artist, $query_val);
			foreach($result as $key=>$value){
				$image_path =  "";
				if($value['facebook_id'] != ""){
					$image_path =  "https://graph.facebook.com/".$value['facebook_id']."/picture?type=large&width=434&height=434";
				}
				elseif($value['gplus_id'] != ""){
					$image_path = $func->google_image($value['gplus_id']);
				}
				elseif($value['twitter_id'] != ""){
					$image_path = "https://twitter.com/".$value['twitter_screen_name']."/profile_image?size=original";
				}
				$result[$key]['image'] = $image_path;
			}
		
			return $result =  json_encode($result);
	}
}

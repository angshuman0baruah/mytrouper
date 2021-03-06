<?php

include SITE_FILE . 'lib/template.php';

class songs extends template {

    function __construct() {
        $func = new functions();
        parent::setTemplate('songs');
    }

    public function handlePage() {
		if($_GET['action'] !=""){
			$this->handleAction($_GET['action']);
		}
		else {
			echo $this->showHomePage();
		}
    }

    public function showHomePage() {
        $head = new header('songs');
        echo $head->getHeader();
        $index_temp = parent::getTemplate();
        $foot = new footer();
        echo $foot->getFooter();
    }
	
	public function handleAction($action, $page = '', $filter = '',  $action_type=''){
		switch ($action) {
			case 'songsList': {
					$return = $this->songs_list();
					if($action_type != ""){
						echo $return;
					}
					else {
						return $return;
					}
					break;
				}
			default : {
					header('location:' . SITE_PATH . '');
				}
		}
	}
	
	private function songs_list($action_type = ''){
		$fun = new DatabaseFunctions();
        $func = new functions();
		$category_where = "";
		$getPage = $_REQUEST['page'];
		if ($getPage =="") {
            $getPage = 1;
        }
        $pageNumber = $getPage - 1;
        $limit = " LIMIT " . $pageNumber * 12 . " , " . 12;
			$query_artist = "select 
						audios.audio_id,
						audios.audio_file_name,
						audios.audio_title,
						audios.audio_length,
						audios.audio_title_clean,
						audios.audio_image_mime,
						ad.name,
						ad.title_clean,
						ud.profile_id
						from audios as audios
						join artist_details as ad
							on audios.user_id = ad.user_id
						join user_details as ud
							on audios.user_id = ud.user_id
						where
							ud.status = ?";
		if($filter_where != ""){
			$query_artist .= $category_where;
		}
		$query_artist .= $limit;
		$query_val = array('1');
		$result = $fun->SelectFromTable($query_artist, $query_val);
		if(!empty($result)){
			foreach($result as $key=>$value){
				$seconds = "";
				if($value['audio_length'] != ""){
					$seconds = $value['audio_length']; 
				}
				else {
					$seconds = 210;
				}
				$hours = floor($seconds / 3600);
				$mins = floor($seconds / 60 % 60);
				$secs = floor($seconds % 60);
				
				if($hours != 0){
					$new_length = sprintf('%02d:%02d:%02d',  $hours, $mins, $secs);
				}
				else {
					$new_length = sprintf('%02d:%02d',  $mins, $secs);
				}
				$result[$key]['audio_length'] = $new_length;
				
				if($value['audio_title'] == ""){
					$result[$key]['audio_title'] = $value['name']. "'s Song";
				}
				//unset($result[$key]['name']);
			}
		}
		else{
			$result = array();
		}
		$result =  json_encode($result);
		if(empty($result)){
			$result = array();
		}
		if($action_type != ""){
			return $result;
		}
		else {
			$songs = json_decode($result);
			if(!empty($songs)){
				foreach($songs as $key=>$value){
			?>
			<div class="col-sm-6 col-md-4">
				<div class="post-box audio horizontal clearfix">
					<a href="<?=SITE_PATH.'profile/'.$songs[$key]->title_clean.'/songs/'.$songs[$key]->audio_title_clean?>" data-play="true" data-title="<?=$songs[$key]->audio_title?>" data-file="<?=SITE_PATH.'res/uploads/audio/'.$songs[$key]->audio_file_name?>" class="image-link play-icon col-xs-4" style="padding-left:0">
						<?php /*
							if($songs[$key]->audio_image_mime == ""){
						?>
								<img src="<?=SITE_PATH?>res/img/audio-album-art.gif" width="150" height="150" alt="">
							<?php } else {
								echo ('<img src="data:'.$songs[$key]->audio_image_mime.';base64,'.$songs[$key]->audio_image.'" width="150" height="150"/>');
							} */?><img src="<?=SITE_PATH?>res/img/audio-album-art.gif" width="150" height="150" alt="">
					</a>
					<div class="extra-info">
						<p class="no-bottom"><a href="<?=SITE_PATH.'profile/'.$songs[$key]->title_clean.'/songs/'.$songs[$key]->audio_title_clean?>" class="black-link"><?= (strlen($songs[$key]->audio_title) > 48) ? substr($songs[$key]->audio_title,0,45).'...' : $songs[$key]->audio_title;?></a></p>
						<span class="small">Author: <a href="<?=SITE_PATH.'profile/'.$songs[$key]->title_clean?>"><?=$songs[$key]->name?></a></span>
						<p class="no-bottom"><span class="small">Duration: <?=$songs[$key]->audio_length?></span></p>
                        <div class="actions pull-right">
                            <span data-playlist="true" data-title="<?=$songs[$key]->audio_title?>" data-file="<?=SITE_PATH.'res/uploads/audio/'.$songs[$key]->audio_file_name?>">
                            	<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M14 10H2v2h12v-2zm0-4H2v2h12V6zm4 8v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM2 16h8v-2H2v2z"/></svg>
                		    </span>
                        </div>
					</div>
				</div>
			</div>
			<?php
			  if($key == 11){
				  break;
			  }
				}
			}
			else{
				echo '<h4>No Audios Found</h4>';
			}
			if(count($songs)>11){
				?>
				<div class="box col-sm-12 text-center" id="songs_<?=($getPage+1)?>">
					<a class="button color no-bottom load_more" data-type="songs" data-page="<?=($getPage+1)?>">Load More Audios</a>
				</div>
			<?php } 
		}
	}
}

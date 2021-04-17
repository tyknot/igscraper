<?php
require __DIR__ . '/vendor/autoload.php';
use Instagram\Api;
use Instagram\Auth\Checkpoint\ImapClient;
use Instagram\Exception\InstagramException;
use Instagram\Model\Media;
use Instagram\Model\MediaDetailed;
use Psr\Cache\CacheException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;


$cachePool = new FilesystemAdapter('Instagram', 0, __DIR__ . '/../cache');
// ig login
$api = new Api($cachePool);
$username = 'iphonecasehutt';
$password = 'tyknott20!';

$imapLogin = new ImapClient('smtp.gmail.com:465', 'johndsmittyy@gmail.com', 'tyknott20!');
$api->login($username, $password, $imapLogin);
// chromazz416 adam22 
$profile = $api->getProfile($_POST['user']);



function getProfile($profile, $api){

	try{
		// get ig profile/media
		echo '<pre>';
			echo '<div class="container">';
				echo '<div class="profile">';
					echo '<p id="profile_id">ID               : ' . $profile->getId().'</p>';
					echo '<p id="profile_name">Full Name        : ' . $profile->getFullName().'</p>';
					echo '<p id="profile_username">UserName         : ' . $profile->getUserName().'</p>';
					echo '<p id="profile_following">Following        : ' . $profile->getFollowing().'</p>';
					echo '<p id="profile_followers">Followers        : ' . $profile->getFollowers().'</p>';
					echo '<p id="profile_bio">Biography        : ' . $profile->getBiography().'</p>';
					echo '<p id="profile_bio_url">External Url     : ' . $profile->getExternalUrl().'</p>';
					echo '<p id="profile_picture">Profile Picture  : ' . $profile->getProfilePicture().'</p>';
					echo '<p id="profile_verified">Verified Account : ' . ($profile->isVerified().'</p>' ? 'Yes' : 'No');
					echo '<p id="profile_private">Private Account  : ' . ($profile->isPrivate().'</p>' ? 'Yes' : 'No');
					echo '<p id="profile_media_count">Medias Count     : ' . $profile->getMediaCount().'</p>';
						//print_r($api->getStories($profile->getId()));
						//print_r($profile->getMedias());

					// first 12 media
					foreach($profile->getMedias() as $value) {

						$media = new Media();
						$link = $value->getLink();
						$media->setLink($link);
						//print_r($mediaDetailed);
						$mediaDetailed = $api->getMediaDetailed($media);
						// mp4
						$videoUrl = $mediaDetailed->getvideoUrl();
						$sideCar = $mediaDetailed->getsideCarItems();
						//print_r($sideCar);
						$mediaCaption = $mediaDetailed->getcaption();
						// image/jpg
						$mediaSrc = $mediaDetailed->getdisplaySrc();

						$medialikes = $mediaDetailed->getlikes();
						$mediaType = $mediaDetailed->gettypeName();
						//echo $mediaType . "\n";
						
						
						//print_r($mediaDetailed);
						
						if($mediaType == "GraphImage"){
							// media is jpg
							$image = true;
							echo '<img id=graphimage src=' . $mediaSrc . '>' . "\n";
						}elseif($mediaType =="GraphVideo"){
							// media is mp4
							$video = true;
							//echo '<img id=video_media src=' . $mediaSrc . '>' . "\n";
							echo'<video width="150" height="240" controls controlsList="nodownload nofullscreen  noremoteplayback">' . 
									'<source src="' . $videoUrl . '"></source>
								</video>' . "\n";
						}elseif($mediaType =="GraphSidecar"){
							// media is multiple jpg // up to 10
							//$side = true;
							//print_r($sideCar);
							//echo $mediaSrc . "\n";
							foreach($sideCar as $side){
								$sideMediaSrc = $side->getDisplayResources()[0];
								echo $sideMediaSrc->src . "\n";
								//echo count($side->getDisplayResources());
							}
						}
					}
				echo '</div>';
			echo '</div>';
		echo '</pre>';

		}catch(InstagramException $e){
				print_r($e->getMessage());
		}catch (CacheException $e) {
			print_r($e->getMessage());
		}
	}




if(isset($_POST['user-submit'])){
	getProfile($profile, $api);
}	


	
	

 
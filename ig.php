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
/*$client = new Client([
	'proxy' => [
		'https' => '' #this is a dummy example, change with your proxy
	]
]);*/
$api = new Api($cachePool, /*$client*/);
$username = '';
$password = '';

$imapLogin = new ImapClient('smtp.gmail.com:465', '', '');
//sleep(5);
$api->login($username, $password, $imapLogin);
// chromazz416 adam22 
$data = json_decode(file_get_contents("php://input"), true);
$profile = $api->getProfile($_POST['user']);

$url = 'https://scontent.cdninstagram.com/v/t50.2886-16/163158867_450400006168173_399033240027273735_n.mp4?_nc_ht=instagram.forf1-3.fna.fbcdn.net&_nc_cat=109&_nc_ohc=9heL4d0-FoUAX9qzrFR&edm=AABBvjUBAAAA&ccb=7-4&oe=60B927D3&oh=7806be6d157732975f446ae42fb27937&_nc_sid=83d603';

/**
*returns base64 endcoded img due to ig cors policy
*/
function encodeImg($url){
	$imageData = base64_encode(file_get_contents($url));
	$src = 'data:image/jpg;base64, ' . $imageData;

	return $src;
	//echo '<img src="'.$src.'">';
}

/**
*returns base64 endcoded mp4 due to ig cors policy
*/
function encodeMp4($url){
	$videoData = base64_encode(file_get_contents($url));
	$src = 'data:video/mp4;base64, ' . $videoData;

	return $src;
	/*echo '<video width="150" height="240" controls controlsList="nodownload nofullscreen  noremoteplayback">' . 
		'<source src="' . $src2 . '"></source>
	</video>';*/
}

function getProfile($profile, $api){
	
	//login($username, $password, $imapLogin);
	try{
		// get ig profile/media
		echo '<pre>';
			echo '<div class="container">';
				echo '<div class="profile">';
					//echo '<p id="profile_id">ID               : ' . $profile->getId().'</p>';
					echo '<p id="profile_name">Full Name        : ' . $profile->getFullName().'</p>';
					echo '<p id="profile_username">UserName         : ' . $profile->getUserName().'</p>';
					echo '<p id="profile_following">Following        : ' . $profile->getFollowing().'</p>';
					echo '<p id="profile_followers">Followers        : ' . $profile->getFollowers().'</p>';
					echo '<p id="profile_bio">Biography        : ' . $profile->getBiography().'</p>';
					echo '<p id="profile_bio_url">     : ' . '<a href="'.$profile->getExternalUrl().'">'.$profile->getExternalUrl().'</a></p>';
					echo '<span id="profile_picture">' . '<img id=graphimage src="' . encodeImg($profile->getProfilePicture()) . '">'.'</span>';
					echo '<p id="profile_verified">Verified Account : ' . ($profile->isVerified().'</p>' ? 'Yes' : 'No');
					echo '<p id="profile_private">Private Account  : ' . ($profile->isPrivate().'</p>' ? 'Yes' : 'No');
					//echo '<p id="profile_media_count">Medias Count     : ' . $profile->getMediaCount().'</p>';
				echo '</div>';
					echo '<div class="media-container">';
					sleep(2);
					// first 12 media
					foreach($profile->getMedias() as $value) {

						$media = new Media();
						$link = $value->getLink();
						$media->setLink($link);
						$mediaDetailed = $api->getMediaDetailed($media);
						// mp4
						$videoUrl = $mediaDetailed->getvideoUrl();
						$sideCar = $mediaDetailed->getsideCarItems();
						$mediaCaption = $mediaDetailed->getcaption();
						// image/jpg
						$mediaSrc = $mediaDetailed->getdisplaySrc();
						$medialikes = $mediaDetailed->getlikes();
						$mediaType = $mediaDetailed->gettypeName();
						//echo $mediaType . "\n";
						
						if($mediaType == "GraphImage"){
							// media is jpg
							$image = true;
							//echo '<img id=graphimage src=' . $mediaSrc . '>' . "\n";
							echo '<img id=graphimage src="' . encodeImg($mediaSrc) . '">' . "\n";
						}elseif($mediaType =="GraphVideo"){ 
							// media is mp4
							$video = true;
							echo'<video width="150" height="240" controls controlsList="nodownload nofullscreen  noremoteplayback">' . 
									'<source src="' . encodeMp4($videoUrl) . '"></source>
								</video>' . "\n";
						}elseif($mediaType =="GraphSidecar"){
							// media is jpg format // up to 10 per media post
							$side = true;
							//print_r($sideCar);
							//echo $mediaSrc . "\n";
							foreach($sideCar as $side){
								$sideMediaSrc = $side->getDisplayResources()[0];
								$sideVideoUrl = $side->getvideoUrl();
								$sideType = $side->gettypeName();

								if($sideType == "GraphVideo"){
									echo'<video width="150" height="240" controls controlsList="nodownload nofullscreen  noremoteplayback">' . 
											'<source src="' . encodeMp4($sideVideoUrl) . '"></source>
										</video>' . "\n";
								}elseif($sideType == "GraphImage"){
									echo '<img id=graphimage src="' . encodeImg($sideMediaSrc->src) . '">' . "\n";
									//echo $sideMediaSrc->src . "\n";
								}
							}
						}
						//print_r($mediaDetailed);
					}
					
				echo '</div>';
				echo '<button name="btn" name="paginate" type="submit"></button>';
			echo '</div>';
		echo '</pre>';

		}catch(InstagramException $e){
				print_r($e->getMessage());
		}catch (CacheException $e) {
			print_r($e->getMessage());
		}
	}

	function paginate($profile, $api){
		// shows more media 
		do {
			$profile = $api->getMoreMedias($profile);
			print_r($profile->getMedias()); // 12 more medias

			// avoid 429 Rate limit from Instagram
			sleep(1);
		} while ($profile->hasMoreMedias());
	}

//echo encodeImg($url);
echo getProfile($profile, $api);

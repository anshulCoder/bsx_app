<?php namespace App\Controllers;

use ThirdParty\Tmdb;

class TmdbController extends BaseController
{

	public function fetch_latest_media()
	{
		$data = array();
		$tm = new Tmdb('08043c1eda8d8dda802ebcf46285bc70');
		echo '<pre>';
		$config_tmdb = $tm->getConfiguration();
		var_dump($config_tmdb);
		try
		{
			$page = 1;
			while (true) 
			{
				$movies = $tm->getNowPlayingMovies($page, 'en-IN');
				echo '<br>Found '.count($movies['results']).' movies, page: '.$page;
				
				if(!empty($movies['results']) && count($movies['results'])>0)
				{
					$mediaModel = model('App\Models\MediaModel', false);
					foreach($movies['results'] as $key => $row)
					{
						$movie_id = $row['id'];
						$actor = [];
						$actress = [];
						$director = [];
						$imgs = [];
						$movie_info = $tm->getMovieCast($movie_id);
						$movie_imgs = $tm->getMovieImages($movie_id);
						echo '<br>Fetching '.$key.' media with name '.$row['title'];
						if(count($movie_info['cast'])>0)
						{
							foreach($movie_info['cast'] as $cKey => $cRow)
							{
								if(count($actor)<1 && $cRow['known_for_department'] == 'Acting' && $cRow['gender'] == 2)
								{
									$actor[] = $cRow['name'];
								}
								if(count($actress)<1 && $cRow['known_for_department'] == 'Acting' && $cRow['gender'] == 1)
								{
									$actress[] = $cRow['name'];
								}
								if(count($director)<1 && $cRow['known_for_department'] == 'Directing')
								{
									$director[] = $cRow['name'];
								}
							}
						}

						if(!empty($movie_imgs['posters']))
						{
							$imgs = array_map(function($v) {return 'https://image.tmdb.org/t/p/w780'.$v['file_path'];}, array_slice($movie_imgs['posters'], 0, 10));
						}
						// save movie....
						$movie_data = array(
							'name' => $row['title'],
							'description' => $row['overview'],
							'release_date' => $row['release_date'],
							'rating' => $row['vote_average'],
							'media_images' => json_encode($imgs),
							'actors' => json_encode($actor),
							'actresses' => json_encode($actress),
							'directors' => json_encode($director)
						);
						$mediaModel->insert($movie_data);
						echo '<br>Media Saved for media #'.$key.' name: '.$row['title'];
					}
					$page++;
				}
				else
				{
					break;
				}
			}
			echo '<br>All Done!';
		}
		catch(\Exception $ex)
		{
			var_dump($ex->getMessage());
		}
	}
}
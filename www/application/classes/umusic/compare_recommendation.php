<?php
	/**
	*compares our recommendation to LastFM's
	*
	*this function computes the precision and recall of our recommendation.
	* Precision being the percentage of our recommendations that is also recommended by LastFM.
	* Or in other words songs recommended by both LastFM and us,
	* devided by the total of <b>our</b> recommended songs.
	* Recall is the percentage of LastFM's recommendations that is also recommended by us.
	* Or in other words it is the songs recommended by both LastFM and us,
	* devided by the total of <b>LastFM's</b> recommended songs.
	*
	*
	*@param part the part of all songs to check recommendations for defaults to 1/100000th of all songs.
	*@param min the bare minimum of our calculated similarity for which we consider two tracks similar.
	*@return the precision and recall in an array.
	*/
	public function compare_recommendations($part = 0.000001, min = 0.8) //for one song only for now since it's quite an extensive calculation.
	{
		/*$array = array(1000000);//this is just for keeping track of the tracks for which the recommendations are compared
		for($i = 0; i<1000000;i++)
		{
			array[i] = 0;
		}*/
		//quite unnecessary if we're only going to compare one song's recommendations
		
		$numerator = 0;
		$denomPrecision = 0;
		$denomRecall = 0;
	
		$amount = $part * 1000000;	
		for($i = 0;i<$amount;i++)
			{
			$song = lcg_value()*1000000;//(pseudo) random number (0,1), which makes the calculation that much easier than with a random integer
			
			/*$song = 0;
			do{
				$random = lcg_value()*1000000;//(pseudo) random number (0,1), which makes the calculation that much easier than with a random integer
				if(!($array[ceil($random)]))
				{
					$song = ceil($random);
				}
				elseif(!($array[floor($random)]))
				{
					$song = floor($random);
				}
			}while($song)
			$array[$song] = 1;*/
			//quite unnecessary if we're only going to compare one song's recommendations
			
			$query = DB::query(Database::SELECT, 'SELECT * FROM similars_dest WHERE rowid=":song"');
			$query->param(':song', $song);
			$result = $query->execute();
			$master = $result['tid'];//I can't reach the database so I don't know if 'tid' is correct
			$similars = $result['target'];
			$LastFMSimilars = explode(', ' ,$similars )//I can't reach the database so I don't know if it's ', ' or ','.
		

			//our recommendation, removed the return statement and sorting, it's unnecessary
			$tracktags = Jelly::query('tracktag')->select_all();
			$vectors = array();
			foreach($tracktags as $tracktag) {
				$trackid = $tracktag->track_id;
				$trackvector = json_decode($tracktag->tags);
				$sim = Umusic::cosSim($trackvector, $master);
				if($sim > $min)
				$vectors[$trackid] = $sim;
			}
			$denomPrecision = count($vectors);
	
			//compare -> precision and recall
			$count = count($LastFMSimilars);
			$denomRecall += $count/2;
			for($j=0;j<$count;j+=2)//yes this is correct LastFM's similarity (or dissimilarity I don't know at this point) values are in between the trackids
			{
				if(in_array($LastFMSimilars[j], array_keys($vectors)))
				{
					//in_array(strtolower($LastFMSimilars[j]), array_map('strtolower', array_keys($vectors))); in case capitalisation is different
					$numerator++;
				}
			}
			return array($numerator/$denomPrecision,$numerator/$denomRecall);
		}        
    }

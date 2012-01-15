<?php
	/**
	*compares our recommendation to LastFM's
	*
	*
	*This function will randomly select a song and consult the lastfm similarity datatbase to select the songs rated similar by lastfm.
	*Then it will compute if we find those songs to be similar. It will then compute the percentage of songs we found to be similar.
	*That percentage will give us an indication of how our recommendation compares to lastfm. (calculating recall only, not precision)
	*
	*
	*@param number the amount of songs to check recommendations for defaults to 1 song.
	*@param min the bare minimum of our calculated similarity for which we consider two tracks similar.
	*@return void this function will no longer return anything, rather it writes a file containing the results of the comparison.
	*/
	public function compare_recommendations($number = 1, min = 0.8) //for one song only for now since it's quite an extensive calculation.
	{
		$Checked = array();
		$comparisons = 0;
		$inCorrect = array();
		$correct = 0;
		for($i = 0;i<$number;i++)
			{
			do
			{
			$query1 = DB::query(Database::SELECT, 'SELECT * FROM similars_dest WHERE rowid=":song"');
			$query1->param(':song', mt_rand(0,1000000));
			$result = $query->execute();
			} while(in_array($result['tid'],$Checked))
			$master = $result['tid'];
			$Checked[] = $master;
			$similars = $result['target'];
			$LastFMSimilars = explode(',' ,$similars );
			
			$count = count($LastFMSimilars);
			$result2 = Jelly::query('tracktag')->where('track_id', '=',$master)->execute();
			$mastervector = json_decode(result2->tags);
			if($count>0)
			{
				$query3 = Jelly::query('tracktag')->where('track_id', '=',$LastFMSimilars[0]);
			}
			for($j=2;j<$count;j+=2)//yes, this is correct LastFM's similarity values are in between the trackids
			{
				$query3->or_where('track_id', '=',$LastFMSimilars[j]);
			}
			
			$vectors = array();
			$tracktags = $query3->execute();
			foreach($tracktags as $tracktag)
			{
				$trackid = $tracktag->track_id;
				$trackvector = json_decode($tracktag->tags);
				$sim = Umusic::cosSim($trackvector, $mastervector);
				$comparisons++;
				if($sim < $min)
				{
					$incorrect[] = $master . ': ' . $trackid;
				}
				else
				{
					$correct++;
				}
			}
			
		}
		$File = "comparing_results.txt";
		$fh = fopen($File, 'w') or die("can't open file");
		fwrite($fh, 'The amount of comparisons made is: ' . $comparisons);
		fwrite($fh, '\n The percentage of correct recommendations is: ' . ($correct*100/$comparisons) . '%\n');
		if(count($inCorrect)>0)
		{
			fwrite($fh, 'LastFM would have recommended these, but we didn\'t:\n');
			foreach($inCorrect as $pair)
			{
				fwrite($fh, 'for ' . $pair . '\n');
			}
		}
		fclose($fh);
	}

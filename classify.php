<?php

  include('FacebookStatusUpdate.class.php');
  $sentimentData = new FacebookStatusUpdate();
  $sentimentData->addToIndex('negativedata.txt', 'negative');
  $sentimentData->addToIndex('positivedata.txt', 'positive');
  
  //Add your data in a "$doc" variable.
  $doc="Uh the movie is so great that I thought I am watching a hollywood film.";
  //Put sentances of string into array.
  $sentences = explode(".", $doc);
  
  //Create array for positive and negative sentiment
  $score = array('positive' => 0, 'negative' => 0);
  
  //Loop through sentances and find sentiment
  foreach($sentences as $sentence) {
          if(strlen(trim($sentence))) {
                  $class = $sentimentData->classify($sentence);
                 
                 //Add sentiment to sentiment score
                  $score[$class]++;
          }
  }
 
  //Reverse sort the score in order to find the most likely sentiment (positive or negative) 
  
  arsort($score);
  
  //To find how much bias there is (assurance), divide the positive sentiment score by the negative
  
  if($score['positive']>0&&$score['negative']>0)
  {
    $assurance = $score['positive']/$score['negative'];
	  
	  //If the assurance is not a decimal...
	  if($assurance>1){
		  $assurance = $score['negative']/$score['positive'];
	  }
  }
  else
  {
	$assurance=1;	
  }
  
  //Remove the least likey alternative 
  array_pop($score);
  
  //<do better/>
  
  //As the sentiment (positive and negative) are the keys in the array, find the key of the sentiment.
  $keyArray = array_keys($score);
  
  //If we're not sure (the assurance (above) is less than .47), it's inconclusive. Otherwise, it's most likely good.
  if($assurance>.30){
      $sentiment = $keyArray[0];
  } else {
      $sentiment = "inconclusive";
  }
  
  //Echo out the sentiment
  echo $sentiment;
?>

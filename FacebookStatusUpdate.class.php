<?php

  class FacebookStatusUpdate{
			private $index = array();
			private $classes = array('positive', 'negative');
			private $classTokenCount = array('positive' => 0, 'negative' => 0);
			private $tokCount = 0;
			private $classDocCount = array('positive' => 0, 'negative' => 0);
			private $docCount = 0;
			private $prior = array('positive' => 0.5, 'negative' => 0.5);
			
		//Add the known data to the index. Takes a text file, and a sentiment (either postive or negative),
		//and if you want to limit the amount of sentiment data analyzed, enter the number of lines you want to sample as $limit.
		//Defaults to 0, meaning analyze everything.
			
			public function addToIndex($file, $class, $limit = 0) {
					$fh = fopen($file, 'r');
					$i = 0;
					if(!in_array($class, $this->classes)) {
							echo "Invalid class specified\n";
							return;
					}
					while($line = fgets($fh)) {
							if($limit > 0 && $i > $limit) {
									break;
							}
							$i++;
						   
							$this->docCount++;
							$this->classDocCount[$class]++;
							$tokens = $this->tokenize($line);
							foreach($tokens as $token) {
									if(!isset($this->index[$token][$class])) {
											$this->index[$token][$class] = 0;
									}
									$this->index[$token][$class]++;
									$this->classTokenCount[$class]++;
									$this->tokCount++;
							}
					}
					fclose($fh);
			}
		   
		   // Classify the data. Takes a string as a parameter.
			public function classify($document) {
					$this->prior['positive'] = $this->classDocCount['positive'] / $this->docCount;
					$this->prior['negative'] = $this->classDocCount['negative'] / $this->docCount;
					$tokens = $this->tokenize($document);
					$classScores = array();

					foreach($this->classes as $class) {
							$classScores[$class] = 1;
							foreach($tokens as $token) {
									$count = isset($this->index[$token][$class]) ?
											$this->index[$token][$class] : 0;

									$classScores[$class] *= ($count + 1) /
											($this->classTokenCount[$class] + $this->tokCount);
							}
							$classScores[$class] = $this->prior[$class] * $classScores[$class];
					}
				   
					arsort($classScores);
					return key($classScores);
			}
			
			//Find matches in either positive or negative sentiment. Takes a string as a parameter.
			private function tokenize($document) {
					$document = strtolower($document);
					preg_match_all('/\w+/', $document, $matches);
					return $matches[0];
			}
	}
?>

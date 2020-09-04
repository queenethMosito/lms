<?php
if(!defined("HELPER_QUESTIONNAIRE"))
{
	define("HELPER_QUESTIONNAIRE", 1);
	
	class Questionnaire_Helper
	{
		public function SelectQuestions($questions, $type, $previous)
		{
			if($type == "STD")
			{
				$selection = array();
				$index = 1;
				foreach($questions as $question)
				{
					$selection[$index] = array("number" => $question->number, "answer" => "");
					$index++;
				}
				return $selection;
			}
			$extra = null;
			if(strpos($type, "-") !== false) list($type, $extra) = explode("-", $type, 2);
			if(!$extra)
			{
				$temp = array();
				foreach($questions as $question)
				{
					$temp[] = array("number" => $question->number, "answer" => "");
				}
				shuffle($temp);
				$selection = array();
				$index = 1;
				foreach($temp as $answer) $selection[$index++] = $answer;
				return $selection;
			}
			else
			{
				$use = (int) $extra;
				
				// Create the pools based on the counts
				$pools = array();
				foreach($questions as $question)
				{
					// Count previous attempts
					$count = 0;
					foreach($previous as $prev)
					{
						foreach($prev as $answer) if($answer->number == 
							$question->number) $count++;
					}
					$count = 1000 + $count;
					$key = "c{$count}";
					if(!isset($pools[$key])) $pools[$key] = array();
					$pools[$key][] = $question;
				}
				
				// Sort the pools themselves, shuffle each pool and group into 1
				$selection = array();
				$index = 1;
				ksort($pools);
				foreach($pools as $pool) 
				{
					shuffle($pool);
					foreach($pool as $question)
					{
						if(sizeof($selection) < $use) $selection[$index++] = array("number" => $question->number, "answer" => "");
					}
				}
				return $selection;
			}
		} 
		
		public function DisplayQuestion($number, $question, $answer, $state, $show_scores)
		{
			$function = "Show{$question->type}";
			if(method_exists($this, $function))
			{
				print("<h3>Question {$number}</h3>");
				print("<div>{$question->question}</div>");
				$this->{$function}($number, $question, $answer, $state, $show_scores);
				return;
			}
			return;
			print("<pre>");
			print_r($question);
			print_r($answer);
			print_r($state);
			print("</pre>");
		}
		
		protected function ShowMS($number, $question, $answer, $state, $show_scores)
		{
			$answers = explode("|", $question->answer);
			while(strlen($answer->answer) < strlen($answers[0])) $answer->answer .= "F"; 
			
			switch($state)
			{
				case 0: // Not started
				case 1: // Progress
					$index = 0;
					?>
					<div style="padding-left: 20px">
					<table width="100%" cellspacing="0" cellpadding="10">
					<?php foreach($question->answers as $qanswer): ?>
						<?php
						$checked = $answer->answer[$index] == "T" ? " checked=\"checked\"" : "";
						$letter = chr(65 + $index);
						$id = "answer_{$index}";
						$name = "answer[{$index}]";
						?>
						<tr>
							<td width="35" align="center">
								<input type="checkbox"<?=$checked?> id="<?=$id?>" name="<?=$name?>" value="T" class="answer" />
								<?php /*<label for="<?=$id?>" style="font-weight: normal"><?=$letter?></label> */ ?>
							</td>
							<td>
								<label for="<?=$id?>" style="font-weight: normal"><?=$qanswer?></label>
							</td>
						</tr>
						<?php
						$index++;
						?>
					<?php endforeach; ?>
					</table>
					</div>
					<script type="text/javascript">
						$(".answer").click(function() { save(); });
					</script>
					<?php
					break;
				case 2: // Passed
				case 3: // Failed
					$index = 0;
					$temp = explode("|", $question->answer);
					$correctAnswer = $temp[0];
					?>
					<div style="padding-left: 20px">
					<?php foreach($question->answers as $qanswer): ?>
						<?php
						$extra = "";
						$checked = $answer->answer[$index] == "T" ? " checked=\"checked\"" : "";
						$letter = chr(65 + $index);
						if(($state == 2 || $show_scores == "During"))
						{
							if($question->scoring == "PA")
							{
							}
							elseif($question->scoring == "PQ")
							{
								if($answer->score == 0)
								{
									if($checked && $correctAnswer[$index] == "T") $extra = "(<span style=\"color: #090\">Correct</span>)";
									elseif($checked) $extra = "(<span style=\"color: #900\">Wrong answer</span>)";
									elseif($correctAnswer[$index] == "T") $extra = "(<span style=\"color: #009\">Correct answer</span>)";
								}
								else
								{
									if($checked) $extra = "(<span style=\"color: #090\">Correct</span>)";
								}
							}
						}
						else
						{
							if($checked) $extra = "(<span style=\"color: #009\">Your answer</span>)";
						}
						?>
						<p>
							<?=$letter?>. <?=$qanswer?> <b><?=$extra?></b>
						</p>
						<?php
						$index++;
						?>
					<?php endforeach; ?>
					</div>
					<?php
					break;
			}
		}
		
		protected function ShowMC($number, $question, $answer, $state, $show_scores)
		{			
			switch($state)
			{
				case 0: // Not started
				case 1: // Progress
					$index = 0;
					?>
					<div style="padding-left: 20px">
					<?php foreach($question->answers as $qanswer): ?>
						<?php
						$letter = chr(65 + $index);
						$checked = $answer->answer == $letter ? " checked=\"checked\"" : "";
						$id = "answer_{$index}";
						$name = "answer";
						?>
						<p>
							<input type="radio"<?=$checked?> id="<?=$id?>" name="<?=$name?>" value="<?=$letter?>" class="answer" />
							<label for="<?=$id?>" style="font-weight: normal"><?=$letter?>. <?=$qanswer?></label>
						</p>
						<?php
						$index++;
						?>
					<?php endforeach; ?>
					</div>
					<script type="text/javascript">
						$(".answer").click(function() { save(); });
					</script>
					<?php
					break;
				case 2: // Passed
				case 3: // Failed
					$index = 0;
					?>
					<div style="padding-left: 20px">
					<?php foreach($question->answers as $qanswer): ?>
						<?php
						$letter = chr(65 + $index);
						$checked = $answer->answer == $letter ? " checked=\"checked\"" : "";
						$extra = "";
						if(($state == 2 || $show_scores == "During"))
						{
							if($checked && $question->answer == $letter) $extra = "(<span style=\"color: #090\">Correct</span>)";
							elseif($checked) $extra = "(<span style=\"color: #900\">Wrong answer</span>)";
							elseif($question->answer == $letter) $extra = "(<span style=\"color: #009\">Correct answer</span>)";
						}
						else
						{
							if($checked) $extra = "(<span style=\"color: #009\">Your answer</span>)";
						}
						?>
						<p>
							<?=$letter?>. <?=$qanswer?> <b><?=$extra?></b>
						</p>
						<?php
						$index++;
						?>
					<?php endforeach; ?>
					</div>
					<?php
					break;
			}
		}
	
		protected function ShowIS($number, $question, $answer, $state, $show_scores)
		{			
			switch($state)
			{
				case 0: // Not started
				case 1: // Progress
					?>
					<div style="padding-left: 20px">
					<p>
						Missing word(s): <input name="answer" id="answer" value="<?=$answer->answer?>" style="width: 200px" />
					</p>
					</div>
					<script type="text/javascript">
						$("#answer").blur(function() { save(); });
					</script>
					<?php					
					break;
				case 2: // Passed
				case 3: // Failed
					?>
					<div style="padding-left: 20px">
					<p>
						Your answer: <?=$answer->answer?>
						<?php
						if($state == 2 || $show_scores == "During")
						{
							if($answer->score == 0)
							{
								print("(<b><span style=\"color: #900\">Incorrect</span></b>).");
								print("<br />Acceptable answers are <i>".str_replace("|", ", ", $question->answer)."</i>");
							}
							else
							{
								print("(<b><span style=\"color: #090\">Correct</span></b>).");
							}
						}
						?>
					</p>
					</div>
					<?php	
					break;
			}
		}
		
		protected function ShowMU($number, $question, $answer, $state, $show_scores)
		{			
			switch($state)
			{
				case 0: // Not started
				case 1: // Progress
					?>
					<div style="padding-left: 20px">
						<br />
						<table cellpadding="5" cellspacing="0">
						<?php for($i = 0; $i < sizeof($question->list); $i++): ?>
						<?php
							$name = "answer[{$i}]";
						?>
						<tr>
							<td rowspan="2" valign="top"><?=($i + 1)?></td>
							<td><?=$question->list[$i]?></td>
						</tr>
						<tr>
							<td>
								<div>
								<select name="<?=$name?>" class="answer">
									<?php for($j = 0; $j < sizeof($question->answers); $j++): ?>
									<?php 
										$letter = chr(65 + $j); 
										$checked = isset($answer->answer[$i]) && $answer->answer[$i] == $letter ? " selected=\"selected\"" : "";
									?>
									<option value="<?=chr(65 + $j)?>"<?=$checked?>><?=$letter?>. <?=$question->answers[$j]?></option>
									<?php endfor; ?>
								</select>
								</div>
								<br />
							</td>
						</tr>
						<?php endfor; ?>
						</table>
					</div>
					<script type="text/javascript">
						$(".answer").change(function() { save(); });
					</script>
					<?php					
					break;
				case 2: // Passed
				case 3: // Failed	
					?>
					<div style="padding-left: 20px">
					<?php for($i = 0; $i < sizeof($question->list); $i++): ?>
					<p><?=$question->list[$i]?></p>
					<br />
					<div style="padding-left: 20px">
						<b>You chose <?=$answer->answer[$i]?></b>
						<?php if($question->answer[$i] == $answer->answer[$i]): ?>
						(<b><span style="color: #090">Correct</span></b>) - 
						<?php 
							$val = ord($answer->answer[$i]) - 65;
							print($question->answers[$val]);
						?>
						<?php else: ?>
						(<b><span style="color: #900">Incorrect</span></b>) - 
						<?php
							$val = ord($answer->answer[$i]) - 65;
							print($question->answers[$val]);
							print("<br /><b>Correct answer is {$question->answer[$i]}</b> - ");
							$val = ord($question->answer[$i]) - 65;
							print($question->answers[$val]);
						?>
						<?php endif; ?>
					</div>
					<br />
					<?php endfor; ?>
					</div>
					<?php
					break;
			}
		}
		
		protected function ShowTF($number, $question, $answer, $state, $show_scores)
		{			
			$letters = array("T" => "True", "F" => "False");
			switch($state)
			{
				case 0: // Not started
				case 1: // Progress
					?>
					<div style="padding-left: 20px">
					<?php foreach($letters as $letter => $qanswer): ?>
						<?php
						$checked = $answer->answer == $letter ? " checked=\"checked\"" : "";
						$id = "answer_{$letter}";
						$name = "answer";
						?>
						<p>
							<input type="radio"<?=$checked?> id="<?=$id?>" name="<?=$name?>" value="<?=$letter?>" class="answer" />
							<label for="<?=$id?>" style="font-weight: normal"><?=$qanswer?></label>
						</p>
					<?php endforeach; ?>
					</div>
					<script type="text/javascript">
						$(".answer").click(function() { save(); });
					</script>
					<?php					
					break;
				case 2: // Passed
				case 3: // Failed	
					$index = 0;
					?>
					<div style="padding-left: 20px">
					<?php foreach($letters as $letter => $qanswer): ?>
						<?php
						$checked = $answer->answer == $letter ? " checked=\"checked\"" : "";
						$extra = "";
						if(($state == 2 || $show_scores == "During"))
						{
							if($checked && $question->answer == $letter) $extra = "(<span style=\"color: #090\">Your answer - correct</span>)";
							elseif($checked) $extra = "(<span style=\"color: #900\">Your answer - incorrect</span>)";
							elseif($question->answer == $letter) $extra = "(<span style=\"color: #009\">Correct answer</span>)";
						}
						else
						{
							if($checked) $extra = "(<span style=\"color: #009\">Your answer</span>)";
						}
						?>
						<p>
							<b><?=$qanswer?></b> <b><?=$extra?></b>
						</p>
						<?php
						$index++;
						?>
					<?php endforeach; ?>
					</div>
					<?php
					break;
			}
		}
		
		public function VerifyAnswers($questionnaire, $answers)
		{
			$questions = array();
			if(!isset($questionnaire->data)) 
			{
				$questionnaire->data = $questionnaire->questions;
			}
			foreach($questionnaire->data as $question) $questions[$question->number] = $question;
			
			foreach($answers as $userNumber => $answer)
			{
				$answer->answered = false;
				$answer->warnings = array();
				$answer->score = 0;
				$answer->totalScore = 0;
				$question = $questions[$answer->number];
				
				$answer->correct = $question->answer;
				
				switch($question->type)
				{
					case "MS":
						// Answered
						$answer->answered = trim($answer->answer) != "";
						
						// Scoring and total score
						if($question->scoring == "PA")
						{
							$answer->totalScore = $question->score * sizeof($question->answers); 
							if($answer->answered)
							{
								$list = explode("|", $question->answer);
								$correct = $list[0];
								for($index = 0; $index < strlen($answer->answer); $index++)
								{
									if($correct[$index] == $answer->answer[$index]) $answer->score++;
								}
							}
						}
						elseif($question->scoring == "PQ")
						{
							$answer->totalScore = $question->score;
							if($answer->answered)
							{
								$list = explode("|", $question->answer);
								if(in_array($answer->answer, $list)) $answer->score = $question->score;
							}
						}
						break;
					case "MC":
					case "TF":
						// Answered
						$answer->answered = trim($answer->answer) != "";
						
						// Scoring and total score
						$answer->totalScore = $question->score;
						$answer->score = ($question->answer == $answer->answer) ? $question->score : 0;
						break;
					case "IS":
						$word = trim(strtolower($answer->answer));
						
						// Answered
						$answer->answered = $word != "";
						
						// Scoring and total score
						$answer->totalScore = $question->score;
						$scores = array();
						$words = explode("|", $question->answer);
						foreach($words as $w)
						{
							$perc = 0.0;
							similar_text($word, $w, $perc);
							$scores[] = $perc;
						}
						$max = max($scores);
						$answer->score = $max >= 85.0 ? $question->score : 0;
						break;
					case "MU":
						// Answered
						$answer->answered = trim($answer->answer) != "";
						
						// Scoring and total score
						if($question->scoring == "PA")
						{
							$answer->totalScore = $question->score * sizeof($question->list); 
							if($answer->answered)
							{
								$correct = $question->answer;
								for($index = 0; $index < strlen($answer->answer); $index++)
								{
									if($correct[$index] == $answer->answer[$index]) $answer->score++;
								}
							}
						}
						elseif($question->scoring == "PQ")
						{
							$answer->totalScore = $question->score;
							if($answer->answered)
							{
								if($answer->answer == $question->answer) $answer->score = $question->score;
							}
						}
						
						// Warnings
						$counts = array();
						for($i = 0; $i < strlen($answer->answer); $i++)
						{
							$letter = $answer->answer[$i];
							if(!isset($counts[$letter])) $counts[$letter] = 0;
							$counts[$letter]++;
						}
						$double = false;
						foreach($counts as $count) if($count > 1) $double = true;
						if($double)
						{
							$answer->warnings[] = "You have matched an answer in question {$userNumber} more than once";
						}
						break;
				}
			}
		}
	}
}
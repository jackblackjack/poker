<?php  
class Card extends Common
{
	public $suits = array(
		'h'=>'Hearts', 'd'=>'Diamonds', 'c'=>'Clubs', 's'=>'Spades'
	);
	public $values = array(
		2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10, 
		'J'=>11, 'Q'=>12, 'K'=>13, 'A'=>14   
	);
	
	public function __construct($suit, $value)
	{
		if(in_array($suit, array_keys($this->suits))){
			$this->suit = $suit;
		}
		
		if(in_array($value, array_keys($this->values))){
			$this->value = $value;
		}
	}
}

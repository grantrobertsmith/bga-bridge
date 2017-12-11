
<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Belote implementation : © David Bonnin <david.bonnin44@gmail.com>
 * Bridge implementation : © Grant Smith <grantrobertsmith@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * bridge.game.php
 *
 * This is the main file for your game logic.
 *
 * In this PHP file, you are going to defines the rules of the game.
 *
 */
require_once (APP_GAMEMODULE_PATH . 'module/table/table.game.php');

// Local constants
//  - Team pairing options

define("TEAM_1_3", 1); // By table order (1rst/3rd versus 2nd/4th)
define("TEAM_1_2", 2); // By table order (1rst/2nd versus 3rd/4th)
define("TEAM_1_4", 3); // By table order (1rst/4th versus 2nd/3rd)
define("TEAM_RANDOM", 4); // At random
class Bridge extends Table

{
	function __construct( )
	{

		// Your global variables labels:
		//  Here, you can assign labels to global variables you are using for this game.
		//  You can use any number of global variables with IDs between 10 and 99.
		//  If your game has options (variants), you also have to associate here a label to
		//  the corresponding ID in gameoptions.inc.php.
		// Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue

		parent::__construct();
		self::initGameStateLabels(array(
			"trickColor" => 11,
			"trickWinner" => 12,
			"trumpColor" => 13,
			"winningCard" => 14,
			"dealer" => 15,
			"firstPlayer" => 16,
			"dixDeDer" => 17,
			"taker" => 19,
			"passCount" => 20,
			"cardOnTop" => 21,
			"litige" => 25,
			"hands" => 26,
			"rebeloteS" => 30,
			"rebeloteH" => 31,
			"rebeloteC" => 32,
			"rebeloteD" => 33,
			"gameLength" => 100,
			"playerTeams" => 101,
			"turnOrder" => 102,
			"allNoTrumps" => 110, // 1 = disabled, 2 = enabled
			
		));
		$this->cards = self::getNew("module.common.deck");
		$this->cards->init("card");
	}

	protected
	function getGameName()
	{
		return "bridge";
	}

	/*
	setupNewGame:
	This method is called only once, when a new game is launched.
	In this method, you must setup the game according to the game rules, so that
	the game is ready to be played.
	*/
	protected
	function setupNewGame($players, $options = array())
	{

		// Set the colors of the players with HTML color code

		$default_colors = array(
			"000000",
			"ff0000",
			"000000",
			"ff0000"
		);
		$start_points = 0;
		$end_points = self::getGameStateValue('gameLength');
		if ($end_points == 1) {
			$end_points = 1000;
		}
		else
		if ($end_points == 2) {
			$end_points = 500;
		}
		else
		if ($end_points == 3) {
			$end_points = 1;
		}
		else {
			throw new BgaVisibleSystemException("Error, gameLength value is not in [1,3]");
		}

		// Create players
		// Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.

		$sql = "INSERT INTO player (player_id, player_no, player_score, player_color, player_canal, player_name, player_avatar) VALUES ";
		$values = array();
		$counter = 0;
		$random_dealer = mt_rand(1, 4);
		$order_values = array();
		foreach($players as $player_id => $player) {
			$order_values[] = $player["player_table_order"];
		}

		sort($order_values);
		$position = array();
		foreach($order_values as $key => $val) {
			$position[$val] = $key + 1;
		}

		foreach($players as $player_id => $player) {
			$color = "ffffff"; // Default to white (should never be left to white unless the following doesn't work)
			$player_no = 9; // Default to 9 (should never be left to 9 unless the following doesn't work)
			$counter++;
			if (self::getGameStateValue('playerTeams') == TEAM_RANDOM) {
				$color = array_shift($default_colors); // Random since the $players order is random
				$player_no = $counter;
			}
			else
			if (isset($player["player_table_order"])) {

				// By default TEAM_1_3

				$table_order = $position[$player["player_table_order"]];

				// If TEAM_1_2 swap 2 and 3

				if (self::getGameStateValue('playerTeams') == TEAM_1_2) {
					$table_order = ($table_order == 2 ? 3 : ($table_order == 3 ? 2 : $table_order));
				} // If TEAM_1_4 swap 4 and 3
				else
				if (self::getGameStateValue('playerTeams') == TEAM_1_4) {
					$table_order = ($table_order == 3 ? 4 : ($table_order == 4 ? 3 : $table_order));
				}

				if (isset($default_colors[$table_order - 1])) {
					$color = $default_colors[$table_order - 1];
					$player_no = ($table_order >= $random_dealer ? // Adjust player_no for randomizing first player (dealer)
					$table_order - $random_dealer + 1 : 4 - ($random_dealer - $table_order) + 1);
				}
			}

			$values[] = "('" . $player_id . "','" . $player_no . "','$start_points','$color','" . $player['player_canal'] . "','" . addslashes($player['player_name']) . "','" . addslashes($player['player_avatar']) . "')";
		}

		$sql.= implode($values, ',');
		self::DbQuery($sql);
		self::reloadPlayersBasicInfos();
		/************ Start the game initialization *****/

		// No trick color, trick winner, trump color, trick winning card

		self::setGameStateInitialValue('trickColor', -1);
		self::setGameStateInitialValue('trickWinner', -1);
		self::setGameStateInitialValue('trumpColor', -1);
		self::setGameStateInitialValue('winningCard', -1);

		// No one has got the "dix de der" or "belote+rebelote"

		self::setGameStateInitialValue('dixDeDer', 0);
		self::setGameStateInitialValue('rebeloteS', 0);
		self::setGameStateInitialValue('rebeloteH', 0);
		self::setGameStateInitialValue('rebeloteC', 0);
		self::setGameStateInitialValue('rebeloteD', 0);


		// No one has taken the visible card, and no one has passed

		self::setGameStateInitialValue('taker', -1);
		self::setGameStateInitialValue('passCount', 0);

		// Visible card not initialized yet

		self::setGameStateInitialValue('cardOnTop', -1);

		// No previous contention

		self::setGameStateInitialValue('litige', 0);

		// Total number of played hands = 0

		self::setGameStateInitialValue('hands', 0);

		// Score to be obtained to end tha game

		self::setGameStateInitialValue('gameLength', $end_points);

		// Init game statistics

		self::initStat("table", "passedHandNbr", 0);
		self::initStat("table", "playedHandNbr", 0);
		self::initStat("table", "litigeNbr", 0);
		self::initStat("player", "takenFirstNbr", 0);
		self::initStat("player", "takenSecondNbr", 0);
		self::initStat("player", "takenThirdNbr", 0);
		self::initStat("player", "wonHandNbr", 0);
		self::initStat("player", "dedansNbr", 0);
		self::initStat("player", "capotNbr", 0);
		self::initStat("player", "dixDeDerNbr", 0);
		self::initStat("player", "beloteNbr", 0);
		self::initStat("player", "trumpNbr", 0);
		self::initStat("player", "litigeWonNbr", 0);
		self::initStat("player", "litigeLostNbr", 0);
		self::initStat("player", "averageScore", 0.0);

		// Create cards

		$cards = array(); //(0 => 0);
		foreach($this->colors as $color_id => $color) // spade, heart, diamond, club
		{
			if ($color_id < 5) {
				for ($value = 2; $value <= 14; $value++) //  2, 3, 4, ... K, A
				{
					$cards[] = array(
						'type' => $color_id,
						'type_arg' => $value,
						'nbr' => 1
					);
				}
			}
		}

		$this->cards->createCards($cards, 'deck');
		$this->activeNextPlayer();
		$currentDealer = self::getActivePlayerId();
		self::setGameStateInitialValue('dealer', $currentDealer);
		self::setGameStateInitialValue('firstPlayer', $currentDealer);
		$this->activeNextPlayer();
		/************ End of the game initialization *****/
	}

	/*
	getAllDatas:
	Gather all informations about current game situation (visible by the current player).
	The method is called each time the game interface is displayed to a player, ie:
	_ when the game starts
	_ when a player refreshes the game page (F5)
	*/
	protected
	function getAllDatas()
	{
		$result = array(
			'players' => array()
		);
		$result['trump'] = self::getGameStateValue('trumpColor');
		$result['dealer'] = self::getGameStateValue('dealer');
		$result['taker'] = self::getGameStateValue('taker');
		$cardOnTop_id = self::getGameStateValue('cardOnTop');
		if ($cardOnTop_id != - 1) {
			$cardOnTop = $this->cards->getCard($cardOnTop_id);
			$result['cardOnTop_id'] = $cardOnTop['id'];
			$result['cardOnTop_color'] = $cardOnTop['type'];
			$result['cardOnTop_val'] = $cardOnTop['type_arg'];
		}
		else {
			$result['cardOnTop_id'] = - 1;
			$result['cardOnTop_color'] = - 1;
			$result['cardOnTop_val'] = - 1;
		}

		$current_player_id = self::getCurrentPlayerId(); // !! We must only return informations visible by this player !!

		// Get information about players

		$sql = "SELECT player_id id, player_score score, player_tricks tricks FROM player ";
		$result['players'] = self::getCollectionFromDb($sql);

		// Cards in player hand
		$result['hand'] = $this->cards->getCardsInLocation('hand', $current_player_id);

		// Cards played on the table
		$result['cardsontable'] = $this->cards->getCardsInLocation('cardsontable');

		// Clockwise or counterclockwise
		$result['orientation'] = self::getGameStateValue('turnOrder');

		return $result;
	}

	/*
	getGameProgression:
	Compute and return the current game progression.
	The number returned must be an integer beween 0 (=the game just started) and
	100 (= the game is finished or almost finished).
	This method is called each time we are in a game state with the "updateGameProgression" property set to true
	(see states.inc.php)
	*/
	function getGameProgression()
	{
		$maximumScore = self::getUniqueValueFromDb("SELECT MAX( player_score ) FROM player");
		$minimumScore = self::getUniqueValueFromDb("SELECT MIN( player_score ) FROM player");
		$end = self::getGameStateValue('gameLength');
		if ($maximumScore >= $end) {
			return 100;
		}

		if ($maximumScore <= 0) {
			return 0;
		}

		$n = 2 * ($end - $maximumScore);
		$res = (100 * ($maximumScore + $minimumScore)) / ($n + $maximumScore + $minimumScore);
		return max(0, min(100, $res)); // Note: 0 => 100
	}

	// ////////////////////////////////////////////////////////////////////////////
	// ////////// Utility functions
	// //////////
	// Return players => direction (N/S/E/W) from the point of view
	//  of current player (current player must be on south)

	function getPlayersToDirection()
	{
		$result = array();
		$players = self::loadPlayersBasicInfos();
		$nextPlayer = self::createNextPlayerTable(array_keys($players));
		$current_player = self::getCurrentPlayerId();
		$turnOrder = self::getGameStateValue('turnOrder');
		if ($turnOrder == 1) $directions = array(
			'S',
			'E',
			'N',
			'W'
		); // counterclockwise order
		else
		if ($turnOrder == 2) $directions = array(
			'S',
			'W',
			'N',
			'E'
		); // clockwise order
		else {
			$directions = array(
				'S',
				'E',
				'N',
				'W'
			); // counterclockwise order

			// throw new BgaVisibleSystemException("Error, not a valid turn order option");

		}

		if (!isset($nextPlayer[$current_player])) {

			// Spectator mode: take any player for south

			$player_id = $nextPlayer[0];
			$result[$player_id] = array_shift($directions);
		}
		else {

			// Normal mode: current player is on south

			$player_id = $current_player;
			$result[$player_id] = array_shift($directions);
		}

		while (count($directions) > 0) {
			$player_id = $nextPlayer[$player_id];
			$result[$player_id] = array_shift($directions);
		}

		return $result;
	}

	function isCardStronger($card1, $card2, $isTrump)
	{
		if ($isTrump) {
			return ($this->cardToRank['trump'][$card1] > $this->cardToRank['trump'][$card2]);
		}
		else {
			return ($this->cardToRank['normal'][$card1] > $this->cardToRank['normal'][$card2]);
		}
	}

	// ////////////////////////////////////////////////////////////////////////////
	// ////////// Player actions
	// //////////

	/*
	Each time a player is doing some game action, one of the methods below is called.
	(note: each method below must match an input method in bridge.action.php)
	*/

	// Play a card from player hand

	function playCard($card_id)
	{
		self::checkAction("playCard");
		$player_id = self::getActivePlayerId();

		// Get all cards in player hand
		// (note: we must get ALL cards in player's hand in order to check if the card played is correct)
		$playerhands = array();
		$playerhands = $this->cards->getCardsInLocation('hand', $player_id);
		$players = self::loadPlayersBasicInfos();
		$nextPlayer = self::createNextPlayerTable(array_keys($players));
		$currentTrickColor = self::getGameStateValue('trickColor');
		$currentTrickWinner = self::getGameStateValue('trickWinner');
		$currentTrumpColor = self::getGameStateValue('trumpColor');
		$currentWinningCard = self::getGameStateValue('winningCard');
		if ($currentWinningCard != - 1) $currentWinningCard = $this->cards->getCard($currentWinningCard);

		$bIsWinnerPartner = ($currentTrickWinner == $nextPlayer[$nextPlayer[$player_id]]);

		// Check that the card is in his hand
		$bIsInHand = false;
		$currentCard = null;
		$bAtLeastOneCardOfCurrentTrickColor = false;
		$bAtLeastOneCardOfCurrentTrickColorOfGreaterValue = false; // Used for all trumps
		$bAtLeastOneCardTrump = false;
		$bAtLeastOneCardTrumpOfGreaterValue = false;
		foreach($playerhands as $card) {
			if ($card['id'] == $card_id) {
				$bIsInHand = true;
				$currentCard = $card;
			}

			if ($card['type'] == $currentTrickColor) {
				$bAtLeastOneCardOfCurrentTrickColor = true;
				if ($currentTrumpColor == 6 /*All trumps */ && $currentWinningCard > 0) {
					if ($this->isCardStronger($card['type_arg'], $currentWinningCard['type_arg'], true)) {
						$bAtLeastOneCardOfCurrentTrickColorOfGreaterValue = true;
					}
				}
			}

			if ($card['type'] == $currentTrumpColor) {
				$bAtLeastOneCardTrump = true;
				if ($currentWinningCard > 0)
				if ($currentWinningCard['type'] == $currentTrumpColor && $this->isCardStronger($card['type_arg'], $currentWinningCard['type_arg'], true)) {
					$bAtLeastOneCardTrumpOfGreaterValue = true;
				}
			}
		}

		if (!$bIsInHand) throw new BgaUserException("This card is not in your hand");
		if ($currentTrickColor == - 1) {

			// You can play any card

		}
		else {

			// The trick started before => we must check the color

			if ($bAtLeastOneCardOfCurrentTrickColor) {

				// he has to play a card of current trick color, and has at least one

				if ($currentCard['type'] != $currentTrickColor) {
					$cardColor = $this->colors[$currentTrickColor]['name'];
					throw new BgaUserException(sprintf( _("You must play a %s"), $cardColor ));
				}
				if ($currentTrumpColor == 6 /*All trumps */ && $bAtLeastOneCardOfCurrentTrickColorOfGreaterValue && $currentWinningCard > 0) {
					if (!($this->isCardStronger($currentCard['type_arg'], $currentWinningCard['type_arg'], true))) {
						$cardValue = $this->values_label[$currentWinningCard['type_arg']];
						$cardColor = $this->colors[$currentTrickColor]['name'];
						throw new BgaUserException(sprintf( _("You must play a card stronger than %s of %s"), $cardValue, $cardColor ));
					}
				}
			}

			if ($bAtLeastOneCardTrump) {

				// he has no card of current color, his partner is not winning, and he has a trump -> he has to play a trump

				if ((!$bAtLeastOneCardOfCurrentTrickColor) && $currentCard['type'] != $currentTrumpColor && !$bIsWinnerPartner) {
					$trumpColorName = $this->colors[$currentTrumpColor]['name'];
					throw new BgaUserException(sprintf( _("You must play a Trump card (%s)"), $trumpColorName ));
				}

				// he has to use a trump and owns a trump stronger than the strongest winning card -> he has to play such a trump

				else
				if (((!$bAtLeastOneCardOfCurrentTrickColor && !$bIsWinnerPartner) || $currentTrickColor == $currentTrumpColor) && $bAtLeastOneCardTrumpOfGreaterValue && $currentWinningCard > 0 && !($this->isCardStronger($currentCard['type_arg'], $currentWinningCard['type_arg'], true))) {
					$cardValue = $this->values_label[$currentWinningCard['type_arg']];
					$cardColor = $this->colors[$currentTrumpColor]['name'];
					throw new BgaUserException(sprintf( _("You must play a Trump card stronger than %s of %s"), $cardValue, $cardColor ));
				}
			}
		}

		// Checks are done! now we can play our card
		$this->cards->moveCard($card_id, 'cardsontable', $player_id);


		// Set the trick color if it hasn't been set yet
		if ($currentTrickColor == - 1) self::setGameStateValue('trickColor', $currentCard['type']);
		if ($currentWinningCard == - 1) {
			self::setGameStateValue('winningCard', $card_id);
			self::setGameStateValue('trickWinner', $player_id);
		}
		else
		if (($currentCard['type'] == $currentWinningCard['type'] && $this->isCardStronger($currentCard['type_arg'], $currentWinningCard['type_arg'], $currentCard['type'] == $currentTrumpColor || $currentTrumpColor == 6 /*All trumps*/)) || ($currentCard['type'] == $currentTrumpColor && $currentWinningCard['type'] != $currentTrumpColor)) {
			self::setGameStateValue('winningCard', $card_id);
			self::setGameStateValue('trickWinner', $player_id);
		}

		// And notify
		self::notifyAllPlayers('playCard', clienttranslate('${player_name} plays ${card_value}${card_type}') , array(
			'i18n' => array(
				'color_displayed',
				'value_displayed'
			) ,
			'card_id' => $card_id,
			'player_id' => $player_id,
			'player_name' => self::getActivePlayerName() ,
			'value' => $currentCard['type_arg'],
			'color' => $currentCard['type'],
			'card_value' => $this->values_label[$currentCard['type_arg']],
			'card_type' => $this->icons[$currentCard['type']]
		));

	
		if($currentCard['type'] == $currentTrumpColor || $currentTrumpColor == 6 /*All trumps*/){
			
			$rebelote = 0; 
			// Value 0 if no belote-rebelote during this hand
			// -2 if some player has both, and didn't play any yet
			// -1 if "belote" has been said
			// player_id if "belote" and "rebelote" have both been said


			$rebeloteName = '';
			switch ($currentCard['type']) {
				case 1:
					$rebeloteName = 'rebeloteS';
					break;
				case 2:
					$rebeloteName = 'rebeloteH';
					break;
				case 3:
					$rebeloteName = 'rebeloteC';
					break;
				case 4:
					$rebeloteName = 'rebeloteD';
					break;
			}
			if ($rebeloteName != ''){
				$rebelote = self::getGameStateValue($rebeloteName); 
			}

			
			if ($rebelote == - 1 && ($currentCard['type_arg'] == 12 || $currentCard['type_arg'] == 13)) { // Q or K trump
				self::incStat(1, "beloteNbr", $player_id);
				self::notifyAllPlayers('belote', clienttranslate('${player_name} says "Rebelote !"') , array(
					'player_id' => $player_id,
					'player_name' => $players[$player_id]['player_name'],
					'speech' => clienttranslate('Rebelote !') ,
				));
				self::setGameStateValue($rebeloteName, $player_id); // Remember the player who said belote+rebelote
			}
			else
			if ($rebelote == - 2 && ($currentCard['type_arg'] == 12 || $currentCard['type_arg'] == 13)) { // Q or K trump
				self::notifyAllPlayers('belote', clienttranslate('${player_name} says "Belote !"') , array(
					'player_id' => $player_id,
					'player_name' => $players[$player_id]['player_name'],
					'speech' => clienttranslate('Belote !') ,
				));
				self::setGameStateValue($rebeloteName, -1);
			}

		}

		// Next player

		$this->gamestate->nextState('playCard');
	}

	function commonAccept($player_id, $trumpColor)
	{
		self::setGameStateValue('hands', 1 + self::getGameStateValue('hands'));
		self::setGameStateValue('taker', $player_id);
		self::setGameStateValue('trumpColor', $trumpColor);
	}

	function acceptFirstRound()
	{
		self::checkAction("acceptFirstRound");
		$players = self::loadPlayersBasicInfos();
		$player_id = self::getActivePlayerId();
		$topCard = $this->cards->getCard(self::getGameStateValue('cardOnTop'));
		self::commonAccept($player_id, $topCard['type']);
		self::incStat(1, "takenFirstNbr", $player_id);
		self::notifyAllPlayers('takeCard', clienttranslate('${player_name} takes the card ${card_value}${card_type}') , array(
			'player_id' => $player_id,
			'player_name' => $players[$player_id]['player_name'],
			'card_value' => $this->values_label[$topCard['type_arg']],
			'card_type' => $this->icons[$topCard['type']],
			'trump' => $topCard['type']
		));
		$this->gamestate->nextState('accept');
	}

	function pass()
	{
		$player_id = self::getActivePlayerId();
		$players = self::loadPlayersBasicInfos();
		self::notifyAllPlayers('pass', clienttranslate('${player_name} passes') , array(
			'player_id' => $player_id,
			'player_name' => $players[$player_id]['player_name']
		));
		$passCount = self::getGameStateValue('passCount');
		$passCount++;
		self::setGameStateValue('passCount', $passCount);
		$this->gamestate->nextState('pass');
	}

	function passFirstRound()
	{
		self::checkAction("passFirstRound");
		self::pass();
	}

	function passSecondRound()
	{
		self::checkAction("passSecondRound");
		self::pass();
	}

	function passThirdRound()
	{
		self::checkAction("passThirdRound");
		self::pass();
	}

	function acceptSecondRound($color)
	{
		self::checkAction("acceptSecondRound");

		$players = self::loadPlayersBasicInfos();
		$player_id = self::getActivePlayerId();
		$topCard = $this->cards->getCard(self::getGameStateValue('cardOnTop'));
		if ($topCard >0 && $topCard['type'] == $color) {
			throw new BgaUserException(self::_("You cannot choose the same Trump as the visible card during second round"));
		}

		if($color > 4) {
			throw new BgaUserException(self::_("You cannot choose All trumps or No trumps during second round"));
		}

		self::commonAccept($player_id, $color);
		self::incStat(1, "takenSecondNbr", $player_id);
		self::notifyAllPlayers('takeCard', clienttranslate('${player_name} chooses ${trump_icon} as the trump suit and takes the card ${card_value}${card_type}') , array(
			'player_id' => $player_id,
			'player_name' => $players[$player_id]['player_name'],
			'card_value' => $this->values_label[$topCard['type_arg']],
			'card_type' => $this->icons[$topCard['type']],
			'trump_icon' => $this->icons[$color],
			'trump' => $color
		));
		$this->gamestate->nextState('accept');
	}

	function acceptThirdRound($color)
	{
		self::checkAction("acceptThirdRound");
		$players = self::loadPlayersBasicInfos();
		$player_id = self::getActivePlayerId();
		$topCard = $this->cards->getCard(self::getGameStateValue('cardOnTop'));
		if ($color < 5) {
			throw new BgaUserException(self::_("You cannot choose a regular Trump during third round"));
		}

		self::commonAccept($player_id, $color);
		self::incStat(1, "takenThirdNbr", $player_id);
		self::notifyAllPlayers('takeCard', clienttranslate('${player_name} chooses ${trump_name} and takes the card ${card_value}${card_type}') , array(
			'player_id' => $player_id,
			'player_name' => $players[$player_id]['player_name'],
			'card_value' => $this->values_label[$topCard['type_arg']],
			'card_type' => $this->icons[$topCard['type']],
			'trump_name' => $this->colors[$color]['name'],
			'trump' => $color
		));
		$this->gamestate->nextState('accept');
	}



	// ////////////////////////////////////////////////////////////////////////////
	// ////////// Game state arguments
	// //////////

	/*
	Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
	These methods function is to return some additional information that is specific to the current
	game state.
	*/
	/*
	Example for game state "MyGameState":
	function argMyGameState()
	{

	// Get some values from the current game situation in database...
	// return values:

	return array(
	'variable1' => $value1,
	'variable2' => $value2,
	...
	);
	}

	*/

	// ////////////////////////////////////////////////////////////////////////////
	// ////////// Game state actions
	// //////////

	/*
	Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
	The action method of state X is called everytime the current game state is set to X.
	*/
	function stNewHand()
	{
		self::setGameStateValue('taker', -1);

		// Take back all cards (from any location => null) to deck

		$this->cards->moveAllCardsInLocation(null, "deck");
		$this->cards->shuffle('deck');
		$players = self::loadPlayersBasicInfos();
		$currentDealer = self::getGameStateValue('dealer');
		if ($currentDealer != - 1) {
			self::notifyAllPlayers('dealCards', clienttranslate('${player_name} deals new hands') , array(
				'player_id' => $currentDealer,
				'player_name' => $players[$currentDealer]['player_name']
			));
		}
		else {
			throw new BgaVisibleSystemException("Error, no one is the dealer");
		}

		self::notifyAllPlayers('startingNewHand', '', array());

		// Deal 5 cards to each players
		// Create deck, shuffle it and give 5 initial cards

		foreach($players as $player_id => $player) {
			$sql = "UPDATE player SET player_tricks=0
					WHERE player_id='$player_id' ";
			self::DbQuery($sql);
			$cards = $this->cards->pickCards(13, 'deck', $player_id);

			// Notify player about his cards

			self::notifyPlayer($player_id, 'newHand', '', array(
				'cards' => $cards
			));
		}

		$topCard = $this->cards->getCardOnTop('deck');
		self::setGameStateValue('cardOnTop', $topCard['id']);
		self::setGameStateValue('trumpColor', -1);
		self::notifyAllPlayers('cardOnTop', '', array(
			'card_id' => $topCard['id'],
			'card_color' => $topCard['type'],
			'card_val' => $topCard['type_arg']
		));
		self::setGameStateValue('passCount', 0);
		$this->gamestate->nextState("");
	}

	function stNextPlayerFirstRound()
	{
		$passCount = self::getGameStateValue('passCount');
		$player_id = self::activeNextPlayer();
		self::giveExtraTime($player_id);
		if ($passCount >= 4) { // everyone passed once
			$this->gamestate->nextState("nextRound");
		}
		else {
			$this->gamestate->nextState("nextPlayer");
		}
	}

	function stNextPlayerSecondRound()
	{
		$passCount = self::getGameStateValue('passCount');
		if ($passCount >= 8) { // everyone passed twice
			$tmp = 0;
			$tmp = self::getGameStateValue('allNoTrumps');
			if ($tmp == 2) {
				$player_id = self::activeNextPlayer();
				self::giveExtraTime($player_id);
				$this->gamestate->nextState("nextRound");
			}
			else
				$this->gamestate->nextState("newHand");
		}
		else {
			$player_id = self::activeNextPlayer();
			self::giveExtraTime($player_id);
			$this->gamestate->nextState("nextPlayer");
		}
	}


	function stNextPlayerThirdRound()
	{
		$passCount = self::getGameStateValue('passCount');
		if ($passCount >= 12) { // everyone passed thrice
			$this->gamestate->nextState("newHand");
		}
		else {
			$player_id = self::activeNextPlayer();
			self::giveExtraTime($player_id);
			$this->gamestate->nextState("nextPlayer");
		}
	}

	function stNewDeal()
	{
		self::incStat(1, "passedHandNbr");
		$players = self::loadPlayersBasicInfos();
		$nextPlayer = self::createNextPlayerTable(array_keys($players));
		self::notifyAllPlayers('noDeal', clienttranslate('No one has taken the card.') , array());
		$currentDealer = self::getGameStateValue('dealer');
		$currentDealer = $nextPlayer[$currentDealer];
		self::setGameStateValue('dealer', $currentDealer);
		$this->gamestate->changeActivePlayer($nextPlayer[$currentDealer]);
		$this->gamestate->nextState("");
	}

	function stFinishDealing()
	{
		self::incStat(1, "playedHandNbr");
		$players = self::loadPlayersBasicInfos();
		$currentDealer = self::getGameStateValue('dealer');
		$trumpColor = self::getGameStateValue('trumpColor');
		if ($currentDealer != - 1) {
			self::notifyAllPlayers('dealCards', clienttranslate('${player_name} finishes dealing new hands.') , array(
				'player_id' => $currentDealer,
				'player_name' => $players[$currentDealer]['player_name']
			));
		}

		$taker = self::getGameStateValue('taker');
		$card = $this->cards->pickCard('deck', $taker);
		self::setGameStateValue('rebeloteS', 0);
		self::setGameStateValue('rebeloteH', 0);
		self::setGameStateValue('rebeloteC', 0);
		self::setGameStateValue('rebeloteD', 0);

		foreach($players as $player_id => $player) {
			if ($player_id == $taker) {
				$cards = $this->cards->pickCards(2, 'deck', $player_id);
			}
			else {
				$cards = $this->cards->pickCards(3, 'deck', $player_id);
			}

			$cards = $this->cards->getPlayerHand($player_id);

			// Notify player about his cards

			self::notifyPlayer($player_id, 'fillHand', '', array(
				'cards' => $cards
			));


			if ($trumpColor == 6) {
				$rebelote = 0; // Spade
				foreach($cards as $card) {
					if ($card['type'] == 1) {
						if ($card['type_arg'] == 12 || $card['type_arg'] == 13) { // Q or K
							$rebelote--;
						}
					}
				}
				if ($rebelote == -2)
					self::setGameStateValue('rebeloteS', -2);

				$rebelote = 0; // Heart
				foreach($cards as $card) {
					if ($card['type'] == 2) {
						if ($card['type_arg'] == 12 || $card['type_arg'] == 13) { // Q or K
							$rebelote--;
						}
					}
				}
				if ($rebelote == -2)
					self::setGameStateValue('rebeloteH', -2);

				$rebelote = 0; // Club
				foreach($cards as $card) {
					if ($card['type'] == 3) {
						if ($card['type_arg'] == 12 || $card['type_arg'] == 13) { // Q or K
							$rebelote--;
						}
					}
				}
				if ($rebelote == -2)
					self::setGameStateValue('rebeloteC', -2);

				$rebelote = 0; // Diamond
				foreach($cards as $card) {
					if ($card['type'] == 4) {
						if ($card['type_arg'] == 12 || $card['type_arg'] == 13) { // Q or K
							$rebelote--;
						}
					}
				}
				if ($rebelote == -2)
					self::setGameStateValue('rebeloteD', -2);
			}
			else {
				$rebelote = 0;
				foreach($cards as $card) {
					if ($card['type'] == $trumpColor) {
						self::incStat(1, "trumpNbr", $player_id);
						if ($card['type_arg'] == 12 || $card['type_arg'] == 13) { // Q or K
							$rebelote--;
						}
					}
				}
				if ($rebelote == - 2) { // some player has belote+rebelote
					switch ($trumpColor) {
						case 1:
							self::setGameStateValue('rebeloteS', -2);
							break;
						case 2:
							self::setGameStateValue('rebeloteH', -2);
							break;
						case 3:
							self::setGameStateValue('rebeloteC', -2);
							break;
						case 4:
							self::setGameStateValue('rebeloteD', -2);
							break;
					}
				}
			}
		}

		self::setGameStateValue('cardOnTop', -1);
		$nextPlayer = self::createNextPlayerTable(array_keys($players));
		$this->gamestate->changeActivePlayer($nextPlayer[$currentDealer]);
		$this->gamestate->nextState("");
	}

	function stNewTrick()
	{
		self::setGameStateValue('trickColor', -1);
		self::setGameStateValue('trickWinner', -1);
		self::setGameStateValue('winningCard', -1);
		$this->gamestate->nextState();
	}

	function stNextPlayer()
	{

		// Active next player OR end the trick and go to the next trick OR end the hand
		if ($this->cards->countCardInLocation('cardsontable') >= 4) {

			// This is the end of the trick

			$best_value_player_id = self::getGameStateValue('trickWinner');
			if ($best_value_player_id == - 1) throw new BgaVisibleSystemException("Error, nobody wins the trick");

			// Move all cards to "cardswon" of the given player

			$this->cards->moveAllCardsInLocation('cardsontable', 'cardswon', null, $best_value_player_id);

			$tricks = 0;
			// Update number of tricks won during this hand
			$sql = "UPDATE player SET player_tricks=player_tricks+1
					WHERE player_id='$best_value_player_id' ";
			self::DbQuery($sql);	
			$tricks = self::getUniqueValueFromDb("SELECT player_tricks FROM player WHERE player_id='$best_value_player_id' ");
			
			
			// Notify
			// Note: we use 2 notifications here so we can pause the display during the first notification
			//  before we move all cards to the winner (during the second)

			$players = self::loadPlayersBasicInfos();
			$nextPlayer = self::createNextPlayerTable(array_keys($players));
			self::notifyAllPlayers('trickWin', clienttranslate('${player_name} wins the trick') , array(
				'player_id' => $best_value_player_id,
				'player_name' => $players[$best_value_player_id]['player_name'],
				'tricks' => $tricks
			));
			self::notifyAllPlayers('giveAllCardsToPlayer', '', array(
				'player_id' => $best_value_player_id
			));
			if ($this->cards->countCardInLocation('hand') == 0) {
				self::notifyAllPlayers('dixDeDer', clienttranslate('${player_name} gets the "dix de der"') , array(
					'player_id' => $best_value_player_id,
					'player_name' => $players[$best_value_player_id]['player_name']
				));
				self::setGameStateValue('dixDeDer', $best_value_player_id);
				self::incStat(1, "dixDeDerNbr", $best_value_player_id);
				$currentDealer = self::getGameStateValue('dealer');
				$currentDealer = $nextPlayer[$currentDealer];
				self::setGameStateValue('dealer', $currentDealer);
				$this->gamestate->changeActivePlayer($nextPlayer[$currentDealer]);

				// End of the hand

				$this->gamestate->nextState("endHand");
			}
			else {

				// Active this player => he's the one who starts the next trick

				$this->gamestate->changeActivePlayer($best_value_player_id);

				// End of the trick

				$this->gamestate->nextState("nextTrick");
			}
		}
		else {

			// Standard case (not the end of the trick)
			// => just active the next player

			$player_id = self::activeNextPlayer();
			self::giveExtraTime($player_id);
			$this->gamestate->nextState('nextPlayer');
		}

	}

	function stEndHand()
	{

		// Count and score points, then end the game or go to the next hand.

		$taker = self::getGameStateValue('taker');
		$litige = self::getGameStateValue('litige');
		$hands = self::getGameStateValue('hands');
		$players = self::loadPlayersBasicInfos();
		$currentTrumpColor = self::getGameStateValue('trumpColor');
		$nextPlayer = self::createNextPlayerTable(array_keys($players));
		$first_player_id = self::getGameStateValue('firstPlayer');
		$second_player_id = $nextPlayer[$first_player_id];
		$third_player_id = $nextPlayer[$second_player_id];
		$fourth_player_id = $nextPlayer[$third_player_id];
		$player_to_team = array();
		$player_to_team[$first_player_id] = 1;
		$player_to_team[$second_player_id] = 2;
		$player_to_team[$third_player_id] = 1;
		$player_to_team[$fourth_player_id] = 2;
		$first_player = $players[$first_player_id];
		$second_player = $players[$second_player_id];
		$third_player = $players[$third_player_id];
		$fourth_player = $players[$fourth_player_id];
		$team_to_points = array(
			1 => 0,
			2 => 0
		); // Regular points
		$team_to_belote = array(
			1 => 0,
			2 => 0
		); // belote+rebelote
		$team_to_dixdeder = array(
			1 => 0,
			2 => 0
		); // dix de der
		$team_to_total = array(
			1 => 0,
			2 => 0
		); // sum of previous 3
		$team_to_score = array(
			1 => 0,
			2 => 0
		); // Can be changed by Capot, Dedans or Litige
		$team_no_trick = array(
			1 => true,
			2 => true
		); // To check for Capot
		$cards = $this->cards->getCardsInLocation("cardswon");
		foreach($cards as $card) // Count regular points
		{
			$player_id = $card['location_arg'];
			$team_id = $player_to_team[$player_id];
			$team_no_trick[$team_id] = false; // At least one card = not capot
			if ($currentTrumpColor == 5) { // No trumps
				$team_to_points[$team_id]+= $this->cardToPoints['no trumps'][$card['type_arg']];
			}
			else
			if ($currentTrumpColor == 6) { // All trumps
				$team_to_points[$team_id]+= $this->cardToPoints['all trumps'][$card['type_arg']];
			}
			else
			if ($card['type'] == $currentTrumpColor) {
				$team_to_points[$team_id]+= $this->cardToPoints['trump'][$card['type_arg']];
			}
			else {
				$team_to_points[$team_id]+= $this->cardToPoints['normal'][$card['type_arg']];
			}
		}

		$dixDeDer = self::getGameStateValue('dixDeDer');
		$team_to_dixdeder[$player_to_team[$dixDeDer]]+= 10;

		$rebelote = self::getGameStateValue('rebeloteS');
		if ($rebelote > 0) {
			$team_to_belote[$player_to_team[$rebelote]]+= 20;
		}
		$rebelote = self::getGameStateValue('rebeloteH');
		if ($rebelote > 0) {
			$team_to_belote[$player_to_team[$rebelote]]+= 20;
		}
		$rebelote = self::getGameStateValue('rebeloteC');
		if ($rebelote > 0) {
			$team_to_belote[$player_to_team[$rebelote]]+= 20;
		}
		$rebelote = self::getGameStateValue('rebeloteD');
		if ($rebelote > 0) {
			$team_to_belote[$player_to_team[$rebelote]]+= 20;
		}


		$team_to_total[1] = $team_to_points[1] + $team_to_belote[1] + $team_to_dixdeder[1];
		$team_to_total[2] = $team_to_points[2] + $team_to_belote[2] + $team_to_dixdeder[2];
		$team_to_score[1] = $team_to_total[1];
		$team_to_score[2] = $team_to_total[2];
		if ($team_to_total[1] < $team_to_total[2]) {
			self::incStat(1, "wonHandNbr", $second_player_id);
			self::incStat(1, "wonHandNbr", $fourth_player_id);
		}
		else
		if ($team_to_total[2] < $team_to_total[1]) {
			self::incStat(1, "wonHandNbr", $first_player_id);
			self::incStat(1, "wonHandNbr", $third_player_id);
		}

		// Test for special cases

		if ($team_no_trick[1]) { // Capot !
			$team_to_score[2]+= 90;
			self::incStat(1, "capotNbr", $second_player_id);
			self::incStat(1, "capotNbr", $fourth_player_id);
			self::notifyAllPlayers('capot', clienttranslate('Team ${second_player_name} and ${fourth_player_name} made Capot !') , array(
				'second_player_name' => $second_player['player_name'],
				'fourth_player_name' => $fourth_player['player_name']
			));
		}
		else
		if ($team_no_trick[2]) { // Capot !
			$team_to_score[1]+= 90;
			self::incStat(1, "capotNbr", $first_player_id);
			self::incStat(1, "capotNbr", $third_player_id);
			self::notifyAllPlayers('capot', clienttranslate('Team ${first_player_name} and ${third_player_name} made Capot !') , array(
				'first_player_name' => $first_player['player_name'],
				'third_player_name' => $third_player['player_name']
			));
		}

		$total_taker = $team_to_total[$player_to_team[$taker]];
		$total_opponents = $team_to_total[3 - $player_to_team[$taker]]; // 1=>2, 2=>1
		if ($total_taker < $total_opponents) { //dedans
			$team_to_score[3 - $player_to_team[$taker]]+= $team_to_points[$player_to_team[$taker]] + $team_to_dixdeder[$player_to_team[$taker]];
			$team_to_score[$player_to_team[$taker]] = $team_to_belote[$player_to_team[$taker]];
			if ($first_player_id == $taker || $third_player_id == $taker) {
				self::incStat(1, "dedansNbr", $first_player_id);
				self::incStat(1, "dedansNbr", $third_player_id);
				self::notifyAllPlayers('dedans', clienttranslate('Team ${first_player_team} and ${second_player_team} misses the contract!') , array(
					'first_player_team' => $first_player['player_name'],
					'second_player_team' => $third_player['player_name']
				));
			}
			else
			if ($second_player_id == $taker || $fourth_player_id == $taker) {
				self::incStat(1, "dedansNbr", $second_player_id);
				self::incStat(1, "dedansNbr", $fourth_player_id);
				self::notifyAllPlayers('dedans', clienttranslate('Team ${first_player_team} and ${second_player_team} misses the contract!') , array(
					'first_player_team' => $second_player['player_name'],
					'second_player_team' => $fourth_player['player_name']
				));
			}
			else {

				// error no taker

			}
		}
		else
		if ($total_taker == $total_opponents) { //litige
			self::incStat(1, "litigeNbr");
			$litige+= $total_taker;
			if ($first_player_id == $taker || $third_player_id == $taker) {
				self::incStat(1, "litigeWonNbr", $second_player_id);
				self::incStat(1, "litigeWonNbr", $fourth_player_id);
				self::incStat(1, "litigeLostNbr", $first_player_id);
				self::incStat(1, "litigeLostNbr", $third_player_id);
				self::notifyAllPlayers('litige', clienttranslate('Contention ! Team ${first_player_team} and ${second_player_team} do not get any points. ${points_litige} additionnal points can be won with the next win.') , array(
					'first_player_team' => $first_player['player_name'],
					'second_player_team' => $third_player['player_name'],
					'points_litige' => $litige
				));
			}
			else
			if ($second_player_id == $taker || $fourth_player_id == $taker) {
				self::incStat(1, "litigeLostNbr", $second_player_id);
				self::incStat(1, "litigeLostNbr", $fourth_player_id);
				self::incStat(1, "litigeWonNbr", $first_player_id);
				self::incStat(1, "litigeWonNbr", $third_player_id);
				self::notifyAllPlayers('litige', clienttranslate('Contention ! Team ${first_player_team} and ${second_player_team} do not get any points. ${points_litige} additionnal points can be won with the next win.') , array(
					'first_player_team' => $second_player['player_name'],
					'second_player_team' => $fourth_player['player_name'],
					'points_litige' => $litige
				));
			}
			else {
				throw new BgaVisibleSystemException("Error, no taker in dedans");
			}

			$team_to_score[$player_to_team[$taker]] = 0;
			self::setGameStateValue('litige', $litige);
			$litige = - 1;
		}

		if ($litige > 0) {
			if ($team_to_score[1] > $team_to_score[2]) {
				$team_to_score[1]+= $litige;
			}
			else
			if ($team_to_score[2] > $team_to_score[1]) {
				$team_to_score[2]+= $litige;
			}
			else {
				throw new BgaVisibleSystemException("Error litige");
			}

			$litige = 0;
			self::setGameStateValue('litige', $litige);
		}

		// Apply scores to player

		foreach($players as $player_id => $player) {
			$points = $team_to_score[$player_to_team[$player_id]];
			if ($points != 0) {
				$sql = "UPDATE player SET player_score=player_score+$points
                        WHERE player_id='$player_id' ";
				self::DbQuery($sql);
			}
		}

		

		// ////////// Display table window with results /////////////////

		$table = array();

		// Header line

		$firstRow = array(
			''
		);
		$firstRow[] = array(
			'str' => 'Team ${first_player_name} and ${third_player_name}',
			'args' => array(
				'first_player_name' => $first_player['player_name'],
				'third_player_name' => $third_player['player_name']
			) ,
			'type' => 'header'
		);
		$firstRow[] = array(
			'str' => 'Team ${second_player_name} and ${fourth_player_name}',
			'args' => array(
				'second_player_name' => $second_player['player_name'],
				'fourth_player_name' => $fourth_player['player_name']
			) ,
			'type' => 'header'
		);
		$table[] = $firstRow;

		// Points

		$newRow = array(
			array(
				'str' => clienttranslate('Regular Points') ,
				'args' => array()
			)
		);
		$newRow[] = $team_to_points[1];
		$newRow[] = $team_to_points[2];
		$table[] = $newRow;

		// Points

		$newRow = array(
			array(
				'str' => clienttranslate('Dix de Der') ,
				'args' => array()
			)
		);
		$newRow[] = $team_to_dixdeder[1];
		$newRow[] = $team_to_dixdeder[2];
		$table[] = $newRow;

		// Points

		$newRow = array(
			array(
				'str' => clienttranslate('Belote+Rebelote') ,
				'args' => array()
			)
		);
		$newRow[] = $team_to_belote[1];
		$newRow[] = $team_to_belote[2];
		$table[] = $newRow;

		// Points

		$newRow = array(
			array(
				'str' => clienttranslate('Total Points') ,
				'args' => array()
			)
		);
		$newRow[] = $team_to_total[1];
		$newRow[] = $team_to_total[2];
		$table[] = $newRow;

		// Points

		$newRow = array(
			array(
				'str' => clienttranslate('Score of the hand') ,
				'args' => array()
			)
		);
		$newRow[] = $team_to_score[1];
		$newRow[] = $team_to_score[2];
		$table[] = $newRow;
		$this->notifyAllPlayers("tableWindow", '', array(
			"id" => 'finalScoring',
			"title" => clienttranslate("Result of this hand") ,
			"table" => $table
		));
		$maxScore = self::getGameStateValue('gameLength');


		$newScores = self::getCollectionFromDb("SELECT player_id, player_score FROM player", true);
		self::notifyAllPlayers("newScores", '', array(
			'newScores' => $newScores
		));

		// /// Test if this is the end of the game

		foreach($newScores as $player_id => $score) {
			if ($score >= $maxScore) {

				// Set last stat : average score

				foreach($players as $player_id => $player) {
					$avgScore = $newScores[$player_id] * 1.0 / $hands;
					self::setStat($avgScore, "averageScore", $player_id);
				}

				// Trigger the end of the game !

				$this->gamestate->nextState("endGame");
				return;
			}
		}

		// Otherwise... new hand !

		$this->gamestate->nextState("nextHand");
	}






	/*
        upgradeTableDb:
        
        You don't have to care about this until your game has been published on BGA.
        Once your game is on BGA, this method is called everytime the system detects a game running with your old
        Database scheme.
        In this case, if you change your Database scheme, you just have to apply the needed changes in order to
        update the game database and allow the game to continue to run with your new version.
    
    */ 

	function upgradeTableDb( $from_version )
    {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345
        
        
        if( $from_version <= 1612019999 )
        {
			try{
				$sql = "ALTER TABLE `player` ADD `player_tricks` INT UNSIGNED NOT NULL DEFAULT '0';";
				self::DbQuery( $sql );
			}catch(Exception $e){}
			try{
				$sql = "UPDATE player SET player_tricks=0";
				self::DbQuery( $sql );
			}catch(Exception $e){}
        }
 
		if( $from_version <= 1612022005 )
        {
            try{
                $sql = "ALTER TABLE `zz_replay1_player` ADD `player_tricks` INT UNSIGNED NOT NULL DEFAULT '0';";
                self::DbQuery( $sql );
            }catch(Exception $e){}
            try{
                $sql = "ALTER TABLE `zz_replay2_player` ADD `player_tricks` INT UNSIGNED NOT NULL DEFAULT '0';";
                self::DbQuery( $sql );
            }catch(Exception $e){}
            try{
                $sql = "ALTER TABLE `zz_replay3_player` ADD `player_tricks` INT UNSIGNED NOT NULL DEFAULT '0';";
                self::DbQuery( $sql );
            }catch(Exception $e){}
        } 
 
    }    


	// ////////////////////////////////////////////////////////////////////////////
	// ////////// Zombie
	// //////////

	/*
	zombieTurn:
	This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
	You can do whatever you want in order to make sure the turn of this player ends appropriately
	(ex: pass).
	*/
	function stZombiePlayCard()
	{
		$player_id = self::getActivePlayerId();
		$currentCard = - 1;
		$playerhands = $this->cards->getCardsInLocation('hand', $player_id);
		$currentTrickColor = self::getGameStateValue('trickColor');
		$currentTrumpColor = self::getGameStateValue('trumpColor');
		$currentWinningCard = self::getGameStateValue('winningCard');
		if ($currentWinningCard != - 1) $currentWinningCard = $this->cards->getCard($currentWinningCard);
		$cardOfTrickColor = - 1;
		$bestTrump = - 1;
		foreach($playerhands as $card) {
			if ($currentCard < 0) {
				$currentCard = $card;
			}

			if ($card['type'] == $currentTrickColor && $currentTrickColor != $currentTrumpColor) {
				if (($currentTrumpColor != 6) || ($cardOfTrickColor < 0) || ($currentTrumpColor == 6 && $cardOfTrickColor >= 0 && $this->isCardStronger($card['type_arg'], $currentTrickColor['type_arg'], true))) {
					$cardOfTrickColor = $card;
					$currentCard = $card;
				}
			}

			if ($card['type'] == $currentTrumpColor && $cardOfTrickColor < 0) {
				if ($bestTrump < 0 || ($this->isCardStronger($card['type_arg'], $bestTrump['type_arg'], true))) {
					$bestTrump = $card;
					$currentCard = $card;
				}
			}
		}

		$card_id = $currentCard['id'];

		// Checks are done! now we can play our card

		$this->cards->moveCard($card_id, 'cardsontable', $player_id);

		// Set the trick color if it hasn't been set yet

		if ($currentTrickColor == - 1) self::setGameStateValue('trickColor', $currentCard['type']);
		if ($currentWinningCard == - 1) {
			self::setGameStateValue('winningCard', $card_id);
			self::setGameStateValue('trickWinner', $player_id);
		}
		else
		if (($currentCard['type'] == $currentWinningCard['type'] && $this->isCardStronger($currentCard['type_arg'], $currentWinningCard['type_arg'], $currentCard['type'] == $currentTrumpColor || $currentTrumpColor == 6)) || ($currentCard['type'] == $currentTrumpColor && $currentWinningCard['type'] != $currentTrumpColor)) {
			self::setGameStateValue('winningCard', $card_id);
			self::setGameStateValue('trickWinner', $player_id);
		}

		// And notify

		self::notifyAllPlayers('playCard', clienttranslate('${player_name} plays ${card_value}${card_type}') , array(
			'i18n' => array(
				'color_displayed',
				'value_displayed'
			) ,
			'card_id' => $card_id,
			'player_id' => $player_id,
			'player_name' => self::getActivePlayerName() ,
			'value' => $currentCard['type_arg'],
			'color' => $currentCard['type'],
			'card_value' => $this->values_label[$currentCard['type_arg']],
			'card_type' => $this->icons[$currentCard['type']]
		));



		if($currentCard['type'] == $currentTrumpColor || $currentTrumpColor == 6 /*All trumps*/){
			
			$rebelote = 0; 
			// Value 0 if no belote-rebelote during this hand
			// -2 if some player has both, and didn't play any yet
			// -1 if "belote" has been said
			// player_id if "belote" and "rebelote" have both been said
			

			$rebeloteName = '';
			switch ($currentCard['type']) {
				case 1:
					$rebeloteName = 'rebeloteS';
					break;
				case 2:
					$rebeloteName = 'rebeloteH';
					break;
				case 3:
					$rebeloteName = 'rebeloteC';
					break;
				case 4:
					$rebeloteName = 'rebeloteD';
					break;
			}
			if ($rebeloteName != ''){
				$rebelote = self::getGameStateValue($rebeloteName); 
			}

			
			if ($rebelote == - 1 && ($currentCard['type_arg'] == 12 || $currentCard['type_arg'] == 13)) { // Q or K trump
				self::incStat(1, "beloteNbr", $player_id);
				self::notifyAllPlayers('belote', clienttranslate('${player_name} says "Rebelote !"') , array(
					'player_id' => $player_id,
					'player_name' => $players[$player_id]['player_name'],
					'speech' => clienttranslate('Rebelote !') ,
				));
				self::setGameStateValue($rebeloteName, $player_id); // Remember the player who said belote+rebelote
			}
			else
			if ($rebelote == - 2 && ($currentCard['type_arg'] == 12 || $currentCard['type_arg'] == 13)) { // Q or K trump
				self::notifyAllPlayers('belote', clienttranslate('${player_name} says "Belote !"') , array(
					'player_id' => $player_id,
					'player_name' => $players[$player_id]['player_name'],
					'speech' => clienttranslate('Belote !') ,
				));
				self::setGameStateValue($rebeloteName, -1); 
			}

		}

		// Next player

		$this->gamestate->nextState("");
	}

	function stZombiePassFirstRound()
	{
		self::pass();
	}

	function stZombiePassSecondRound()
	{
		self::pass();
	}

	function stZombiePassThirdRound()
	{
		self::pass();
	}

	function zombieTurn($state, $active_player)
	{
		$statename = $state['name'];
		if ($state['type'] == "game") {

			// Should not happen

			return;
		}

		if ($state['type'] == "activeplayer") {
			switch ($statename) {
			case "firstRound":
				$this->gamestate->nextState("zombiePass");
				break;

			case "secondRound":
				$this->gamestate->nextState("zombiePass");
				break;

			case "thirdRound":
				$this->gamestate->nextState("zombiePass");
				break;

			case "playerTurn":
				$this->gamestate->nextState("zombiePass");
				break;

			default:
				$this->gamestate->nextState("zombiePass");
				break;
			}

			return;
		}

		if ($state['type'] == "multipleactiveplayer") {

			// Make sure player is in a non blocking status for role turn

			$sql = "
                UPDATE  player
                SET     player_is_multiactive = 0
                WHERE   player_id = $active_player
            ";
			self::DbQuery($sql);
			$this->gamestate->updateMultiactiveOrNextState('');
			return;
		}

		throw new BgaUserException("Zombie mode not supported at this game state: " . $statename);
	}
}







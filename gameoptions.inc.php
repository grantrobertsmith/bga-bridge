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
 * gameoptions.inc.php
 *
 * Bridge game options description
 * 
 * In this file, you can define your game options (= game variants).
 *   
 * Note: If your game has no variant, you don't have to modify this file.
 *
 * Note²: All options defined in this file should have a corresponding "game state labels"
 *        with the same ID (see "initGameStateLabels" in bridge.game.php)
 *
 * !! It is not a good idea to modify this file when a game is running !!
 *
 */

$game_options = array(

    100 => array(
        'name' => totranslate('Game length'),
        'values' => array(
            1 => array( 'name' => totranslate('Classic (1000 points)') ),
            2 => array( 'name' => totranslate('Half-game (500 points)'), 'tmdisplay' => totranslate('Half-game') ),
            3 => array( 'name' => totranslate('One round'), 'tmdisplay' => totranslate('One round') )
            // 1 => array( 'name' => totranslate('1 Rubber')),
            // 2 => array( 'name' => totranslate('2 Rubbers')),
            // 3 => array( 'name' => totranslate('8 Hands')),
            // 4 => array( 'name' => totranslate('12 Hands'))
        ),
        'default' => 1
    ),

    101 => array(
        'name' => totranslate('Teams'),
        'values' => array(
                1 => array( 'name' => totranslate( 'By table order (1rst/3rd versus 2nd/4th)' )),
                2 => array( 'name' => totranslate( 'By table order (1rst/2nd versus 3rd/4th)' )),
                3 => array( 'name' => totranslate( 'By table order (1rst/4th versus 2nd/3rd)' )),
                4 => array( 'name' => totranslate( 'At random' ) ),
        ),
        'default' => 1
    ),
	
	102 => array(
		'name' => totranslate( 'Turn order' ),
		'values' => array(
			1 => array( 'name' => totranslate( 'Counterclockwise' )),
			2 => array( 'name' => totranslate( 'Clockwise' )),
		),
		'default' => 1
	),


	110 => array(
		'name' => totranslate( 'All trumps / No trumps' ),
		'values' => array(
			1 => array( 'name' => totranslate( 'Disabled' )),
			2 => array( 'name' => totranslate( 'Enabled' ), 'tmdisplay' => totranslate('All trumps / No trumps'), 'beta' => true),
		),
		'default' => 1
	),

    //  beta=true => this option is in beta version right now.
    //  nobeginner=true  =>  this option is not recommended for beginners

);



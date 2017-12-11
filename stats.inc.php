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
 * stats.inc.php
 *
 * Bridge game statistics description
 *
 */

/*
  In this file, you are describing game statistics, that will be displayed at the end of the
  game.
    
  !! After modifying this file, you must use "Reload  statistics configuration" in BGA Studio backoffice ("Your game configuration" section):
  http://en.studio.boardgamearena.com/admin/studio
    
  There are 2 types of statistics:
  _ table statistics, that are not associated to a specific player (ie: 1 value for each game).
  _ player statistics, that are associated to each players (ie: 1 value for each player in the game).

  Statistics types can be "int" for integer, "float" for floating point values, and "bool" for boolean
    
  Once you defined your statistics there, you can start using "initStat", "setStat" and "incStat" method
  in your game logic, using statistics names defined below.
    
  !! It is not a good idea to modify this file when a game is running !!

  If your game is already public on BGA, please read the following before any change:
  http://en.doc.boardgamearena.com/Post-release_phase#Changes_that_breaks_the_games_in_progress
    
  Notes:
  * Statistic index is the reference used in setStat/incStat/initStat PHP method
  * Statistic index must contains alphanumerical characters and no space. Example: 'turn_played'
  * Statistics IDs must be >=10
  * Two table statistics can't share the same ID, two player statistics can't share the same ID
  * A table statistic can have the same ID than a player statistics
  * Statistics ID is the reference used by BGA website. If you change the ID, you lost all historical statistic data. Do NOT re-use an ID of a deleted statistic
  * Statistic name is the English description of the statistic as shown to players
    
  */

$stats_type = array(

    // Statistics global to table
    "table" => array(

        /*"handNbr" => array(   "id"=> 10,
        "name" => totranslate("Total number of hands"), 
        "type" => "int" ),*/

        "passedHandNbr" => array(   "id"=> 11,
        "name" => totranslate("Number of passed hands"), 
        "type" => "int" ),

        "playedHandNbr" => array(   "id"=> 12,
        "name" => totranslate("Number of played hands"), 
        "type" => "int" ),

       "litigeNbr" => array(   "id"=> 13,
        "name" => totranslate("Number of Contentions"), 
        "type" => "int" ),
 
        

    ),
    
    // Statistics existing for each player
    "player" => array(

        "takenFirstNbr" => array(   "id"=> 10,
        "name" => totranslate("Number of takes in first round"), 
        "type" => "int" ),
                                
        "takenSecondNbr" => array(   "id"=> 11,
        "name" => totranslate("Number of takes in second round"), 
        "type" => "int" ),

		"takenThirdNbr" => array(   "id"=> 30,
        "name" => totranslate("Number of takes in third round"), 
        "type" => "int" ),

        "wonHandNbr" => array(   "id"=> 12,
        "name" => totranslate("Number of hands won"), 
        "type" => "int" ),

        "dedansNbr" => array(   "id"=> 13,
        "name" => totranslate("Number of Lost Contracts"), 
        "type" => "int" ),

        "capotNbr" => array(   "id"=> 14,
        "name" => totranslate("Number of Capots"), 
        "type" => "int" ),

        "dixDeDerNbr" => array(   "id"=> 15,
        "name" => totranslate("Number of last trick won"), 
        "type" => "int" ),

        "beloteNbr" => array(   "id"=> 16,
        "name" => totranslate("Number of Belote-Rebelote"), 
        "type" => "int" ),

        "trumpNbr" => array(   "id"=> 17,
        "name" => totranslate("Number of trumps"), 
        "type" => "int" ),

        "litigeWonNbr" => array(   "id"=> 18,
        "name" => totranslate("Number of Contentions while not taking"), 
        "type" => "int" ),

        "litigeLostNbr" => array(   "id"=> 19,
        "name" => totranslate("Number of Contentions while taking"), 
        "type" => "int" ),

        "averageScore" => array(   "id"=> 20,
        "name" => totranslate("Average score per hand"), 
        "type" => "float" ),
        
		
 
      
    )

);

<?php

$gameinfos = array( 


// Game designer (or game designers, separated by commas)
    'designer' => '',

// Game artist (or game artists, separated by commas)
    'artist' => '',

// Year of FIRST publication of this game. Can be negative.
    'year' => 1886,                 

// Game publisher
    'publisher' => '',                     

// Url of game publisher website
    'publisher_website' => '',   

// Board Game Geek ID of the publisher
    'publisher_bgg_id' => 171,

// Board game geek ID of the game
    'bgg_id' => 2181,


// Players configuration that can be played (ex: 2 to 4 players)
    'players' => array( 4 ),    

// Suggest players to play with this number of players. Must be null if there is no such advice, or if there is only one possible player configuration.
    'suggest_player_number' => null,

// Discourage players to play with these numbers of players. Must be null if there is no such advice.
    'not_recommend_player_number' => null,
// 'not_recommend_player_number' => array( 2, 3 ),      // <= example: this is not recommended to play this game with 2 or 3 players


// Estimated game duration, in minutes (used only for the launch, afterward the real duration is computed)
    'estimated_duration' => 40,           

// Time in second add to a player when "giveExtraTime" is called (speed profile = fast)
    'fast_additional_time' => 7,           

// Time in second add to a player when "giveExtraTime" is called (speed profile = medium)
    'medium_additional_time' => 16,           

// Time in second add to a player when "giveExtraTime" is called (speed profile = slow)
    'slow_additional_time' => 23,           

// If you are using a tie breaker in your game (using "player_score_aux"), you must describe here
// the formula used to compute "player_score_aux". This description will be used as a tooltip to explain
// the tie breaker to the players.
// Note: if you are NOT using any tie breaker, leave the empty string.
//
// Example: 'tie_breaker_description' => totranslate( "Number of remaining cards in hand" ),
    'tie_breaker_description' => "",

// Game is "beta". A game MUST set is_beta=1 when published on BGA for the first time, and must remains like this until all bugs are fixed.
    'is_beta' => 1,                     

// Is this game cooperative (all players wins together or loose together)
    'is_coop' => 0, 


// Complexity of the game, from 0 (extremely simple) to 5 (extremely complex)
    'complexity' => 4,    

// Luck of the game, from 0 (absolutely no luck in this game) to 5 (totally luck driven)
    'luck' => 2,

// Strategy of the game, from 0 (no strategy can be setup) to 5 (totally based on strategy)
    'strategy' => 3,    

// Diplomacy of the game, from 0 (no interaction in this game) to 5 (totally based on interaction and discussion between players)
    'diplomacy' => 1,    

// Game interface width range (pixels)
// Note: game interface = space on the left side, without the column on the right
'game_interface_width' => array(

    // Minimum width
    //  default: 760
    //  maximum possible value: 760 (ie: your game interface should fit with a 760px width (correspond to a 1024px screen)
    //  minimum possible value: 320 (the lowest value you specify, the better the display is on mobile)
    'min' => 740,

    // Maximum width
    //  default: null (ie: no limit, the game interface is as big as the player's screen allows it).
    //  maximum possible value: unlimited
    //  minimum possible value: 760
    'max' => null
),

// Games categories
//  You can attribute any number of "tags" to your game.
//  Each tag has a specific ID (ex: 22 for the category "Prototype", 101 for the tag "Science-fiction theme game")
    'tags' => array( 1 // abstract
    , 11 // medium length
    , 23 // classic
    , 200 // card game
    )
);

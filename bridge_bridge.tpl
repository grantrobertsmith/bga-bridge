{OVERALL_GAME_HEADER}

<!-- 
--------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- Belote implementation : © David Bonnin <david.bonnin44@gmail.com>
-- Bridge implementation : © Grant Smith <grantrobertsmith@gmail.com>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
--
-- Creative Commons Attributions: 
-- dealer-icon.png & taker-icon.png from the farm-fresh iconset by Fat Cow Web Hosting (http://www.fatcow.com/) - License: Creative Commons (Attribution 3.0 United States https://creativecommons.org/licenses/by/3.0/us/)
-------
-->

<div id="whole_table" class="whole_table">

	<div id="bidding_info" class="bidding_info">
		<div id="biddingPalette" class="biddingPalette whiteblock">
			<h3>{BIDDING_PALETTE}</h3>

			  <svg width=0 height=0>
				<defs>
				  <radialGradient id="grad1" cx="50%" cy="50%" r="80%" fx="0%" fy="0%">
					<stop offset="0%" style="stop-color:white;stop-opacity:1" />
					<stop offset="100%" style="stop-color:darkgray;stop-opacity:1" />
				  </radialGradient>
				</defs>
			  </svg>
			  
			  <br>
			  
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>1</text>
				  <text x=22 y=17>&clubs;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>1</text>
				  <text x=22 y=17 fill=red>&diams;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>1</text>
				  <text x=22 y=17 fill=red>&hearts;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>1</text>
				  <text x=22 y=17>&spades;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=5 y=17>1</text>
				  <text x=14 y=17>NT</text>
				</g>
			  </svg>
			  
			  <br>
			  
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>2</text>
				  <text x=22 y=17>&clubs;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>2</text>
				  <text x=22 y=17 fill=red>&diams;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>2</text>
				  <text x=22 y=17 fill=red>&hearts;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>2</text>
				  <text x=22 y=17>&spades;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=5 y=17>2</text>
				  <text x=14 y=17>NT</text>
				</g>
			  </svg>
			  
			  <br>
			  
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>3</text>
				  <text x=22 y=17>&clubs;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>3</text>
				  <text x=22 y=17 fill=red>&diams;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>3</text>
				  <text x=22 y=17 fill=red>&hearts;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>3</text>
				  <text x=22 y=17>&spades;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=5 y=17>3</text>
				  <text x=14 y=17>NT</text>
				</g>
			  </svg>
			  
			  <br>
			  
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>4</text>
				  <text x=22 y=17>&clubs;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>4</text>
				  <text x=22 y=17 fill=red>&diams;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>4</text>
				  <text x=22 y=17 fill=red>&hearts;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>4</text>
				  <text x=22 y=17>&spades;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=5 y=17>4</text>
				  <text x=14 y=17>NT</text>
				</g>
			  </svg>
			  
			  <br>
			  
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>5</text>
				  <text x=22 y=17>&clubs;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>5</text>
				  <text x=22 y=17 fill=red>&diams;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>5</text>
				  <text x=22 y=17 fill=red>&hearts;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>5</text>
				  <text x=22 y=17>&spades;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=5 y=17>5</text>
				  <text x=14 y=17>NT</text>
				</g>
			  </svg>
			  
			  <br>
			  
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>6</text>
				  <text x=22 y=17>&clubs;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>6</text>
				  <text x=22 y=17 fill=red>&diams;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>6</text>
				  <text x=22 y=17 fill=red>&hearts;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>6</text>
				  <text x=22 y=17>&spades;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=5 y=17>6</text>
				  <text x=14 y=17>NT</text>
				</g>
			  </svg>
			  
			  <br>
			  
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>7</text>
				  <text x=22 y=17>&clubs;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>7</text>
				  <text x=22 y=17 fill=red>&diams;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>7</text>
				  <text x=22 y=17 fill=red>&hearts;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>7</text>
				  <text x=22 y=17>&spades;</text>
				</g>
			  </svg>
			  <svg width=42 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=40 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=5 y=17>7</text>
				  <text x=14 y=17>NT</text>
				</g>
			  </svg>
			  
			  <br>
			  
			  <svg width=107 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=105 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=22 y=17>DOUBLE</text>
				</g>
			  </svg>
			  <svg width=3 height=22 />
			  <svg width=107 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=105 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=11 y=17>REDOUBLE</text>
				</g>
			  </svg>

			  <br>
			  
			  <svg width=107 height=22>
				<g style="cursor:pointer">
				  <rect x=1 y=1 width=105 height=20 rx=10 ry=10 fill="url(#grad1)" stroke=black />
				  <text x=33 y=17>PASS</text>
				</g>
			  </svg>

			  <br>

			<div id="cardToTake" class="cardToTake"></div>
		</div>

		<div id="trumpBlock" class="trumpBlock whiteblock">
			<h3>{TRUMP}</h3>
			<div id="trumpColor" class="trumpColor"></div>
		</div>
	</div>

	<div id="playertables">
		<!-- BEGIN player -->
		<div class="playertable whiteblock playertable_{DIR}">
			<div class="playertablename" style="color:#{PLAYER_COLOR}">
				{PLAYER_NAME}			
			<div id="discussion_bubble_{PLAYER_ID}" class="discussion_bubble"> </div>
			</div>
			<div class="playertablecard" id="playertablecard_{PLAYER_ID}">
			</div>
		</div>
		<!-- END player -->
		
		<div id="taker_icon"></div>
		<div id="dealer_icon"></div>
		<div id="orientation"></div>
	</div>
	
	<div id="myhand_wrap" class="myHand whiteblock">
		<h3>{MY_HAND}</h3>
		<div id="myhand">
		</div>
	</div>

</div>


<script type="text/javascript">

var jstpl_cardontable = '<div class="cardontable" id="cardontable_${player_id}" style="background-position:-${x}px -${y}px">\
                        </div>';

var jstpl_cardvisible = '<div class="cardvisible" id="cardvisible" style="background-position:-${x}px -${y}px">\
                        </div>';


var jstpl_spade = '<div class="spade" id="spade">\
                        </div>';
 
var jstpl_heart = '<div class="heart" id="heart">\
                        </div>';

var jstpl_diamond = '<div class="diamond" id="diamond">\
                        </div>';

var jstpl_club = '<div class="club" id="club">\
                        </div>';
						
var jstpl_all_trumps = '<div class="all_trumps" id="all_trumps">\
                        </div>';

var jstpl_no_trumps = '<div class="no_trumps" id="no_trumps">\
                        </div>';

var jstpl_dealer = '<div class="dealer" id="dealer">\
                        </div>';
var jstpl_taker = '<div class="taker" id="taker">\
                        </div>';

var jstpl_player_board = '<div class="cp_board">\
    <div id="tricks_p${id}" class="tricks_icon"></div> &#x00D7 <span id="trickscount_p${id}">0</span>\
</div>';


</script>  

{OVERALL_GAME_FOOTER}

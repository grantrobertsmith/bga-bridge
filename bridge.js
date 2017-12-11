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
 * bridge.js
 *
 * Bridge user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */
define([
        "dojo", "dojo/_base/declare",
        "ebg/core/gamegui",
        "ebg/counter",
        "ebg/stock"
    ],
    function(dojo, declare) {
        return declare("bgagame.bridge", ebg.core.gamegui, {
            constructor: function() {
                console.log('bridge constructor');

                this.playerHand = null;
                this.cardwidth = 72;
                this.cardheight = 96;
                this.trumpWidth = 68;
                this.cardOnTop_id = -1;
                this.cardOnTop_val = -1;
                this.cardOnTop_color = -1;
                this.trump = -1;
                this.dealer = -1;
                this.taker = -1;

            },



            setup: function(gamedatas) {
                console.log("Starting game setup");

                this.trump = gamedatas.trump;
                this.dealer = gamedatas.dealer;
                this.taker = gamedatas.taker;

                this.cardOnTop_id = gamedatas.cardOnTop_id;
                this.cardOnTop_color = gamedatas.cardOnTop_color;
                this.cardOnTop_val = gamedatas.cardOnTop_val;

                
				// Setting up player boards
				for( var player_id in gamedatas.players )
				{
					var player = gamedatas.players[player_id];
							
					// Setting up players boards if needed
					var player_board_div = $('player_board_'+player_id);
					dojo.place( this.format_block('jstpl_player_board', player ), player_board_div );

					var div = document.getElementById('trickscount_p' + player_id);
					if (typeof div != 'undefined') {
						div.innerHTML = player['tricks'];
					}

				}
                this.addTooltipToClass("tricks_icon", _("Tricks won during this hand"), '');


                // Player hand
                this.playerHand = new ebg.stock();
                this.playerHand.create(this, $('myhand'), this.cardwidth, this.cardheight);
                this.playerHand.image_items_per_row = 13;
                this.playerHand.setSelectionMode(1);
                dojo.connect(this.playerHand, 'onChangeSelection', this, 'onPlayerHandSelectionChanged');

                // Create cards types:
                for (var color = 1; color <= 4; color++) {
                    for (var value = 2; value <= 14; value++) {
                        // Build card type id
                        var card_val_id = this.getCardUniqueId(color, value);
                        if (color == this.trump || this.trump == 6 /*all trumps*/ ) {
                            this.playerHand.addItemType(card_val_id, this.get_trump_weight(color, value), g_gamethemeurl + 'img/cards.jpg', card_val_id);
                        } else {
                            this.playerHand.addItemType(card_val_id, this.get_normal_weight(color, value), g_gamethemeurl + 'img/cards.jpg', card_val_id);
                        }
                    }
                }

                console.log(this.gamedatas.hand);
                // Cards in player's hand
                for (var i in this.gamedatas.hand) {
                    var card = this.gamedatas.hand[i];
                    var color = card.type;
                    var value = card.type_arg;
                    this.playerHand.addToStockWithId(this.getCardUniqueId(color, value), card.id);
                }

                // Cards played on table
                for (i in this.gamedatas.cardsontable) {
                    var card = this.gamedatas.cardsontable[i];
                    var color = card.type;
                    var value = card.type_arg;
                    var player_id = card.location_arg;
                    this.playCardOnTable(player_id, color, value, card.id);
                }

                // Tooltips 
                this.addTooltipToClass("playertablecard", _("Card played on the table"), '');

                this.addTooltip('myhand', '', _('Play a card'));
                this.addTooltip('dealer_icon', _('Dealer for this hand'), '');
                this.addTooltip('taker_icon', _('Taker for this hand'), '');


                this.addTooltipToClass("cardToTake", _("Visible card that can be taken"), '');


                if (this.cardOnTop_id >= 0) {
                    this.makeCardVisible(this.cardOnTop_color, this.cardOnTop_val, this.cardOnTop_id);
                }

                if (this.trump > 0) {
                    this.displayTrump(this.trump);
                }

                if (this.dealer > 0) {
                    this.setDealer(this.dealer);
                }
                if (this.taker > 0) {
                    this.setTaker(this.taker);
                } else {
                    this.noTaker();
                }



				var div = document.getElementById("orientation");
				if(typeof div != 'undefined'){
					if(this.gamedatas.orientation == 1)
						div.className = "counterclockwise_icon";
					else if(this.gamedatas.orientation == 2)
						div.className = "clockwise_icon";
				}
				


                // Setup game notifications to handle (see "setupNotifications" method below)
                this.setupNotifications();

                this.ensureSpecificImageLoading(['../common/point.png']);

                console.log("Ending game setup");
            },


            ///////////////////////////////////////////////////
            //// Game & client states

            // onEnteringState: this method is called each time we are entering into a new game state.
            //                  You can use this method to perform some user interface changes at this moment.
            //
            onEnteringState: function(stateName, args) {
                console.log('Entering state: ' + stateName);

                switch (stateName) {


                    case 'playerTurn':

                        var items = this.playerHand.getSelectedItems();
                        if (items.length != 1) {
                            this.playerHand.unselectAll();
                        } else if (this.isCurrentPlayerActive()) {
                            var card_id = items[0].id;

                            this.ajaxcall("/bridge/bridge/playCard.html", {
                                id: card_id,
                                lock: true
                            }, this, function(result) {}, function(is_error) {});
                            this.playerHand.unselectAll();
                        }
                        break;
                        /* Example:
			  
			  case 'myGameState':
			  
			  // Show some HTML block at this game state
			  dojo.style( 'my_html_block_id', 'display', 'block' );
			  
			  break;
		       */


                    case 'dummmy':
                        break;
                }
            },

            // onLeavingState: this method is called each time we are leaving a game state.
            //                 You can use this method to perform some user interface changes at this moment.
            //
            onLeavingState: function(stateName) {
                console.log('Leaving state: ' + stateName);

                switch (stateName) {

                    /* Example:
			  
			  case 'myGameState':
			  
			  // Hide the HTML block we are displaying only during this game state
			  dojo.style( 'my_html_block_id', 'display', 'none' );
			  
			  break;
		       */


                    case 'dummmy':
                        break;
                }
            },

            // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
            //                        action status bar (ie: the HTML links in the status bar).
            //        
            onUpdateActionButtons: function(stateName, args) {
                console.log('onUpdateActionButtons: ' + stateName);
                console.log(this.cardOnTop_name);

                if (this.isCurrentPlayerActive()) {
                    switch (stateName) {

                        case 'firstRound':
                            this.addActionButton('acceptFirstRound_button', _('Take the card'), 'onAcceptFirstRound');
                            this.addActionButton('rejectFirstRound_button', _('Pass'), 'onRejectFirstRound');
                            break;

                        case 'secondRound':
                            this.addActionButton('acceptSpade_button', '<div class="spade_icon" id="spade_icon"></div>', 'onAcceptSpade');
                            this.addActionButton('acceptHeart_button', '<div class="heart_icon" id="spade_icon"></div>', 'onAcceptHeart');
                            this.addActionButton('acceptClub_button', '<div class="club_icon" id="spade_icon"></div>', 'onAcceptClub');
                            this.addActionButton('acceptDiamond_button', '<div class="diamond_icon" id="spade_icon"></div>', 'onAcceptDiamond');
                            this.addActionButton('rejectSecondRound_button', _('Pass'), 'onRejectSecondRound');
                            break;

						case 'thirdRound':
							this.addActionButton('acceptNoTrumps_button', _('No trumps'), 'onAcceptNoTrumps');
							this.addActionButton('acceptAllTrumps_button', _('All trumps'), 'onAcceptAllTrumps');
							this.addActionButton('rejectThirdRound_button', _('Pass'), 'onRejectThirdRound');
                    }
                }
            },

            ///////////////////////////////////////////////////
            //// Utility methods

            /*
		 
		 Here, you can defines some utility methods that you can use everywhere in your javascript
		 script.
		 
               */

            enable_speech_bubble: function(id, speech) {
                var div = document.getElementById('discussion_bubble_' + id);
                if (typeof div != 'undefined') {
                    div.style.display = 'block';
                    div.innerHTML = speech;
                }
            },

            disable_speech_bubble: function(id) {
                var div = document.getElementById('discussion_bubble_' + id);
                if (typeof div != 'undefined') {
                    div.style.display = 'none';
                    div.innerHTML = "";
                }
            },

            new_trump: function(trump) {
                this.trump = trump;
                console.log('trump : ' + this.trump);
                this.displayTrump(this.trump);
                var myArray = new Array();
                if (trump < 5) {
					for (var col = 1; col <= 4; col++) {
                        for (var value = 2; value <= 14; value++) {
                            var card_val_id = this.getCardUniqueId(col, value);
                            myArray[card_val_id] = this.get_normal_weight(col, value);
                        }
					}
                    for (var value = 2; value <= 14; value++) {
                        var card_val_id = this.getCardUniqueId(trump, value);
                        myArray[card_val_id] = this.get_trump_weight(trump, value);
                    }
                } else if (trump == 6) {
                    for (var col = 1; col <= 4; col++)
                        for (var value = 2; value <= 14; value++) {
                            var card_val_id = this.getCardUniqueId(col, value);
                            myArray[card_val_id] = this.get_trump_weight(col, value);
                        }
                }
                this.playerHand.changeItemsWeight(myArray);


            },

            reset_trump: function() {
                var color = this.trump;
                var name = '';
                if (color > 0) {
					this.trump = -1;
                    var myArray = new Array();
                    for (var col = 1; col <= 4; col++) {
                        for (var value = 2; value <= 14; value++) {
                            var card_val_id = this.getCardUniqueId(col, value);
                            myArray[card_val_id] = this.get_normal_weight(col, value);
                        }
                    }
                    this.playerHand.changeItemsWeight(myArray);
                    if (color == 1) {
                        name = 'spade';
                    } else if (color == 2) {
                        name = 'heart';
                    } else if (color == 3) {
                        name = 'club';
                    } else if (color == 4) {
                        name = 'diamond';
                    } else if (color == 5) {
                        name = 'no_trumps';
                    } else if (color == 6) {
                        name = 'all_trumps';
                    }
                    var anim = this.slideToObject(name, name);
                    dojo.connect(anim, 'onEnd', function(node) {
                        dojo.destroy(node);
                    });
                    anim.play();
                }
            },

			// Base order is S-H-C-D
			// Modify order depending on trump, to keep alternate colors
			get_modified_color: function(color) {
				if(this.trump == 2 && color == 4) // trump H -> D is placed between S and C
					return 2;
				else if(this.trump == 3 && color == 1) // trump C -> S is placed between H and D
					return 3;
				else return color; // default
			},

            get_normal_weight: function(color, value) {
                return 13 * (this.get_modified_color(color) - 1) + value;
            },

            get_trump_weight: function(color, value) {
                return 13 * (this.get_modified_color(color) - 1) + value + 13;
            },

            // Get card unique identifier based on its color and value
            getCardUniqueId: function(color, value) {
                return (color - 1) * 13 + (value - 2);
            },

            setDealer: function(player_id) {
                // Slide into position (bottom right of this player play zone)
                this.slideToObjectPos('dealer_icon', 'playertablecard_' + player_id, 78, 78, 1000).play();
            },

            setTaker: function(player_id) {
                // Slide into position (bottom left of this player play zone)
                this.slideToObjectPos('taker_icon', 'playertablecard_' + player_id, -38, 78, 1000).play();
            },

            noTaker: function() {
                // Slide into position (bottom left of the card to be taken)
                this.slideToObjectPos('taker_icon', 'cardToTake', -38, 73, 1000).play();
            },


            playCardOnTable: function(player_id, color, value, card_id) {
                // player_id => direction
                dojo.place(
                    this.format_block('jstpl_cardontable', {
                        x: this.cardwidth * (value - 2),
                        y: this.cardheight * (color - 1),
                        player_id: player_id
                    }), 'playertablecard_' + player_id);

                this.disable_speech_bubble(player_id);

                if (player_id != this.player_id) {
                    // Some opponent played a card
                    // Move card from player panel
                    this.placeOnObject('cardontable_' + player_id, 'overall_player_board_' + player_id);
                } else {
                    // You played a card. If it exists in your hand, move card from there and remove
                    // corresponding item

                    if ($('myhand_item_' + card_id)) {
                        this.placeOnObject('cardontable_' + player_id, 'myhand_item_' + card_id);
                        this.playerHand.removeFromStockById(card_id);
                    }
                }

                // In any case: move it to its final destination
                this.slideToObject('cardontable_' + player_id, 'playertablecard_' + player_id, 500, 0).play();

            },


            makeCardVisible: function(color, value, card_id) {
                // player_id => direction
                dojo.place(
                    this.format_block('jstpl_cardvisible', {
                        x: this.cardwidth * (value - 2),
                        y: this.cardheight * (color - 1)
                    }), 'cardToTake');

                this.placeOnObject('cardvisible', 'cardToTake');

                // In any case: move it to its final destination
                this.slideToObject('cardvisible', 'cardToTake').play();

            },

            destroyVisibleCard: function() {
                if (this.cardOnTop_id != -1) {
                    var anim = this.slideToObject('cardvisible', 'cardToTake');
                    dojo.connect(anim, 'onEnd', function(node) {
                        dojo.destroy(node);
                    });
                    anim.play();
                    this.cardOnTop_id = -1;
                }
            },

            displayTrump: function(trumpColor) {
                // player_id => direction

                if (trumpColor == 1) {
                    dojo.place(
                        this.format_block('jstpl_spade', {}), 'trumpColor');
                    this.placeOnObject('spade', 'trumpColor');
                    this.slideToObject('spade', 'trumpColor').play();
                    this.addTooltip('spade', _('Spade'), '');

                } else if (trumpColor == 2) {
                    dojo.place(
                        this.format_block('jstpl_heart', {}), 'trumpColor');
                    this.placeOnObject('heart', 'trumpColor');
                    this.slideToObject('heart', 'trumpColor').play();
                    this.addTooltip('heart', _('Heart'), '');

                } else if (trumpColor == 3) {
                    dojo.place(
                        this.format_block('jstpl_club', {}), 'trumpColor');
                    this.placeOnObject('club', 'trumpColor');
                    this.slideToObject('club', 'trumpColor').play();
                    this.addTooltip('club', _('Club'), '');

                } else if (trumpColor == 4) {
                    dojo.place(
                        this.format_block('jstpl_diamond', {}), 'trumpColor');
                    this.placeOnObject('diamond', 'trumpColor');
                    this.slideToObject('diamond', 'trumpColor').play();
                    this.addTooltip('diamond', _('Diamond'), '');

                } else if (trumpColor == 5) {
                    dojo.place(
                        this.format_block('jstpl_no_trumps', {}), 'trumpColor');
                    this.placeOnObject('no_trumps', 'trumpColor');
                    this.slideToObject('no_trumps', 'trumpColor').play();
                    this.addTooltip('no_trumps', _('No trumps'), '');

                } else if (trumpColor == 6) {
                    dojo.place(
                        this.format_block('jstpl_all_trumps', {}), 'trumpColor');
                    this.placeOnObject('all_trumps', 'trumpColor');
                    this.slideToObject('all_trumps', 'trumpColor').play();
                    this.addTooltip('all_trumps', _('All trumps'), '');

                }


            },


            ///////////////////////////////////////////////////
            //// Player's action

            /*
		 
		 Here, you are defining methods to handle player's action (ex: results of mouse click on 
		 game objects).
		 
		 Most of the time, these methods:
		 _ check the action is possible at this game state.
		 _ make a call to the game server
		 
               */



            onPlayerHandSelectionChanged: function() {
                var items = this.playerHand.getSelectedItems();
                if (items.length != 1) {
                    this.playerHand.unselectAll();
                } else if (this.isCurrentPlayerActive()) {
                    if (this.checkAction('playCard', true)) {
                        // Can play a card

                        var card_id = items[0].id;

                        this.ajaxcall("/bridge/bridge/playCard.html", {
                            id: card_id,
                            lock: true
                        }, this, function(result) {}, function(is_error) {});
                        this.playerHand.unselectAll();
                    }

                }
            },


            onAcceptFirstRound: function() {
                if (this.checkAction('acceptFirstRound', true)) {
                    this.ajaxcall("/bridge/bridge/acceptFirstRound.html", {
                        lock: true
                    }, this, function(result) {}, function(is_error) {});
                }
            },

            onRejectFirstRound: function() {
                if (this.checkAction('passFirstRound', true)) {
                    this.ajaxcall("/bridge/bridge/passFirstRound.html", {
                        lock: true
                    }, this, function(result) {}, function(is_error) {});
                }
            },




            onAcceptSpade: function() {
                if (this.checkAction('acceptSecondRound', true)) {
                    this.ajaxcall("/bridge/bridge/acceptSecondRound.html", {
                        lock: true,
                        chosenTrump: 1
                    }, this, function(result) {}, function(is_error) {});
                }
            },

            onAcceptHeart: function() {
                if (this.checkAction('acceptSecondRound', true)) {
                    this.ajaxcall("/bridge/bridge/acceptSecondRound.html", {
                        lock: true,
                        chosenTrump: 2
                    }, this, function(result) {}, function(is_error) {});
                }
            },

            onAcceptClub: function() {
                if (this.checkAction('acceptSecondRound', true)) {
                    this.ajaxcall("/bridge/bridge/acceptSecondRound.html", {
                        lock: true,
                        chosenTrump: 3
                    }, this, function(result) {}, function(is_error) {});
                }
            },

            onAcceptDiamond: function() {
                if (this.checkAction('acceptSecondRound', true)) {
                    this.ajaxcall("/bridge/bridge/acceptSecondRound.html", {
                        lock: true,
                        chosenTrump: 4
                    }, this, function(result) {}, function(is_error) {});
                }
            },

			onRejectSecondRound: function() {
                if (this.checkAction('passSecondRound', true)) {
                    this.ajaxcall("/bridge/bridge/passSecondRound.html", {
                        lock: true
                    }, this, function(result) {}, function(is_error) {});
                }
            },


			onAcceptNoTrumps: function() {
                if (this.checkAction('acceptThirdRound', true)) {
                    this.ajaxcall("/bridge/bridge/acceptThirdRound.html", {
                        lock: true,
                        chosenTrump: 5
                    }, this, function(result) {}, function(is_error) {});
                }
            },

			onAcceptAllTrumps: function() {
                if (this.checkAction('acceptThirdRound', true)) {
                    this.ajaxcall("/bridge/bridge/acceptThirdRound.html", {
                        lock: true,
                        chosenTrump: 6
                    }, this, function(result) {}, function(is_error) {});
                }
            },

            onRejectThirdRound: function() {
                if (this.checkAction('passThirdRound', true)) {
                    this.ajaxcall("/bridge/bridge/passThirdRound.html", {
                        lock: true
                    }, this, function(result) {}, function(is_error) {});
                }
            },



            ///////////////////////////////////////////////////
            //// Reaction to cometD notifications

            /*
		 setupNotifications:
		 
		 In this method, you associate each of your game notifications with your local method to handle it.
		 
		 Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                 your bridge.game.php file.
		 
               */
            setupNotifications: function() {
                console.log('notifications subscriptions setup');

                dojo.subscribe('startingNewHand', this, "notif_startingNewHand");
                dojo.subscribe('newHand', this, "notif_newHand");
                dojo.subscribe('fillHand', this, "notif_fillHand"); 
                dojo.subscribe('dealCards', this, "notif_dealCards");

                dojo.subscribe('cardOnTop', this, "notif_cardOnTop");
                dojo.subscribe('pass', this, "notif_pass");
				dojo.subscribe('takeCard', this, "notif_takeCard");
				dojo.subscribe('noDeal', this, "notif_noDeal");

                dojo.subscribe('playCard', this, "notif_playCard");
				dojo.subscribe('belote', this, "notif_belote");
                dojo.subscribe('giveAllCardsToPlayer', this, "notif_giveAllCardsToPlayer");
                dojo.subscribe('trickWin', this, "notif_trickWin");
                
                dojo.subscribe('dixDeDer', this, "notif_dixDeDer");
                dojo.subscribe('newScores', this, "notif_newScores");

                dojo.subscribe('capot', this, "notif_capot");
                dojo.subscribe('dedans', this, "notif_dedans");
                dojo.subscribe('litige', this, "notif_litige");

				this.notifqueue.setSynchronous('newScores', 4000);
                this.notifqueue.setSynchronous('trickWin', 1200);
                this.notifqueue.setSynchronous('giveAllCardsToPlayer', 1300);
                this.notifqueue.setSynchronous('fillHand', 1000);
                this.notifqueue.setSynchronous('noDeal', 500);
                this.notifqueue.setSynchronous('playCard', 500);
            },

            notif_startingNewHand: function(notif) {
                this.noTaker();
				for (var player_id in this.gamedatas.players){
					var div = document.getElementById('trickscount_p' + player_id);
					if (typeof div != 'undefined') {
						div.innerHTML = "0";
					}
				}
            },

            notif_newHand: function(notif) {
                // We received a new hand of 5 cards.
                this.playerHand.removeAll();

                for (var i in notif.args.cards) {
                    var card = notif.args.cards[i];
                    var color = card.type;
                    var value = card.type_arg;
                    this.playerHand.addToStockWithId(this.getCardUniqueId(color, value), card.id);
                }
            },


            notif_fillHand: function(notif) {
                // We received a new full hand of 8 cards.
                this.playerHand.removeAll();

                for (var i in notif.args.cards) {
                    var card = notif.args.cards[i];
                    var color = card.type;
                    var value = card.type_arg;
                    this.playerHand.addToStockWithId(this.getCardUniqueId(color, value), card.id);
                }
            },


            notif_takeCard: function(notif) {
                var taker_id = notif.args.player_id;
                var anim = this.slideToObject('cardvisible', 'playertablecard_' + taker_id, 700, 0);
				dojo.connect(anim, 'onEnd', this, 'fadeOutAndDestroy');
                /*dojo.connect(anim, 'onEnd', function(node) {
                    dojo.destroy(node);
                });*/
                anim.play();
                this.cardOnTop_id = -1;
                this.new_trump(notif.args.trump);
                this.setTaker(taker_id);
            },

            notif_playCard: function(notif) {
                // Play a card on the table
                this.playCardOnTable(notif.args.player_id, notif.args.color, notif.args.value, notif.args.card_id);
            },
            notif_trickWin: function(notif) {
				var div = document.getElementById('trickscount_p' + notif.args.player_id);
				if (typeof div != 'undefined') {
					div.innerHTML = notif.args.tricks;
				}
            },
            notif_dixDeDer: function(notif) {

            },
            notif_pass: function(notif) {

            },
            notif_belote: function(notif) {
                if (typeof notif.args.speech != 'undefined')
                    this.enable_speech_bubble(notif.args.player_id, notif.args.speech);
            },
            notif_dealCards: function(notif) {
                this.setDealer(notif.args.player_id);
            },
            notif_noDeal: function(notif) {

            },
            notif_capot: function(notif) {

            },
            notif_dedans: function(notif) {

            },
            notif_litige: function(notif) {

            },
            notif_cardOnTop: function(notif) {
                this.destroyVisibleCard();
                this.cardOnTop_id = notif.args.card_id;
                this.cardOnTop_color = notif.args.card_color;
                this.cardOnTop_val = notif.args.card_val;
                this.makeCardVisible(this.cardOnTop_color, this.cardOnTop_val, this.cardOnTop_id);
                this.reset_trump();
            },
            notif_giveAllCardsToPlayer: function(notif) {
                // Move all cards on table to given table, then destroy them
                var winner_id = notif.args.player_id;
                for (var player_id in this.gamedatas.players) {
                    var anim = this.slideToObject('cardontable_' + player_id, 'playertablecard_' + winner_id, 700, 0);
					
					dojo.connect(anim, 'onEnd', this, 'fadeOutAndDestroy');
                    /*dojo.connect(anim, 'onEnd', function(node) {
                        dojo.destroy(node);
                    });*/
                    anim.play();
					
                }
            },

            notif_newScores: function(notif) {
                // Update players' scores

                for (var player_id in notif.args.newScores) {
                    this.disable_speech_bubble(player_id);
                    this.scoreCtrl[player_id].toValue(notif.args.newScores[player_id]);
                }
            }

        });
    });
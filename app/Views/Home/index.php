<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Betting HomePage</title>
    <style>
        div.enlarge:hover {
          box-shadow: 0 0 0 99999px rgba(0, 0, 0, 0.8);
          position: relative;
          z-index: 9999;
        }
        .box {
              display: flex;
            }
        div.example {
          margin: 5px; 
          height:350px;
          width: 120px;
          border-radius: 2%;
          transition: 500ms;
          right: 0;
        }
        .progress {
          height: 2rem;
        }
        @media screen and (min-width: 901px) {
          div.example {
            width: 145px;
          }
        }
        .hidden {
          display: none;
        }
        .card:hover .hidden {
            display: flex;    
        }
        .card:hover {
            transform: scale(1.1); 
            z-index: 100;
        }
    </style>
</head>
<body>
    <?= $header ?>

<div style="background-image: linear-gradient(to bottom, #198754, black); color: white;">

    <div class="container-fluid" id="ranksDiv">
        <div class="row" style="margin-bottom: 25px; height: 80px; align-items: center;">
            <div class="col-1" style="align-items: center; text-align: left; padding-bottom: 14px;">
                <i id="ranks" class="bi bi-record-circle" style="font-size: 30px; transition-property: font-size; transition-duration: 1s;"></i>
            </div>
            <div class="col-11" style="vertical-align: middle;">
                <h5 style="margin-bottom: auto; margin-top: auto;">
                    <span class="btn btn-large btn-success" style="font-size: large;">TOP PLAYERS</span>
                    <span>---------</span>
                    <span class="btn btn-large btn-success" style="font-size: large;">MY RANK</span>
                </h5>
            </div>
        </div>
        <div class="row ranking-wrapper" style="width: 100%; margin-left: 5px;">
        </div>
    </div>

    <br>

    <hr style="width: 90%; margin-bottom: 30px; margin: 0px auto 30px auto;">

    <div class="container-fluid" id="publicDiv">
        <div class="row" style="margin-bottom: 7px; height: 80px; align-items: center;">
            <div class="col-1" style="align-items: center; text-align: left; padding-bottom: 14px;">
                <i id="publicBets" class="bi bi-record-circle" style="font-size: 30px; transition-property: font-size; transition-duration: 1s;"></i>
            </div>
            <div class="col-11" style="padding-left: 0;;">
                <h4 style="font-size: 25px;">PARTICIPATE IN PUBLIC BETS</h4>
            </div>
        </div>
        <div class="row" style="height: 297px;">
            <div class="col-1 text-center">
                <button id="betBack" type="button" class="btn btn-lg" style="background-image: url(); background-size: cover; background-position: right; color: black; font-size: xx-large; height: 290px; width: 90%; padding-left: 0;">&lt</button>
            </div>
            <?php
                if(count($public_battles)>0)
                {
                    foreach($public_battles as $key => $row)
                    {
                        ?>
                            <div class="col-3" style="width: 27.75%;">
                              <div class="card betBattle" style="transition: 1s;">
                                <div class="card-body" style="width: 100%; color: black; padding: 8px 0 0 0;">
                                    <div class="row" style="margin-bottom: 0; padding: 0 15px 0 15px;">
                                        <div class="col-4">
                                            <div>
                                                <!-- <i class="fas fa-exchange-alt" style="font-size: 30px;"></i> -->
                                            </div>    
                                        </div>
                                        <div class="col-5" style="padding-left: 30px;">
                                            <div><?= $row['battle_end_date']; ?></div>
                                        </div>
                                        <div class="col-1" style="text-align: right; justify-content: right; padding-top: 4px;">
                                            <div style="width: 10px; height: 10px; border-radius: 50%; background-color: green; padding: auto; margin: auto;">&nbsp&nbsp&nbsp&nbsp&nbsp</div>
                                        </div>
                                        <div class="col-2" style="font-size: 12px; padding: 0; text-align: right;">
                                            <i class="fas fa-user-friends"></i>
                                            <span><?= ($row['player1_additional'] + $row['player2_additional']) ?></span>
                                        </div>
                                    </div>
                                    <div class="row" style="text-align: center; padding: 0 15px 0 15px;">
                                        <div class="col-5">
                                            <?php
                                                $media_imgs = json_decode($row['media_images'], TRUE);
                                            ?>                             
                                            <img id="imgC1" src="<?= $media_imgs[0]; ?>" style="border-radius: 50%; width: 150px;">
                                        </div>
                                        <div class="col-7">
                                            <h4 style="margin-top: 23px; margin-bottom: 20px;">Here is a prediction made - <?= $row['battle_description']; ?></h4>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 15px; margin-bottom: 15px; text-align: center;">
                                        <div class="col-6">
                                            <h5 style="margin-bottom: 2px;"><?= $row['media_name']; ?></h5>
                                            <h6>Bet Battle</h6>
                                        </div>
                                        <div class="col-6" style="text-align: center;">
                                            <h5 style="margin-bottom: 0;">STAKE</h5>
                                            <h6 style="font-weight: 800;">Rs<?= $row['battle_amount']; ?></h6>
                                        </div>
                                    </div>
                                    <div class="row hidden" style="font-weight: 600;">
                                        <div class="col-6" style="text-align: center; font-size: large; font-weight: 800;">FOR</div>
                                        <div class="col-6" style="text-align: center; font-size: large; font-weight: 800;">AGAINST</div>
                                    </div>
                                    <div class="row hidden" style="font-weight: 600;">
                                        <div class="col-6" style="text-align: center;"><?= ($row['player_for'] == 1 ? $row['player1_name'] : $row['player2_name']) ?></div>
                                        <div class="col-6" style="text-align: center;"><?= ($row['player_against'] == 1 ? $row['player1_name'] : $row['player2_name']) ?></div>
                                    </div>
                                    <!-- <div class="row hidden" style="font-size: 50px; padding: 0;">
                                        <div class="col-6" style="text-align: start;">
                                            <i class="fas fa-hand-holding-usd"></i>
                                            <span style="font-size: 30px;">Rs</span>
                                        </div>
                                        <div class="col-6" style="text-align: right;">
                                            <span style="font-size: 30px;">Rs</span>
                                            <i class="fas fa-hand-holding-usd fa-flip-horizontal"></i>
                                        </div>
                                    </div> -->
                                    <div class="row hidden" style="font-size: 20px; font-family: 'RocknRoll One', sans-serif; padding: 0 15px 10px 15px;">
                                        <div class="col-6" style="text-align: start; padding: 0;">
                                            <div id="joinFor" class="btn btn-sm btn-dark" style="width: 90%;" data-battle-id="<?= $row['battle_id']; ?>">JOIN "FOR" Rs<?= $row['additional_bet_amount']; ?></div>
                                        </div>
                                        <div class="col-6" style="text-align: right; padding: 0;">
                                          <div id="joinAgainst" class="btn btn-sm btn-dark" style="width: 90%;" data-battle-id="<?= $row['battle_id']; ?>">JOIN "AGAINST" Rs<?= $row['additional_bet_amount']; ?></div>
                                        </div>
                                    </div>
                                    <!-- <div class="row hidden" style="width: 100%; margin: 0; margin-top: 10px;">
                                        <div class="col-12 btn btn-lg btn-success" style="text-align: center;">
                                            BUY - $50
                                        </div>
                                    </div> -->
                                    <div class="row timer" style="width: 100%; background-color: #dbca2e; border-radius: 2%; margin: 0 auto 0 auto;;">
                                        <div class="col-12" style="text-align: center;">
                                            <?= $row['battle_end_date']; ?>
                                        </div>
                                    </div>
                                </div>
                              </div>
                            </div>
                        <?php
                    }
                }
                else
                {
                    ?>
                    <h5>No Battles Found</h5>
                    <?php
                }
            ?>
            <div class="col-1 text-center">
                <button id="betForward" type="button" class="btn btn-lg" style="background-image: url(publicBet.png); background-size: cover; color: black; font-size: xx-large; height: 290px; width: 90%; padding-right: 0;">&gt</button>
            </div>
        </div>
    </div>

    <br>
    <br>
    <br>

    <hr style="width: 90%; margin-bottom: 30px; margin: 0px auto 30px auto;">

    <div class="container-fluid" id="barsDiv">
      <div class="row" style="margin-bottom: 7px; height: 80px; align-items: center;">
          <div class="col-1" style="align-items: center; text-align: left; padding-bottom: 14px;">
              <i id="bars" class="bi bi-record-circle" style="font-size: 30px; transition-property: font-size; transition-duration: 1s;"></i>
          </div>
          <div class="col-11" style="padding-left: 0;">
              <h5 style="font-size: 25px;">MAKE YOUR OWN PREDICTIONS</h5>
          </div>
      </div>
      <div class="row">
          <div class="container-fluid row" id="barContainer" style="justify-content: center; display: flex; flex-wrap: nowrap; text-align: center;">
            <?php
                if(count($medias)>0)
                {
                    $media_count = 0;
                    foreach($medias as $key => $row)
                    {
                        if(empty($row['media_images'])) continue;
                        $imgs = json_decode($row['media_images'], TRUE);

                        if($media_count == 0)
                        {
                            $media_count++;
                            ?>
                            <div class="col-auto example fullBar">
                                <div class="row enlarge" id="upperBar" style="height: 20%; background-image: url(<?= $imgs[0]; ?>); background-size: 100%; border-radius: 2%; font-size: x-small; ">
                                  <span style="vertical-align: baseline;">
                                    <div class="dropdown">
                                      <button class="btn btn-success btn-sm dropdown-toggle" style="width: 100%; text-align: center; font-size: xx-small; font-weight: bold;" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        BET
                                      </button>
                                      <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li><a href="../Synopsis2/index2.html" onclick="btnFunction()" class="dropdown-item btn">Bet Sequel</a></li>
                                        <li><a href="../Synopsis2/index.html" onclick="btnFunction()" class="dropdown-item btn">Bet Accuracy</a></li>
                                        <li><a href="../Synopsis2/index3.html" onclick="btnFunction()" class="dropdown-item btn">Bet Battle</a></li>
                                      </ul>
                                    </div>
                                  </span>
                                </div>
                            <?php
                        }
                        else
                        {
                            $media_count--;
                            ?>
                                <div style="height: 7px;"></div>
                                <div class="row enlarge" id="lowerBar" style="height: 80%;background-image: url(<?= $imgs[0];?>); background-size: 100%; border-radius: 2%; font-size: x-small;">
                                  <span style="vertical-align: baseline;">
                                    <div class="dropdown">
                                      <button class="btn btn-success btn-sm dropdown-toggle" style="width: 100%; text-align: center; font-size: xx-small; font-weight: bold;" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        BET
                                      </button>
                                      <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li><a href="../Synopsis2/index2.html" onclick="btnFunction()" class="dropdown-item btn">Bet Sequel</a></li>
                                        <li><a href="../Synopsis2/index.html" onclick="btnFunction()" class="dropdown-item btn">Bet Accuracy</a></li>
                                        <li><a href="../Synopsis2/index3.html" onclick="btnFunction()" class="dropdown-item btn">Bet Battle</a></li>
                                      </ul>
                                    </div>
                                  </span>
                                </div>
                              </div>
                            <?php
                        }
                    }
                }
                else
                {
                    ?>
                    <h5>No Medias Found</h5>
                    <?php
                }
            ?>
          </div>
      </div>
      <div class="row" style="background-color: black; border-radius: 70%; height: 10px;"></div>
    </div>
  <br>
</div>
</body>
<script type="text/javascript" src="/assets/js/main-base.js"></script>

<script>
	function fetch_ranks()
	{
		$.ajax({
			type: 'GET',
			dataType: 'json',
			url: '/home/fetch_top_ranks',
			success: function(data) {
				if(data.ranks && data.ranks.length>0)
				{
					var barsHtml = '';
					var maxProgress = data.ranks[0].points_total;
					for(var i=0;i<data.ranks.length;i++)
					{
						var currentProgress = Math.round((data.ranks[i].points_total/maxProgress)*100);
						barsHtml += '<div><span>'+data.ranks[i].name+'</span>';
                		barsHtml += '<div class="progress">';
                  		barsHtml += '<div class="progress-bar bg-success" role="progressbar" style="width: '+currentProgress+'%" aria-valuenow="'+currentProgress+'" aria-valuemin="0" aria-valuemax="100">'+currentProgress+'</div></div><br></div>';
					}
					$('.ranking-wrapper').html(barsHtml);
				}
			},
			error: function(err) {
				console.log(`Error ${err}`);
			}
		});
	}

	setInterval(fetch_ranks(), 5*60*1000);
</script>
</html>
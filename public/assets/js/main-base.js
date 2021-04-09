var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
  return new bootstrap.Popover(popoverTriggerEl)
})

var x = 0;
var xperc = "";
var full = false;


document.querySelector("#ranksDiv").addEventListener("mouseenter", function(){
    document.querySelector("#ranks").style.fontSize = "40px";
    document.querySelector("#ranks").style.marginTop = "0px";
})

document.querySelector("#ranksDiv").addEventListener("mouseleave", function(){
    document.querySelector("#ranks").style.fontSize = "30px";
    document.querySelector("#ranks").style.marginTop = "5px";
})

document.querySelector("#barsDiv").addEventListener("mouseenter", function(){
    document.querySelector("#bars").style.fontSize = "40px";
    document.querySelector("#bars").style.marginTop = "0px";
})

document.querySelector("#barsDiv").addEventListener("mouseleave", function(){
    document.querySelector("#bars").style.fontSize = "30px";
    document.querySelector("#bars").style.marginTop = "5px";
})

document.querySelector("#publicDiv").addEventListener("mouseenter", function(){
  document.querySelector("#publicBets").style.fontSize = "40px";
  document.querySelector("#publicBets").style.marginTop = "0px";
})

document.querySelector("#publicDiv").addEventListener("mouseleave", function(){
  document.querySelector("#publicBets").style.fontSize = "30px";
  document.querySelector("#publicBets").style.marginTop = "5px";
})


document.querySelector("#betBack").addEventListener("click", function(){
  document.querySelector("#img1").setAttribute("src", "https://i.icanvas.com/list-square/japanese-movie-posters-2.jpg");
  document.querySelector("#img2").setAttribute("src", "https://i.pinimg.com/originals/4b/b4/a9/4bb4a9564ce8afd5deb7c4fd48c663a1.jpg");
  document.querySelector("#img3").setAttribute("src", "https://www.picclickimg.com/d/l400/pict/303263215754_/Classic-Photography-2020-Square-Wall-Calendar-NEW.jpg");
  document.querySelector("#betBack").style.backgroundImage = 'url()' ;
  document.querySelector("#betForward").style.backgroundImage = 'url(publicBet.png)' ;
})

document.querySelector("#betForward").addEventListener("click", function(){
  document.querySelector("#img1").setAttribute("src", "https://i.pinimg.com/originals/4b/b4/a9/4bb4a9564ce8afd5deb7c4fd48c663a1.jpg");
  document.querySelector("#img2").setAttribute("src", "https://www.picclickimg.com/d/l400/pict/303263215754_/Classic-Photography-2020-Square-Wall-Calendar-NEW.jpg");
  document.querySelector("#img3").setAttribute("src", "https://dynaimage.cdn.cnn.com/cnn/q_auto,w_412,c_fill,g_auto,h_412,ar_1:1/http%3A%2F%2Fcdn.cnn.com%2Fcnnnext%2Fdam%2Fassets%2F191004124125-rambo---eric-sojay---600---hi.jpg");
  document.querySelector("#betForward").style.backgroundImage = 'url()' ;
  document.querySelector("#betBack").style.backgroundImage = 'url(publicBet.png)' ;
})


$(document).on('click', '.joinBattle', function(e) {
  var battle_id = $(this).attr('data-battle-id');
  var rooting_for_user = $(this).attr('data-user-id');
  var battle_amt = $(this).attr('data-amt');

  if(confirm("Confirm participation?"))
  {
    $('.loader').removeClass('d-none');
    $.ajax({
      type: "POST",
      dataType: 'json',
      url: '/user/save_additional_bet/json',
      data: {bet_battle_id: battle_id, rooting_for_user: rooting_for_user,
        bet_amount: battle_amt},
      success: function(data) {
        $('.loader').addClass('d-none');
        if(data.status) 
        {
          alert("Successfully participated in battle!");
          window.location.reload();
        }
        else
        {
          alert(data.error);
        }
      },
      error: function(err) {
        $('.loader').addClass('d-none');
        alert("Some Error Occured!");
      }
    });
  }
});

var movieTimer;
$(document).ready(function() {
  movieTimer = setInterval(function() {
    $('.upperBar').each(function(index, u) {
      var release_date = new Date($(u).attr('data-release-date'));
      var now_date = new Date();
      var increase_height = (100/(release_date.getTime() - now_date.getTime()));
      var old_height = Number(u.getBoundingClientRect()['height']);
      var new_height = Math.round((old_height+Number(increase_height)) * 100) / 100;
      u.style.height = new_height+'px';
      //$(u).height(new_height+"px");
      console.log(old_height, $(u).height(), new_height);
      // $(u).height(function(index, currentHeight) {
      //   return currentHeight + increase_height;
      // });
    });
    $('.lowerBar').each(function(index, u) {
      var release_date = new Date($(u).attr('data-release-date'));
      var now_date = new Date();
      var decrease_height = (100/(release_date.getTime() - now_date.getTime()));
      var old_height = $(u).height();
      $(u).height(old_height-decrease_height);

      // $(u).height(function(index, currentHeight) {
      //   return currentHeight - decrease_height;
      // });
    });
  }, 10000);
});
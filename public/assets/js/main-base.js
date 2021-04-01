var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
  return new bootstrap.Popover(popoverTriggerEl)
})

var x = 0;
var xperc = "";
var full = false;

// function btnFunction () {
//     alert("THIS WILL TAKE YOU TO THE SYNOPSIS PAGE BASED ON THE BET_TYPE SELECTED! ");
// }

function addOne () {
    document.querySelector('#barContainer').insertAdjacentHTML(
        'afterbegin',
        `<div class="col-auto example fullBar">
        <div class="row enlarge" id="upperBar" style="height: 0%; background-image: url(https://occ-0-1723-1722.1.nflxso.net/dnm/api/v6/XsrytRUxks8BtTRf9HNlZkW2tvY/AAAABemUFIQksgwOxG1UZfxZ2rcOpAo_cGc3h8CkWvoe7Ia360WnEKLIPP5VjJ5cobOxwF6tSJ7ieFBzQA7VYSPTrtjnbBxCsf0WJ06KrrYo-r8DJHp_XBkrKatPnV3azQAygyoIR_35C9P6TE0uuMXvon9xTdxa4lU.jpg); background-size: 100%; border-radius: 2%; font-size: x-small; ">
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
        <div style="height: 4px;"></div>
        <div class="row enlarge" id="lowerBar" style="height: 100%;background-image: url(https://images-na.ssl-images-amazon.com/images/I/51RwLbQwRyL._SL1200_.jpg); background-size: 100%; border-radius: 2%; font-size: x-small;">
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
      </div>`      
      )
}


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

// $(document).on('click', '#collapseLinks', function(e) {
//   $('.linx').toggle();
//   $('#mainLink').toggle();
// });

// document.querySelector("#collapseLinks").addEventListener("click", function(){
//   if(document.querySelector(".linx").style.opacity != '0') {
//     for (let index = 0; index < document.querySelectorAll(".linx").length; index++) {
//       document.querySelectorAll(".linx")[index].style.opacity = '0';
//     }
//   }
//   else {
//     for (let index = 0; index < document.querySelectorAll(".linx").length; index++) {
//       document.querySelectorAll(".linx")[index].style.opacity = '1';
//     }
//     document.querySelector("#mainLink").style.opacity = '0';
//   }
// })

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



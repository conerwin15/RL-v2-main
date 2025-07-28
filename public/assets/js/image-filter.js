/******************* Image Filters  *******************/

var quizImg = " ";
var currWidth = " ";
$(document).ready(function () {

  $('.quiz-img').on('click', function () {
    var image = $(this).attr('src');
    $('#quizModal').on('show.bs.modal', function () {
      $(".quizimage").attr("src", image);
      $(".quizimage").css("width", '100%');
    });

     angle = 0;
    $('#zoomin').prop('disabled', false);
    $('#zoomin').css('color', '#fff');
    $('#zoomout').prop('disabled', false);
    $('#zoomout').css('color', '#fff');
    $('#showImage').css('margin-left', '0px');
    $('#quizModal').modal('show');
    
  });

  $('#quizModal').on('hidden.bs.modal', function () {
    angle = 0;
    $("#showImage").css({'transform': 'rotate('+angle+'deg)'});
  });
  
});


/* Function for zoom in */
function zoomin(){
    $('#zoomout').prop('disabled', false);
    $('#zoomout').css('color', '#fff');
    quizImg = document.getElementById("showImage");
    currWidth = quizImg.clientWidth;
   
    if(currWidth >= 1200){
      $('#zoomin').prop('disabled', true);
      // $('#zoomin').css('color', 'red !important');
      return false;
    } else{
      $('#zoomin').prop('disabled', false);
      $('#zoomin').css('color', '#0097C4');
      $('#showImage').css('margin-left', '-=15px');
      quizImg.style.width = (currWidth + 30) + "px";
    } 
}

/* Function for zoom out */
function zoomout(){
    $('#zoomin').prop('disabled', false);
    $('#zoomin').css('color', '#fff');
    quizImg = document.getElementById("showImage");
    currWidth = quizImg.clientWidth;
    if(currWidth <= 200){
      $('#zoomout').prop('disabled', true);
      // $('#zoomout').css('color', 'red !important');
      return false;
    } else{
      $('#zoomout').prop('disabled', false);
      $('#zoomout').css('color', '#0097C4');
      $('#showImage').css('margin-left', '+=15px');
      quizImg.style.width = (currWidth - 30) + "px";
    } 
}  

/* Function for image rotation */

function rotateImage(buttonId) {
 
  if(buttonId == 'left_rotate') {
    angle -= 90;
    if(angle == (-360))
    {
      angle = 0;
    }
  } else {
    angle += 90;
    if(angle == (360))
    {
      angle = 0;
    }
  }

  $("#showImage").css({'transform': 'rotate('+angle+'deg)'});
}

/* Function to open image in fullscreen mode */
function openFullscreen() {
  var imageId = document.getElementById("showImage");
  if (imageId.requestFullscreen) {
    imageId.requestFullscreen();
  } else if (imageId.webkitRequestFullscreen) { /* Safari */
  imageId.webkitRequestFullscreen();
  } else if (imageId.msRequestFullscreen) { /* IE11 */
  imageId.msRequestFullscreen();
  }
}

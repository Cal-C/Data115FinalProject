<!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>

<h2>This is a heading</h2>

<p>This is a paragraph.</p>
<p>This is another paragraph.</p>

<button>Click me</button>

<div id="images"></div>
<canvas style="margin:0;padding:0;position:relative;left:50px;top:50px;" id="imgCanvas" width="250" height="250" onclick="draw(e)"></canvas>

<script>
$(document).ready(function(){
  $("button").click(function(){
    $("p").hide();
  });
});

var canvas = document.getElementById("imgCanvas");
var context = canvas.getContext("2d");

function createImageOnCanvas(imageId) {
    canvas.style.display = "block";
    document.getElementById("images").style.overflowY = "hidden";
    var img = new Image(300, 300);
    img.src = document.getElementById(imageId).src;
    context.drawImage(img, (0), (0)); //onload....
}

function draw(e) {

var canvas = document.getElementById("imgCanvas");
var context = canvas.getContext("2d");
var rect = canvas.getBoundingClientRect();
var posx = e.clientX - rect.left;
var posy = e.clientY - rect.top;

context.fillStyle = "#000000";
context.beginPath();
context.arc(posx, posy, 50, 0, 2 * Math.PI);
context.fill();
}

function getMousePos(canvas, evt) {
    var rect = canvas.getBoundingClientRect();
    return {
        x: evt.clientX - rect.left,
        y: evt.clientY - rect.top
    };
}

</script>
</body>
</html>
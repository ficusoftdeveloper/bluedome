var canvas = '',
    context = '',
    image_path = '',
    dragging = false,
    dragStartLocation = '',
    snapshot = '';
    fid = '';
    canvas_id = '';
    modal = '';


function getCanvasCoordinates(event) {
    var x = event.clientX - canvas.getBoundingClientRect().left,
        y = event.clientY - canvas.getBoundingClientRect().top;

    return {x: x, y: y};
}

function takeSnapshot() {
  snapshot = context.getImageData(0,0,canvas.width, canvas.height);
}

function restoreSnapshot() {
  context.putImageData(snapshot,0,0);
}

function drawLine(position) {
    context.beginPath();
    context.moveTo(dragStartLocation.x, dragStartLocation.y);
    context.lineTo(position.x, dragStartLocation.y);
    context.stroke();
}

/*
function placeCoordinates(position) {
//  var elm = document.getElementById("window-position");
//  var elm1 = document.getElementById("image-position");
//  var mf = elm1.getAttribute("mf");
  //elm.innerHTML = "<p class='report_p'>Window Cordinates : Start Location <b>(x: " + dragStartLocation.x + ",y:  " +
  //dragStartLocation.y + ")</b> and End Location <b>(x: " + position.x + ",y: " +
  //dragStartLocation.y + ")<b></p>";

  //elm1.innerHTML = "<p class='report_p'>Image Cordinates : Start Location <b>(x: " + (dragStartLocation.x * mf) + ",y:  " +
  //(dragStartLocation.y * mf) + ")</b> and End Location <b>(x: " + (position.x * mf) + ",y: " +
  //(dragStartLocation.y * mf) + ")<b></p>";
} */

function calculatePixelDistances(position) {
  var elm = document.getElementById("window-distance");
  var image_class = "image-distance-" + fid;
  var elm1 = document.getElementById(image_class);
  var mf = elm1.getAttribute("mf");
  // horizontal distance of line.
  var wxdist = position.x - dragStartLocation.x;

  // Distance between points wrt image.
  var ixdist = (mf * wxdist).toFixed(2);
  elm1.innerHTML = "<p class='report_p'>Image Distance : Distance between points on object (In Pixels) = <b>" + ixdist + "</b>";
  $.ajax({
    type: "POST",
    url: elm1.getAttribute('data-callback'),
    data: {
      pixels: ixdist,
      fid: fid
    }
  });
}

function dragStart(event) {
    dragging = true;
    dragStartLocation = getCanvasCoordinates(event);
    takeSnapshot();
}

function drag(event) {
    var position;
    if (dragging === true) {
        restoreSnapshot();
        position = getCanvasCoordinates(event);
        drawLine(position);
    }
}

function dragStop(event) {
    dragging = false;
    //restoreSnapshot();
    var position = getCanvasCoordinates(event);
    drawLine(position);
  //  placeCoordinates(position);
    calculatePixelDistances(position);
}

function make_base(image_path) {
  base_image = new Image();
  base_image.src = image_path;
  base_image.onload = function(){
    context.drawImage(base_image, 0, 0, 760, 500);
  }
}

function loadBinary(event) {
  fid = event.getAttribute("data-fid");

  // Get the modal.
  var binModal = "binModal"+fid;
  modal = document.getElementById(binModal);

  // Get the image and insert it inside the modal.
  var binImg = "binImg"+fid;
  var img = document.getElementById(binImg);
  var bin = "bin"+fid;
  var modalImg = document.getElementById(bin);
  var captionText = document.getElementById("caption");

  // Modal.
  modal.style.display = "block";
  modalImg.src = img.getAttribute("modal-src");
  captionText.innerHTML = img.alt;
}

function closeModal(event) {
  modal.style.display = "none";
}

function loadCanvas(event) {
  fid = event.getAttribute("data-fid");
  canvas_id = "canvas"+fid;
  canvas = document.getElementById(canvas_id);
  context = canvas.getContext('2d');
  context.strokeStyle = 'purple';
  context.lineWidth = 6;
  context.lineCap = 'round';

  canvas.addEventListener('mousedown', dragStart, false);
  canvas.addEventListener('mousemove', drag, false);
  canvas.addEventListener('mouseup', dragStop, false);

  // get image-path.
  image_path = canvas.getAttribute("data-image-path");
  make_base(image_path);
}

function saveImage(event) {
  var fid = event.getAttribute('data-fid');
  var dataURL = canvas.toDataURL();
  var distance_poo = 'distance_poo[' + fid + ']';
  var unit_poo = 'unit_poo[' + fid + ']';
  var distance = document.getElementById(distance_poo).value;
  if (distance <= 0) {
    alert('Invalid distance between points on object.');
    return;
  }
  var unit = document.getElementById(unit_poo).value;
  $.ajax({
    type: "POST",
    url: event.getAttribute('data-callback'),
    data: {
      imgBase64: dataURL,
      fid: fid,
      distance: distance,
      unit: unit
    }
  }).done(function(o) {
    window.location.reload();
  });
}

function resetCalibration(event) {
  var fid = event.getAttribute('data-fid');
  $.ajax({
    type: "POST",
    url: event.getAttribute('data-callback'),
    data: {
      fid: fid
    }
  }).done(function(o) {
    window.location.reload();
  });
}

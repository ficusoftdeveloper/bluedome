<!--==========================
Footer
============================-->
<script>
// Get the modal
var modal = document.getElementById('youtubeModal');

// Get the button that opens the modal
var btn = document.getElementById("youtube-modal");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal
btn.onclick = function() {
  modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
</script>
</body>
<footer class="footer-panel">
  <div class="container">
<div class="row">
  <div class="col-sm-12 text-center">
    <p class="highlight2">The Approach to Visual Inspection and measurements <br>
Has Not Fundamentally Changed Since the 19<sup>th</sup> Century...until now.</p>  <p> See how Blue DOME AI technology is disrupting the multi-trillion dollar construction and infrastructure industry.</p>
     <ul class="footer-nav">
      <!--<li>
        <a href="overview.php">Overview  </a>
      </li>-->
      <li>
        <a href="<?php echo site_url('pages/solution') ?>">Solution </a>
      </li>
      <!--<li>
        <a href="technology.php"> Technology </a>  </li>
      <li>
        <a href="advantages.php">Advantages </a> </li>-->
    <li>
        <a href="<?php echo site_url('pages/inspection') ?>">Inspection Process  </a>
      </li>
    <!--<li>
        <a href="testimonials.php">Testimonials  </a>
      </li>-->
    <li>
        <a href="<?php echo site_url('pages/contact') ?>">Contact</a>
      </li>
    </ul>

    <!--<div class="ft-social">
            <a href="#"><i class="animatable bounceIn fab fa-facebook-f"></i></a>
            <a href="#"><i class="animatable bounceIn fab fa-twitter"></i></a>
            <a href="#"><i class="animatable bounceIn fab fa-linkedin"></i></a>
            <a href="#"><i class="animatable bounceIn fab fa-youtube"></i></a>

            <a href="#"><i class="animatable bounceIn fas fa-rss"></i></a>
    </div>-->
  <div class="copyright">© 2018 - Blue DOME technologies</div>
  </div>
</div>
</div>
</footer>

</html>

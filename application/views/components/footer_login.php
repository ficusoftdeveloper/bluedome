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

        <div id="feedback">
            <a href="<?php echo site_url('user/login') ?>">Login</a>
        </div>
        <div id="feedback1">
            <a href="<?php echo site_url('user/register') ?>">Register</a>
        </div> 

        <script type="text/javascript">
            // toggle password visibility
            $('#password + .fa').on('click', function() {
                $(this).toggleClass('fa-eye-slash').toggleClass('fa-eye'); // toggle our classes for the eye icon
                $('#password').togglePassword(); // activate the hideShowPassword plugin
            });
        </script>
</body>
<style type="text/css">
@media all and (max-width: 500px) {
    .contact-form{padding: 44px 10px !important;}
    #feedback1{top: 74% !important;}
}

@media screen and (min-width: 500px) and (max-width: 900px) {
    #feedback1{top: 62% !important;}
}
    label{font-weight: normal;
        font-size: 15px;}
    input, textarea{margin-bottom: 5px;}
    .contact-form {
        background: #fff;
        padding: 44px 60px;
        box-shadow: 0px 0px 2px 1px #cab6b6;
    }
    
    .contact-form p {
        margin-bottom: 0px;
    }
    
    main {
        background-color: #fafafa;
    }
    
    #password + .fa {
        cursor: pointer;
        pointer-events: all;
    }
    
    #password + .fa:before {
        color: #8fbfb4;
        font-size: 34px;
        top: 11px;
        position: relative;
        right: 13px;
    }
    /* sticky button */
    
    #feedback1 {
        height: 0px;
        width: 85px;
        position: fixed;
        right: 0;
        top: 71%;
        z-index: 1000;
        transform: rotate(-90deg);
        -webkit-transform: rotate(-90deg);
        -moz-transform: rotate(-90deg);
        -o-transform: rotate(-90deg);
        filter: progid: DXImageTransform.Microsoft.BasicImage(rotation=3);
    }
    
    #feedback1 a {
        display: block;
        background: #428bca;
        height: 52px;
        width: 150px;
        color: #fff;
        font-family: Arial, sans-serif;
        font-size: 17px;
        font-weight: bold;
        text-decoration: none;
        text-align: center;
        align-items: center;
        padding-top: 10px;
    }
    
    #feedback1 a:hover {
        background: #428bca;
    }
    
    #feedback {
        height: 0px;
        width: 85px;
        position: fixed;
        right: 0;
        top: 46%;
        z-index: 1000;
        transform: rotate(-90deg);
        -webkit-transform: rotate(-90deg);
        -moz-transform: rotate(-90deg);
        -o-transform: rotate(-90deg);
        filter: progid: DXImageTransform.Microsoft.BasicImage(rotation=3);
    }
    
    #feedback a {
        display: block;
        background: #428bca;
        height: 50px;
        padding-top: 10px;
        width: 130px;
        text-align: center;
        color: #fff;
        font-family: Arial, sans-serif;
        font-size: 17px;
        font-weight: bold;
        text-decoration: none;
    }
    
    #feedback a:hover {
        background: #428bca;
    }
</style>
</html>
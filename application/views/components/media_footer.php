<!--==========================
Footer
============================-->
<footer id="footer">
<div class="container">
<div class="row">
  <div class="col-sm-12 text-center">
    <p class="highlight2">The Approach to Visual Inspection and measurements <br>
Has Not Fundamentally Changed Since the 19<sup>th</sup> Century...until now.</p>  <p> See how Blue DOME AI technology is disrupting the multi-trillion dollar construction and infrastructure industry.</p>
     <ul class="footer-nav">
      <!--<li>
        <a href="overview.html">Overview  </a>
      </li>-->
      <li>
        <a href="solutions.html">Solution </a>
      </li>
      <!--<li>
        <a href="technology.html"> Technology </a>  </li>
      <li>
        <a href="advantages.html">Advantages </a> </li>-->
    <li>
        <a href="inspection.html">Inspection Process  </a>
      </li>
    <!--<li>
        <a href="testimonials.html">Testimonials  </a>
      </li>-->
    <li>
        <a href="contact.html">Contact</a>
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

</footer><!-- #footer -->
</body>
<style type="text/css">
.report_p{font-size: 13px;
    margin-bottom: 5px;
    margin-top: 6px;
    color: #000;}
    .report_p span{padding-left: 25px;}
 .nav-tabs>li>a {
            border: 1px solid #f1f1f1;
            color: #000;
            font-weight: bold;
        }
        
        .nav-tabs .active a {
            border-top: 3px solid #dc3545 !important;
            border-radius: 0px !important;
        }
        
        .fa-arrow-right {
            position: relative;
            bottom: 5px;
            left: 30px;
            font-size: 30px;
        }

.head_title{padding-top: 20px;padding-bottom: 20px;font-size: 13px;     line-height: 0;}
    .process_table thead tr th {
        padding-top: 3%;
        padding-bottom: 2%;
    }
    .table>tbody>tr>td,.table>thead>tr>th{ border-bottom: 1px solid #ddd !important;}
    .process_table tbody tr td {
        vertical-align: middle;
    }
   .tab-pane{border-left: 1px solid #ddd;
    border-right: 1px solid #ddd;
    border-bottom: 1px solid #ddd;}

.table-striped>tbody>tr:nth-child(even) {
    background-color: #f9f9f9;
}
.table-striped>tbody>tr:nth-child(odd) {
    background-color: #fff;
}
    /*checkbox*/
    input[type=checkbox] {width: auto;}
.styled-checkbox {
  position: absolute;
  opacity: 0;
}
.styled-checkbox + label {
  position: relative;
  cursor: pointer;
  padding: 0;
}
.styled-checkbox + label:before {
  content: '';
  margin-right: 10px;
  display: inline-block;
  vertical-align: text-top;
  width: 20px;
  height: 20px;
  background: rgb(255, 255, 255);
    border: 1px solid #6c757d4f;
}
.styled-checkbox:hover + label:before {
  background: #002060;
}
.styled-checkbox:focus + label:before {
  box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.12);
}
.styled-checkbox:checked + label:before {
  background: #002060;
}
.styled-checkbox:disabled + label {
  color: #b8b8b8;
  cursor: auto;
}
.styled-checkbox:disabled + label:before {
  box-shadow: none;
  background: #ddd;
}
.styled-checkbox:checked + label:after {
  content: '';
  position: absolute;
  left: 5px;
  top: 12px;
  background: white;
  width: 2px;
  height: 2px;
  box-shadow: 2px 0 0 white, 4px 0 0 white, 4px -2px 0 white, 4px -4px 0 white, 4px -6px 0 white, 4px -8px 0 white;
  -webkit-transform: rotate(45deg);
          transform: rotate(45deg);
}


/*radio button*/
[type="radio"]:checked,
[type="radio"]:not(:checked) {
    position: absolute;
    left: -9999px;
}
[type="radio"]:checked + label,
[type="radio"]:not(:checked) + label
{
    position: relative;
    padding-left: 28px;
    cursor: pointer;
    line-height: 20px;
    display: inline-block;
    color: #666;
}
[type="radio"]:checked + label:before,
[type="radio"]:not(:checked) + label:before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 18px;
    height: 18px;
    border: 1px solid #ddd;
    border-radius: 100%;
    background: #fff;
}
[type="radio"]:checked + label:after,
[type="radio"]:not(:checked) + label:after {
    content: '';
    width: 12px;
    height: 12px;
    background: #002060;
    position: absolute;
    top: 3px;
    left: 3px;
    border-radius: 100%;
    -webkit-transition: all 0.2s ease;
    transition: all 0.2s ease;
}
[type="radio"]:not(:checked) + label:after {
    opacity: 0;
    -webkit-transform: scale(0);
    transform: scale(0);
}
[type="radio"]:checked + label:after {
    opacity: 1;
    -webkit-transform: scale(1);
    transform: scale(1);
}
</style>
</html>
<script type="text/javascript">
function checkImage(checkboxElem) {
  var imageId = "#"+checkboxElem.id.replace("styled-checkbox", "image");
  if (checkboxElem.checked) {
     $(imageId).attr('disabled', false);
  } else {
     $(imageId).attr('disabled', true);
  }
}

function dimImage(checkboxElem) {
  var dimBlockId = "#"+checkboxElem.id.replace("styled-dim-checkbox", "dim-block");
  if(checkboxElem.checked) {
    $(dimBlockId).show();
  } else {
    $(dimBlockId).hide();
  }
}
</script>

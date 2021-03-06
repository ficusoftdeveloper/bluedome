<main>
  <section class="inner-banner text-center">
    <img src="<?php echo base_url('assets/img/process.jpg') ?>">
      <div class="banner-content">
          <div class="container">
              <h1>Process</h1>
          </div>
      </div>
  </section>
  <?php if (null != $this->session->flashdata('error_msg')): ?>
    <div class="error-msg" style="color: red; padding: 20px">
        <?php echo $this->session->flashdata('error_msg') ?>
    </div>
    <?php endif; ?>
    <?php if (null != $this->session->flashdata('success_msg')): ?>
    <div class="success-msg" style="color: green; padding: 20px">
        <?php echo $this->session->flashdata('success_msg') ?>
    </div>
  <?php endif; ?>

  <section class="page-content">
    <div class="container">
      <div class="techno-sec">
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#capture">Capture Images</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="capture" class="tab-pane fade in active">
                                    <div class="container">
                                        <?php $this->load->view('components/instruction'); ?>
                                        <?php echo form_open_multipart('media/do_image_upload');?>
                                        <div class="text-center">
                                        <?php echo "<input id='file-input' type='file' required='required' name='userfile' size='20' accept='image/*' />"; ?>
                                        <?php echo "<input type='submit' name='postSubmit' class='submit' style='background: #454545;text-transform: uppercase;padding: 10px 30px 6px 30px;' value='CAPTURE IMAGE' /> ";?>
                                        <?php echo "<a href='" . site_url('process') . "' > Back </a>";?>
                                        </div>
                                        <?php echo "</form>"?>


                                        <?php echo form_open_multipart('');?>
                                        <?php if ($images): ?>
                                        <?php foreach ($images as $image) { ?>
                                        <div class="row" style="margin-top: 6px;">
                                            <div class="col-md-3" style="padding-right: 0;">
                                                <div style="height: 160px;width: 100%;">
                                                    <img src="<?php echo $image['url'] ?>" class="img-responsive" style="height: 160px;width: 100%;">
                                                </div> 
                                            </div>
                                            <div class="col-md-4" style="background: #f6f6f6;padding-left: 0;">
                                                <div style="padding-top: 10%;padding-left: 4%;">
                                                 <div class="input-group">
                                                    <input id="image-<?php echo $image['fid'] ?>" disabled required type="text" class="form-control" name="image_filename[<?php echo $image['fid'] ?>]" placeholder="Enter File Name">
                                                    <span class="input-group-addon"><i class="fas fa-paperclip"></i></span>
                                                  </div>                                               
                                                  <div class="row dist_inform">
                                                      <div class="col-md-12 col-lg-6 col-xs-12">
                                                          <p class="report_p">Distance of camera from Object</p>
                                                      </div>
                                                      <div class="col-md-6 col-lg-3 col-xs-6">
                                                          <span><input type="text" class="form-control dist_val" name="distance_cfo[<?php echo $image['fid'] ?>]" > </span>
                                                      </div>
                                                      <div class="col-md-6 col-lg-3 col-xs-6">
                                                           <span><select class="form-control dist_prop" name="unit_cfo[<?php echo $image['fid'] ?>]">
                                                            <option value="inch">inch</option>
                                                            <option value="ft">ft</option></select></span>
                                                      </div>
                                                  </div>
                                                   <div class="row dist_inform" id="dim-block-<?php echo $image['fid'] ?>" style="display: none;">
                                                      <div class="col-md-12 col-lg-6 col-xs-12">
                                                          <p class="report_p">Distance between point on Object</p>
                                                      </div>
                                                      <div class="col-md-6 col-lg-3 col-xs-6">
                                                          <span><input type="text" class="form-control dist_val" name="distance_poo[<?php echo $image['fid'] ?>]" > </span>
                                                      </div>
                                                      <div class="col-md-6 col-lg-3 col-xs-6">
                                                           <span><select class="form-control dist_prop" name="unit_poo[<?php echo $image['fid'] ?>]">
                                                            <option value="inch">inch</option>
                                                            <option value="ft">ft</option></select></span>
                                                      </div>
                                                  </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 capture_video_image" style="background: #f6f6f6;padding-left: 0;padding-top: 1%;">
                                                <div class="row">
                                                    <div class="col-md-4 col-xs-4 text-center">
                                                        <p>Visual</p>
                                                        <input class="styled-checkbox" id="styled-visual-checkbox-<?php echo $image['fid'] ?>" type="checkbox" name="image_visual[<?php echo $image['fid'] ?>]" value="<?php echo $image['fid'] ?>" > <label for="styled-visual-checkbox-<?php echo $image['fid']?>"></label>
                                                    </div>
                                                    <div class="col-md-4 col-xs-4 text-center">
                                                        <p>Dim</p>
                                                        <input class="styled-checkbox" onchange="dimImage(this)" id="styled-dim-checkbox-<?php echo $image['fid'] ?>" type="checkbox" name="image_dim[<?php echo $image['fid'] ?>]" value="<?php echo $image['fid'] ?>" > <label for="styled-dim-checkbox-<?php echo $image['fid'] ?>"></label>
                                                    </div>
                                                    <div class="col-md-4 col-xs-4 text-center">
                                                       <p>Select</p>
                                                        <input class="styled-checkbox" onchange="checkImage(this)" id="styled-checkbox-<?php echo $image['fid'] ?>" type="checkbox" name="image_check[<?php echo $image['fid'] ?>]" value="<?php echo $image['fid'] ?>"> <label for="styled-checkbox-<?php echo $image['fid'] ?>"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>

                                        <div class="col-md-12" style="margin-top: 3%;">
                                            <div>
                                                <input type="submit" name="uploadFiles" value="Upload" class="submit" style="background: #dd5847;text-transform: uppercase;padding: 10px 30px 6px 30px;">
                                            </div>
                                        </div>
                                        <?php echo '</form>' ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php $this->load->view('components/process'); ?>
                                <?php $this->load->view('components/report'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <p>&nbsp;</p>
            </div>
        </section>
    </main>
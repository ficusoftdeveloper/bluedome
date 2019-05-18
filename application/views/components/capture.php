<div id="capture" class="tab-pane fade in active">
  <div class="container">
    <?php $this->load->view('components/instruction') ?>
    <?php $this->load->view('components/status_msg') ?>

    <?php echo form_open_multipart('media/do_file_upload'); ?>
    <div class="row">
      <div class="col-md-4" style="padding-left: 0;">
        <div style="padding-top: 10%;padding-left: 4%;">
          <div class="input-group">
            <span><select required class="form-control dist_prop" name="operation">
              <option value="">--Select Operation--</option>
              <option value="detect_and_measure_cracks">Detect and Measure Cracks</option>
              <option value="detect_and_locate_objects">Detect and Locate Objects</option></select></span>
          </div>
        </div>
      </div>
      <div class="col-md-4" style="padding-left: 0;">
        <div style="padding-top: 10%;padding-left: 4%;">
          <div class="input-group">
            <?php echo "<input id='' type='file' required class='form-control' name='userfile' style='padding:4px;'>" ?>
            <span class="input-group-addon"><i class="fas fa-paperclip"></i></span>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
    <div class="col-md-12" style="margin-top: 3%;">
      <div>
        <?php echo "<input type='submit' name='imageSubmit' disabled value='Capture Image' class='submit' style='background: #454545;text-transform: uppercase;padding: 10px 30px 6px 30px;'>"; ?>
        <?php echo "<input type='submit' name='videoSubmit' disabled value='Capture Video' class='submit' style='background: #454545;text-transform: uppercase;padding: 10px 30px 6px 30px;'>"; ?>
        <?php echo "<input type='submit' name='fileSubmit' value='Upload' class='submit' style='background: #dd5847;text-transform: uppercase;padding: 10px 30px 6px 30px;'>"; ?>
      </div>
    </div>
    </div>
    <?php echo '</form>'; ?>

    <!-- List images/videos -->
    <?php if (!empty($files)): ?>
    <?php foreach ($files as $file) :?>
    <?php echo form_open_multipart('media/action'); ?>
    <div class="row" style="margin-top: 6px;">

      <div class="col-md-3" style="padding-right: 0;">
        <div class="image-container" style="height: 160px;width: 100%;">
          <?php if ($file['is_calibrated']) {  ?>
            <?php $filepath = base_url('uploads/raw/') . $file['calibrated_filename']; ?>
            <?php $height = '160px';?>
          <?php } else { ?>
            <?php $filepath = base_url('uploads/raw/') . $file['filename']; ?>
            <?php $height = '160px';?>
          <?php } ?>
          <?php if ($file['is_image']): ?>
            <img src="<?php echo $filepath; ?>" class="img-responsive image" style="height: <?php echo $height ?>;width: 100%;">
          <?php else: ?>
            <video height="<?php echo $height ?>" width="100%" controls>
              <source src="<?php echo $filepath; ?>" type="video/mp4">
              <source src="<?php echo $filepath; ?>" type="video/ogg">
              Your browser does not support HTML5 video.
            </video>
          <?php endif; ?>
          <div class="middle">
            <!-- Button to Open the Modal -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal<?php echo $file['fid'] ?>" data-fid="<?php echo $file['fid'] ?>" onclick="loadCanvas(this)">
              <?php if ($file['is_calibrated']): ?>
                VIEW
              <?php else: ?>
                CALIBRATE
              <?php endif; ?>
            </button>
          </div>
        </div>
      </div>
      <div class="col-md-4" style="background: #f6f6f6;padding-left: 0;">
        <div style="padding-top: 4%;padding-left: 4%;">
          <div class="input-group">
            <input disabled required id="image-<?php echo $file['fid'] ?>" type="text" class="form-control" name="filename[<?php echo $file['fid'] ?>]" placeholder="Enter File Name" value="<?php echo preg_replace('/\\.[^.\\s]{3,4}$/', '', $file['filename']); ?>">
            <span class="input-group-addon"><i class="fas fa-paperclip"></i></span>
          </div>
          <div class="row dist_inform">
            <div class="col-md-12 col-lg-6 col-xs-12">
              <p class="report_p">Distance of camera from Object</p>
            </div>
            <div class="col-md-6 col-lg-3 col-xs-6">
              <span><input type="text" class="form-control dist_val" name="distance_cfo[<?php echo $file['fid'] ?>]" value="<?php if(isset($file['distance_cfo'])) { echo $file['distance_cfo']; } ?>"> </span>
            </div>
            <div class="col-md-6 col-lg-3 col-xs-6">
              <span><select class="form-control dist_prop" name="unit_cfo[<?php echo $file['fid'] ?>]">
                <option value="inch" <?php if(isset($file['unit_cfo']) && ($file['unit_cfo'] == 'inch')) { echo 'selected'; } ?>>inch</option>
                <option value="ft" <?php if(isset($file['unit_cfo']) && ($file['unit_cfo'] == 'ft')) { echo 'selected'; } ?>>ft</option>
                <option value="cm" <?php if(isset($file['unit_cfo']) && ($file['unit_cfo'] == 'cm')) { echo 'selected'; } ?>>cm</option>
                <option value="mm" <?php if(isset($file['unit_cfo']) && ($file['unit_cfo'] == 'mm')) { echo 'selected'; } ?>>mm</option></select></span>
            </div>
          </div>
          <?php if ($file['is_calibrated']): ?>
          <div class="row dist_inform" id="dim-block-<?php echo $file['fid'] ?>" <?php if(isset($file['is_image_dim']) && ($file['is_image_dim'] == 0)) { ?> style="display: none;" <?php } else { ?> style="margin-top:-15px;" <?php } ?>>
            <div class="col-md-12 col-lg-6 col-xs-12">
              <p class="report_p">Distance between point on Object</p>
            </div>
            <div class="col-md-6 col-lg-3 col-xs-6">
              <span><input disabled type="text" class="form-control dist_val" name="distance_poo[<?php echo $file['fid'] ?>]" value="<?php if(isset($file['distance_poo'])) { echo $file['distance_poo']; } ?>"> </span>
            </div>
            <div class="col-md-6 col-lg-3 col-xs-6">
              <span><select disabled class="form-control dist_prop" name="unit_poo[<?php echo $file['fid'] ?>]">
                <option value="inch" <?php if(isset($file['unit_poo']) && ($file['unit_poo'] == 'inch')) { echo 'selected'; } ?>>inch</option>
                <option value="ft" <?php if(isset($file['unit_poo']) && ($file['unit_poo'] == 'ft')) { echo 'selected'; } ?>>ft</option>
                <option value="cm" <?php if(isset($file['unit_poo']) && ($file['unit_poo'] == 'cm')) { echo 'selected'; } ?>>cm</option>
                <option value="mm" <?php if(isset($file['unit_poo']) && ($file['unit_poo'] == 'mm')) { echo 'selected'; } ?>>mm</option></select></span>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <div class="col-md-5 capture_video_image" style="background: #f6f6f6;padding-left: 0;padding-top: 1%;">
        <div class="row" style="padding-left: 10px;padding-top: 10px;">
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-3">
                        <input type="radio" id="cat<?php echo $file['fid'] ?>" name="radio-group-<?php echo $file['fid'] ?>" checked><label for="test1">Crack</label>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-4 text-center">
              <input class="styled-checkbox" onchange="checkImage(this)" id="styled-checkbox-<?php echo $file['fid'] ?>" type="checkbox" name="file_check[<?php echo $file['fid'] ?>]" value="<?php echo $file['fid'] ?>"> <label for="styled-checkbox-<?php echo $file['fid'] ?>">Select</label>
            </div>
        </div>
        <?php if ($file['is_calibrated']): ?>
        <div class="row dist_inform" id="dim-block-<?php echo $file['fid'] ?>" <?php if(isset($file['is_image_dim']) && ($file['is_image_dim'] == 0)) { ?> style="display: none;" <?php } ?>>
            <div class="col-md-16 col-lg-12 col-xs-12">
              <p class="report_p">Distance between point on Object (In pixels) = <b><?php echo $file['pixels']?></b></p>
            </div>
          </div>
           <?php endif; ?>
          <div class="row dist_inform" id="dim-block">
            <div class="col-md-16 col-lg-12 col-xs-12">
              <?php if ($file['operation'] == 'detect_and_locate_objects'): ?>
                <?php $operation = 'Detect and locate objects'; ?>
              <?php else: ?>
                <?php $operation = 'Detect and measure cracks'; ?>
              <?php endif; ?>
              <p class="report_p">Selected Operation = <b><?php echo $operation ?></b></p>
            </div>
          </div>
      </div>
    </div>
  <?php endforeach; ?>

    <div class="text-center" style="margin-top: 3%;">
      <input type="submit" name="saveFiles" value="Save" class="submit" style="background: #454545;padding-bottom: 5px;text-transform: uppercase;">
      <input type="submit" name="deleteFiles" value="Delete" class="submit" style="background: #454545;padding-bottom: 5px;text-transform: uppercase;">
      <input type="submit" name="processFiles" value="Process" class="submit" style="background: #dd5847;padding-bottom: 5px;text-transform: uppercase;">
    </div>
    <?php echo '</form>'; ?>
  <?php endif; ?>
  </div>

  <!-- The Modal -->
  <?php if (!empty($files)): ?>
  <?php foreach ($files as $file) :?>
  <?php //echo form_open_multipart('calibration/save'); ?>
  <div class="modal fade" id="myModal<?php echo $file['fid'] ?>">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">MEDIA CALIBRATION TOOL</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <!-- Modal body -->
        <div class="modal-body">
          <?php if ($file['is_calibrated']) {  ?>
            <?php $filepath = base_url('uploads/raw/') . $file['calibrated_filename']; ?>
            <img src="<?php echo $filepath; ?>" style="cursor: crosshair;" width="760" height="500" />
            <div id="image-distance-<?php echo $file['fid'] ?>"><p class='report_p'>Image Distance : Distance between points on object (In Pixels) = <b><?php echo $file['pixels'] ?></b></div>

              <div class="row dist_inform" id="dim-block">
                <div class="col-md-12 col-lg-6 col-xs-12">
                  <p class="report_p">Distance between point on Object</p>
                </div>
                <div class="col-md-6 col-lg-3 col-xs-6">
                  <span><input disabled type="text" class="form-control dist_val" id="distance_poo[<?php echo $file['fid'] ?>]" name="distance_poo[<?php echo $file['fid'] ?>]" value="<?php if(isset($file['distance_poo'])) { echo $file['distance_poo']; } ?>"> </span>
                </div>
                <div class="col-md-6 col-lg-3 col-xs-6">
                  <span><select disabled class="form-control dist_prop" id="unit_poo[<?php echo $file['fid'] ?>]" name="unit_poo[<?php echo $file['fid'] ?>]">
                    <option value="inch" <?php if(isset($file['unit_poo']) && ($file['unit_poo'] == 'inch')) { echo 'selected'; } ?>>inch</option>
                    <option value="ft" <?php if(isset($file['unit_poo']) && ($file['unit_poo'] == 'ft')) { echo 'selected'; } ?>>ft</option>
                    <option value="cm" <?php if(isset($file['unit_poo']) && ($file['unit_poo'] == 'cm')) { echo 'selected'; } ?>>cm</option>
                    <option value="mm" <?php if(isset($file['unit_poo']) && ($file['unit_poo'] == 'mm')) { echo 'selected'; } ?>>mm</option></select></span>
                </div>
              </div>

              <!-- Modal footer -->
              <div class="modal-footer">
                <button type="button" onclick="resetCalibration(this)" data-callback="<?php echo site_url('calibration/reset') ?>" data-fid="<?php echo $file['fid'] ?>" class="btn btn-danger" data-dismiss="modal">Reset to Original</button>
              </div>
          <?php } else { ?>
            <?php $filepath = base_url('uploads/raw/') . $file['filename']; ?>
            <?php if ($file['is_image']): ?>
              <canvas style="cursor: crosshair;" id="canvas<?php echo $file['fid'] ?>" data-image-path="<?php echo $filepath ?>" width="760" height="500"></canvas>
              <div id="image-distance-<?php echo $file['fid'] ?>" data-fid="<?php echo $file['fid'] ?>" mf="<?php echo round($file['file_width']/760, 3); ?>" data-callback="<?php echo site_url('calibration/set-pixels') ?>"></div>
            <?php else: ?>
              <?php $filepath = base_url('assets/img/default.png'); ?>
              <canvas style="cursor: crosshair;" id="canvas<?php echo $file['fid'] ?>" data-image-path="<?php echo $filepath ?>" width="760" height="500"></canvas>
              <div id="image-distance-<?php echo $file['fid'] ?>" data-fid="<?php echo $file['fid'] ?>" mf="<?php echo round(1, 3); ?>" data-callback="<?php echo site_url('calibration/set-pixels') ?>"></div>
            <?php endif; ?>

            <div class="row dist_inform" id="dim-block">
              <div class="col-md-12 col-lg-6 col-xs-12">
                <p class="report_p">Distance between point on Object</p>
              </div>
              <div class="col-md-6 col-lg-3 col-xs-6">
                <span><input type="text" class="form-control dist_val" id="distance_poo[<?php echo $file['fid'] ?>]" name="distance_poo[<?php echo $file['fid'] ?>]" value="<?php if(isset($file['distance_poo'])) { echo $file['distance_poo']; } ?>"> </span>
              </div>
              <div class="col-md-6 col-lg-3 col-xs-6">
                <span><select class="form-control dist_prop" id="unit_poo[<?php echo $file['fid'] ?>]" name="unit_poo[<?php echo $file['fid'] ?>]">
                  <option value="inch" <?php if(isset($file['unit_poo']) && ($file['unit_poo'] == 'inch')) { echo 'selected'; } ?>>inch</option>
                  <option value="ft" <?php if(isset($file['unit_poo']) && ($file['unit_poo'] == 'ft')) { echo 'selected'; } ?>>ft</option>
                  <option value="cm" <?php if(isset($file['unit_poo']) && ($file['unit_poo'] == 'cm')) { echo 'selected'; } ?>>cm</option>
                  <option value="mm" <?php if(isset($file['unit_poo']) && ($file['unit_poo'] == 'mm')) { echo 'selected'; } ?>>mm</option></select></span>
              </div>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
              <button type="button" onclick="saveImage(this)" data-callback="<?php echo site_url('calibration/save') ?>" data-fid="<?php echo $file['fid'] ?>" class="btn btn-danger" data-dismiss="modal">Save</button>
              <button type="button" onclick="resetCalibration(this)" data-callback="<?php echo site_url('calibration/reset') ?>" data-fid="<?php echo $file['fid'] ?>" class="btn btn-danger" data-dismiss="modal">Reset to Original</button>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
  <?php //echo '</form>'; ?>
<?php endforeach; ?>
<?php endif; ?>
</div>

<?php if (!empty($report_files)): ?>
<div id="report" class="tab-pane fade" style="padding-top: 2%;">
    <?php echo form_open_multipart('media/action'); ?>
    <div class="container">
        <?php foreach($report_files as $report_file): ?>
        <div class="row" style="margin-top: 8px;">
            <div class="col-md-3" style="padding-right: 0px;">
                <div style="height: 160px;width: 100%;">
                  <?php if ($report_file['is_image']): ?>
                    <a href="<?php echo base_url('uploads/raw/'.$report_file['filename']); ?>">
                    <img src="<?php echo base_url('uploads/raw/'.$report_file['filename']); ?>" class="img-responsive" style="height: 160px;width: 100%;">
                    </a>
                  <?php else: ?>
                    <video height="160px" width="100%" controls>
                      <source src="<?php echo base_url('uploads/raw/'.$report_file['rawname']); ?>" type="video/mp4">
                      <source src="<?php echo base_url('uploads/raw/'.$report_file['rawname']); ?>" type="video/ogg">
                      Your browser does not support HTML5 video.
                    </video>
                  <?php endif; ?>
                </div>
            </div>
            <?php if ($report_file['operation'] != 'detect_and_locate_objects'): ?>
            <div class="col-md-3" style="padding-left: 8px;padding-right: 1px;">
                <div class="image-container" style="height: 160px;width: 100%;">
                    <a href="<?php echo base_url('uploads/processed/detect/'.$report_file['processed_filename']); ?>">
                    <img id="binImg<?php echo $report_file['fid'] ?>" src="<?php echo base_url('uploads/processed/detect/'.$report_file['processed_filename']); ?>" modal-src="<?php echo base_url('uploads/processed/measure/'.$report_file['measout_filename']); ?>" class="img-responsive" alt="<?php echo $report_file['filename'] ?>" style="height: 160px;width: 100%;">
                    </a>
                    <div class="middle">
                      <!-- Button to Open the Modal -->
                      <button type="button" class="btn btn-primary" data-target="#binModal<?php echo $report_file['fid'] ?>" data-fid="<?php echo $report_file['fid'] ?>" onclick="loadBinary(this)">
                        SHOW BINARY
                      </button>
                      <!-- End of Button display -->
                    </div>
                </div>
            </div>
          <?php else: ?>
            <div class="col-md-3" style="padding-left: 8px;padding-right: 1px;">
                <div class="image-container" style="height: 160px;width: 100%;">
                    <a href="<?php echo base_url('uploads/raw/'.$report_file['rawname']); ?>">
                      <video height="160px" width="100%" controls>
                        <source src="<?php echo base_url('uploads/raw/'.$report_file['rawname']); ?>" type="video/avi">
                        <source src="<?php echo base_url('uploads/raw/'.$report_file['rawname']); ?>" type="video/ogg">
                        Your browser does not support HTML5 video.
                      </video>
                    </a>
                    <div class="middle">
                      <!-- Button to Open the Modal -->
                      <button type="button" class="btn btn-primary" data-target="#binModal<?php echo $report_file['fid'] ?>" data-fid="<?php echo $report_file['fid'] ?>" onclick="loadBinary(this)">
                        SHOW BINARY
                      </button>
                      <!-- End of Button display -->
                    </div>
                </div>
            </div>
          <?php endif; ?>
            <!-- Modal to display binary image. -->
            <div id="binModal<?php echo $report_file['fid'] ?>" class="report-modal">
              <span class="report-close" onclick="closeModal(this)">&times;</span>
              <img class="report-modal-content" id="bin<?php echo $report_file['fid'] ?>" />
              <div id="caption"></div>
            </div>
            <!-- End of Modal -->
            <div class="col-md-6" style="background: #f6f6f6;padding-left: 0;">
                <div class="row" style="padding-left: 10px;padding-top: 10px;">
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="radio" id="cat<?php echo $report_file['fid'] ?>" name="radio-group<?php echo $report_file['fid'] ?>" checked><label for="test<?php echo $report_file['fid'] ?>"><?php if ($report_file['operation'] == 'detect_and_locate_objects'): ?>Show Objects<?php else: ?>Crack <?php endif; ?></label>
                            </div>
                            <div class="col-md-3">
                              <?php if ($report_file['operation'] == 'detect_and_locate_objects'): ?>
                                <p><a target="_blank" href="<?php echo site_url('media/map/' . $report_file['fid']); ?>"><i class="fa fa-map-marker" title="View objects on map" style="font-size:24px;color:red;padding-left:20px;"></a></i></p>
                              <?php endif; ?>
                            </div>
                            <div class="col-md-3"></div>
                            <?php if ($report_file['operation'] != 'detect_and_locate_objects'): ?>
                            <div class="col-md-3">
                                <p><b>Unit: </b> <?php echo $report_file['props']['unit_poo'] ?></p>
                            </div>
                          <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($report_file['operation'] != 'detect_and_locate_objects'): ?>
                    <div class="col-md-2">
                      <p><a href="<?php echo site_url('media/download/' . $report_file['fid']); ?>"><i class="fa fa-download" title="Download Reports" style="font-size:24px;color:red;padding-left:20px;"></a></i></p>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ($report_file['operation'] != 'detect_and_locate_objects'): ?>
                <div class="row" style="padding-left: 40px;">
                  <div class="col-md-2"></div>
                  <div class="col-md-10">
                    <div class="row">
                      <div class="col-md-3">
                        <p style="text-align: center;"><b>Area</b></p>
                      </div>
                      <div class="col-md-3">
                        <p style="text-align: center;"><b>Length</b></p>
                      </div>
                      <div class="col-md-3">
                        <p style="text-align: center;"><b>Width</b></p>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endif; ?>


                <?php if (!empty($report_file['csv_sum_data'])): ?>
                <div class="row" style="padding-left: 40px; height:80px;overflow-y: scroll">
                    <?php foreach ($report_file['csv_sum_data'] as $csv_data): ?>
                    <div class="col-md-2">
                        <p style="padding-top: 7px;">ID : <?php echo $csv_data['crack_id'] ?></p>
                    </div>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-3">
                                <input disabled class="form-control" id="" type="text" value="<?php echo $csv_data['area'] ?>" placeholder="Length">
                            </div>
                            <div class="col-md-3">
                                <input disabled class="form-control" id="" type="text" value="<?php echo $csv_data['length'] ?>" placeholder="Width">
                            </div>
                            <div class="col-md-3">
                                <input disabled class="form-control" id="" type="text" value="<?php echo $csv_data['width'] ?>" placeholder="Area">
                            </div>
                        </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <div class="row">
            <div class="col-md-12 text-center" style="padding-top: 20px;">
                <input type="submit" name="disableFiles" value="Delete" class="submit" style="background: #454545;padding-bottom: 5px;text-transform: uppercase;">
                <input type="submit" value="EXPORT" class="submit" style="background: #dd5847;padding-bottom: 5px;text-transform: capitalize;">
            </div>
        </div>
    </div>
    <?php echo '</form>'; ?>
</div>
<?php endif; ?>
<div id="report" class="tab-pane fade table-responsive">
    <p role="alert" class="alert alert-warning" style="color:orange; padding: 20px;">Report is empty.</p>
</div>

<main>
<section class="inner-banner text-center" style="background: #584936;">
<img src="<?php echo base_url() ?>/assets/img/solution.jpg">
    <div class="banner-content">
        <div class="container">
            <h1>Process</h1>
        </div>
    </div>
</section>
<section class="page-content">
<div class="container">
    <?php if(isset($success_msg)) : ?>
        <div id="success_msg">
            <?php echo $success_msg; ?>
        </div>
    <?php endif; ?>
    <ul class="nav nav-tabs">
        <li class="active"><a href="<?php echo site_url('process') ?>">Capture Image/Video</a></li>
        <li><a href="#">Process</a></li>
        <li><a href="#">Report</a></li>
    </ul>
	   <div class="form-container">
        <?php echo form_open_multipart('media/do_video_upload');?>
		<div class="text-center">
        	<!--<div id="camera_info"></div>
    		<div id="camera"></div><br> -->
            <?php echo "<input id='file-input' type='file' required='required' name='userfile' size='20' accept='video/*' />"; ?>
            <?php echo "<label>Filename: </label>"; ?>
            <?php echo "<input type='textfield' required='required' name='video_filename'/>"; ?>
            <?php echo "<input type='submit' id='submit-btn' class='btn btn-success btn-sm' name='submit' value='UPLOAD' /> ";?>
            <?php echo "</form>"?>
            <!--<input type="file" name="image" size=20 />
            <input type="submit" name="snapSubmit" class="btn btn-success btn-sm" value="Take Snapshot"> -->
    		<!--<button id="take_snapshots" class="btn btn-success btn-sm">Take Snapshots</button> -->
      	</div>
        </form>

      	<div class="col-md-6">
            <table class="table table-bordered">
            <thead>
                <tr>
                    <th>VIDEO NAME</th><th>DOWNLOAD LINKS</th>
                </tr>
            </thead>
            <tbody id="imagelist">
                <?php if ($videos): ?>
                <?php foreach ($videos as $video) { ?>
                    <tr>
                    <td><?php echo $video['filename'] ?></td>
                    <td><a class="btn btn-success btn-sm" target="_blank" href="<?php echo $video['url'] ?>">Download Video</a></td>
                    </tr>
                <?php } ?>
            <?php endif; ?>
            </tbody>
            </table>
        </div>
	</div>
</div>
</section>
</main>
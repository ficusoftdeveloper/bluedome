<main>
<section class="inner-banner text-center" style="background: #584936;">
<img src="<?php echo base_url('assets/img/process.jpg') ?>">
    <div class="banner-content">
        <div class="container">
            <h1>Process</h1>
        </div>
    </div>
</section>
<section class="page-content">
<div class="container">
    <div class="techno-sec">
    <ul class="nav nav-tabs">
        <li class="active"><a href="<?php echo site_url('process') ?>">Capture Image/Video</a></li>
        <li><a href="#">Process</a></li>
        <li><a href="#">Report</a></li>
    </ul>

	<div class="form-container">
        <?php echo form_open_multipart('media/do_image_upload');?>
        <?php $this->load->view('components/instruction'); ?>
		<div class="text-center">
        	<?php echo "<input id='file-input' type='file' required='required' name='userfile' size='20' accept='image/*' />"; ?>
            <?php echo "<input type='submit' name='postSubmit' class='submit' style='background: #454545;text-transform: uppercase;padding: 10px 30px 6px 30px;' value='CAPTURE IMAGE' /> ";?>
            <?php echo "</form>"?>
      	</div>
        </form>


        <?php echo form_open_multipart('');?>
      	<div class="col-md-12">
            <table class="table table-bordered">
            <thead>
                <tr>
                    <th>SR NO.</th><th>IMAGE</th><th>IMAGE FILE NAME</th><th>SELECT</th>
                </tr>
            </thead>
            <tbody id="imagelist">
            <?php if ($images): ?>
                <?php $counter = 1; ?>
                <?php foreach ($images as $image) { ?>
                    <tr>
                    <td><?php echo $counter ?></td>
                    <td><a href="<?php echo $image['url'] ?>"><img src="<?php echo $image['url'] ?>" height=250 width=350/></a></td>
                    <td><?php echo "<input id='image-". $image['fid']. "' type='textfield' required name='image_filename[]' disabled/>"; ?></td>
                    <td><?php echo "<input value='" . $image['fid'] . "' id='check-". $image['fid']. "' onchange='checkImage(this)' type='checkbox' name='image_check[]'/>"; ?></td>
                    </tr>
                    <?php $counter++ ?>
                <?php } ?>
            <?php endif; ?>
            </tbody>
            </table>
            <div class="text-center">
             <?php echo "<input type='submit' id='submit-btn' name='uploadFiles' class='btn btn-success btn-sm' value='UPLOAD IMAGES' /> ";?>
            </div>
        </div>
        </form>
	</div>
</div>
</div>
</section>
</main>
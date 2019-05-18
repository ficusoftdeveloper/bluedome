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
    <?php if(isset($error)) : ?>
        <div id="error_msg">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <ul class="nav nav-tabs">
        <li><a href="<?php echo site_url('process') ?>">Capture Image/Video</a></li>
        <li class="active"><a href="<?php echo site_url('process/upload') ?>">Process</a></li>
        <li><a href="#">Report</a></li>
    </ul>
    <?php echo form_open_multipart('');?>
    <div class="col-md-12">
            <table class="table table-bordered">
            <thead>
                <tr>
                    <th>IMAGE</th><th>NAME</th><th>DATE</th><th>SIZE</th><th>TYPE</th><th>STATUS</th>
                </tr>
            </thead>
            <tbody id="imagelist">
            <?php if ($files): ?>
                <?php $counter = 1; ?>
                <?php foreach ($files as $file) { ?>
                    <?php if ($file['filetype'] == 'image') { $type = 'images'; } else { $type = 'videos'; } ?>
                    <?php $filepath = base_url() . '/uploads/' . $type . '/raw/' . $file['filename'] . '.jpg'; ?>
                    <?php if ($file['is_processed'] == 0) { $status = 'Pending'; } else { $status = 'Processed'; } ?>
                    <tr>
                    <td><img src="<?php echo $filepath ?>" height=250 width=350 /></td>    
                    <td><?php echo $file['filename'] ?></td>
                    <td><?php echo $file['date_captured'] ?></td>
                    <td><?php echo '2 MB' ?></td>
                    <td><?php echo $file['filetype'] ?></td>
                    <td style="color: red"><?php echo $status ?></td>
                    <td><input type="checkbox" name="process_file[]" value="<?php echo $file['fid']?>" /></td>
                    </tr>
                <?php } ?>
            <?php endif; ?>
            </tbody>
            </table>
            <div class="text-center">
             <?php echo "<input type='submit' id='submit-btn' name='processFiles' class='btn btn-success btn-sm' value='PROCESS' /> ";?>
            </div>
	</div>
    </form>
</div>
</section>
</main>
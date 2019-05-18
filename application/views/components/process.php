<?php if (!empty($processed_files)): ?>
<div id="process" class="tab-pane fade table-responsive">
    <?php echo form_open_multipart('media/action'); ?>
    <table class="table table-striped process_table">
        <thead>
            <tr>
            <th class="text-center">Image</th>
            <th>Name</th>
            <th>Date</th>
            <th>Size</th>
            <th>#</th>
            <th>Type</th>
            <th>Status</th>
            <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($processed_files as $processed_file): ?>
        <tr>
            <td>
            <div style="height: 50px;width: 50px;margin: auto;">
              <?php if ($processed_file['is_image']): ?>
                <img src="<?php echo base_url('uploads/raw/'.$processed_file['filename']); ?>" class="img-responsive center-block" style="height: 50px;width: 50px;">
              <?php else: ?>
                <video height="50px" width="50px" controls>
                  <source src="<?php echo base_url('uploads/raw/'.$processed_file['rawname']); ?>" type="video/mp4">
                  <source src="<?php echo base_url('uploads/raw/'.$processed_file['rawname']); ?>" type="video/ogg">
                  Your browser does not support HTML5 video.
                </video>
              <?php endif; ?>
            </div>
            </td>
            <td><?php echo preg_replace('/\\.[^.\\s]{3,4}$/', '', $processed_file['filename']); ?></td>
            <td><?php echo $processed_file['date_processed'] ?></td>
            <td><?php echo $processed_file['filesize'] . ' KB' ?></td>
            <td>1</td>
            <td><?php echo $processed_file['filetype'] ?></td>
            <td style="color: cornflowerblue;">Processed</td>
            <td>
            <input class="styled-checkbox" id="styled-checkbox-<?php echo $processed_file['fid'] ?>" name="file_check[<?php echo $processed_file['fid'] ?>]" type="checkbox" value="<?php echo $processed_file['fid'] ?>"> <label for="styled-checkbox-<?php echo $processed_file['fid'] ?>"></label>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="text-center">
        <input type="submit" name="deleteFiles" value="Delete" class="submit" style="background: #454545;padding-bottom: 5px;text-transform: uppercase;">
        <input type="submit" name="reprocessFiles" value="Process" class="submit" style="background: #dd5847;padding-bottom: 5px;text-transform: uppercase;">
    </div>
    <?php echo '</form>'; ?>
</div>
<?php endif; ?>
<div id="process" class="tab-pane fade table-responsive">
    <p role="alert" class="alert alert-warning" style="color:orange; padding: 20px;">No file is processed yet.</p>
</div>

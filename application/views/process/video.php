<div id="container">
    <div id="page-title">
    <h1><?php echo $pageTitle; ?></h1>
    </div>
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
    <div id="body">
        <?php echo form_open_multipart('');?>
        <div class="text-center">
            <!--<div id="camera_info"></div>
            <div id="camera"></div><br> -->
            <?php echo "<input type='file' required='required' name='userfile' size='20' accept='video/*' />"; ?>
            <?php echo "<label>Filename: </label>"; ?>
            <?php echo "<input type='textfield' required='required' name='video_filename'/>"; ?>
            <?php echo "<input type='submit' id='submit-btn' name='postSubmit' class='btn btn-success btn-sm' value='UPLOAD' /> ";?>
            <?php echo "</form>"?>
            <!--<input type="file" name="image" size=20 />
            <input type="submit" name="snapSubmit" class="btn btn-success btn-sm" value="Take Snapshot"> -->
            <!--<button id="take_snapshots" class="btn btn-success btn-sm">Take Snapshots</button> -->
        </div>
        </form>
    </div>
</div>
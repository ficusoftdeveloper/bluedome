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
        <?php if ($filename) : ?>
        <div id="filename">
            <b>Filename : </b> <?php echo $filename ?>
        </div>
    <?php endif; ?>
        <div class="col-md-6">
            <table class="table table-bordered">
            <thead>
                <tr>
                    <th>SR NO.</th><th>PROCESSED IMAGE</th><th>PROCESSED FILE NAME</th><th>ACTIONS</th>
                </tr>
            </thead>
            <tbody id="imagelist">
                <?php if (isset($revisons)) : ?>
                    <?php $counter = 1; ?>
                    <?php foreach ($revisons as $revision) { ?>
                        <tr>
                        <td><?php echo $counter ?></td>
                        <td><video width="150" controls>
                            <source src="<?php echo $revision['url'] ?>" type="video/mp4">
                            <source src="mov_bbb.ogg" type="video/ogg">
                                Your browser does not support HTML5 video.
                        </video></td>
                        <td><?php echo $revision['filename'] ?></td>
                        <td><a target="_blank" href="<?php echo $revision['url'] ?>" class="btn btn-success btn-sm">VIEW</a></td>
                        </tr>
                    <?php } ?>
                <?php endif; ?>
            </tbody>
            </table>
        </div>
    </div>
</div>
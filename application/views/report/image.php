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
                        <td><img src="<?php echo $revision['url'] ?>" width="150" height="150" /></td>
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
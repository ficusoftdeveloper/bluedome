<?php if (null != $this->session->flashdata('error_msg')): ?>
	<div role="alert" class="error-msg alert alert-danger" style="color: red; padding: 20px">
        <?php echo $this->session->flashdata('error_msg') ?>
    </div>
    <?php endif; ?>
    <?php if (null != $this->session->flashdata('success_msg')): ?>
    <div role="alert" class="success-msg alert alert-success" style="color: green; padding: 20px">
        <?php echo $this->session->flashdata('success_msg') ?>
    </div>
<?php endif; ?>

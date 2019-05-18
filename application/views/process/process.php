<main>
    <section class="inner-banner text-center">
        <img src="<?php echo base_url('assets/img/process.jpg') ?>">
        <div class="banner-content">
            <div class="container">
                <h1>Process</h1>
            </div>
        </div>
    </section>
        
    <?php if (null != $this->session->flashdata('error_msg')): ?>
    <div class="error-msg" style="color: red; padding: 20px">
        <?php echo $this->session->flashdata('error_msg') ?>
    </div>
    <?php endif; ?>
    <?php if (null != $this->session->flashdata('success_msg')): ?>
    <div class="success-msg" style="color: green; padding: 20px">
        <?php echo $this->session->flashdata('success_msg') ?>
    </div>
    <?php endif; ?>

    <section class="page-content">
        <div class="container">
            <div class="techno-sec">
                <div class="row">
                    <div class="col-md-12">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#capture">Capture Image/Video</a></li>
                            <li><a data-toggle="tab" href="#process">Process</a></li>
                            <li><a data-toggle="tab" href="#report">Report</a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="capture" class="tab-pane fade in active">
                                <div class="container">
                                    <?php $this->load->view('components/instruction'); ?> 
                                        <div class="col-md-12" style="margin-top: 3%;">
                                        <div>
                                            <a href="<?php echo site_url('media/image') ?>"><input type="submit" value="Capture Image" class="submit " style="background: #454545;text-transform: uppercase;padding: 10px 30px 6px 30px;"></a>
                                            <a href="<?php echo site_url('media/video') ?>"><input type="submit" value="Capture Video" class="submit " style="background: #454545;text-transform: uppercase;padding: 10px 30px 6px 30px;"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php $this->load->view('components/process'); ?>
                            <?php $this->load->view('components/report'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <p>&nbsp;</p>
        </div>
    </section>
</main>
<main>
<section class="inner-banner text-center">
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
            <div class="row">
                <div class="col-md-12">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#capture">Capture Image/Video</a></li>
                        <li><a data-toggle="tab" href="#process">Process</a></li>
                        <li><a data-toggle="tab" href="#report">Report</a></li>
                     </ul>
                	<div class="tab-content">
                    	<?php $this->load->view('components/capture') ?>
                    	<?php $this->load->view('components/process') ?>
                    	<?php $this->load->view('components/report') ?>
                	</div>
                </div>
            </div>
        </div>
        <p>&nbsp;</p>
    </div>
</section>
</main>

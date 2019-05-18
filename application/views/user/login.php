<main>
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
                            <div class="col-md-2 col-lg-3"> </div>
                            <div class="col-md-8 col-lg-6">
                                <h1 style="text-align: center;margin-bottom: 5%;color: #000;">Login</h1>
                                <div class="contact-form animatable bounceIn">
                                     <?php echo form_open_multipart('');?>
                                        <div class="form-group">
                                            <label>User Email</label>
                                            <input name="user_email" type="text" class="" id="" placeholder="">
                                        </div>
                                        <div id="wrapper">
                                            <div class="form-group has-feedback">
                                                <label>Password</label>
                                                <input name="user_password" type="password" class="" id="password" placeholder="Password">
                                                <i class="fa fa-eye form-control-feedback"></i>
                                            </div>
                                        </div>
                                        <input name="postSubmit" type="submit" value="Log In" class="submit btn-block" style="background: #dd5847;padding-bottom: 5px;text-transform: capitalize;">
                                    <?php echo '</form>' ?>
                                    <p class="text-center" style="padding-top: 30px;"><a href="<?php echo site_url('user/forgot-password') ?>" style="color: #000;">Forgot Password?</a></p>
                                </div>
                                <!--<p class="text-center" style="margin-bottom: 0; padding-top: 10%;color: #000;">Don't have an account? <a href="<?php // echo site_url('user/register') ?>" style="padding-left: 6px;color: #2d55a7;"> Sign Up</a></p> -->
                            </div>
                        </div>
                    </div>
                    <p>&nbsp;</p>
                </div>
            </section>
</main>
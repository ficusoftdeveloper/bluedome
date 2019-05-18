<main>
    <?php if (null != $this->session->flashdata('error_msg')): ?>
    <div class="error-msg" style="color: red; padding: 20px;">
        <?php echo $this->session->flashdata('error_msg') ?>
    </div>
    <?php endif; ?>
    <?php if (null != $this->session->flashdata('success_msg')): ?>
    <div class="success-msg">
        <?php echo $this->session->flashdata('success_msg') ?>
    </div>
    <?php endif; ?>

<section class="page-content">
    <div class="container">
                    <div class="techno-sec">
                        <div class="row">
                            <div class="col-md-12">
                                <h1 style="text-align: center;margin-bottom: 3%;color: #000;">Company Registration </h1>
                            </div>
                        </div>
                        <?php echo form_open_multipart('');?>
                        <div class="row contact-form">
                            <div class="col-md-6">
                                <div class=" animatable bounceIn">
                                    <div class="form-group">
                                        <label>Company Name</label>
                                        <input type="text" name="company_name" class="" id="" placeholder="">
                                    </div>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class=" animatable bounceIn">
                                    <div class="form-group">
                                        <label>User Name</label>
                                        <input type="text" name="user_name" class="" id="" placeholder="">
                                    </div>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class=" animatable bounceIn">
                                    <div id="wrapper">
                                        <div class="form-group has-feedback">
                                            <label>Password</label>
                                            <input type="password" name="user_password" class="" id="password" placeholder="Password">
                                            <i class="fa fa-eye form-control-feedback"></i>
                                        </div>
                                    </div>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class=" animatable bounceIn">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="text" name="user_email" class="" id="" placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                              <h3 class="text-center" style="color: #000;padding: 20px;">Company Details</h3>
                            </div>
                              <div class="col-md-6">
                                <div class=" animatable bounceIn">                              
                                    <div class="form-group">
                                        <label>Website</label>
                                        <input type="text" name="company_website" class="" id="" placeholder="">
                                    </div>
                                </div>
                            </div> 
                            
                            <div class="col-md-6">
                                <div class=" animatable bounceIn">
                                    <div class="form-group">
                                        <label>Mobile</label>
                                        <input type="text" name="user_mobile" class="" id="" placeholder="">
                                    </div> 
                                </div>
                              </div>                           
                            <div class="col-md-12">
                                <div class=" animatable bounceIn">
                                    <div class="form-group">
                                        <label>Address</label>
                                        <input type="text" name="user_address" class="" id="" placeholder="">
                                    </div>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class=" animatable bounceIn">                               
                                    <div class="form-group">
                                        <label>City</label>
                                        <input type="text" name="user_city" class="" id="" placeholder="">
                                    </div> 
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class=" animatable bounceIn">                               
                                    <div class="form-group">
                                        <label>State</label>
                                        <input type="text" name="user_state" class="" id="" placeholder="">
                                    </div>
                                </div>
                              </div>                              
                              <div class="col-md-6">
                                <div class=" animatable bounceIn">                               
                                    <div class="form-group">
                                        <label>Zip</label>
                                        <input type="text" name="user_zip" class="" id="" placeholder="">
                                    </div>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class=" animatable bounceIn">                                
                                    <div class="form-group">
                                      <label>Logo</label>
                                      <div class="file-upload-wrapper" data-text="Select your file!">
                                        <input name="file-upload-field" type="file" class="file-upload-field" value="">
                                      </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                               <input type="submit" name="postSubmit" value="Register" class="submit btn-block" style="background: #dd5847;padding-bottom: 5px;text-transform: capitalize;">
                            </div>
                            <div class="col-md-4"></div>
                        </div>
                        <?php echo '</form>' ?>

                    </div>
                    <p>&nbsp;</p>
                </div>
            </section>
</main>

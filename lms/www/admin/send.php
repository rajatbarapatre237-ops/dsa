<?php 
    include 'include/auth_session.php';
    require_once 'connect/db.php';
    require_once 'connect/fun.php';

    $connect = new connect();
    $fun=new fun($connect->dbconnect());


    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'phpmailer/src/Exception.php';
    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';

   
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'abcoedtech49@gmail.com';
        $mail->Password = "gcqqciuktzwzckqn";
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        
        $mail->setFrom('abcoedtech49@gmail.com');
        if(isset($_GET['id'])){
            // echo "inside id";
            $stud = $fun->getStudentByID($_GET['id']);
            $student = mysqli_fetch_assoc($stud);
            $mail->addAddress($student['email']);

            $mail->isHTML(true);
            
            $mail->Subject = "Collect your Student Id ";
            $body = '
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
    
    <head>
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1" name="viewport">
        <meta name="x-apple-disable-message-reformatting">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="telephone=no" name="format-detection">
        <title></title>
        <!--[if (mso 16)]>
        <style type="text/css">
        a {text-decoration: none;}
        </style>
        <![endif]-->
        <!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]-->
        <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
        <o:AllowPNG></o:AllowPNG>
        <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
    </head>
            <div class="es-wrapper-color">
            
            <table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0">
                <tbody>
                    <tr>
                        <td class="esd-email-paddings" valign="top">
                            <table cellpadding="0" cellspacing="0" class="es-header" align="center">
                                <tbody>
                                    <tr>
                                        <td class="esd-stripe" align="center" esd-custom-block-id="89330">
                                            <table class="es-header-body" width="600" cellspacing="0" cellpadding="0" align="center">
                                                <tbody>
                                                    <tr>
                                                        <td class="esd-structure es-p5t es-p5b es-p5r es-p5l" align="left">
                                                           
                                                            <table class="es-left" cellspacing="0" cellpadding="0" align="left">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="es-m-p0r esd-container-frame" width="188" valign="top" align="center">
                                                                            <table width="100%" cellspacing="0" cellpadding="0">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="esd-block-image es-m-p0l es-p5b" align="left" style="font-size:0"><a href="https://acetech.abcoedtech.com" target="_blank"><img src="https://acetech.abcoedtech.com/student/assets/img/logoace.png" al t alt="Sunrise logo" title="AceTech Logo" width="108"></a></td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <!--[if mso]></td><td width="20"></td><td width="382" valign="top"><![endif]-->
                                                            <table cellspacing="0" cellpadding="0" align="right">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="esd-container-frame" width="382" align="left">
                                                                            <table c ellspacing="0" width="100%" cellpadding="0">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="esd-block-menu" esd-img-prev-h="16" esd-img-prev-w="16">
                                                                                            <table class="es-menu" width="100%" cellspacing="0" cellpadding="0">
                                                                                                <tbody>
                                                                                                    <tr class="links">
                                                                                                        <td class="es-p10t es-p10b es-p5r es-p5l" style="padding-bottom: 25px; padding-top: 25px; " width="25.00%" bgcolor="transparent" align="center"><a style="color: #a5a29b;" href="https://acetech.abcoedtech.com/courses.php">Courses</a></td>
                                                                                                        <td class="es-p10t es-p10b es-p5r es-p5l" style="padding-bottom: 25px; padding-top: 25px; " width="25.00%" bgcolor="transparent" align="center"><a style="color: #a5a29b;" href="https://acetech.abcoedtech.com/stud_registration.php">Student Registration</a></td>
                                                                                                        
                                                                                                    </tr>
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <!--[if mso]></td></tr></table><![endif]-->
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="es-content" cellspacing="0" cellpadding="0" align="center">
                                <tbody>
                                    <tr>
                                        <td class="esd-stripe" align="center">
                                            <table class="es-content-body" width="600" cellspacing="0" cellpadding="0" align="center">
                                                <tbody>
                                                    <tr>
                                                        <td class="esd-structure" align="left">
                                                            <table width="100%" cellspacing="0" cellpadding="0">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="esd-container-frame" width="600" valign="top" align="center">
                                                                            <table width="100%" cellspacing="0" cellpadding="0">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="esd-block-image" align="center" style="font-size:0"><img class="adapt-img" src="https://tlr.stripocdn.email/content/guids/CABINET_d76dc63418b630746d937a046ab741d3/images/11281501160433999.png" alt="Welcome aboard" title="Welcome aboard" width="600"></td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="esd-structure" style="background-color: #ffffff;" bgcolor="#ffffff" align="left">
                                                            <table width="100%" cellspacing="0" cellpadding="0">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="esd-container-frame" width="600" valign="top" align="center">
                                                                            <table width="100%" cellspacing="0" cellpadding="0">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="esd-block-text es-p25t es-p10b" align="center">
                                                                                            <h2>Welcome to DSA!</h2>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="esd-block-text es-p5t es-p15b es-p15r es-p15l" >
                                                                                            <p style="line-height: 150%;">You are now being tranferred as DSA Regular Student from being a registered student.</p><br>
                                                                                            <p>Collect your ID: DSA'.$student['id'].'</p><br>
                                                                                            <p>Register to DSA using this ID. Follow Below Steps: </p><br>




                                                                                            <p>STEP 1: Go to <a href="linklinklinklink" target="_blank">this link</a>.</p>





                                                                                            <p>STEP 2: Click on "Register to Create Account".  </p>
                                                                                            <p>STEP 3: Enter same student ID as alloted to you and enter password as you like.  </p>
                                                                                            <p>STEP 4: Click on Register.  </p>
                                                                                            <p>STEP 5: You will be redirected to your dashboard.  </p>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <table cellpadding="0" cellspacing="0" class="es-footer" align="center">
                                <tbody>
                                    <tr>
                                        <td class="esd-stripe" esd-custom-block-id="1259" align="center">
                                            <table class="es-footer-body" width="600" cellspacing="0" cellpadding="0" align="center">
                                                <tbody>
                                                    <tr>
                                                        <td class="esd-structure es-p25t es-p25b es-p20r es-p20l" align="left">
                                                            <!--[if mso]><table width="560" cellpadding="0" cellspacing="0"><tr><td width="194"><![endif]-->
                                                            <table class="es-left" cellspacing="0" cellpadding="0" align="left">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="es-m-p0r es-m-p20b esd-container-frame" width="174" align="center">
                                                                            <table width="100%" cellspacing="0" cellpadding="0">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="es-m-txt-с esd-block-text es-p10b" align="left">
                                                                                            <h4 style="color: #fec903;">Contact Us<br></h4>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="es-m-txt-с esd-block-text" align="left" esd-links-underline="none">
                                                                                            <p><a target="_blank" style="text-decoration: none;" href="tel:9765778349">9765778349</a></p>
                                                                                            <p><a target="_blank" href="mailto:abcoedtech@gmail.com" style="text-decoration: none;">abcoedtech@gmail.com</a></p>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                        <td class="es-hidden" width="20"></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <!--[if mso]></td><td width="173"><![endif]-->
                                                            <table class="es-left" cellspacing="0" cellpadding="0" align="left">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="es-m-p0r es-m-p20b esd-container-frame" width="173" align="center">
                                                                            <table width="100%" cellspacing="0" cellpadding="0">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="esd-block-text es-p10b" align="left">
                                                                                            <h4 style="color: #fec903;">Site Links<br></h4>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="esd-block-text" align="left">
                                                                                            <p>
                                                                                                <a href="https://acetech.abcoedtech.com/courses.php" target="_blank">Courses</a><br>
                                                                                                <a href="https://acetech.abcoedtech.com/stud_registration.php" target="_blank">Registeration</a><br>
                                                                                                <a href="https://acetech.abcoedtech.com/index.php#contact" target="_blank">Contact</a>
                                                                                            </p>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <!--[if mso]></td><td width="20"></td><td width="173"><![endif]-->
                                                            <table class="es-right" cellspacing="0" cellpadding="0" align="right">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="es-m-p0r es-m-p20b esd-container-frame" width="173" align="center">
                                                                            <table width="100%" cellspacing="0" cellpadding="0">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="es-m-txt-с esd-block-text es-p10b" align="left">
                                                                                            <h4 style="color: #fec903; line-height: 120%;">Social networks<br></h4>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="esd-block-social" align="left" style="font-size:0">
                                                                                            <table class="es-table-not-adapt es-social" cellspacing="0" cellpadding="0">
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td class="es-p10r" valign="top" align="center"><a href="https://whatsapp.com/channel/0029Va9fizdBVJl1JW0oOz0i "><img title="WhatsApp" src="https://www.freepnglogos.com/uploads/whatsapp-logo-app-png-4.png" alt="WhatsApp" width="32" height="32"></a></td>
                                                                                                        <td class="es-p10r" valign="top" align="center"><a href><img title="Facebook" src="https://w7.pngwing.com/pngs/282/704/png-transparent-facebook-messenger-logo-icon-facebook-facebook-logo-blue-text-trademark.png" alt="Fb" width="32" height="32"></a></td>
                                                                                                        <td class="es-p10r" valign="top" align="center"><a href><img title="Linkedin" src="https://www.freepnglogos.com/uploads/linkedin-logo-design-30.png" alt="Li" width="32" height="32"></a></td>
                                                                                                        <td valign="top" align="center"><a href><img title="Instagram" src="https://img.freepik.com/free-vector/instagram-icon_1057-2227.jpg?size=626&ext=jpg" alt="Ig" width="32" height="32"></a></td>
                                                                                                    </tr>
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <!--[if mso]></td></tr></table><![endif]-->
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="esd-structure" align="left">
                                                            <table width="100%" cellspacing="0" cellpadding="0">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="esd-container-frame" width="600" valign="top" align="center">
                                                                            <table width="100%" cellspacing="0" cellpadding="0">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="esd-block-spacer" align="center" style="font-size:0">
                                                                                            <table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td style="border-bottom: 1px solid #cccccc; background:none; height:1px; width:100%; margin:0px 0px 0px 0px;"></td>
                                                                                                    </tr>
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="esd-structure es-p15t es-p15b es-p20r es-p20l" align="left">
                                                            <!--[if mso]><table width="560" cellpadding="0" cellspacing="0"><tr><td width="270" valign="top"><![endif]-->
                                                            <table class="es-left" cellspacing="0" cellpadding="0" align="left">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="es-m-p20b esd-container-frame" width="270" align="left">
                                                                            <table width="100%" cellspacing="0" cellpadding="0">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="esd-block-text" align="left">
                                                                                            <p><a target="_blank" href="#">Privacy policy</a> | <a target="_blank" href="#">Terms of use</a> | <a target="_blank" href="#">Contact us</a></p>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <!--[if mso]></td><td width="20"></td><td width="270" valign="top"><![endif]-->
                                                            <table class="es-right" cellspacing="0" cellpadding="0" align="right">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="esd-container-frame" width="270" align="left">
                                                                            <table width="100%" cellspacing="0" cellpadding="0">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td align="right" class="esd-block-text">
                                                                                            <p><a target="_blank" class="unsubscribe">Unsubscribe</a></p>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <!--[if mso]></td></tr></table><![endif]-->
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="esd-footer-popover es-content" cellspacing="0" cellpadding="0" align="center">
                                <tbody>
                                    <tr>
                                        <td class="esd-stripe" align="center">
                                            <table class="es-content-body" style="background-color: transparent;" width="600" cellspacing="0" cellpadding="0" align="center">
                                                <tbody>
                                                    <tr>
                                                        <td class="esd-structure es-p30t es-p30b es-p20r es-p20l" align="left">
                                                            <table width="100%" cellspacing="0" cellpadding="0">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="esd-container-frame" width="560" valign="top" align="center">
                                                                            <table width="100%" cellspacing="0" cellpadding="0">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="esd-block-image es-infoblock made_with" align="center" style="font-size:0"><a target="_blank" href="https://abcoedtech.com/abcoed/"><img src="admin/assets/img/AbCoEdTech.png" alt width="125"></a></td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        </body>
    
        </html>';
            $mail->Body = $body;

            $mail->send();
        }else{
            echo"id not found";
        }
        
       header("Location: view_reg_students.php");
        
?>
<!DOCTYPE html>
<html>
<head>
  <title>Recasoft</title>
</head>
<body>
<style type="text/css">
.email_wrap {
  width: 600px;
  max-width: calc(100% - 2rem);
  margin-left: auto;
  margin-right: auto;
}
.col-one {
  padding: 50px 50px 30px 50px;
}  
@media screen and (max-width: 575px) {
  .col-one {
    padding: 24px;
  }
}
@media screen and (max-width: 480px) {
  .col-one {
    padding: 10px;
  }
}
</style>
<div class="email_wrap">   
<table style="border-collapse:collapse;margin-top:36px;border-radius:8px;background:#fff;width:100%;margin:auto;">
              <tbody><tr>
                <td style="font-family:OpenSans,&quot;Open Sans&quot;,sans-serif;font-size:14px;vertical-align:top;box-sizing:border-box;padding:5px">
                  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border:0;width:100%">
                    <tbody><tr>
                      <td align="left" style="font-family:OpenSans,&quot;Open Sans&quot;,sans-serif;font-size:14px;vertical-align:top">
                        
                        <img src="{{asset('public/assets/app/media/img/logos/logo.png')}}" alt="Recasoft Technologies" height="auto" style="width: 220px; display:block;border:none;max-width:100%;margin-bottom:24px" class="CToWUd">

                        <p style="font-family:OpenSans,&quot;Open Sans&quot;,sans-serif;font-weight:normal;margin:0;margin-bottom:12px;font-size:18px;padding:12px 0;text-align:left;">
                          Hello {{$name??''}},
                        </p>
                        
                        <p style="font-family:OpenSans,&quot;Open Sans&quot;,sans-serif;font-weight:normal;margin-bottom:12px;font-size:16px;padding-bottom:12px;text-align:left;">
                          A request has been received to change the password for your Recasoft account. Please click the link below to reset your password.
                        </p>
                        <table border="0" cellpadding="0" cellspacing="0" class="m_-5192168380993195071btn-primary" style="border-collapse:collapse;border:0;box-sizing:border-box;width:100%">
                          <tbody>
                            <tr>
                              <td align="left" style="font-family:OpenSans,&quot;Open Sans&quot;,sans-serif;font-size:14px;vertical-align:top;padding-bottom:15px">
                                <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border:0;width:100%">
                                  <tbody>
                                    <tr>
                                      <td> 
                                        <a href="{{$url??''}}" style="text-align:center;border-radius:5px;box-sizing:border-box;display:inline-block;font-size:1px;font-weight:bold;margin:0;padding:8px 25px;text-decoration:none;font-size:16px;background-color:#15394c;border-color:#15394c;color:#ffffff" target="_blank">
                                        Reset Password
                                        <u></u><u></u><u></u><u></u>
                                        </a> 
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        
                        </td></tr><tr>
                          <td align="left" style="font-family:OpenSans,&quot;Open Sans&quot;,sans-serif;font-size:14px;vertical-align:top">
                          </td>
                        </tr>
                      
                    
                  </tbody></table>
                </td>
              </tr>
              <tr>
                <td>
                  <table width="100%" style="border-width:0;border-collapse:collapse;margin-top:0;margin-bottom:0;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;background-color:#f6f9fc;border-bottom-left-radius:8px;border-bottom-right-radius:8px">
                    <tbody>
                      <tr>
                        <td height="120" style="padding:24px;border-width:0;border-collapse:collapse;margin-top:0;margin-bottom:0;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:56px;padding-left:56px">
                          <p style="font-family:OpenSans,&quot;Open Sans&quot;,sans-serif;text-align:left;font-size:12px;font-weight:normal;margin:0;margin-bottom:12px;color:#525f7f">For assistance: <a href="mailto:support@recasoft.no" target="_blank">support@recasoft.no</a></p>
                          <p style="font-family:OpenSans,&quot;Open Sans&quot;,sans-serif;text-align:left;font-size:12px;font-weight:normal;margin:0;color:#525f7f">Recasoft Technologies, Fornebu/Norway</p>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
            </tbody></table>
</div>
</body>
</html>
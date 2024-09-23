<!DOCTYPE html>
<html>

<head>
    <title>Recasoft</title>
</head>

<strong>
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
        <table
            style="border-collapse:collapse;margin-top:36px;border-radius:8px;background:#fff;width:100%;margin:auto;">
            <tbody>
                <tr>
                    <td
                        style="font-family:OpenSans,&quot;Open Sans&quot;,sans-serif;font-size:14px;vertical-align:top;box-sizing:border-box;padding: 5px;">
                        <table border="0" cellpadding="0" cellspacing="0"
                            style="border-collapse:collapse;border:0;width:100%">
                            <tbody>
                                <tr>
                                    <td
                                        style="font-family:OpenSans,&quot;Open Sans&quot;,sans-serif;font-size:14px;vertical-align:top">

                                        <img src="{{ asset('public/assets/app/media/img/logos/logo.png') }}"
                                            alt="Recasoft Technologies"
                                            style="width: 200px;height:auto; display:block;border:none;max-width:100%;margin-bottom:24px"
                                            class="CToWUd">

                                        <div style="text-align: left;">
                                            <span>
                                                <b style="font-size: 15px;"> {{ $company_name ?? '' }}</b><span style="font-weight: 100"> sent a new service request for machines given below.</span>
                                            </span><br><br>
                                            
                                            <span style="font-size: 15px;">
                                                <span style="font-weight: 100"> Urgent Request: <span><b>{{ isset($urgent) ? $urgent : 'No' }}</b>
                                            </span><br><br>

                                            <span>
                                                <span style="font-size: 15px;">Equipment name: </span><br>
                                            <strong> {!! nl2br($deviceNames) !!}</strong>
                                            </span><br><br>

                                            <span>
                                            <span style="font-size: 15px;"> Description: </span>
                                           <span style="font-weight: 100"> {!! isset($description) ? nl2br($description) : '' !!} </span>
                                            </span> <br><br>

                                            <span style="font-weight: 100">
                                            <b> Phone No: </b> {{ $phone_number }}</span>

                                            <p>
                                            <strong> Go to project: </strong> <a style="font-weight: 100"
                                                href="{{ route('home', $company_id) }}">{{ route('home', $company_id) }}</a>
                                            </p>
                                            <strong>


                                        </div>


                                    </td>
                                </tr>


                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table width="100%"
                            style="border-width:0;border-collapse:collapse;margin-top:0;margin-bottom:0;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;background-color:#f6f9fc;border-bottom-left-radius:8px;border-bottom-right-radius:8px">
                            <tbody>
                                <tr>
                                    <td height="120"
                                        style="padding:24px;border-width:0;border-collapse:collapse;margin-top:0;margin-bottom:0;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:20px;padding-left:20px">


                                        <div
                                            style="font-size: 13px; font-family: OpenSans,'Open Sans',sans-serif;    color: #666;    line-height: 28px; text-align: center;">
                                            For assistance: <a href="mailto:support@recasoft.no"
                                                style="color: #15394c;    text-decoration: none;    font-weight: 500;">support@recasoft.no</a>
                                            <strong><br>
                                            <span>
                                                Recasoft Technologies, Fornebu/Norway
                                            </span>
                                        </div>


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

</html>

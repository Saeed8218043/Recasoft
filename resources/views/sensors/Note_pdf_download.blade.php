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

                                        <img src="{{url('public/assets/app/media/img/logos/logo.png')}}"
                                            alt="Recasoft Technologies" height="auto"
                                            style="width: 150px; display:block;border:none;max-width:70%;margin-bottom:15px"
                                            class="CToWUd">
                                        <div> <h2>Notes</h2> </div>
                                        <div style="text-align: left;">
                                           <p><strong>Company Name: </strong>  {{$company_name}} </p>
                                           <p><strong>Device: </strong>  {{$device_name}} </p>
                                           <p><strong>Note Name: </strong>  {{$name}} </p>
                                           <p><strong>Notes: </strong> {{$notes}}</p>
                                            <p><strong>Date:</strong> {{$date}}</p>

                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                </tr>
            </tbody>
        </table>

    </div>
</body>

</html>

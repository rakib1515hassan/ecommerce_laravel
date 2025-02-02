<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

    <title>{{translate('New order received')}}</title>

    <style>

        body {
            background-color: #FFFFFF;
            padding: 0;
            margin: 0;
        }
    </style>

</head>

<body style="background-color: #FFFFFF; padding: 0; margin: 0;">

<table border="0" cellpadding="0" cellspacing="10" height="100%" bgcolor="#FFFFFF" width="100%"
       style="max-width: 650px;" id="bodyTable">

    <tr>

        <td align="center" valign="top">

            <table border="0" cellpadding="0" cellspacing="0" width="100%" id="emailContainer"
                   style="font-family:Arial; color: #333333;">

                <!-- Logo -->
                @php($logo=\App\Models\BusinessSetting::where(['type'=>'company_web_logo'])->first()->value)
                <tr>

                    <td align="left" valign="top" colspan="2"
                        style="border-bottom: 1px solid #CCCCCC; padding-bottom: 10px;">
                        <img alt="" border="0" src="{{url('/').'/storage/company/'.$logo}}" title=""
                             class="sitelogo" width="60%" style="max-width:250px;"/>
                    </td>

                </tr>

                <!-- Title -->

                <tr>

                    <td align="left" valign="top" colspan="2"
                        style="border-bottom: 1px solid #CCCCCC; padding: 20px 0 10px 0;">
                        <span
                            style="font-size: 18px; font-weight: normal;">{{translate('Notification mail for new order received')}}</span>
                    </td>

                </tr>

                <!-- Messages -->

                <tr>

                    <td align="left" valign="top" colspan="2" style="padding-top: 10px;">

                        <span style="font-size: 12px; line-height: 1.5; color: #333333;">

                            {{translate('We have sent you this email to notify that you have a new order. You will be able to see your orders after login to your panel')}}.

                            <br/><br/>

                            {{translate('New order ID for you')}} :

                            <a href="javascript:"> <h3 style="font-weight: 1000">{{$id}}</h3> </a>

                            <br/><br/>

                            {{translate('If you need help, or you have any other questions, feel free to email us')}}.

                            <br/><br/>

                            {{translate('From')}} {{$web_config['name']->value}}

                        </span>

                    </td>

                </tr>

            </table>

        </td>

    </tr>

</table>

</body>

</html>

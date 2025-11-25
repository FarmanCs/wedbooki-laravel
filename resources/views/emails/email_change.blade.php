<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body style="margin:0; padding:0; background-color:#f3f4f6; font-family:Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding: 40px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0"
                   style="background:white; border-radius:12px; padding:40px;
                              box-shadow:0 4px 20px rgba(0,0,0,0.08);">

                <!-- Header -->
                <tr>
                    <td style="font-size:28px; font-weight:bold; color:#111827; padding-bottom:20px;">
                        Hello,
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="font-size:16px; color:#374151; line-height:1.6; padding-bottom:30px;">
                       Your Email  has been change successfuly, if its not you then let us know to take neccessory action.
                    </td>
                </tr>

                <!-- Button -->
                <tr>
                    <td align="center" style="padding-bottom:40px;">
                        <a href="{{ $url ?? '#' }}"
                           style="background-color:#2563eb; color:white;
                                      padding:14px 28px; border-radius:8px;
                                      text-decoration:none; font-size:16px;
                                      font-weight:600; display:inline-block;">
                            Button Text
                        </a>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="font-size:14px; color:#6b7280; text-align:center;">
                        Thanks,<br>
                        <strong>{{ config('app.name') }}</strong>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>

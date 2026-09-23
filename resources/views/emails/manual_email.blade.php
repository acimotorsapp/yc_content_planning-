<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $manualSubject }} - YC Content Planning</title>
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            min-width: 100%;
            background-color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
        }
        @media only screen and (max-width: 600px) {
            .email-container { width: 100% !important; max-width: 100% !important; border-radius: 0 !important; }
            .email-padding { padding-left: 16px !important; padding-right: 16px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 20px 0; background-color: #f1f5f9;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 0 8px;">
                <table class="email-container" border="0" cellpadding="0" cellspacing="0" width="600" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06); border: 1px solid #e2e8f0;">
                    <tr>
                        <td height="5" bgcolor="#d6001c" style="background-color: #d6001c; line-height: 5px; font-size: 5px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center" style="padding: 24px 20px 18px 20px; background-color: #ffffff; border-bottom: 1px solid #f1f5f9;">
                            @if(file_exists(public_path('images/logo.png')))
                                <img src="{{ $message->embed(public_path('images/logo.png')) }}" alt="YC Content Planning by ACI Motors" width="220" height="46" style="width: 220px; max-width: 100%; height: auto; display: block; margin: 0 auto; border: 0;" />
                            @else
                                <div style="font-size: 22px; font-weight: 800; letter-spacing: 1px; color: #0f172a; text-transform: uppercase;">
                                    YC CONTENT PLANNING
                                </div>
                                <div style="font-size: 11px; font-weight: 700; color: #d6001c; letter-spacing: 2px; text-transform: uppercase; margin-top: 3px;">
                                    BY ACI MOTORS
                                </div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="email-padding" style="padding: 24px;">
                            <h1 style="margin: 0 0 16px 0; font-size: 20px; line-height: 1.35; color: #0f172a; font-weight: 800;">
                                {{ $manualSubject }}
                            </h1>
                            <div style="font-size: 14px; line-height: 1.7; color: #334155;">
                                {!! nl2br(e($body)) !!}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-padding" style="padding: 22px 24px; background-color: #0f172a; text-align: center; color: #94a3b8; font-size: 12px; line-height: 1.6;">
                            <div style="font-weight: 700; color: #f8fafc; font-size: 13px; margin-bottom: 4px; letter-spacing: 0.5px;">
                                YC Content Planning &bull; ACI Motors Ltd.
                            </div>
                            <div>
                                This message was sent from the YC Content Planning admin panel.
                            </div>
                            <div style="margin-top: 6px; color: #64748b; font-size: 11px;">
                                &copy; {{ date('Y') }} ACI Motors Ltd. All rights reserved.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Scheduled Events - YC Content Planning</title>
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
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
            .sub-banner-left { display: block !important; width: 100% !important; text-align: left !important; }
            .sub-banner-right { display: block !important; width: 100% !important; text-align: left !important; padding-top: 8px !important; }
            .cta-wrapper { width: 100% !important; max-width: 100% !important; }
            .event-btn-td { display: block !important; width: 100% !important; margin-bottom: 8px !important; }
            .event-btn-link { display: block !important; width: 100% !important; text-align: center !important; box-sizing: border-box !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 20px 0; background-color: #f1f5f9;">
    <!-- Main Outer Wrapper -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 0 8px;">
                <!-- Card Container -->
                <table class="email-container" border="0" cellpadding="0" cellspacing="0" width="600" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06); border: 1px solid #e2e8f0;">
                    
                    <!-- Top Brand Accent Bar (Solid Yamaha Red - 100% Mobile & Outlook Compatible) -->
                    <tr>
                        <td height="5" bgcolor="#d6001c" style="background-color: #d6001c; line-height: 5px; font-size: 5px;">&nbsp;</td>
                    </tr>

                    <!-- Header with Logo -->
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

                    <!-- Notification Banner & Date (Balanced Layout) -->
                    <tr>
                        <td class="email-padding" style="padding: 14px 24px; background-color: #f8fafc; border-bottom: 1px solid #f1f5f9;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td class="sub-banner-left" align="left" style="vertical-align: middle;">
                                        <span style="display: inline-block; background-color: #fee2e2; color: #991b1b; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; padding: 4px 10px; border-radius: 9999px;">
                                            @if(($daysAhead ?? 0) > 0)
                                                Schedule Reminder
                                            @else
                                                Daily Event Notification
                                            @endif
                                        </span>
                                    </td>
                                    <td class="sub-banner-right" align="right" style="vertical-align: middle; color: #64748b; font-size: 12px; font-weight: 600;">
                                        📅 Scheduled: {{ \Carbon\Carbon::parse($targetDate ?? now())->format('l, j F Y') }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Greeting & Intro -->
                    <tr>
                        <td class="email-padding" style="padding: 20px 24px 16px 24px;">
                            <div style="font-size: 18px; font-weight: 700; color: #0f172a; line-height: 1.4; margin-bottom: 6px;">
                                Hello {{ $user->name }}@if(!empty($user->designation)) <span style="display: inline-block; font-size: 14px; font-weight: 600; color: #64748b; margin-left: 2px;">({{ $user->designation }})</span>@endif,
                            </div>
                            @if(($daysAhead ?? 0) > 0)
                            <p style="margin: 0; font-size: 14px; color: #475569; line-height: 1.6;">
                                This is an advance reminder that you have <strong style="color: #0f172a; font-weight: 700;">{{ $events->count() }}</strong> content event(s) scheduled for publication in <strong style="color: #d6001c;">{{ $daysAhead }} days</strong> on <strong style="color: #0f172a;">{{ \Carbon\Carbon::parse($targetDate ?? now())->format('l, j F Y') }}</strong>. Please review and prepare the content below:
                            </p>
                            @else
                            <p style="margin: 0; font-size: 14px; color: #475569; line-height: 1.6;">
                                You have <strong style="color: #0f172a; font-weight: 700;">{{ $events->count() }}</strong> content event(s) scheduled for today. Please review the details below:
                            </p>
                            @endif
                        </td>
                    </tr>

                    <!-- Events List Section -->
                    <tr>
                        <td class="email-padding" style="padding: 0 24px 16px 24px;">
                            @foreach($events as $index => $event)
                                <div style="margin-bottom: 18px; background-color: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid @if(($event->team_type ?? '') == 'product_team') #d6001c @elseif(($event->team_type ?? '') == 'digital_marketing') #2563eb @else #7c3aed @endif; border-radius: 8px; padding: 16px 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                                    
                                    <!-- Event Title & Badges -->
                                    <h3 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 700; color: #0f172a; line-height: 1.35;">
                                        {{ $event->content_title ?? 'Post #'.($event->post_no ?? ($index + 1)) }}
                                    </h3>

                                    <div style="margin-bottom: 10px;">
                                        @if($event->team_type)
                                            <span style="display: inline-block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 3px 8px; border-radius: 4px; background-color: #f1f5f9; color: #334155; margin-right: 6px; margin-bottom: 4px;">
                                                🏷️ {{ str_replace('_', ' ', strtoupper($event->team_type)) }}
                                            </span>
                                        @endif

                                        @if($event->platform)
                                            <span style="display: inline-block; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 4px; background-color: #e0f2fe; color: #0369a1; margin-right: 6px; margin-bottom: 4px;">
                                                🌐 {{ $event->platform }}
                                            </span>
                                        @endif

                                        @if($event->product ?? $event->product_focus)
                                            <span style="display: inline-block; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 4px; background-color: #fef3c7; color: #92400e; margin-bottom: 4px;">
                                                🏍️ {{ $event->product ?? $event->product_focus }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Event Details Table -->
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 13px; line-height: 1.5; color: #475569; border-top: 1px dashed #e2e8f0; padding-top: 8px; margin-top: 6px;">
                                        <tr>
                                            <td width="95" style="vertical-align: top; color: #64748b; font-weight: 600; padding: 4px 0;">Publish Date:</td>
                                            <td style="vertical-align: top; color: #0f172a; font-weight: 700; padding: 4px 0;">
                                                {{ $event->event_date->format('l, F j, Y') }}
                                                @if(($daysAhead ?? 0) > 0)
                                                    <span style="display: inline-block; font-size: 11px; font-weight: 700; color: #d6001c; background-color: #fef2f2; border: 1px solid #fee2e2; padding: 1px 6px; border-radius: 4px; margin-left: 6px;">In {{ $daysAhead }} days</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($event->content_objective)
                                            <tr>
                                                <td width="95" style="vertical-align: top; color: #64748b; font-weight: 600; padding: 4px 0;">Objective:</td>
                                                <td style="vertical-align: top; color: #1e293b; padding: 4px 0;">{{ $event->content_objective }}</td>
                                            </tr>
                                        @endif
                                        @if($event->format)
                                            <tr>
                                                <td width="95" style="vertical-align: top; color: #64748b; font-weight: 600; padding: 4px 0;">Format:</td>
                                                <td style="vertical-align: top; color: #1e293b; padding: 4px 0;">{{ $event->format }}</td>
                                            </tr>
                                        @endif
                                        @if($event->shoot_date)
                                            <tr>
                                                <td width="95" style="vertical-align: top; color: #64748b; font-weight: 600; padding: 4px 0;">Shoot Date:</td>
                                                <td style="vertical-align: top; color: #1e293b; padding: 4px 0;">{{ \Carbon\Carbon::parse($event->shoot_date)->format('M d, Y') }}</td>
                                            </tr>
                                        @endif
                                        @if($event->financial_budget && $event->financial_budget !== '0')
                                            <tr>
                                                <td width="95" style="vertical-align: top; color: #64748b; font-weight: 600; padding: 4px 0;">Fin Budget:</td>
                                                <td style="vertical-align: top; color: #1e293b; padding: 4px 0;">৳{{ $event->financial_budget }}</td>
                                            </tr>
                                        @endif
                                        @if($event->boosting_budget && $event->boosting_budget !== '0')
                                            <tr>
                                                <td width="95" style="vertical-align: top; color: #64748b; font-weight: 600; padding: 4px 0;">Boost Budget:</td>
                                                <td style="vertical-align: top; color: #1e293b; padding: 4px 0;">৳{{ $event->boosting_budget }}</td>
                                            </tr>
                                        @endif
                                    </table>

                                    <!-- Event Card Action Links -->
                                    <div style="margin-top: 12px; padding-top: 10px; border-top: 1px solid #f1f5f9;">
                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td class="event-btn-td" bgcolor="#0f172a" style="background-color: #0f172a; border-radius: 6px;">
                                                    <a class="event-btn-link" href="{{ route('events.show', ['event' => $event->id]) }}" target="_blank" style="display: inline-block; padding: 8px 16px; font-size: 12px; font-weight: 700; color: #ffffff; text-decoration: none; border-radius: 6px;">
                                                        View Event Details &rarr;
                                                    </a>
                                                </td>
                                                @if(!empty($event->drive_link))
                                                    <td width="8">&nbsp;</td>
                                                    <td class="event-btn-td" bgcolor="#eff6ff" style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px;">
                                                        <a class="event-btn-link" href="{{ $event->drive_link }}" target="_blank" style="display: inline-block; padding: 7px 14px; font-size: 12px; font-weight: 600; color: #1d4ed8; text-decoration: none; border-radius: 6px;">
                                                            📁 View Drive Asset
                                                        </a>
                                                    </td>
                                                @endif
                                            </tr>
                                        </table>
                                    </div>

                                </div>
                            @endforeach
                        </td>
                    </tr>

                    <!-- Global CTA Buttons Section (Solid Bulletproof Buttons for Mobile & Outlook) -->
                    <tr>
                        <td class="email-padding" align="center" style="padding: 16px 24px 28px 24px; border-top: 1px solid #f1f5f9; background-color: #fafbfc;">
                            <p style="margin: 0 0 16px 0; font-size: 13px; color: #64748b;">
                                Need to update schedules, upload creative assets, or modify details?
                            </p>
                            <table class="cta-wrapper" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 380px; margin: 0 auto;">
                                <!-- Primary Action: Open Dashboard Calendar (Solid Yamaha Red) -->
                                <tr>
                                    <td align="center" bgcolor="#d6001c" style="background-color: #d6001c; border-radius: 8px;">
                                        <a href="{{ ($baseUrl ?? url('/')) . '/dashboard' }}" target="_blank" style="display: block; width: 100%; padding: 13px 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 14px; font-weight: bold; color: #ffffff; text-decoration: none; text-align: center; border-radius: 8px; box-sizing: border-box;">
                                            Open Dashboard Calendar &rarr;
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
                                </tr>
                                <!-- Secondary Action: Login to Portal -->
                                <tr>
                                    <td align="center" bgcolor="#ffffff" style="background-color: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 8px;">
                                        <a href="{{ ($baseUrl ?? url('/')) . '/login' }}" target="_blank" style="display: block; width: 100%; padding: 11px 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 14px; font-weight: 600; color: #334155; text-decoration: none; text-align: center; border-radius: 8px; box-sizing: border-box;">
                                            Login to Portal
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="email-padding" style="padding: 22px 24px; background-color: #0f172a; text-align: center; color: #94a3b8; font-size: 12px; line-height: 1.6;">
                            <div style="font-weight: 700; color: #f8fafc; font-size: 13px; margin-bottom: 4px; letter-spacing: 0.5px;">
                                YC Content Planning &bull; ACI Motors Ltd.
                            </div>
                            <div>
                                This is an automated daily notification for marketing &amp; product team alignments.
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

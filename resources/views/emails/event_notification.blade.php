<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f5;
            color: #18181b;
            margin: 0;
            padding: 0;
        }
        .container {
            max-w-xl: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-top: 40px;
            margin-bottom: 40px;
        }
        .header {
            text-align: center;
            border-bottom: 1px solid #e4e4e7;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: 900;
            letter-spacing: 1px;
            color: #000;
        }
        h1 {
            font-size: 20px;
            color: #27272a;
            margin-top: 0;
        }
        .event-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
        }
        .event-title {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 5px;
            margin-top: 0;
        }
        .event-meta {
            font-size: 14px;
            color: #64748b;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #a1a1aa;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">YC Content Planning</div>
        </div>
        
        <h1>Hello {{ $user->name }},</h1>
        <p>You have <strong>{{ $events->count() }}</strong> event(s) scheduled for today ({{ now()->format('F j, Y') }}).</p>
        
        <div>
            @foreach($events as $event)
                <div class="event-card">
                    <h3 class="event-title">{{ $event->content_title ?? 'Post #'.$event->post_no }}</h3>
                    <div class="event-meta">
                        @if($event->team_type) <strong>Team:</strong> {{ str_replace('_', ' ', strtoupper($event->team_type)) }}<br> @endif
                        @if($event->content_objective) <strong>Objective:</strong> {{ $event->content_objective }}<br> @endif
                        @if($event->product ?? $event->product_focus) <strong>Product:</strong> {{ $event->product ?? $event->product_focus }}<br> @endif
                        @if($event->platform) <strong>Platform:</strong> {{ $event->platform }}<br> @endif
                        @if($event->drive_link) <strong>Link:</strong> <a href="{{ $event->drive_link }}">View Asset</a><br> @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <p>Please log in to your dashboard to review or manage these events.</p>
        
        <div class="footer">
            &copy; {{ date('Y') }} YC Content Planning. All rights reserved.
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecom-3 API Services</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-primary: #090d16;
            --bg-secondary: #0f172a;
            --accent-glow: rgba(99, 102, 241, 0.15);
            --accent-primary: #6366f1;
            --accent-secondary: #a855f7;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: rgba(255, 255, 255, 0.08);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-main);
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient background glow effects */
        .ambient-glow-1 {
            position: absolute;
            top: -10%;
            left: -10%;
            width: 50%;
            height: 50%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
            z-index: 0;
            pointer-events: none;
        }

        .ambient-glow-2 {
            position: absolute;
            bottom: -10%;
            right: -10%;
            width: 50%;
            height: 50%;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.08) 0%, transparent 70%);
            z-index: 0;
            pointer-events: none;
        }

        .container {
            width: 100%;
            max-width: 900px;
            padding: 40px 20px;
            z-index: 10;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        /* Glassmorphic main card */
        .card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 50px 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        /* Decorative glowing top line */
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary));
        }

        /* Brand / Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, #fff 0%, var(--text-muted) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        .badge-api {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #818cf8;
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Main Info Section */
        .main-info {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        h1 {
            font-size: 48px;
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: -1.5px;
            background: linear-gradient(135deg, #ffffff 30%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p.subtitle {
            font-size: 18px;
            color: var(--text-muted);
            line-height: 1.6;
            max-width: 600px;
        }

        /* Status grid */
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }

        .status-item {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 16px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
        }

        .status-item:hover {
            border-color: rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.04);
            transform: translateY(-2px);
        }

        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #10b981;
            box-shadow: 0 0 10px #10b981;
        }

        .status-label {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-muted);
        }

        .status-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main);
            margin-left: auto;
        }

        /* Action Buttons */
        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }

        .btn {
            padding: 16px 32px;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
            color: #fff;
            border: none;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.35);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.03);
            color: var(--text-main);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        /* Code/API Preview block */
        .code-preview {
            background: #030712;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            color: #38bdf8;
            overflow-x: auto;
            position: relative;
        }

        .code-preview::after {
            content: 'GET /api/v1/status';
            position: absolute;
            top: 12px;
            right: 16px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.3);
        }

        .code-string { color: #34d399; }
        .code-key { color: #f472b6; }
        .code-num { color: #fbbf24; }

        /* Footer */
        .footer {
            text-align: center;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.25);
            margin-top: 10px;
        }

        /* Responsive changes */
        @media(max-width: 768px) {
            .card {
                padding: 30px 20px;
            }
            h1 {
                font-size: 36px;
            }
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            .badge-api {
                align-self: flex-start;
            }
        }
    </style>
</head>
<body>

    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <div class="container">
        <div class="card">
            <div class="header">
                <div class="logo">ECOM-3</div>
                <div class="badge-api">API Engine Active</div>
            </div>

            <div class="main-info">
                <h1>Commerce API Backend</h1>
                <p class="subtitle">The high-performance API backend powering your custom frontends, seller portals, and commerce channels. Optimized for Vue.js and SPA integrations.</p>
            </div>

            <div class="status-grid">
                <div class="status-item">
                    <div class="status-indicator"></div>
                    <div class="status-label">API Gateway</div>
                    <div class="status-value">Online</div>
                </div>
                <div class="status-item">
                    <div class="status-indicator"></div>
                    <div class="status-label">Database</div>
                    <div class="status-value">Connected</div>
                </div>
                <div class="status-item">
                    <div class="status-indicator"></div>
                    <div class="status-label">Admin Core</div>
                    <div class="status-value">Ready</div>
                </div>
            </div>

            <div class="code-preview">
<span class="code-key">{</span>
  <span class="code-key">"status"</span>: <span class="code-string">"success"</span>,
  <span class="code-key">"message"</span>: <span class="code-string">"Ecom-3 API Engine is running"</span>,
  <span class="code-key">"environment"</span>: <span class="code-string">"local"</span>,
  <span class="code-key">"version"</span>: <span class="code-string">"3.0.0-api"</span>,
  <span class="code-key">"services"</span>: <span class="code-key">{</span>
    <span class="code-key">"admin_panel"</span>: <span class="code-string">"/admin/login"</span>,
    <span class="code-key">"rest_api"</span>: <span class="code-string">"/api/v1"</span>
  <span class="code-key">}</span>
<span class="code-key">}</span></div>

            <div class="actions">
                <a href="/admin/login" class="btn btn-primary">
                    Enter Admin Panel &rarr;
                </a>
                <a href="/api/v1/products" class="btn btn-secondary" target="_blank">
                    Explore API Routes
                </a>
            </div>
        </div>

        <div class="footer">
            &copy; 2026 Ecom-3 Engine. All Rights Reserved. Backend Mode Active.
        </div>
    </div>

</body>
</html>

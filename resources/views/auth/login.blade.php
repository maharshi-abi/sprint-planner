<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }

        /* Animated gradient background */
        .bg-animated {
            background: linear-gradient(135deg, #1e1b4b, #312e81, #1e3a5f, #0f172a);
            background-size: 400% 400%;
            animation: gradientShift 12s ease infinite;
        }
        @keyframes gradientShift {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Floating orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.25;
            animation: floatOrb 8s ease-in-out infinite;
        }
        .orb-1 { width: 420px; height: 420px; background: #6366f1; top: -100px; left: -100px; animation-delay: 0s; }
        .orb-2 { width: 320px; height: 320px; background: #8b5cf6; bottom: -80px; right: -60px; animation-delay: 3s; }
        .orb-3 { width: 250px; height: 250px; background: #06b6d4; top: 40%; left: 60%; animation-delay: 6s; }
        @keyframes floatOrb {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(-30px) scale(1.05); }
        }

        /* Grid pattern overlay */
        .grid-pattern {
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* Card slide-up */
        .card-enter {
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(32px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Input focus glow */
        .input-field {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            color: #f1f5f9;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .input-field::placeholder { color: rgba(255,255,255,0.35); }
        .input-field:focus {
            outline: none;
            border-color: rgba(99,102,241,0.8);
            background: rgba(255,255,255,0.10);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.20);
        }

        /* Tick marks floating */
        .tick {
            position: absolute;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.05em;
            color: rgba(255,255,255,0.15);
            white-space: nowrap;
            animation: tickFloat 6s ease-in-out infinite;
        }
        @keyframes tickFloat {
            0%, 100% { transform: translateY(0); opacity: 0.15; }
            50%       { transform: translateY(-10px); opacity: 0.30; }
        }

        /* Btn shine */
        .btn-shine {
            position: relative;
            overflow: hidden;
        }
        .btn-shine::after {
            content: '';
            position: absolute;
            top: 0; left: -75%;
            width: 50%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.18), transparent);
            animation: shine 3s ease-in-out infinite;
        }
        @keyframes shine {
            0% { left: -75%; }
            60%, 100% { left: 125%; }
        }

        /* Checkbox custom */
        input[type="checkbox"] { accent-color: #6366f1; }

        /* Label */
        label { color: rgba(255,255,255,0.65); font-size: 0.8rem; font-weight: 500; letter-spacing: 0.04em; text-transform: uppercase; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-animated relative overflow-hidden">

    <!-- Grid overlay -->
    <div class="absolute inset-0 grid-pattern pointer-events-none"></div>

    <!-- Floating orbs -->
    <div class="orb orb-1 pointer-events-none"></div>
    <div class="orb orb-2 pointer-events-none"></div>
    <div class="orb orb-3 pointer-events-none"></div>

    <!-- Floating code snippets -->
    <div class="tick pointer-events-none" style="top:12%; left:6%; animation-delay:0s;">sprint.start()</div>
    <div class="tick pointer-events-none" style="top:22%; right:8%; animation-delay:2s;">track_time(task)</div>
    <div class="tick pointer-events-none" style="bottom:28%; left:4%; animation-delay:4s;">velocity += 1</div>
    <div class="tick pointer-events-none" style="bottom:15%; right:5%; animation-delay:1s;">report.generate()</div>
    <div class="tick pointer-events-none" style="top:55%; left:8%; animation-delay:3s;">timer.running</div>
    <div class="tick pointer-events-none" style="top:38%; right:3%; animation-delay:5s;">kpi.update()</div>

    <!-- Login card -->
    <div class="relative z-10 w-full max-w-md px-4">
        <div class="card-enter rounded-3xl overflow-hidden shadow-2xl"
             style="background: rgba(15,23,42,0.70); backdrop-filter: blur(28px) saturate(150%); border: 1px solid rgba(255,255,255,0.10);">

            <!-- Top accent bar -->
            <div class="h-1 w-full bg-gradient-to-r from-indigo-500 via-violet-500 to-cyan-500"></div>

            <div class="p-8 sm:p-10">

                <!-- Logo + brand -->
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-bold text-lg leading-tight">{{ config('app.name') }}</p>
                        <p class="text-indigo-300/70 text-xs font-medium">Sprint · Timer · Reports</p>
                    </div>
                </div>

                <!-- Heading -->
                <div class="mb-8">
                    <h1 class="text-white text-3xl font-bold tracking-tight">Welcome back</h1>
                    <p class="text-slate-400 text-sm mt-1.5">Sign in to track your sprints and work sessions.</p>
                </div>

                <!-- Error messages -->
                @if($errors->any())
                <div class="mb-5 rounded-xl px-4 py-3 text-sm font-medium text-rose-300 border border-rose-500/30" style="background:rgba(244,63,94,0.12);">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block mb-2">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email', 'developer@example.com') }}" required autocomplete="email"
                                class="input-field w-full rounded-xl pl-10 pr-4 py-3 text-sm"
                                placeholder="you@example.com">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input id="password" type="password" name="password" value="password" required autocomplete="current-password"
                                class="input-field w-full rounded-xl pl-10 pr-12 py-3 text-sm"
                                placeholder="••••••••">
                            <button type="button" id="togglePwd" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition-colors">
                                <svg id="eyeOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg id="eyeClosed" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember -->
                    <div class="flex items-center gap-2.5">
                        <input id="remember" type="checkbox" name="remember" class="w-4 h-4 rounded">
                        <label for="remember" class="normal-case text-slate-400 text-sm font-normal cursor-pointer" style="text-transform:none; letter-spacing:0; color:rgba(148,163,184,0.8);">Remember me for 30 days</label>
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                        class="btn-shine w-full py-3.5 rounded-xl font-semibold text-white text-sm tracking-wide transition-all duration-200 hover:shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 active:translate-y-0"
                        style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                        Sign in to Dashboard
                    </button>
                </form>

                <!-- Hint -->
                <p class="mt-6 text-xs text-center" style="color:rgba(148,163,184,0.45);">
                    Default credentials: <span style="color:rgba(148,163,184,0.70);">developer@example.com</span> / <span style="color:rgba(148,163,184,0.70);">password</span>
                </p>

            </div><!-- /p-8 -->
        </div><!-- /card -->

        <!-- Footer -->
        <p class="text-center text-xs mt-6" style="color:rgba(255,255,255,0.2);">
            &copy; {{ date('Y') }} {{ config('app.name') }} &mdash; Developer Productivity Suite
        </p>
    </div>

    <script>
        // Password toggle
        const pwd = document.getElementById('password');
        const eyeOpen = document.getElementById('eyeOpen');
        const eyeClosed = document.getElementById('eyeClosed');
        document.getElementById('togglePwd').addEventListener('click', function() {
            const isText = pwd.type === 'text';
            pwd.type = isText ? 'password' : 'text';
            eyeOpen.classList.toggle('hidden', !isText);
            eyeClosed.classList.toggle('hidden', isText);
        });
    </script>
</body>
</html>

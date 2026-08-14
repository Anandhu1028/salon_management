<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>403 - Access Denied | SalonPro</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
<style>
  body {
    background:
      radial-gradient(circle at 15% 20%, rgba(168,85,247,0.35), transparent 40%),
      radial-gradient(circle at 85% 15%, rgba(236,72,153,0.25), transparent 45%),
      radial-gradient(circle at 50% 90%, rgba(147,51,234,0.25), transparent 50%),
      #0b0713;
    font-family: 'Inter', system-ui, sans-serif;
  }
  .glow-line {
    background: linear-gradient(90deg, transparent, #a855f7, #ec4899, transparent);
    height: 1px;
  }
  .card {
    background: rgba(255,255,255,0.97);
    box-shadow: 0 25px 60px -15px rgba(168,85,247,0.45);
  }
  .btn-grad {
    background: linear-gradient(90deg, #7c3aed, #ec4899);
  }
  .icon-ring {
    background: radial-gradient(circle, rgba(236,72,153,0.15), rgba(124,58,237,0.05));
  }
</style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center px-4 relative overflow-hidden">

  <div class="glow-line absolute top-0 left-0 w-full"></div>

  <!-- Brand -->
  <div class="flex items-center gap-3 mb-10">
    <div class="w-10 h-10 rounded-xl btn-grad flex items-center justify-center shadow-lg">
      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M6 18L18 6M9 12a3 3 0 11-6 0 3 3 0 016 0zM21 12a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>
    </div>
    <div>
      <h1 class="text-white font-bold text-lg leading-tight">SalonPro</h1>
      <p class="text-gray-400 text-[10px] tracking-widest uppercase">Management System</p>
    </div>
  </div>

  <!-- Card -->
  <div class="card rounded-3xl px-8 py-10 sm:px-12 sm:py-12 max-w-md w-full text-center">

    <div class="icon-ring mx-auto w-20 h-20 rounded-full flex items-center justify-center mb-6">
      <svg class="w-10 h-10 text-pink-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
      </svg>
    </div>

    <p class="text-xs font-semibold tracking-widest text-pink-500 uppercase mb-2">Error 403</p>
    <h2 class="text-2xl font-bold text-gray-800 mb-3">Access Restricted</h2>
    <p class="text-gray-500 text-sm mb-8 leading-relaxed">
      You don't have permission to view this page
    </p>

    <div class="flex flex-col sm:flex-row gap-3">
      <a href="#" class="flex-1 px-5 py-3 rounded-xl border border-gray-200 text-gray-600 font-medium hover:bg-gray-50 transition text-sm">
        Go Back
      </a>
      <a href="/" class="flex-1 btn-grad px-5 py-3 rounded-xl text-white font-semibold hover:opacity-90 transition text-sm flex items-center justify-center gap-2">
        Dashboard
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
        </svg>
      </a>
    </div>
  </div>

  <p class="text-gray-500 text-xs mt-8">&copy; 2026 SalonPro. All rights reserved.</p>

</body>
</html>
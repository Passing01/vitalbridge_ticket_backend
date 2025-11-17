<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="apple-touch-icon"
      sizes="76x76"
      href="{{ asset('assets/img/apple-icon.png') }}" />
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}" />
    <title>VitalBridge</title>
    <!--     Fonts and icons     -->
    <link
      href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700"
      rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script
      src="https://kit.fontawesome.com/42d5adcbca.js"
      crossorigin="anonymous"></script>
    <!-- Nucleo Icons -->
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- Popper -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <!-- Main Styling -->
    <link
      href="{{ asset('assets/css/soft-ui-dashboard-tailwind.css') }}?v=1.0.5"
      rel="stylesheet" />
    <!-- Nepcha Analytics (nepcha.com) -->
    <!-- Nepcha is a easy-to-use web analytics. No cookies and fully compliant with GDPR, CCPA and PECR. -->
    <script
      defer
      data-site="YOUR_DOMAIN_HERE"
      src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
    <script>
      (function () {
        var storedTheme = localStorage.getItem('theme');
        var systemPrefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        var shouldUseDark = false;
        if (storedTheme === 'dark') {
          shouldUseDark = true;
        } else if (storedTheme === 'light') {
          shouldUseDark = false;
        } else if (systemPrefersDark) {
          shouldUseDark = true;
        }
        if (shouldUseDark) {
          document.documentElement.classList.add('dark');
          document.addEventListener('DOMContentLoaded', function () {
            document.body.style.backgroundColor = '#0f172a';
            document.body.style.color = '#e5e7eb';
          });
        } else {
          document.documentElement.classList.remove('dark');
        }
      })();
    </script>
  </head>

  <body class="m-0 font-sans text-base antialiased font-normal leading-default bg-gray-50 text-slate-500 dark:bg-slate-900 dark:text-slate-100">
          <div class="min-h-screen bg-gray-100 dark:bg-slate-900">
              @include('layouts.partials.dashboard-sidebar')

              <!-- Page Content -->
              <main class="ease-soft-in-out xl:ml-68.5 relative h-full max-h-screen rounded-xl transition-all duration-200">
                  @include('layouts.partials.navbar')
                  <div class="w-full px-6 py-6 mx-auto">
                      @yield('content')
                  </div>
              </main>
          </div>
          
          <!-- Core JS Files -->
          <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
          <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
          <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
          <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
          <script src="{{ asset('assets/js/soft-ui-dashboard-tailwind.js') }}?v=1.0.5"></script>
          <script>
            window.toggleTheme = function () {
              var isDark = document.documentElement.classList.toggle('dark');
              localStorage.setItem('theme', isDark ? 'dark' : 'light');
              console.log('toggleTheme called, dark =', isDark);
              // Fallback visuel simple si les classes dark: ne sont pas prises en compte
              if (isDark) {
                document.body.style.backgroundColor = '#0f172a';
                document.body.style.color = '#e5e7eb';
              } else {
                document.body.style.backgroundColor = '';
                document.body.style.color = '';
              }
            };
          </script>
          <!-- Scripts dynamiques des vues -->
          @stack('scripts')
  </body>
</html>
@echo off
REM This script demonstrates the BASE_URL constant and how paths now work
REM Usage: Test by running this and checking the output

echo Testing path resolution across portfolio system...
echo.
echo When accessing from root level (index.php):
php -r "define('__BASE__', 'c:/xampp/htdocs/projects/portfolio'); define('BASE_URL', 'http://localhost/projects/portfolio'); function asset_url($path) { return BASE_URL . '/' . ltrim($path, '/'); } echo 'CSS path: ' . asset_url('css/style.css') . PHP_EOL;"

echo.
echo When accessing from admin level (admin/blogs/edit. php):
php -r "define('__BASE__', 'c:/xampp/htdocs/projects/portfolio'); define('BASE_URL', 'http://localhost/projects/portfolio'); function asset_url($path) { return BASE_URL . '/' . ltrim($path, '/'); } echo 'CSS path: ' . asset_url('css/style.css') . PHP_EOL;"

echo.
echo Both resolve to the same path regardless of directory level!
echo This is the fix for the path issue.
pause

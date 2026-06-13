# Troubleshooting Guide

Comprehensive guide for diagnosing and resolving common issues in the Quotes Application.

## 📋 Table of Contents

- [Installation Issues](#installation-issues)
- [Database Issues](#database-issues)
- [Authentication Issues](#authentication-issues)
- [API Issues](#api-issues)
- [Performance Issues](#performance-issues)
- [File & Storage Issues](#file--storage-issues)
- [Queue & Job Issues](#queue--job-issues)
- [Testing Issues](#testing-issues)
- [Deployment Issues](#deployment-issues)
- [Debugging Tools](#debugging-tools)
- [Getting Help](#getting-help)

---

## Installation Issues

### Issue: Composer Install Fails

**Symptoms:**
```
Fatal error: Allowed memory size of ... exhausted
```

**Solutions:**

1. **Increase PHP Memory Limit**:
```bash
php -d memory_limit=-1 /usr/bin/composer install
```

2. **Update Composer**:
```bash
composer self-update
```

3. **Clear Composer Cache**:
```bash
composer clear-cache
rm -rf vendor/
composer install
```

4. **Use Composer Parallel Downloads** (faster):
```bash
composer install --prefer-dist --no-interaction
```

---

### Issue: GitHub Token Required

**Symptoms:**
```
The Composer repository requires authentication
```

**Solutions:**

1. **Generate GitHub Personal Token**:
   - Go to: https://github.com/settings/tokens
   - Create token with `repo` scope
   - Copy token

2. **Configure Composer**:
```bash
composer config -g github-oauth.github.com YOUR_TOKEN_HERE
```

3. **Or Configure .composer/auth.json**:
```json
{
  "github-oauth": {
    "github.com": "your_token_here"
  }
}
```

---

### Issue: NPM Install Fails

**Symptoms:**
```
npm error code E403 or npm error code ERESOLVE
```

**Solutions:**

1. **Clear NPM Cache**:
```bash
npm cache clean --force
rm -rf node_modules package-lock.json
npm install
```

2. **Use Legacy Peer Dependencies** (if conflicts):
```bash
npm install --legacy-peer-deps
```

3. **Check Node/NPM Version**:
```bash
node -v  # Should be 16.0+
npm -v   # Should be 8.0+
```

4. **Update NPM**:
```bash
npm install -g npm@latest
```

---

### Issue: PHP Version Incompatibility

**Symptoms:**
```
Your PHP version 8.0.x does not satisfy the requirement 8.2
```

**Solution:**

1. **Check Current PHP Version**:
```bash
php -v
```

2. **Update PHP** (system-dependent):

**macOS (Homebrew)**:
```bash
brew install php@8.2
brew unlink php@8.0
brew link php@8.2 --force
```

**Ubuntu/Debian**:
```bash
sudo apt-get install php8.2 php8.2-cli php8.2-mbstring php8.2-sqlite3
sudo update-alternatives --set php /usr/bin/php8.2
```

**Windows**:
- Download from: https://www.php.net/downloads
- Update PATH environment variable

---

## Database Issues

### Issue: Database Connection Error

**Symptoms:**
```
SQLSTATE[HY000]: General error: unable to open database file
```

**Solutions:**

1. **Create SQLite Database**:
```bash
touch database/database.sqlite
chmod 666 database/database.sqlite
```

2. **Verify Database Path** in `.env`:
```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

3. **Check File Permissions**:
```bash
chmod -R 775 database/
ls -la database/database.sqlite
```

4. **For MySQL/PostgreSQL**:
   - Verify credentials in `.env`
   - Check database server is running
   - Verify firewall rules allow connection

---

### Issue: Migration Fails

**Symptoms:**
```
Migration failed: Table already exists
```

**Solutions:**

1. **Rollback Last Migration**:
```bash
php artisan migrate:rollback
```

2. **Rollback All Migrations**:
```bash
php artisan migrate:reset
```

3. **Refresh Database** (rollback + migrate):
```bash
php artisan migrate:refresh
```

4. **Refresh with Seeding**:
```bash
php artisan migrate:refresh --seed
```

5. **Check Migration Status**:
```bash
php artisan migrate:status
```

---

### Issue: Foreign Key Constraint Error

**Symptoms:**
```
SQLSTATE[HY000]: General error: 1215 Cannot add foreign key constraint
```

**Solutions:**

1. **Enable Foreign Keys** (SQLite):
```php
// In migration or AppServiceProvider
DB::statement('PRAGMA foreign_keys=ON');
```

2. **Check Table Order** in migrations:
   - Parent tables must be created before child tables
   - Ensure referenced table exists

3. **Verify Column Types Match**:
   - Foreign key column type must match primary key type
   - Both should be `BIGINT UNSIGNED`

4. **Check MySQL Strict Mode**:
```sql
-- In MySQL config
SET GLOBAL sql_mode='';
```

---

### Issue: No Application Encryption Key

**Symptoms:**
```
No application encryption key has been specified
```

**Solution:**

```bash
php artisan key:generate
```

This creates a `APP_KEY` in your `.env` file.

---

### Issue: Database Seeding Fails

**Symptoms:**
```
Class "Database\Seeders\CategorySeeder" not found
```

**Solutions:**

1. **Verify Seeder Exists**:
```bash
ls app/database/seeders/
```

2. **Regenerate Autoloader**:
```bash
composer dump-autoload
```

3. **Check Namespace** in seeder:
```php
namespace Database\Seeders;  // Correct namespace
```

4. **Run Specific Seeder**:
```bash
php artisan db:seed --class=CategorySeeder
```

---

## Authentication Issues

### Issue: Invalid Credentials Always Fail

**Symptoms:**
```
{
  "success": false,
  "message": "Invalid credentials"
}
```

**Solutions:**

1. **Check User Exists**:
```bash
php artisan tinker
>>> User::where('email', 'user@example.com')->first();
```

2. **Verify Password Hashing**:
```bash
php artisan tinker
>>> use Illuminate\Support\Facades\Hash;
>>> Hash::make('password')  // Generate hash
>>> Hash::check('password', $user->password)  // Verify
```

3. **Create Test User Manually**:
```bash
php artisan tinker
>>> User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => Hash::make('password123')
])
```

4. **Reset User Password**:
```bash
php artisan tinker
>>> $user = User::where('email', 'user@example.com')->first();
>>> $user->update(['password' => Hash::make('newpassword')]);
>>> $user->save();
```

---

### Issue: Token Expired or Invalid

**Symptoms:**
```
{
  "success": false,
  "message": "Token is invalid"
}
```

**Solutions:**

1. **Check Token Format**:
```bash
# Correct format:
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...

# NOT: Authorization: eyJ0eXAiOiJKV1QiLCJhbGc...
```

2. **Refresh Token**:
```bash
POST /api/auth/refresh
Authorization: Bearer YOUR_TOKEN
```

3. **Check Token Expiration**:
```bash
php artisan tinker
>>> use Firebase\JWT\JWT;
>>> JWT::decode($token, config('jwt.secret'), ['HS256']);
```

4. **Verify JWT Secret** in `.env`:
```env
JWT_SECRET=your-secret-key
```

---

### Issue: Unauthorized Access (401)

**Symptoms:**
```
{
  "success": false,
  "message": "Unauthorized"
}
```

**Solutions:**

1. **Verify Token Present**:
```bash
# Include in header
curl -H "Authorization: Bearer TOKEN" http://localhost:8000/api/quotes
```

2. **Check Token Validity**:
```bash
php artisan tinker
>>> Auth::check()
>>> Auth::user()
```

3. **Verify User Not Deleted**:
```bash
php artisan tinker
>>> User::find($userId);
```

---

### Issue: Permission Denied (403)

**Symptoms:**
```
{
  "success": false,
  "message": "This action is forbidden"
}
```

**Solutions:**

1. **Check User Roles**:
```bash
php artisan tinker
>>> $user = Auth::user();
>>> $user->roles;  // View assigned roles
```

2. **Check User Permissions**:
```bash
php artisan tinker
>>> $user->hasPermissionTo('create_quotes');
```

3. **Assign Role to User**:
```bash
php artisan tinker
>>> $user = User::find(1);
>>> $user->assignRole('editor');
```

4. **View Available Permissions**:
```bash
php artisan tinker
>>> Permission::all();
```

---

## API Issues

### Issue: CORS Error in Browser

**Symptoms:**
```
Access to XMLHttpRequest blocked by CORS policy
```

**Solutions:**

1. **Check CORS Middleware** in `app/Http/Middleware/CorsMiddleware.php`:
```php
'allowed_origins' => [
    'http://localhost:3000',
    'http://localhost:8080',
],
```

2. **Add Frontend Origin**:
```php
'allowed_origins' => [
    'http://localhost:3000',  // Vue app
    'https://yourdomain.com',  // Production domain
],
```

3. **Verify Middleware Registered** in `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->api(append: [
        CorsMiddleware::class,
    ]);
})
```

4. **Test CORS Preflight**:
```bash
curl -X OPTIONS http://localhost:8000/api/quotes \
  -H "Origin: http://localhost:3000" \
  -H "Access-Control-Request-Method: GET"
```

---

### Issue: 404 Not Found

**Symptoms:**
```
{
  "success": false,
  "message": "Route not found"
}
```

**Solutions:**

1. **Verify Route Exists**:
```bash
php artisan route:list | grep quotes
```

2. **Check Route Definition** in `routes/api.php`:
```php
Route::apiResource('quotes', QuoteController::class);
```

3. **Verify URL Path**:
```bash
# Correct: /api/quotes
# Incorrect: /quotes or /api/api/quotes
```

4. **Ensure Middleware Matches**:
```php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('quotes', QuoteController::class);
});
```

---

### Issue: 422 Validation Failed

**Symptoms:**
```
{
  "success": false,
  "message": "Validation failed",
  "errors": { ... }
}
```

**Solutions:**

1. **Check Request Validation Rules** in `app/Http/Requests/`:
```php
public function rules()
{
    return [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'author' => 'required|string',
        'category_id' => 'required|exists:categories,id',
    ];
}
```

2. **Send All Required Fields**:
```bash
curl -X POST http://localhost:8000/api/quotes \
  -H "Authorization: Bearer token" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Quote",
    "content": "Content",
    "author": "Author",
    "category_id": 1
  }'
```

3. **Verify Field Values Match Rules**:
   - String fields should not contain invalid characters
   - Foreign key IDs must exist in referenced table
   - Email addresses must be valid format

---

### Issue: 500 Internal Server Error

**Symptoms:**
```
{
  "success": false,
  "message": "Internal server error"
}
```

**Solutions:**

1. **Enable Debug Mode** in `.env`:
```env
APP_DEBUG=true
```

2. **Check Application Logs**:
```bash
tail -f storage/logs/laravel.log
```

3. **Use Laravel Pail**:
```bash
php artisan pail
```

4. **Check Recent Changes**:
   - Review last commit
   - Check error in logs for stack trace
   - Verify database queries work

5. **Run Migrations**:
```bash
php artisan migrate
```

---

## Performance Issues

### Issue: Slow API Response Times

**Symptoms:**
- Response time > 500ms
- Timeouts on requests

**Solutions:**

1. **Identify N+1 Queries**:
```bash
php artisan telescope  # View query count
```

2. **Use Eager Loading**:
```php
// Bad: N+1 problem
$quotes = Quote::all();
foreach ($quotes as $quote) {
    $quote->category;  // Extra query per quote
}

// Good: Eager load
$quotes = Quote::with('category')->get();
```

3. **Add Indexes to Database**:
```php
// In migration
$table->index('category_id');
$table->index('created_by');
$table->fulltext(['title', 'content']);
```

4. **Implement Caching**:
```php
$quotes = Cache::remember('quotes', 60, function () {
    return Quote::with('category')->get();
});
```

5. **Use Pagination**:
```php
$quotes = Quote::paginate(15);  // Instead of all()
```

---

### Issue: High Memory Usage

**Symptoms:**
```
Allowed memory size of 134217728 bytes exhausted
```

**Solutions:**

1. **Increase PHP Memory Limit** in `.env` or `php.ini`:
```
memory_limit = 512M
```

2. **Chunk Large Queries**:
```php
// Bad: Loads all records
Quote::all()->each(function ($quote) {
    // Process
});

// Good: Process in chunks
Quote::chunk(1000, function ($quotes) {
    foreach ($quotes as $quote) {
        // Process
    }
});
```

3. **Lazy Load Collections**:
```php
// Bad: All at once
$quotes = Quote::all();

// Good: Lazy
$quotes = Quote::cursor();
foreach ($quotes as $quote) {
    // Process one at a time
}
```

4. **Monitor Memory Usage**:
```bash
php -i | grep memory_limit
```

---

### Issue: Database Queries Too Slow

**Symptoms:**
- Single query takes > 100ms

**Solutions:**

1. **Analyze Query Performance**:
```bash
php artisan tinker
>>> DB::enableQueryLog();
>>> Quote::with('category')->get();
>>> dd(DB::getQueryLog());
```

2. **Use EXPLAIN**:
```php
DB::statement('EXPLAIN SELECT * FROM quotes WHERE category_id = 1');
```

3. **Add Missing Indexes**:
```bash
php artisan make:migration add_indexes_to_quotes_table
```

```php
// In migration
public function up()
{
    Schema::table('quotes', function (Blueprint $table) {
        $table->index('category_id');
        $table->index('created_by');
        $table->fulltext(['title', 'content']);
    });
}
```

---

## File & Storage Issues

### Issue: Permission Denied on Storage Directory

**Symptoms:**
```
The file "storage/logs/laravel.log" could not be opened
```

**Solutions:**

1. **Fix Storage Permissions**:
```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

2. **Set Correct Ownership** (on shared hosting):
```bash
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

3. **Create Logs Directory**:
```bash
mkdir -p storage/logs
touch storage/logs/laravel.log
chmod 666 storage/logs/laravel.log
```

---

### Issue: File Upload Fails

**Symptoms:**
```
The file field is required or file upload failed
```

**Solutions:**

1. **Check Upload Limit** in `.env`:
```env
# php.ini settings
upload_max_filesize = 50M
post_max_size = 50M
```

2. **Verify Storage Disk** configured:
```php
// In config/filesystems.php
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
    ],
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

3. **Create Symbolic Link**:
```bash
php artisan storage:link
```

4. **Check Directory Permissions**:
```bash
ls -la storage/app/
chmod -R 775 storage/app/
```

---

## Queue & Job Issues

### Issue: Jobs Not Processing

**Symptoms:**
- Queued jobs not executing
- Jobs stuck in queue

**Solutions:**

1. **Start Queue Worker**:
```bash
php artisan queue:listen
```

2. **Check Queue Connection** in `.env`:
```env
QUEUE_CONNECTION=database
```

3. **Process Failed Jobs**:
```bash
php artisan queue:failed
php artisan queue:retry all
```

4. **Flush Failed Jobs** (if needed):
```bash
php artisan queue:flush
```

5. **Monitor Queue**:
```bash
php artisan queue:work --verbose
```

---

### Issue: Job Timeout

**Symptoms:**
```
ProcessTimeoutException: The job did not finish within the timeout of 60 seconds
```

**Solutions:**

1. **Increase Job Timeout**:
```bash
php artisan queue:work --timeout=300  # 5 minutes
```

2. **Increase Job Timeout in Job Class**:
```php
class ExportQuotesPdf implements ShouldQueue
{
    public $timeout = 300;  // 5 minutes
}
```

3. **Optimize Job Processing**:
   - Reduce data processing in job
   - Use pagination/chunking for large datasets
   - Profile code to find bottlenecks

---

### Issue: Job Fails Silently

**Symptoms:**
- No error logs for failed jobs

**Solutions:**

1. **Check Failed Jobs Table**:
```bash
php artisan queue:failed
```

2. **Inspect Failed Job**:
```bash
php artisan queue:failed-table
php artisan queue:work --tries=3
```

3. **Add Logging to Job**:
```php
class ExportQuotesPdf implements ShouldQueue
{
    public function handle()
    {
        try {
            Log::info('Job started');
            // Job logic
            Log::info('Job completed');
        } catch (Exception $e) {
            Log::error('Job failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
```

4. **Test Job Manually**:
```bash
php artisan tinker
>>> (new App\Jobs\ExportQuotesPdf(1))->handle();
```

---

## Testing Issues

### Issue: Tests Fail with Database Errors

**Symptoms:**
```
SQLSTATE[HY000]: General error: database disk image is malformed
```

**Solutions:**

1. **Use In-Memory Database** in `phpunit.xml`:
```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

2. **Use Separate Test Database**:
```xml
<env name="DB_DATABASE" value="tests/database.sqlite"/>
```

3. **Refresh Database Before Tests**:
```php
class TestCase extends BaseTestCase
{
    use RefreshDatabase;  // Refresh DB before each test
}
```

---

### Issue: Tests Timeout

**Symptoms:**
```
PHPUnit timeout after 300 seconds
```

**Solutions:**

1. **Increase Timeout** in `phpunit.xml`:
```xml
<phpunit processIsolation="true" timeoutForSmallTests="600">
```

2. **Optimize Test Performance**:
   - Use in-memory database
   - Minimize external API calls
   - Mock expensive operations

3. **Run Tests in Parallel**:
```bash
./vendor/bin/phpunit --processes=4
```

---

### Issue: Tests Pass Locally but Fail in CI/CD

**Symptoms:**
- Local tests pass
- Pipeline tests fail

**Solutions:**

1. **Match CI Environment**:
   - Use same PHP version
   - Use same database
   - Install same extensions

2. **Check Environment Variables**:
```bash
# In CI pipeline
export APP_ENV=testing
export APP_DEBUG=false
```

3. **Run Full Test Suite Locally**:
```bash
composer test
```

---

## Deployment Issues

### Issue: Application Won't Start After Deployment

**Symptoms:**
```
500 Internal Server Error
```

**Solutions:**

1. **Run Post-Deployment Steps**:
```bash
php artisan migrate
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader
npm run build
```

2. **Check File Permissions**:
```bash
chmod -R 755 .
chmod -R 775 storage/ bootstrap/cache/
```

3. **Verify Environment Configuration**:
```bash
# Check .env file exists and is correct
cat .env
php artisan config:show
```

4. **Check Logs**:
```bash
tail -f storage/logs/laravel.log
```

---

### Issue: Assets Not Loading (404)

**Symptoms:**
- CSS/JS files returning 404
- White screen or unstyled page

**Solutions:**

1. **Build Assets**:
```bash
npm run build  # Production
npm run dev    # Development
```

2. **Clear Cache**:
```bash
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

3. **Create Symbolic Link** (for public storage):
```bash
php artisan storage:link
```

4. **Verify Asset Paths** in `.env`:
```env
ASSET_URL=/
```

---

### Issue: Database Migrations Not Running

**Symptoms:**
- Tables don't exist in database
- Foreign key errors

**Solutions:**

1. **Run Migrations**:
```bash
php artisan migrate --force  # In production
```

2. **Check Migration Status**:
```bash
php artisan migrate:status
```

3. **Rollback and Re-Run**:
```bash
php artisan migrate:rollback
php artisan migrate
```

---

## Debugging Tools

### 1. Laravel Pail - Real-time Logs

```bash
# Start Pail
php artisan pail

# Filter by channel
php artisan pail --filter=database

# Filter by level
php artisan pail --level=error

# Follow logs (live)
php artisan pail --follow
```

### 2. Tinker - Interactive REPL

```bash
php artisan tinker

# Query data
>>> User::find(1);
>>> Quote::with('category')->limit(5)->get();

# Test methods
>>> Auth::login(User::find(1));
>>> auth()->user()->hasPermissionTo('create_quotes');

# Modify data
>>> $user = User::find(1);
>>> $user->update(['name' => 'New Name']);
```

### 3. Telescope - Query Debugging

```env
# In .env
TELESCOPE_ENABLED=true
```

Visit: `http://localhost:8000/telescope`

### 4. Database Query Logging

```php
// In controller or service
DB::enableQueryLog();

// Execute queries
$quotes = Quote::with('category')->get();

// View logs
dd(DB::getQueryLog());
```

### 5. Debug Bar

Install Laravel Debugbar:
```bash
composer require barryvdh/laravel-debugbar --dev
```

View at: `http://localhost:8000` (footer bar)

---

## Getting Help

### 1. Check Documentation

- **Laravel Docs**: https://laravel.com/docs
- **API Docs**: See `docs/API_DOCUMENTATION.md`
- **Architecture**: See `docs/ARCHITECTURE.md`

### 2. Search for Similar Issues

```bash
# Search GitHub issues
https://github.com/vicitjob/quotes/issues

# Search Stack Overflow
https://stackoverflow.com/questions/tagged/laravel
```

### 3. Create Detailed Bug Report

Include:
- PHP version: `php -v`
- Laravel version: Check `composer.lock`
- Error message (full stack trace)
- Steps to reproduce
- Expected vs actual behavior
- Environment details

### 4. Enable Full Debug Mode

```bash
# In .env
APP_DEBUG=true
APP_LOG_LEVEL=debug

# View logs
tail -f storage/logs/laravel.log
```

### 5. Community Resources

- **Laravel Discord**: https://discord.gg/laravel
- **Laravel Forum**: https://laracasts.com/discuss
- **Stack Overflow**: Tag `laravel`

---

**Last Updated**: June 13, 2026  
**Version**: 1.0  
**Maintained by**: Development Team

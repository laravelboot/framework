<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/17 10:48
 * @version
 */

return [
    'name'            => env('APP_NAME', 'My Application'),
    'env'             => env('APP_ENV', 'production'),
    'debug'           => env('APP_DEBUG', true),
    'url'             => env('APP_URL', 'http://localhost'),
    'timezone'        => env('APP_TIMEZONE', 'PRC'),
    'locale'          => env('APP_LOCALE', 'zh-cn'),
    'fallback_locale' => env('APP_LOCALE_FALLBACK', 'zh-cn'),
    'key'             => env('APP_KEY'),
    'cipher'          => 'AES-256-CBC',
    'log'             => env('APP_LOG', 'daily'),
    'log_level'       => env('APP_LOG_LEVEL', 'debug'),
    'providers'       => [
        //LaravelBoot\Foundation\Attachment\AttachmentServiceProvider::class,
        LaravelBoot\Foundation\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        //LaravelBoot\Foundation\Bus\BusServiceProvider::class,
        //LaravelBoot\Foundation\Cache\CacheServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        LaravelBoot\Foundation\Composer\ComposerServiceProvider::class,
        LaravelBoot\Foundation\Console\ConsoleServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        LaravelBoot\Foundation\Database\DatabaseServiceProvider::class,
        //LaravelBoot\Foundation\Debug\DebugServiceProvider::class,
        //LaravelBoot\Foundation\Editor\EditorServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        //LaravelBoot\Foundation\Extension\ExtensionServiceProvider::class,
        LaravelBoot\Foundation\Flow\FlowServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        //LaravelBoot\Installer\InstallerServiceProvider::class,
        //LaravelBoot\Foundation\Image\ImageServiceProvider::class,
        //LaravelBoot\Foundation\Mail\MailServiceProvider::class,
        //LaravelBoot\Foundation\Member\MemberServiceProvider::class,
        LaravelBoot\Foundation\Database\MigrationServiceProvider::class,
        LaravelBoot\Foundation\Module\ModuleServiceProvider::class,
        //LaravelBoot\Foundation\Navigation\NavigationServiceProvider::class,
        //LaravelBoot\Foundation\Notification\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        LaravelBoot\Foundation\Passport\PassportServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        LaravelBoot\Foundation\Permission\PermissionServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        //LaravelBoot\Foundation\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        //LaravelBoot\Foundation\SearchEngine\SearchEngineServiceProvider::class,
        //LaravelBoot\Foundation\Session\SessionServiceProvider::class,
        LaravelBoot\Foundation\Setting\SettingServiceProvider::class,
        LaravelBoot\Foundation\Theme\ThemeServiceProvider::class,
        LaravelBoot\Foundation\Translation\TranslationServiceProvider::class,
        LaravelBoot\Foundation\Validation\ValidationServiceProvider::class,
        LaravelBoot\Foundation\Yaml\YamlServiceProvider::class,
        LaravelBoot\Foundation\Http\HttpServiceProvider::class,
        LaravelBoot\Foundation\Network\NetworkServiceProvider::class
        //LaravelBoot\Foundation\Administration\AdministrationServiceProvider::class,
    ],
    'aliases'         => [
        'App'          => Illuminate\Support\Facades\App::class,
        'Artisan'      => Illuminate\Support\Facades\Artisan::class,
        'Auth'         => Illuminate\Support\Facades\Auth::class,
        'Blade'        => Illuminate\Support\Facades\Blade::class,
        'Cache'        => Illuminate\Support\Facades\Cache::class,
        'Config'       => Illuminate\Support\Facades\Config::class,
        'Cookie'       => Illuminate\Support\Facades\Cookie::class,
        'Crypt'        => Illuminate\Support\Facades\Crypt::class,
        'DB'           => Illuminate\Support\Facades\DB::class,
        'Eloquent'     => Illuminate\Database\Eloquent\Model::class,
        'Event'        => Illuminate\Support\Facades\Event::class,
        'File'         => Illuminate\Support\Facades\File::class,
        'Gate'         => Illuminate\Support\Facades\Gate::class,
        'Hash'         => Illuminate\Support\Facades\Hash::class,
        'Lang'         => Illuminate\Support\Facades\Lang::class,
        'Log'          => Illuminate\Support\Facades\Log::class,
        'Mail'         => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password'     => Illuminate\Support\Facades\Password::class,
        'Queue'        => Illuminate\Support\Facades\Queue::class,
        'Redirect'     => Illuminate\Support\Facades\Redirect::class,
        'Redis'        => Illuminate\Support\Facades\Redis::class,
        'Request'      => Illuminate\Support\Facades\Request::class,
        'Response'     => Illuminate\Support\Facades\Response::class,
        'Route'        => Illuminate\Support\Facades\Route::class,
        'Schema'       => Illuminate\Support\Facades\Schema::class,
        'Session'      => Illuminate\Support\Facades\Session::class,
        'Storage'      => Illuminate\Support\Facades\Storage::class,
        'URL'          => Illuminate\Support\Facades\URL::class,
        'Validator'    => Illuminate\Support\Facades\Validator::class,
        'View'         => Illuminate\Support\Facades\View::class,
    ],
];

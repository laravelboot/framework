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
namespace LaravelBoot\Foundation\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Yaml\Yaml;

/**
 * Class KeyGenerateCommand.
 */
class KeyGenerateCommand extends Command
{
    /**
     * @var string
     */
    protected $description = 'Set the application key';

    /**
     * @var \LaravelBoot\Foundation\Application
     */
    protected $laravel;

    /**
     * @var string
     */
    protected $signature = 'key:generate {--show : Display the key instead of modifying files}';

    /**
     * Command handler.
     *
     * @return bool
     */
    public function fire()
    {
        $key = $this->generateRandomKey();
        if ($this->option('show')) {
            $this->line('<comment>' . $key . '</comment>');

            return false;
        }
        $this->setKeyInEnvironmentFile($key);
        $this->laravel['config']['app.key'] = $key;
        $this->info("Application key [$key] set successfully.");

        return true;
    }

    /**
     * Set the application key in the environment file.
     *
     * @param string $key
     */
    protected function setKeyInEnvironmentFile($key)
    {
        $path = $this->laravel->environmentFilePath();

        file_exists($path) || touch($path);

        $environments = new Collection($this->laravel->make(Yaml::class)->parse(file_get_contents($path)));
        $environments->put('APP_KEY', $key);

        file_put_contents($path, $this->laravel->make(Yaml::class)->dump($environments->toArray()));
    }

    /**
     * Generate a random key for the application.
     *
     * @return string
     */
    protected function generateRandomKey()
    {
        return 'base64:' . base64_encode(random_bytes($this->laravel['config']['app.cipher'] == 'AES-128-CBC' ? 16 : 32));
    }
}

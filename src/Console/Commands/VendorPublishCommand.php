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
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\MountManager;

/**
 * Class VendorPublishCommand.
 */
class VendorPublishCommand extends Command
{
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var string
     */
    protected $signature = 'vendor:publish {--force : Overwrite any existing files.}
            {--provider= : The service provider that has assets you want to publish.}
            {--tag=* : One or many tags that have assets you want to publish.}';

    /**
     * @var string
     */
    protected $description = 'Publish any publishable assets from vendor packages';

    /**
     * VendorPublishCommand constructor.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Command handler.
     */
    public function fire()
    {
        $tags = $this->option('tag');
        $tags = $tags ?: [null];
        foreach ((array)$tags as $tag) {
            $this->publishTag($tag);
        }
    }

    /**
     * Publishes the assets for a tag.
     *
     * @param string $tag
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function publishTag($tag)
    {
        $paths = ServiceProvider::pathsToPublish($this->option('provider'), $tag);
        if (empty($paths)) {
            return $this->comment("Nothing to publish for tag [{$tag}].");
        }
        foreach ($paths as $from => $to) {
            if ($this->files->isFile($from)) {
                $this->publishFile($from, $to);
            } elseif ($this->files->isDirectory($from)) {
                $this->publishDirectory($from, $to);
            } else {
                $this->error("Can't locate path: <{$from}>");
            }
        }
        $this->info("Publishing complete for tag [{$tag}]!");
    }

    /**
     * Publish the file to the given path.
     *
     * @param string $from
     * @param string $to
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function publishFile($from, $to)
    {
        if ($this->files->exists($to) && !$this->option('force')) {
            return;
        }
        $this->createParentDirectory(dirname($to));
        $this->files->copy($from, $to);
        $this->status($from, $to, 'File');
    }

    /**
     * Publish the directory to the given directory.
     *
     * @param $from
     * @param $to
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function publishDirectory($from, $to)
    {
        $manager = new MountManager([
            'from' => new Flysystem(new LocalAdapter($from)),
            'to'   => new Flysystem(new LocalAdapter($to)),
        ]);
        foreach ($manager->listContents('from://', true) as $file) {
            if ($file['type'] === 'file' && (!$manager->has('to://' . $file['path']) || $this->option('force'))) {
                $manager->put('to://' . $file['path'], $manager->read('from://' . $file['path']));
            }
        }
        $this->status($from, $to, 'Directory');
    }

    /**
     * Create the directory to house the published files if needed.
     *
     * @param $directory
     */
    protected function createParentDirectory($directory)
    {
        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }
    }

    /**
     * Write a status message to the console.
     *
     * @param $from
     * @param $to
     * @param $type
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function status($from, $to, $type)
    {
        $from = str_replace(base_path(), '', realpath($from));
        $to = str_replace(base_path(), '', realpath($to));
        $this->line('<info>Copied ' . $type . '</info> <comment>[' . $from . ']</comment> <info>To</info> <comment>[' . $to . ']</comment>');
    }
}

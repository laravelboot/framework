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
namespace LaravelBoot\Foundation\Module;

/**
 * Class Module.
 */
class Module
{
    /**
     * @var array
     */
    protected $alias;

    /**
     * @var string|array
     */
    protected $author;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string
     */
    protected $entry;

    /**
     * @var string
     */
    protected $identification;

    /**
     * @var bool
     */
    protected $installed = false;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|array
     */
    protected $script;

    /**
     * @var array
     */
    protected $stylesheet;

    /**
     * @var string
     */
    protected $version;

    /**
     * Module constructor.
     *
     * @param string $name
     */
    public function __construct($name = null)
    {
        $this->identification = $name;
    }

    /**
     * @return array
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Author of module.
     *
     * @return string|array
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Description of module.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Directory of module.
     *
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Entry of module.
     *
     * @return string
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * Identification of module.
     *
     * @return string
     */
    public function getIdentification()
    {
        return $this->identification;
    }

    /**
     * Name of module.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Script of module.
     *
     * @return string|array
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * Stylesheet of module.
     *
     * @return array
     */
    public function getStylesheet()
    {
        return $this->stylesheet;
    }

    /**
     * Version of module.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Enabled of module.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Module install status.
     *
     * @return bool
     */
    public function isInstalled()
    {
        return $this->installed;
    }

    /**
     * @param array $alias
     */
    public function setAlias(array $alias)
    {
        $this->alias = $alias;
    }

    /**
     * Set module's author.
     *
     * @param string|array $author
     */
    public function setAuthor($author)
    {
        $author = collect($author)->transform(function($value) {
            if(is_array($value))
                return implode(' <', $value) . '>';
            return $value;
        });

        $this->author = $author->toArray();
    }

    /**
     * Set module's enabled.
     *
     * @param $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * Set module's description.
     *
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Set module's directory.
     *
     * @param string $directory
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
    }

    /**
     * Set module's entry.
     *
     * @param $entry
     */
    public function setEntry($entry)
    {
        $this->entry = $entry;
    }

    /**
     * Set module's identification.
     *
     * @param string $identification
     */
    public function setIdentification($identification)
    {
        $this->identification = $identification;
    }

    /**
     * Set module's install status.
     *
     * @param $installed
     */
    public function setInstalled($installed)
    {
        $this->installed = $installed;
    }

    /**
     * Set module's name.
     *
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Set module's script.
     *
     * @param string|array $script
     */
    public function setScript($script)
    {
        $this->script = $script;
    }

    /**
     * Set module's stylesheet.
     *
     * @param array $stylesheet
     */
    public function setStylesheet(array $stylesheet)
    {
        $this->stylesheet = $stylesheet;
    }

    /**
     * Set module's version.
     *
     * @param $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }
}

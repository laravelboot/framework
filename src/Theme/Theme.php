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
namespace LaravelBoot\Foundation\Theme;

/**
 * Class Theme.
 */
class Theme
{
    /**
     * @var string|array
     */
    protected $author;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var bool
     */
    protected $installed = false;

    /**
     * @var string
     */
    protected $name;

    /**
     * Theme constructor.
     *
     * @param string $name
     * @param string|array $author
     * @param string $description
     */
    public function __construct($name = null, $author = null, $description = null)
    {
        $this->author = $author;
        $this->description = $description;
        $this->name = $name;
    }

    /**
     * Author of theme.
     *
     * @return string|array
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Description of theme.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Theme install status.
     *
     * @return bool
     */
    public function getInstalled()
    {
        return $this->installed;
    }

    /**
     * Name of theme.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set theme's author.
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
     * Set theme's description.
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Set theme's install status.
     *
     * @param bool $installed
     */
    public function setInstalled($installed)
    {
        $this->installed = $installed;
    }

    /**
     * Set theme's name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}

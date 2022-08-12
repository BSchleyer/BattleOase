<?php


namespace battleoase\battlecore\languageSystem\objects;


class Language {

    protected string $_name;
    protected string $_locale;
    protected string $_emoji;
    protected string $_prefix;
    protected array $_contributors;
    protected mixed $_values;

    public function __construct(string $name, string $locale, string $emoji, string $prefix, array $contributors, $values)
    {
        $this->_name = $name;
        $this->_locale = $locale;
        $this->_emoji = $emoji;
        $this->_prefix = $prefix;
        $this->_contributors = $contributors;
        $this->_values = $values;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * @return array
     */
    public function getContributors(): array
    {
        return $this->_contributors;
    }

    /**
     * @return string
     */
    public function getEmoji(): string
    {
        return $this->_emoji;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->_locale;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->_prefix;
    }

    /**
     * @return mixed
     */
    public function getValues()
    {
        return $this->_values;
    }


}
<?php

namespace Songshenzong\Api\Traits;

/**
 * Trait Hypermedia
 *
 * @package Songshenzong\Api\Traits
 */
trait Hypermedia
{

    /**
     * @var array
     */
    protected $Hypermedia = [];

    /**
     * @return array
     */
    public function getHypermedia(): array
    {
        return $this->Hypermedia;
    }

    /**
     * @param string $key
     * @param        $value
     *
     * @return self
     */
    public function setHypermedia(string $key, $value): self
    {
        $this->Hypermedia[$key] = $value;
        return $this;
    }

    /**
     * @param string $value
     *
     * @return self
     */
    public function setDocumentationUrl(string $value): self
    {
        $this->Hypermedia['documentation_url'] = $value;
        return $this;
    }


    /**
     * @param string $value
     *
     * @return self
     */
    public function setAuthorizationsUrl(string $value): self
    {
        $this->Hypermedia['authorizations_url'] = $value;
        return $this;
    }

    /**
     * @param string $value
     *
     * @return self
     */
    public function setCurrentUserUrl(string $value): self
    {
        $this->Hypermedia['current_user_url'] = $value;
        return $this;
    }

}

<?php

namespace Songshenzong\Api\Traits;

trait Hypermedia
{

    /**
     * @var array
     */
    protected $Hypermedia = [];

    /**
     * @return array
     */
    public function getHypermedia()
    {
        return $this->Hypermedia;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setHypermedia($key, $value)
    {
        $this->Hypermedia[$key] = $value;
        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setDocumentationUrl($value)
    {
        $this->Hypermedia['documentation_url'] = $value;
        return $this;
    }


    /**
     * @param $value
     *
     * @return $this
     */
    public function setAuthorizationsUrl($value)
    {
        $this->Hypermedia['authorizations_url'] = $value;
        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setCurrentUserUrl($value)
    {
        $this->Hypermedia['current_user_url'] = $value;
        return $this;
    }

}

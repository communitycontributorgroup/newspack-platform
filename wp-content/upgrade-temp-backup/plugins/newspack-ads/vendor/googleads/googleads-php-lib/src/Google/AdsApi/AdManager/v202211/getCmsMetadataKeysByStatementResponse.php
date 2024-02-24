<?php

namespace Google\AdsApi\AdManager\v202211;


/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class getCmsMetadataKeysByStatementResponse
{

    /**
     * @var \Google\AdsApi\AdManager\v202211\CmsMetadataKeyPage $rval
     */
    protected $rval = null;

    /**
     * @param \Google\AdsApi\AdManager\v202211\CmsMetadataKeyPage $rval
     */
    public function __construct($rval = null)
    {
      $this->rval = $rval;
    }

    /**
     * @return \Google\AdsApi\AdManager\v202211\CmsMetadataKeyPage
     */
    public function getRval()
    {
      return $this->rval;
    }

    /**
     * @param \Google\AdsApi\AdManager\v202211\CmsMetadataKeyPage $rval
     * @return \Google\AdsApi\AdManager\v202211\getCmsMetadataKeysByStatementResponse
     */
    public function setRval($rval)
    {
      $this->rval = $rval;
      return $this;
    }

}

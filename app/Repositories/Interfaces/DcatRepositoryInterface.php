<?php

namespace Tdt\Platform\Repositories\Interfaces;

/**
 * The interface for the DcatRepository class.
 * Provides in functionalities to CRUD (Stat, Geo)DCAT-AP objects.
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */

interface DcatRepositoryInterface
{
    /**
     * Get a DCAT object by URI
     *
     * @param integer $uri
     *
     * @return array
     */
    public function get($uri);

    /**
     * Get all of the DCAT objects
     *
     * @param integer $offset
     * @param integer $limit
     *
     *
     * @param array
     */
    public function getAll($offset, $limit);

    /**
     * Add a DCAT object, return its ID
     *
     * @param array $dcat_object
     *
     * @return integer
     */
    public function add($dcat_object);

    /**
     * Delete a DCAT object by its URI
     *
     * @param integer $uri
     *
     * @return bool
     */
    public function delete($uri);
}

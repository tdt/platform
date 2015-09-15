<?php

namespace Tdt\Platform\Repositories;

/**
 * The DcatRepository class.
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */

use Tdt\Platform\Repositories\Interfaces\DcatRepositoryInterface;
use Tdt\Platform\Models\Dcat\Dataset;

class DcatRepository implements DcatRepositoryInterface
{
    public function __construct(Dataset $datasets)
    {
        $this->datasets = $datasets;
    }

    /**
     * Get a DCAT object by URI
     *
     * @param integer $uri
     *
     * @return array
     */
    public function get($uri)
    {

    }

    /**
     * Get all of the DCAT objects
     *
     * @param integer $offset
     * @param integer $limit
     *
     *
     * @param array
     */
    public function getAll($offset = 0, $limit = 500)
    {
        $datasets = $this->datasets->skip($offset)->limit($limit)->get();

        return $datasets->toArray();
    }

    /**
     * Add a DCAT object, return its ID
     *
     * @param array $config
     *
     * @return integer
     */
    public function add($config)
    {
        $dcat_dataset = $this->datasets->create([]);

        foreach ($config as $key => $val) {
            $dcat_dataset->$key = $val;
        }

        return $dcat_dataset->save();
    }

    /**
     * Delete a DCAT object by its URI
     *
     * @param integer $uri
     *
     * @return bool
     */
    public function delete($uri)
    {
        $dcat_dataset = $this->datasets->where('uri', $uri)->first();

        if (!empty($dcat_dataset)) {
            return $dcat_dataset->delete();
        }
    }
}

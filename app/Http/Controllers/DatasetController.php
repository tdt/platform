<?php

namespace Tdt\Platform\Http\Controllers;

/**
 * The DatasetController class handles all CRUD operations on datasets.
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */

use Tdt\Platform\Http\Controllers\Controller;
use Tdt\Platform\Repositories\Interfaces\DcatRepositoryInterface;

class DatasetController extends Controller
{
    public function __construct(DcatRepositoryInterface $datasets)
    {
        $this->datasets = $datasets;
    }

    public function index()
    {
        $datasets = $this->datasets->getAll();

        return response()->json($datasets);
    }
}

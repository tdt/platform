<?php

namespace Tdt\Platform\Models\Dcat;

/**
 * The base DCAT-AP class, based on the DCAT-AP specification.
 * https://joinup.ec.europa.eu/asset/dcat_application_profile/asset_release/dcat-application-profile-data-portals-europe-final-draf
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */

use Jenssegers\Mongodb\Model as Model;

class Dataset extends Model
{
    protected $fillable = ['uri', 'description', 'publisher', 'title', 'contact', 'distribution', 'theme', 'type', 'issued_timestamp', 'modified_timestamp'];

    protected $collection = 'dcat_datasets';
}

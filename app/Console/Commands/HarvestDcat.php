<?php

namespace Tdt\Platform\Console\Commands;

use Illuminate\Console\Command;
use Tdt\Platform\Models\Dcat\Dataset;

class HarvestDcat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'platform:harvestdcat {uri} {format=turtle}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Harvest DCAT datasets from DCAT documents.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $uri = $this->argument('uri');
        $format = strtolower($this->argument('format'));

        $dcatRepository = \App::make('Tdt\Platform\Repositories\Interfaces\DcatRepositoryInterface');

        // Check if the URI is a valid one
        while (!filter_var($uri, FILTER_VALIDATE_URL)) {
            $this->error("The URI you provided ($uri) is not valid!");
            $uri = $this->ask('Provide a valid URI to the DCAT document');
        }

        // Try to read the DCAT document
        try {
            $graph = \EasyRdf_Graph::newAndLoad($uri, $format);
        } catch (\EasyRdf_Exception $ex) {
            $this->error("Something went wrong while parsing the DCAT document, the error we got is: " . $ex->getMessage());
        }

        // Parse the datasets and add them to the DCAT collection
        $datasets = $graph->allOfType('dcat:Dataset');

        foreach ($datasets as $dataset) {
            // Add them to the DCAT collection
            $resource = $graph->resource($dataset->getUri());

            $dcat_object = [];

            // Get the DCAT literal multi-value properties
            $properties = [ 'title' => 'dc:title',
                            'description' => 'dc:description',
                            'theme' => 'dcat:theme',
                            'contact' => 'adms:contactPoint',
                            'publisher' => 'dc:publisher',
                        ];

            foreach ($properties as $key => $property) {
                $dcat_object[$key] = $this->getProperties($resource, $property);
            }

            // Get the issued and modified date
            $issued = $this->getLiteral($resource, 'dc:issued');
            $modified = $this->getLiteral($resource, 'dc:modified');

            if (!empty($issued)) {
                $dcat_object['issued_timestamp'] = $issued;
            }

            if (!empty($modified)) {
                $dcat_object['modified_timestamp'] = $modified;
            }

            // Get the DCAT distribution
            $distributions = $resource->all('dcat:distribution');

            $dcat_distributions = [];

            foreach ($distributions as $distribution) {
                $dcat_distribution = [];

                // Get the description
                $dcat_distribution['description'] = $this->getProperties($distribution, 'dc:description');
                $license = 'No license specified';

                // Get the license
                if (!empty($distribution->get('dc:license'))) {
                    $license = $distribution->get('dc:license');

                    $license = $license->getUri();
                }

                $dcat_distribution['license'] = $license;

                // Get the accessURL
                $dcat_distribution['accessURL'] = $this->getProperties($distribution, 'dcat:accessURL');

                array_push($dcat_distributions, $dcat_distribution);
            }

            $dcat_object['distribution'] = $dcat_distributions;
            $dcat_object['uri'] = $dataset->getUri();

            // Delete the DCAT entry for the given URI
            $dcatRepository->delete($dataset->getUri());

            $dcatRepository->add($dcat_object);
        }
    }

    /**
     * Get all values of a property of a resource
     *
     * @param \EasyRdf_Resource $resource
     * @param string            $property
     *
     * @return array
     */
    private function getProperties($resource, $property)
    {
        $properties = [];
        $resource_properties = $resource->all($property);

        foreach ($resource_properties as $resource_property) {
            if ($resource_property instanceof \EasyRdf_Literal) {
                array_push($properties, ['value' => $resource_property->getValue(), 'lang' => $resource_property->getLang()]);
            } elseif ($resource_property instanceof \EasyRdf_Resource) {
                array_push($properties, $resource_property->getUri());
            }
        }

        return $properties;
    }

    /**
     * Get a single value for a literal property of a resource
     *
     * @param \EasyRdf_Resource $resource
     * @param string            $property
     *
     * @return string|integer|null
     */
    private function getLiteral($resource, $property)
    {
        $literal = $resource->getLiteral($property);

        if (!empty($literal)) {
            return $literal->getValue();
        }

        return $literal;
    }
}

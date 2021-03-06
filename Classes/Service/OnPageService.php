<?php
/**
 *
 */

namespace HDNET\OnpageIntegration\Service;

use HDNET\OnpageIntegration\Loader\ApiResultLoader;

class OnPageService extends AbstractService
{

    /**
     * @var ApiResultLoader
     */
    protected $loader;

    /**
     * OnPageService constructor.
     *
     * @param ApiResultLoader $loader
     */
    public function __construct(ApiResultLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * So that you get the error report of an
     * api call you have to crawl the graph api call
     * from a filter.
     *
     * In the next the you load the error report keys and
     * commit it into the errorReport function.
     *
     * errorReport generates the error report and afterwards
     * its stores in the field 'errors'.
     * '     *
     *
     * @param array           $buildData
     * @param string          $section
     * @param ApiResultLoader $loader
     */
    public function build($buildData, $section)
    {
        $i = 0;
        foreach ($buildData as &$element) {
            $graphDataArray = $this->loader->load('zoom_' . $section . '_' . $i . '_graph');
            $errorReportKey = $element['errors'];
            $element['errors'] = $this->errorReport($graphDataArray, $errorReportKey);
            $i++;
        }
        return $buildData;
    }

    /**
     * Generates the error report key of an api call and
     * return the result.
     *
     * @param mixed $graphApiCallResult
     * @param mixed $errorReportKey
     *
     * @return int
     */
    protected function errorReport($graphApiCallResult, $errorReportKey)
    {
        $totalErrors = 0;
        foreach ($graphApiCallResult as $element) {
            if (in_array('sum', $errorReportKey)) {
                foreach ($errorReportKey['hidden'] as $hidden) {
                    if (in_array($hidden, $element)) {
                        continue 2;
                    }
                }
                $totalErrors += $element['count'];
            }
            if (in_array($errorReportKey['show'], $element)) {
                $totalErrors += $element['count'];
            }
        }
        return $totalErrors;
    }

    /**
     * Fitted $tableApiCallResult by the elements of
     * $showTableKey
     *
     * @param string $apiCall
     * @param array  $showTableKey
     *
     * @return array
     */
    public function showColumns($apiCall, array $showTableKey)
    {
        $apiCallResult = $this->loader->load($apiCall);

        $fittedTablesRecords = [];
        foreach ($apiCallResult as $singleCallElement) {
            foreach ($showTableKey as $key) {
                if (array_key_exists($key, $singleCallElement)) {
                    $singleRecordArray[$key] = $singleCallElement[$key];
                }
                if ($key === 'documents') {
                    $documents = [
                        'mime'       => $singleCallElement['mime'],
                        'meta_title' => $singleCallElement['meta_title'],
                        'url'        => $singleCallElement['url'],
                    ];
                    $singleRecordArray['document'] = $documents;
                }
            }
            $fittedTablesRecords[] = $this->replaceNULL($singleRecordArray);
        }
        return $fittedTablesRecords;
    }

    /**
     * Build only for development
     */
    public function replaceNULL($array)
    {
        foreach ($array as &$element) {
            if (empty($element)) {
                $element = "Keine";
            }
        }
        return $array;
    }
}

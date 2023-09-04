<?php

namespace App\Helper;
use App\Models\Logger;
use DB;
class Tools 
{
    private string $working_dir;
    private string $working_xml;
    private Logger $logger;
    public function __construct()
    {
        $this->working_dir = env('BUSTABLE_WORKING_DIR');
        $files = $this->cleanDir(scandir($this->working_dir ));
        foreach($files as $file){
            $this->working_xml = $file;
            $this->process();
        }
    }

    private function cleanDir(array $files) {
        $tmpFile = [];
        foreach($files as $file) {
            if($file != '.' && $file != '..') {
                $tmpFile[] = $file;
            }
        }
        return $tmpFile;
       
    }

    private function isExist(): bool
    {
        $this->logger = new Logger();
        $log = $this->logger->firstWhere('xml_file', $this->working_xml);
        if($log) return true;
        return false;
    }

    private function process()
    {
        // check if the file exist in the Logger
        if(!$this->isExist()){
            // check if file exist
            if(file_exists($this->working_dir . $this->working_xml)){
                $data = $this->getArray();
                $isContinue = true;
                try {
                    $routes = $data['RouteSections']['RouteSection'] ?? null;
                    $isNull = $data['RouteSections']['RouteSection']['RouteLink'] ?? null;
                    if($isNull != null) {
                        $isContinue = $this->buildRoute( $routes);
                    } else {                       
                        foreach($routes as $rlink ) {
                            if($isContinue) {                                                         
                                $isContinue = $this->buildRoute($rlink);
                            }
                        }  
                    }
                     
                    $journeys = $data['JourneyPatternSections']['JourneyPatternSection'];
                    $isNull = $data['JourneyPatternSections']['JourneyPatternSection']['JourneyPatternTimingLink'] ?? null;
                    if($isNull != null) {
                        $isContinue = $this->buildJourney($data['JourneyPatternSections']['JourneyPatternSection']['JourneyPatternTimingLink']);
                    } else {
                        foreach($journeys as $jlink) {
                            if($isContinue) {                                               
                                $isContinue = $this->buildJourney($jlink['JourneyPatternTimingLink']);
                            }
                        }     
                    }
                                     
                    $locales = $data['NptgLocalities']['AnnotatedNptgLocalityRef'] ?? null;
                    $stopPoints = $data['StopPoints']['StopPoint'] ?? null;
                } catch(Exception $err) {
                    info('Failed in xml file. '. $this->working_xml );
                    info('Error - '.$err->getMessage() );
                }
                if($locales == null ||  $stopPoints == null ) {
                    info('Failed migration xml file. '. $this->working_xml );                   
                    $isContinue = false;
                }
                if($isContinue) {
                    $isContinue = $this->buildLocale($locales);
                }    
                if($isContinue) {
                    $isContinue = $this->buildStopPoint($stopPoints);
                }
                if(!$isContinue) {
                    $this->rollback();
                } else {
                    DB::table('logger')->insert([
                        'xml_file' => $this->working_xml
                    ]);
                }
            }
        }
    }

    private function buildLocale(array $locales): bool
    {
        $insertLocale = [];
        foreach($locales as $locale)  {
            $localeData = [
                'NptgLocalityRef' => $locale['NptgLocalityRef'],
                'LocalityName' => $locale['LocalityName'],
                'xml_file' => $this->working_xml,
            ];

            $insertLocale[] = $localeData;

        }
        try {
            //dd($insertLocale);
            DB::table('locale')->insert($insertLocale);
            
        }  catch(Exception $e) {
            return false;
        } 
        return true;
    }

    private function rollback()
    {
        
    }
    private function getArray()
    {
        $fullPath = $this->working_dir . $this->working_xml;
        $xml = simplexml_load_file($fullPath);
        $json = json_encode($xml);
        $toArray = json_decode($json, true);
        return $toArray;
    }

    private function buildRoute(array $routes): bool
    {
        $insertRoute = [];
        $insertRouteLink = [];
        info($this->working_xml);
        foreach($routes['RouteLink'] as $route) {
            $routeData = [
                'routeid' => $route['@attributes']['id'],
                'from_stopref' => $route['From']['StopPointRef'],
                'to_stopref' => $route['To']['StopPointRef'],
                'distance' => $route['Distance'],
                'direction' => $route['Direction'],
                'xml_file' => $this->working_xml,
            ];
            $insertRoute[] = $routeData;
            foreach($route['Track']['Mapping']['Location'] as $routeLink)
            {
                $routeLinkData = [
                    'routeid' => $route['@attributes']['id'],
                    'long' => $routeLink['Longitude'],
                    'lang' => $routeLink['Latitude'],
                    'xml_file' => $this->working_xml,
                ];
                $insertRouteLink[] = $routeLinkData;
            }
        }
        try {
            DB::table('route')->insert($insertRoute);
            DB::table('routelink')->insert($insertRouteLink);
        }  catch(Exception $e) {
            return false;
        } 
        return true;
    }    

    private function buildStopPoint(array $stopPoints): bool
    {       
        $insertStopPoint = [];
        foreach($stopPoints as $stopPoint){
            $stopPointData = [
                'atcocode' => $stopPoint['AtcoCode'],
                'nptglicalityref' => $stopPoint['Place']['NptgLocalityRef'],
                'commonname' => $stopPoint['Descriptor']['CommonName'],
                'long' => $stopPoint['Place']['Location']['Longitude'],
                'lang' => $stopPoint['Place']['Location']['Latitude'],
                'xml_file' => $this->working_xml,
            ];
            $insertStopPoint[] = $stopPointData;
        }
        try {
            DB::table('stoppoint')->insert($insertStopPoint);

        }  catch(Exception $e) {
            return false;
        }         
        return true;
    }

    private function buildJourney(array $journeys):bool 
    {       
        $insertJourney = [];
        foreach($journeys as $journey) {
            $journeyData = [
                'from_StopPointRef' => $journey['From']['StopPointRef'],
                'from_seqno' => $journey['From']['@attributes']['SequenceNumber'],
                'from_DynamicDestinationDisplay' => $journey['From']['DynamicDestinationDisplay'],
                'from_activity' => $journey['From']['Activity'],
                'from_timingstatus' => $journey['From']['TimingStatus'],
                'to_StopPointRef' => $journey['To']['StopPointRef'],
                'to_seqno' => $journey['To']['@attributes']['SequenceNumber'],
                'to_DynamicDestinationDisplay' => $journey['To']['DynamicDestinationDisplay'],
                'to_activity' => $journey['To']['Activity'],
                'to_timingstatus' => $journey['To']['TimingStatus'],
                'runtime' => $journey['RunTime'],
                'xml_file' => $this->working_xml,
            ];
            $insertJourney[] = $journeyData;
            
        }     
        try {
            DB::table('journey')->insert($insertJourney);
        }  catch(Exception $e) {
            return false;
        } 
        
        return true;
    }
}
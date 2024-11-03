<?php

namespace App\Controllers;

use App\System\Core\Controller;
use App\Models\Car;
use App\Models\Driver;
use App\Models\Race;
use App\Models\Track;
use \daandesmedt\PHPHeadlessChrome\HeadlessChrome;

class TrackController extends Controller
{
    public function __construct() {
        $this->checkAuth();
    }
    public function index($id=null)
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            if($id){
                $car = (new Track($id));
                if($car){
                    $this->response($car);
                }
                else{
                    $this->error_400('Record with car number'. $id .'Not Found !!');
                }
            }
            else{
                $car = (new Track)->get();
                $this->response($car);
            
            }
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (isset($_POST['name']) && isset($_POST['type']) && isset($_POST['laps']) && isset($_POST['baseLapTime'])){
                $track = new Track;
                if ($_POST['type'] == "race" || $_POST['type'] == "street") {
                    $track->type = $_POST['type'];
                }
                else{
                    $this->error_400("The Track type must be either 'race' or 'street'. ");
                }
                $track->name = $_POST['name'];
                $track->laps = $_POST['laps'];
                $track->baseLapTime = $_POST['baseLapTime'];
                $track->save();
                redirect(url('track/'.$track->id));
            }       
            else{
                $this->error_400("All fields are required !");
            }
        }
        if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
            if($id){
                (new Track($id))->delete();
                $this->response("Record with Track id $id deleted successfully.");
            }
            else{
                $this->error_400("Id field is required !");
            }
        }
    }

    public function track_races($id=null)
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            if($id){
                $race = (new Race)->where('track',$id)->first();
                if($race){
                    $this->response($race);
                }
                else{
                    $this->error_400("Record with Driver number $id Not Found !!");
                }
            }
            else{
                $this->error_400("id field is required !\n");
            }
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (isset($id)){
                $Track = (new Track)->where('id',$id)->first();
                if($Track){
                    $race = new Race;
                    $race->track = $id;
                    $race->save();
                    http_response_code(200);
                    redirect(url('race/'.$race->id));
                }
                else{
                    $this->error_400("Record with Track id $id Not Found !!");
                }
            }       
            else{
                $this->error_400("Track Id fields is required !");
            }
        }
    }

    public function scrape($id=null)
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            $headlessChromer = new HeadlessChrome();
            $headlessChromer->setUrl('https://www.formula1.com/en/racing/2024');
            $headlessChromer->setBinaryPath('C:\Program Files\Google\Chrome\Application\chrome');
            $headlessChromer->useMobile();
            $headlessChromer->setArgument('--headless','old');
            $headlessChromer->setWindowSize(1920, 1080);

            $headlessChromer->setOutputDirectory(__DIR__);
            // sleep(5);
            do{
                $htmlString = $headlessChromer->getDOM();
                libxml_use_internal_errors(true);
                // file_put_contents("fileicecream.html", $htmlString); 
                // var_dump($htmlString);
                $doc = new \DOMDocument();
                $doc->loadHTML($htmlString);
    
                $xpath = new \DOMXPath($doc);
                $xpathQuery = '//*[@id="maincontent"]/div/div[1]/div[2]/div/div/a';
                $elements = $xpath->query($xpathQuery);
                if ($elements === false) {
                    echo "XPath query failed.";
                    return;
                }
            }while($elements->length === 0);
            
            $track = [];
            $counter = 0;
            /** @var \DOMElement $element */
            foreach ($elements as $element) {
                // printing the html to verify !
                // $elementDom = new \DOMDocument();
                // $elementDom->appendChild($elementDom->importNode($element, true));
                // echo $elementDom->saveHTML() . "\n";
                if ($element->nodeName === 'a') {
                    $href = $element->getAttribute('href');

                    if ($counter === 0) {
                        $counter++;
                        continue;
                    }
                    $headlessChromer->setUrl('https://www.formula1.com' . $href ."/circuit");
                    // sleep(5);
                    do{
                        $htmlString_2 = $headlessChromer->getDOM();
                        libxml_use_internal_errors(true);
                        // file_put_contents("fileicecream.html", $htmlString_2); 
                        // var_dump($htmlString_2);
                        $doc = new \DOMDocument();
                        $doc->loadHTML($htmlString_2);
            
                        $xpath = new \DOMXPath($doc);
                        // //*[@id="maincontent"]/div/div[1]/div[2]/div/div/a[1]/fieldset
                        $xpathQuery_name = '//*[@id="maincontent"]/div[2]/div/div[2]/fieldset/legend/div/h2/div';
                        $elements_name = $xpath->query($xpathQuery_name);
                        if ($elements_name === false) {
                            echo "Name XPath query failed.";
                            exit;
                        }

                        $xpathQuery_laps = '//*[@id="maincontent"]/div[2]/div/div[2]/fieldset/div/div[2]/div/div[1]/div/div[2]/h2';
                        $elements_laps = $xpath->query($xpathQuery_laps);
                        if ($elements_laps === false) {
                            echo "Laps XPath query failed.";
                            exit;
                        }
                        $xpathQuery_bst_lap_time = '//*[@id="maincontent"]/div[2]/div/div[2]/fieldset/div/div[2]/div/div[1]/div/div[5]/h2';
                        $elements_bst_lap_time = $xpath->query($xpathQuery_bst_lap_time);
                        if ($elements_bst_lap_time === false) {
                            echo "Best lap time XPath query failed.";
                            exit;
                        }

                        $xpathQuery_type = '//*[@id="maincontent"]/div[2]/div/div[3]/div/div[1]/section/div';
                        $elements_type = $xpath->query($xpathQuery_type);
                        if ($elements_type === false) {
                            echo "Type XPath query failed.";
                            exit;
                        }

                    }while($elements_name->length === 0);

                    $TrackName = $elements_name->item(0)->textContent; 
                    $Laps = $elements_laps->item(0)->textContent; 
                    $Best_lap_time = $elements_bst_lap_time->item(0)->textContent; 
                    $Type = implode("\n", array_map(function($element) {
                                return $element->textContent;
                            }, iterator_to_array($elements_type))); 
                    if (strpos(strtolower($Type), 'street') !== false) {
                        $track_type = 'street';
                    } else {
                        $track_type = 'race';
                    }
                    list($minutes, $seconds) = explode(':', $Best_lap_time);
                    $totalSeconds = (float)$minutes * 60 + (float)$seconds;
                    $track[]=[
                        "name"=>$TrackName,
                        "type"=>$track_type,
                        "laps"=>$Laps,
                        "baseLapTime"=>$totalSeconds,
                    ];
                    // break;
                }
            }
            $this->response($track);
        }
    }
}
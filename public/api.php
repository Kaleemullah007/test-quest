<?php require_once('../config.php');

require_once '../vendor/autoload.php'; 
use PhpOffice\PhpSpreadsheet\Reader\Xlsx; 
use PhpOffice\PhpSpreadsheet\Reader\Csv; 

// Upload Csv File
if(isset($_POST['uploadBTN'])){ 
    echo json_encode(UploadFile());
}

// Refresh Chart
if(isset($_POST['refresh_chart'])){  
    echo  json_encode(GraphData());
}


// Prepare Graph data
function GraphData()
{
    global $db;
    $totall =  0;
$totala = 0;
$graphData = 'SELECT DATE(`time_microseconds`) AS date, SUM(leads) AS leads, SUM(activates) AS activates FROM marketing_data GROUP BY DATE(`time_microseconds`)';
$graphs = $db->query($graphData);
$label = array();
$dataset = array();
$count = 0;
foreach($graphs as $graph){
    $day =  date('d',strtotime($graph['date']));
    if($day%2 == 0)
    $label[] = date('m/d',strtotime($graph['date']));
    else
    $label[] ='';
    $dataset[$count]['label'] = 'Leads';
    $dataset[$count]['backgroundColor'] = "#caf270";
    $dataset[$count]['data'][] = $graph['leads'];
    $dataset[$count+1]['label'] = 'Activates';
    $dataset[$count+1]['backgroundColor'] = "#45c490";
    $dataset[1]['data'][] = $graph['activates'];
    $totall +=  $graph['leads'];
    $totala += $graph['activates'];
}
if(!empty($dataset)){


$dataset[0]['label'] = $dataset[0]['label'].' Total :'.format_large_number($totall); 
$dataset[1]['label'] = $dataset[1]['label'].' Total :'.format_large_number($totala);
}
else{

    $label[] = 'No Data';
    $dataset[$count]['label'] = 'Leads';
    $dataset[$count]['backgroundColor'] = "#caf270";
    $dataset[$count]['data'][] = 0;
    $dataset[$count+1]['label'] = 'Activates';
    $dataset[$count+1]['backgroundColor'] = "#f9ff00";
    $dataset[1]['data'][] = 0;
}

return ['label'=>$label,'dataset'=>$dataset];


}

// Format Number for Graph
function format_large_number($number, $precision = 2) {
    if ($number < 1000) {
        return number_format($number, $precision);
    } elseif ($number < 1000000) {
        return number_format($number / 1000, $precision) . 'K';
    } elseif ($number < 1000000000) {
        return number_format($number / 1000000, $precision) . 'M';
    } else {
        return number_format($number / 1000000000, $precision) . 'B';
    }
}

function UploadFile()
{
     global $db;
     // Allowed mime types
    $excelMimes = array('text/csv','text/xls', 'text/xlsx', 
    'application/excel', 'application/vnd.msexcel',
    'application/vnd.ms-excel', 
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); 
    $error = false; 
    $message = 'Problem in Uploading';
    // Validate whether selected file is a Excel file 
    if(!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $excelMimes)){ 
        $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        if('csv' == $extension) {
            
            $reader = new Csv(); 
        } else {
            
            $reader = new Xlsx(); 
        }
        
        
        // If the file is uploaded 
        if(is_uploaded_file($_FILES['file']['tmp_name'])){ 
          
            $spreadsheet = $reader->load($_FILES['file']['tmp_name']); 
            $worksheet = $spreadsheet->getActiveSheet();  
            $worksheet_arr = $worksheet->toArray(); 
        
            // Remove column name array
            unset($worksheet_arr[0]); 
 
            foreach($worksheet_arr as $row){ 
                $time_microseconds = $row[0]; 
                $leads = $row[1]; 
                $activates = $row[2]; 
                $date = date('Y-m-d H:i:s',$time_microseconds/1000); 
                // Insert marketing data in the database 
                $db->query("INSERT INTO marketing_data (time_microseconds,leads,activates) 
                VALUES ('".$date."', '".$leads."', '".$activates."')"); 
               
            } 
             
            $error = true; 
            $message = 'Uploaded '.$extension.' Successfully file';
        } 
        else{ 
            $error = false; 
            $message = "File didn't upload";
        }
    }else{ 
        $error = false; 
        $message = 'Only Allowed files are uploaded, like csv or xlxs';
    } 
    
    return ['error'=>$error,'message'=>$message];

}

?>



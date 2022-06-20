<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include '../DB.class.php';
$db = new DB;

if( isset($_POST['quotelist']) && $_POST['quotelist'] == 'list' ){
    getQuoteList($db);
}else{
    echo json_encode(array("type" => "error", "msg" => "Invalid request."));
}

/*Update Quote By ID*/
function updateQuote(Request $request){
    $response = $this->tokenresponse;
    if( $response['type'] == 'success' && isset($request->qid) && isset($request->quote) && isset($request->writtenby)){
        $quote = DailyQuotes::where('id', $request->qid)->first();
        if(!is_null($quote)){
            if(DailyQuotes::where('id', $request->qid)->update(array('quotes' => $request->quote, 'quotesby' => $request->writtenby))){
                $response['type'] = "success";
                $response['message'] = "Quote details updated";
            }else{
                $response['type'] = "error";
                $response['message'] = "Failed to update quote details";
            }
            
        }else{
            $response['type'] = "error";
            $response['message'] = "No quote found";
        }
        unset($response['data']);
        return response()->json($response);
    }
    else{
        $response['type'] = "error";
        $response['message'] = "Invalid request";
        unset($response['data']);
        return response()->json($response);
    }
}

function newQuote(Request $request){
    $response = $this->tokenresponse;
    if( $response['type'] == 'success' && isset($request->quotes) && isset($request->by)){
        $userid = $this->tokenresponse['data']->id;
        $request->userid = $userid;
        $quote = new DailyQuotes();
        if( $quote->saveQuotes($request) ){
            $response['type'] = "success";
            $response['message'] = "New quote saved";
        }else{
            $response['type'] = "error";
            $response['message'] = "Failed to update quote details";
        }
        unset($response['data']);
        return response()->json($response);
    }
    else{
        $response['type'] = "error";
        $response['message'] = "Invalid request";
        unset($response['data']);
        return response()->json($response);
    }
}

function deleteQuote(Request $request){
    $response = $this->tokenresponse;
    if( $response['type'] == 'success' && isset($request->qid)){
        $quote = new DailyQuotes();
        $checkquote = DailyQuotes::where('id', $request->qid)->first();
        if( !is_null($checkquote) && $quote->deleteQuote($request) ){
            $response['type'] = "success";
            $response['message'] = "Quote deleted";
        }else{
            $response['type'] = "error";
            $response['message'] = "Failed to delete quote";
        }
        unset($response['data']);
        return response()->json($response);
    }
    else{
        $response['type'] = "error";
        $response['message'] = "Invalid request";
        unset($response['data']);
        return response()->json($response);
    }
}

/*Get Quotes List*/
function getQuoteList($db){
    $con = array(
            'orderby' => 'id desc'
        );
    $quotes_list = $db->getRows('dailyquotes', $con);
    if( !empty($quotes_list) ){
        echo json_encode(array("type" => "success", "msg" => "Data found.", "quotes_list" => $quotes_list));
    }else{
        echo json_encode(array("type" => "error", "msg" => "No data found."));
    }
}

/*Make Live a quote to display on Krukonnect App Dashboard*/
function makeLiveQuote($db){
    $response = $this->tokenresponse;
    if( $response['type'] == 'success' && !is_null($request->quoteid)){
        //0 - Not Used, 1 - Used
        $quoteid = $request->quoteid;
        $quote = DailyQuotes::select('quotes', 'quotesby')->where('id', $quoteid)->first();
        if(!is_null($quote)){
            $detail = json_encode($quote->toArray());
            $path = public_path().'/quote';
            $file = '/quoteofday.txt';
            \File::isDirectory($path) or \File::makeDirectory($path, 0777, true, true);
            
            \File::put($path.$file, $detail);
            //echo $path.$file;
            
            if(DailyQuotes::where('id', $quoteid)->update(['status' => 1])){
                
                $response['type'] = "success";
                $response['message'] = "Quote status updated";
            }else{
                $response['type'] = "error";
                $response['message'] = "Failed to update quote status";
            }
        }else{
            $response['type'] = "error";
            $response['message'] = "Invalid request";
        }
        
        return response()->json($response);
    }
    else{
        $response['type'] = "error";
        return response()->json($response);
    }
}

function checkCron(){
    $dailyquotes = new DailyQuotes;
    $dailyquotes->makeLiveQuoteByAutoJob();
}
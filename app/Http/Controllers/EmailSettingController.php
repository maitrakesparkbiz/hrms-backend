<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmailSettingController extends Controller
{
    function saveEmailSettings(Request $request){
        $data = $request->all();
        if(isset($data['id'])){
            $data['id'] = $this->emailRepo->EmailSettingOfId($data['id']);
            $res = $this->emailRepo->update($data['id'],$data);
        }else {
            $prepare_data = $this->emailRepo->prepareData($data);
            $res = $this->emailRepo->create($prepare_data);
        }
        if(!empty($res)){
            return response()->json('success');
        }
    }

    function getEmailSettings(){
        $data = $this->emailRepo->getEmailSettings();
        if(!empty($data)) {
            return response()->json($data[0]);
        }
        return null;
    }
}

?>
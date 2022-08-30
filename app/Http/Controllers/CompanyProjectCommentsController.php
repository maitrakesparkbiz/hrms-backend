<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CompanyProjectCommentsController extends Controller
{ 
    function doComment(Request $request)
    {
        $data = $request->all();
        $currentUser = JWTAuth::user()->getId();
        $notify = [];
        $data['conv_id'] = $this->companyProjectConvRepo->CompanyProjectConvOfId($data['conv_id']);
        $data['emp_id'] = $this->userRepo->UserOfId($data['emp_id']);
        $prepareData = $this->companyProjectCommentRepo->prepareData($data);
        $create = $this->companyProjectCommentRepo->create($prepareData);
        $comment = ['id' => $create->getId(), 'emp_id' => $create->getEmpId()->getId(), 'msg_text' => $create->getMsgText()];
        $tlFlag = null;
        $baFlag = null;
        if($create){
            $projectId = $this->companyProjectRepo->CompanyProjectOfId($data['project_id']);
            $baProjectId = $this->companyProjectBaRepo->CompanyProjectBaOfId( $data['ba_project_id']);
            if($data['is_tl']) {
               $tlFlag = 128;
               $baFlag = 129;
            } else {
                $tlFlag = 129;
                $baFlag = 128;
            }
            $updateTl['flag'] = $this->optionRepo->OptionOfId($tlFlag);
            $updatedTl = $this->companyProjectRepo->update($projectId,$updateTl);
 
            $updateBa['flag'] = $this->optionRepo->OptionOfId($baFlag);
            $updateBa['ba_tl_flag'] = $this->optionRepo->OptionOfId($tlFlag);
            $updatedBa = $this->companyProjectBaRepo->update($baProjectId, $updateBa);



            // create notification data
            if($data['is_tl']) {
                // $emp_id = $updatedBa->getEmpId()->getId();
                $emp_id = $data['conv_id']->getUser2()->getId();
            } else{
                // $emp_id = $updatedTl->getCreatedBy()->getId();
                $emp_id = $data['conv_id']->getUser1()->getId();
            }
            
            $from_emp = $create->getEmpId()->getFirstname() . ' ' . $create->getEmpId()->getLastname();

            $title = 'Project Comment';
            $details = 'Comment Received from ' . $from_emp;

            $notify[] = [
                'channel_name' => 'project-update-' . $emp_id,
                'from_emp' => $from_emp,
                'title' => $title,
                'details' => $details
            ];
            // create activity box data
            $prepareNotify['emp_id'] = $this->userRepo->UserOfId($emp_id);
            $prepareNotify['from_emp'] = $this->userRepo->UserOfId($currentUser);
            $prepareNotify['title'] = $title;
            $prepareNotify['details'] = $details;
            $prepare = $this->activityBoxRepo->prepareData($prepareNotify);
            $this->activityBoxRepo->create($prepare);
                // end
            // end
            $response['data'] = $notify;
            $response['comment'] = $comment;
            return response()->json(['status' => 'success','data' => $response]);
        }
    }
}
 
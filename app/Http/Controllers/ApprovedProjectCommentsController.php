<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class ApprovedProjectCommentsController extends Controller
{
    function doApprovedCommentSr(Request $request)
    {
        $data = $request->all();
        $notify = [];
        $data['u_id'] = $this->userRepo->UserOfId($data['u_id']);
        $data['conv_id'] = $this->approvedProjectConvRepo->ApprovedProjectConvOfId($data['conv_id']);
        $prepare_data = $this->approvedProjectCommentsRepo->prepareData($data);
        $create = $this->approvedProjectCommentsRepo->create($prepare_data);
        if ($create) {
            $flagID = $this->approvedProjectFlagRepo->ApprovedProjectFlagOfId($data['project_flag_id']);
            if (isset($data['sr_ba'])) {
                $updateData['flag_sales'] = $this->optionRepo->OptionOfId(129);
                $updateData['flag_ba'] = $this->optionRepo->OptionOfId(128);
            } else {
                $updateData['flag_sales'] = $this->optionRepo->OptionOfId(128);
                $updateData['flag_ba'] = $this->optionRepo->OptionOfId(129);
            }
            $update = $this->approvedProjectFlagRepo->update($flagID, $updateData);
            if ($update) {
                $comment = ['id' => $create->getId(), 'u_id' => $create->getUId()->getId(), 'msg_text' => $create->getMsgText()];

                // notification data
                if (isset($data['sr_ba'])) {
                    $emp_id = $update->getProjectId()->getCreatedBy()->getId();
                } else {
                    $emp_id = $update->getProjectId()->getAssignedBa()->getId();
                }
                $from_emp = $create->getUId()->getFirstname() . ' ' . $create->getUId()->getLastname();
                $notify[] = ['channel_name' => 'project-update-' . $emp_id,
                    'from_emp' => $from_emp,
                    'title' => 'Comment',
                    'details' => 'Comment Received from ' . $from_emp];
                // end

                $response['data'] = $notify;
                $response['comment'] = $comment;
                return response()->json(['status' => 'success', 'data' => $response]);
            }
        }
        return response()->json(['status' => 'error']);
    }

    function doApprovedCommentJr(Request $request)
    {
        $data = $request->all();
        $notify = [];
        $data['u_id'] = $this->userRepo->UserOfId($data['u_id']);
        $data['conv_id'] = $this->approvedProjectConvRepo->ApprovedProjectConvOfId($data['conv_id']);
        $prepare_data = $this->approvedProjectCommentsRepo->prepareData($data);
        $create = $this->approvedProjectCommentsRepo->create($prepare_data);
        if ($create) {
            $flagID = $this->approvedProjectFlagRepo->ApprovedProjectFlagOfId($data['project_flag_id']);
            if (isset($data['jr_ba'])) {
                $updateData['flag_ba_to_jr'] = $this->optionRepo->OptionOfId(129);
                $updateData['flag_jr_ba'] = $this->optionRepo->OptionOfId(128);
            } else {
                $updateData['flag_ba_to_jr'] = $this->optionRepo->OptionOfId(128);
                $updateData['flag_jr_ba'] = $this->optionRepo->OptionOfId(129);
            }
            $update = $this->approvedProjectFlagRepo->update($flagID, $updateData);
            if ($update) {
                $comment = ['id' => $create->getId(), 'u_id' => $create->getUId()->getId(), 'msg_text' => $create->getMsgText()];
                // notification data
                if (isset($data['jr_ba'])) {
                    $emp_id = $update->getProjectId()->getAssignedBa()->getId();
                } else {
                    $emp_id = $update->getProjectId()->getAssignedJrBa()->getId();
                }
                $from_emp = $create->getUId()->getFirstname() . ' ' . $create->getUId()->getLastname();
                $notify[] = ['channel_name' => 'project-update-' . $emp_id,
                    'from_emp' => $from_emp,
                    'title' => 'Comment',
                    'details' => 'Comment Received from ' . $from_emp];
                $response['data'] = $notify;
                $response['comment'] = $comment;
                // end
                return response()->json(['status' => 'success', 'data' => $response]);
            }
        }
        return response()->json(['status' => 'error']);
    }

    function getApprovedProjectComments(Request $request)
    {
        $data = $request->all();
        $conv_id = $this->approvedProjectConvRepo->getSrConvId($data['project_id'], $data['user1'], $data['user2']);
        if (!empty($conv_id)) {
            $c_id = $conv_id[0]['id'];
            $projectComments = $this->approvedProjectCommentsRepo->getProjectComments($c_id);
            return response()->json(['conv_id' => $c_id, 'data' => $projectComments]);
        }
        return response()->json('no_data');
    }
}

?>
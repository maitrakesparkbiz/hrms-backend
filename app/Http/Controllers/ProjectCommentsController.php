<?php

namespace App\Http\Controllers;

use App\Events\CommentEvent;
use App\Events\SendStatusUpdate;
use Illuminate\Http\Request;

class ProjectCommentsController extends Controller
{
    function getProjectComments(Request $request)
    {
        $data = $this->projectCommentsRepo->getProjectComments($request->conv_id);
        return response()->json($data);
    }

    function getSrProjectComments(Request $request)
    {
        $data = $request->all();
        $conv_id = $this->projectConversationRepo->getSrConvId($data['project_id'], $data['project_ba_id']);
        if (!empty($conv_id)) {
            $c_id = $conv_id[0]['id'];
            $projectComments = $this->projectCommentsRepo->getProjectComments($c_id);
            return response()->json(['conv_id' => $c_id, 'data' => $projectComments]);
        }
        return 'no_data';
    }

    function getJrProjectComments(Request $request)
    {
        $data = $request->all();
        $conv_id = $this->projectConversationRepo->getJrConvId($data['project_id'], $data['project_ba_id'], $data['project_jr_ba_id']);
        if (!empty($conv_id)) {
            $c_id = $conv_id[0]['id'];
            $projectComments = $this->projectCommentsRepo->getProjectComments($c_id);
            return response()->json(['conv_id' => $c_id, 'data' => $projectComments]);
        }
        return 'no_data';
    }

    function doCommentSr(Request $request)
    {
        $data = $request->all();
        $notify = [];
        $data['u_id'] = $this->userRepo->UserOfId($data['u_id']);
        $data['conv_id'] = $this->projectConversationRepo->ConvOfId($data['conv_id']);

        // for further update query
        $data['project_id'] = $this->projectRepo->ProjectOfId($data['project_id']);
        $data['project_ba_id'] = $this->projectBaRepo->ProjectOfId($data['project_ba_id']);
        // end

        $prepare_data = $this->projectCommentsRepo->prepareData($data);
        $create = $this->projectCommentsRepo->create($prepare_data);
        if ($create) {
            if (isset($data['sr_ba'])) {
                $updateData1['status_flag'] = $this->optionRepo->OptionOfId(129);
                $updateData2['flag'] = $this->optionRepo->OptionOfId(128);
            } else {
                $updateData1['status_flag'] = $this->optionRepo->OptionOfId(128);
                $updateData2['flag'] = $this->optionRepo->OptionOfId(129);
            }
            $updateProject = $this->projectRepo->update($data['project_id'], $updateData1);
            $updateBaProject = $this->projectBaRepo->update($data['project_ba_id'], $updateData2);

            $response['status'] = 'success';
            $response['data'] = ['id' => $create->getId(), 'u_id' => $create->getUId()->getId(), 'msg_text' => $create->getMsgText()];
            if ($updateProject && $updateBaProject) {
                if (isset($data['sr_ba'])) {
                    $emp_id = $updateProject->getCreatedBy()->getId();
                } else {
                    $emp_id = $updateBaProject->getEmpId()->getId();
                }
                $from_emp = $create->getUId()->getFirstname() . ' ' . $create->getUId()->getLastname();
                $notify[] = ['channel_name' => 'project-update-' . $emp_id,
                    'from_emp' => $from_emp,
                    'title' => 'Comment',
                    'details' => 'Comment Received from ' . $from_emp];
                $data['comment'] = $response['data'];
                $data['data'] = $notify;
                return response()->json(['status' => 'success', 'data' => $data]);
            }
        }
        return response()->json(['status' => 'error']);
    }

    function doCommentJr(Request $request)
    {
        $data = $request->all();
        $notify = [];
        $data['u_id'] = $this->userRepo->UserOfId($data['u_id']);
        $data['conv_id'] = $this->projectConversationRepo->ConvOfId($data['conv_id']);
        // for further update query
        $data['project_ba_id'] = $this->projectBaRepo->ProjectOfId($data['project_ba_id']);
        $data['project_jr_ba_id'] = $this->projectJrBaRepo->ProjectJrBaOfId($data['project_jr_ba_id']);
        // end
        $prepare_data = $this->projectCommentsRepo->prepareData($data);
        $create = $this->projectCommentsRepo->create($prepare_data);
        if ($create) {
            if (isset($data['jr_ba'])) {
                $updateData2['sr_to_jr_flag'] = $this->optionRepo->OptionOfId(129);
                $updateData2['flag'] = $this->optionRepo->OptionOfId(128);
            } else {
                $updateData2['sr_to_jr_flag'] = $this->optionRepo->OptionOfId(128);
                $updateData2['flag'] = $this->optionRepo->OptionOfId(129);
            }
            $updateJrBaProject = $this->projectJrBaRepo->update($data['project_jr_ba_id'], $updateData2);
            $response['status'] = 'success';
            $response['data'] = ['id' => $create->getId(), 'u_id' => $create->getUId()->getId(), 'msg_text' => $create->getMsgText()];
            if ($updateJrBaProject) {
                if (isset($data['jr_ba'])) {
                    $emp_id = $updateJrBaProject->getBaProjectId()->getEmpId()->getId();
                } else {
                    $emp_id = $updateJrBaProject->getEmpId()->getId();
                }
                $from_emp = $create->getUId()->getFirstname() . ' ' . $create->getUId()->getLastname();
                $notify[] = ['channel_name' => 'project-update-' . $emp_id,
                    'from_emp' => $from_emp,
                    'title' => 'Comment',
                    'details' => 'Comment Received from ' . $from_emp];
                $data['comment'] = $response['data'];
                $data['data'] = $notify;
                return response()->json(['status' => 'success', 'data' => $data]);
            }
        }
        return response()->json('error');
    }
}
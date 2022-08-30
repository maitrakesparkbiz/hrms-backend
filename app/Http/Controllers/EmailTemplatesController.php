<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmailTemplatesController extends Controller
{
    // added comment again
    function saveEmailTemplate(Request $request)
    {
        $data = $request->all();
        foreach ($data['template'] as $row) {
            if (isset($row['id'])) {
                $row['id'] = $this->emailTemplatesRepo->EmailTemplatesOfId($row['id']);
                $update = $this->emailTemplatesRepo->update($row['id'], $row);
                if (empty($update)) {
                    return response()->json('error');
                }
            } else {
                if (!empty($row['template_name']) || !empty($row['content'])) {
                    $prepare_data = $this->emailTemplatesRepo->prepareData($row);
                    $create = $this->emailTemplatesRepo->create($prepare_data);
                    if (empty($create)) {
                        return response()->json('error');
                    }
                }
            }
        }

        if (count($data['deleteIds']) > 0) {
            foreach ($data['deleteIds'] as $deleteId) {
                $delete = $this->emailTemplatesRepo->EmailTemplatesOfId($deleteId);
                $this->emailTemplatesRepo->delete($delete);
            }
        }
        return response()->json('success');
    }
    function saveTemplate(Request $request)
    {
        $data = $request->all();

            if ($request['id'] != null) {
                $request['id'] = $this->emailTemplatesRepo->EmailTemplatesOfId($request['id']);
                $update = $this->emailTemplatesRepo->update($request['id'], $data);
                if ($update) {
                    return response()->json('updated');
                }
            } else {
                if (!empty($request['template_name'])) {
                    $prepare_data = $this->emailTemplatesRepo->prepareData($data);
                    $create = $this->emailTemplatesRepo->create($prepare_data);
                    if ($create) {
                        return response()->json('created');
                    }
                }
            }

        return response()->json('success');
    }
    function deleteTemplate(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            if (isset($value['isSelected']) && $value['isSelected'] == 1) {
                $expense = $this->emailTemplatesRepo->EmailTemplatesOfId($value['id']);
                $data = $this->emailTemplatesRepo->delete($expense);
            }
        }

        return response()->json(['response' => 'success']);
    }

    function getEmailTemplate()
    {
        $data = $this->emailTemplatesRepo->getEmailTemplate();
        if (!empty($data)) {
            return response()->json($data);
        }
        return null;
    }

    function getAllEmailTemplateDataTable(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $allRecords = $this->emailTemplatesRepo->countAllEmailTemplate();
        $filterRecords = $this->emailTemplatesRepo->getFilterRecords($search);
        $data = $this->emailTemplatesRepo->getAllEmailTemplateDataTable($order, $column_name, $search, $start, $length);

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }
    function getTemplateById(Request $request)
    {
        $data = $this->emailTemplatesRepo->getTemplateById($request->id);
        return response()->json($data);
    }

    function getAllTemplatesOption()
    {
        $data = $this->emailTemplatesRepo->getAllTemplatesOption();
        $variables = $this->optionRepo->getAllOptionsBySelectId(15);
        if (!empty($variables)) {
            $variables = $variables[0]['value_text'];
        }
        $res = ['data' =>  $data, 'variables' => $variables];
        return response()->json($res);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function saveContact(Request $request)
    {
        $data = $request->all();

        if ($request->id) {
            $award = $this->contactRepo->ContactOfId($request->id);
            $res = $this->contactRepo->update($award, $data);
            return response()->json("Contact Update succesfully");
        } else {
            $prepared_data = $this->contactRepo->prepareData($data);
            $create = $this->contactRepo->create($prepared_data);
            return response()->json("Contact Add succesfully");
        }
    }

    public function getAllContact()
    {
        $contact_data = $this->contactRepo->getAllContact();
        return $this->jsonResponse($contact_data);
    }

    public function getAllContactsDatatable(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $allRecords = $this->contactRepo->countAllContacts();
        $data = $this->contactRepo->getAllContactsDatatable($column_name, $order, $search, $start, $length);
        $filteredRows = $this->contactRepo->countFilteredRows($search);
        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filteredRows) > 0 ? (int)$filteredRows[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    public function deleteContact(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            if (isset($value['isSelected']) && $value['isSelected'] == 1) {
                $contact = $this->contactRepo->ContactOfId($value['id']);
                $res = $this->contactRepo->delete($contact);
            }
        }
        return $this->jsonResponse($res);
    }
    public function getContactByID(Request $request)
    {
        return $this->contactRepo->getContactById($request->id);
    }
}

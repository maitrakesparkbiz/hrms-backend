<?php

namespace App\Http\Controllers;

use App\Entities\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    function getAllPermission()
    {
        $data = $this->permissionRepo->getAllPermissions();
        $tempArr = [];

        foreach ($data as $value) {
            $tempArr[$value['cat_id']][] = $value;
        }
        $basic_per = ['VIEW', 'ADD', 'EDIT', 'DELETE'];

        foreach ($tempArr as $key => $value) {
            if (count($tempArr[$key]) == 1) {
                $tempArr[$key]['title'] = $tempArr[$key][0]['cat_name'];
                $tempArr[$key]['status'] = false;
                $tempArr[$key]['per'][] = ['title' => 'view', 'id' => $tempArr[$key][0]['id'], 'status' => false];
            } else {
                foreach ($tempArr[$key] as $key1 => $value1) {
                    $strAfterFirstDot = substr($value1['name'], strpos($value1['name'], '.') + 1, strlen($value1['name']));
                    if (in_array($strAfterFirstDot, $basic_per)) {
                        $tempArr[$key]['title'] = $tempArr[$key][0]['cat_name'];
                        $tempArr[$key]['status'] = false;
                        $tempArr[$key]['per'][] = ['title' => $value1['title'], 'id' => $value1['id'], 'status' => false];
                    }
                    if ($value1['cat_status'] > 0) {
                        $tempArr[$key]['sub_tab'][$value1['cat_status']][] = $value1;
                    }
                }
                if (isset($tempArr[$key]['sub_tab'])) {
                    $i = 0;
                    foreach ($tempArr[$key]['sub_tab'] as $key2 => $sub_tab) {
                        foreach ($sub_tab as $val) {
                            $title_arr = explode('.', $val['name']);
                            if (!isset($tempArr[$key]['tabs'][$i]['title'])) {
                                $tempArr[$key]['tabs'][$i]['title'] = ucwords(strtolower(str_replace('-', ' ', $title_arr[1])));
                                $tempArr[$key]['tabs'][$i]['status'] = false;
                            }
                            $tempArr[$key]['tabs'][$i]['per'][] = ['title' => $val['title'], 'id' => $val['id'], 'status' => false];
                        }
                        $i++;
                    }
                }
            }

            foreach ($tempArr[$key] as $key1 => $value1) {
                if (isset($value1['cat_status'])) {
                    unset($tempArr[$key][$key1]);
                }
            }
        }

        foreach ($tempArr as $key => $value) {
            if (isset($tempArr[$key]['sub_tab'])) {
                unset($tempArr[$key]['sub_tab']);
            }
        }

        $arr = array_values($tempArr);

        return $this->jsonResponse($arr);
    }

    function addNewPermission(Request $request)
    {
        // print_r($request->only('permission'));
        $permission = $request->all();
        $newpermission = [];

        foreach ($permission as $data) {
            foreach ($data as $key => $value) {
                if ($key == 'permission_name') {
                    $name = $value;
                }
                if ($value == 1) {
                    $checked = $key;
                    $insertdata = $name . "." . $checked;
                    array_push($newpermission, ['name' => $insertdata]);
                }
            }
        }
        foreach ($newpermission as $perdata) {
            $permission_data = new Permission($perdata);
            $new = $this->permissionRepo->addNewPermission($permission_data);


        }
        return $this->jsonResponse(['message' => 'Permisssion Insert Succesfully']);


    }
}

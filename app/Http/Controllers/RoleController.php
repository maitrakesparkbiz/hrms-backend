<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LaravelDoctrine\ORM\Loggers\Formatters\ReplaceQueryParams;

class RoleController extends Controller
{
    // todo check if user has permission to grant or revoke permission
    function grantPermission(Request $request, $role_id, $permission_id)
    {
        $role = $this->roleRepo->getRole($role_id);
        if ($role === null) {
            return $this->errorResponse(['message' => 'role not found']);
        }

        $permission = $this->permissionRepo->getPermission($permission_id);
        if ($permission === null) {
            return $this->errorResponse(['message' => 'permission not found']);
        }

        if ($role->hasPermissionTo($permission->getName())) {
            $message = 'Role ' . $role->getName() . ' already has permission to ' . $permission->getName();
            return $this->errorResponse(['message' => $message]);
        }

        $role->grantPermission($permission);
        $this->roleRepo->save($role);
        $message = 'Role ' . $role->getName() . ' granted ' . $permission->getName() . ' permission';
        return $this->jsonResponse(['message' => $message]);
    }

    function revokePermission(Request $request, $role_id, $permission_id)
    {
        $role = $this->roleRepo->getRole($role_id);
        if ($role === null) {
            return $this->errorResponse(['message' => 'role not found']);
        }

        $permission = $this->permissionRepo->getPermission($permission_id);
        if ($permission === null) {
            return $this->errorResponse(['message' => 'permission not found']);
        }

        if (!$role->hasPermissionTo($permission->getName())) {
            $message = 'Role ' . $role->getName() . ' don\'t have ' . $permission->getName() . ' permission to revoke';
            return $this->errorResponse(['message' => $message]);
        }

        if ($role->getName() === 'Super Admin') {
            return $this->errorResponse(['message' => 'Can not perform any action on this role']);
        }

        $role->revokePermission($permission);
        $this->roleRepo->save($role);
        return $this->jsonResponse(['message' => 'permission revoked']);
    }

    function getAllRoles()
    {
        $data = $this->roleRepo->getRoles();
        return $this->jsonResponse($data);
    }

    function getAllRolesDatatable(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $allRecords = $this->roleRepo->countAllRole();
        $filterRecords = $this->roleRepo->getFilterRecords($search);
        $data = $this->roleRepo->getAllRolesDatatable($order, $column_name, $search, $start, $length);
        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    //    function testRole(){
    //        $data = $this->roleRepo->tempAllRoles();
    //        return $data;
    //    }

    function getRoleWithoutEmployee()
    {
        $data = $this->roleRepo->getRolesWithoutEmployee();
        return $this->jsonResponse($data);
    }


    function getRolePermission(Request $request)
    {
        $role_id = $request->id;
        // todo check if given role_id exist or not
        $existingPermission = $this->roleRepo->getAllPermissionByRoleId($role_id);
        $allPermission = $this->permissionRepo->getAllPermissions();

        foreach ($allPermission as $key => $permission) {
            $allPermission[$key]['has_permission'] = in_array($permission, $existingPermission);
        }
        $role = $this->roleRepo->getRole($role_id);
        return $this->jsonResponse([
            'role_name' => $role->getName(),
            'description' => $role->getDescription(),
            'role_id' => $role->getId(),
            'permissions' => $allPermission
        ]);
    }


    public function getRolewithPermission(Request $request)
    {
        $roles = $this->roleRepo->RoleById($request->id);
        return $roles;
    }

    public function getRoleData(Request $request)
    {
        $role_id = $request->role_id;
        $role_data = $this->roleRepo->RolePermissionById($role_id);
        $data['name'] = $role_data[0]['role_name'];
        $data['description'] = $role_data[0]['description'];
        $data['permissions'] = $this->createPermissionArr($role_data);
        return $data;
    }


    function createPermissionArr($permissions)
    {
        $tempArr = [];
        foreach ($permissions as $value) {
            if ($value['cat_id']) {
                $tempArr[$value['cat_id']][] = $value;
            }
        }

        $basic_per = ['VIEW', 'ADD', 'EDIT', 'DELETE'];

        foreach ($tempArr as $key => $value) {
            if (count($tempArr[$key]) == 1) {
                $tempArr[$key]['title'] = $tempArr[$key][0]['cat_name'];
                $tempArr[$key]['per'][] = ['title' => 'view'];
            } else {
                foreach ($tempArr[$key] as $key1 => $value1) {
                    $strAfterFirstDot = substr($value1['name'], strpos($value1['name'], '.') + 1, strlen($value1['name']));
                    if (in_array($strAfterFirstDot, $basic_per)) {
                        $tempArr[$key]['title'] = $tempArr[$key][0]['cat_name'];
                        $tempArr[$key]['per'][] = ['title' => $value1['title']];
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
                            }
                            $tempArr[$key]['tabs'][$i]['per'][] = ['title' => $val['title']];
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

        return array_values($tempArr);
    }

    function saveRole(Request $request)
    {
        $data = $request->all();
        $temp_data['name'] = $data['name'];
        $temp_data['description'] = $data['description'];

        if (isset($data['id']) && $data['id'] != null) {
            $role = $this->roleRepo->RoleOfId($data['id']);
            $temp_data['name'] = $data['name'];
            $temp_data['description'] = $data['description'];

            foreach ($data['permissions'] as $per) {
                if ($per['status']) {
                    $role->addPermission($this->permissionRepo->PermissionOfId($per['id']));
                } else {
                    $role->removePermission($this->permissionRepo->PermissionOfId($per['id']));
                }
            }
            $this->roleRepo->update($role, $temp_data);
            return $this->jsonResponse("Updated SuccessFully");
        } else {
            foreach ($data['permissions'] as $per) {
                if ($per['status']) {
                    $temp_data['permissions'][] = $this->permissionRepo->PermissionOfId($per['id']);
                }
            }

            $res = $this->roleRepo->prepareData($temp_data);
            $result = $this->roleRepo->create($res);
            return $this->jsonResponse($result);
        }
    }

    function deleteRole(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            if (isset($value['isSelected']) && $value['isSelected'] == 1) {
                $role = $this->roleRepo->RoleOfId($value['id']);
                $data = $this->roleRepo->delete($role);
            }
        }
        return $this->jsonResponse($data);
    }
}

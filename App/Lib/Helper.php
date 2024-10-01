<?php

namespace App\Lib;

class Helper {

    /**
     * parseData
     *
     * @param  array $foodArray 
     * @return response array 
     */
    public function parseData($foodArray) {
        $data = [];
        
        // Check if 'assignData' exists and has items
        if (isset($foodArray['assignData']) && is_array($foodArray['assignData']) && count($foodArray['assignData']) > 0) {
            $foodArray['assignData'] = array_map(fn($dietPlan) => $dietPlan->jsonSerialize(), $foodArray['assignData']);
            $assignedArray = array_column($foodArray['assignData'], 'food_id');

            foreach ($foodArray['foodData'] as $value) {
                $value['assigned'] = in_array($value['id'], $assignedArray) ? 1 : 0;
                $data[$value['assigned'] ? 'assigned' : 'unAssigned'][] = $value;
            }
        } else {
            // If no assignData, mark all as unassigned
            foreach ($foodArray['foodData'] as $value) {
                $value['assigned'] = 0;
                $data['unAssigned'][] = $value;
            }
        }

        return $data;
    }

    public function validateFoodAssignment(array $req): array {
        $isFoodIdNumeric = isset($req['food_id']) && preg_match('/^-?\d+(\.\d+)?$/', $req['food_id']); 
        $isResidentIdNumeric = isset($req['resident_id']) && preg_match('/^-?\d+(\.\d+)?$/', $req['resident_id']); 

        if (empty($req['food_id']) || !$isFoodIdNumeric || empty($req['resident_id']) || !$isResidentIdNumeric) {
            return ['status' => 'failed', 'error' => 'All fields are required'];
        }

        return array_merge($req, ['created_by' => $req['created_by'] ?? null]);
    }

    public function validateInsertUpdateFoodParams(array $req): array {
        $isCategoryNumeric = isset($req['category']) && preg_match('/^-?\d+(\.\d+)?$/', $req['category']); 
        $isIddsiLevelNumeric = isset($req['iddsi_level']) && preg_match('/^-?\d+(\.\d+)?$/', $req['iddsi_level']); 
        
        if (empty($req['name']) || empty($req['category']) || empty($req['iddsi_level']) || !$isCategoryNumeric || !$isIddsiLevelNumeric) {
            return ['status' => 'failed', 'error' => 'All fields are required'];
        }

        return array_merge($req, ['created_by' => $req['created_by'] ?? null]);
    }

    public function validateInsertUpdateResidentParams(array $req): array {
        $isIddsiLevelNumeric = isset($req['iddsi_level']) && preg_match('/^-?\d+(\.\d+)?$/', $req['iddsi_level']); 
        if (empty($req['name']) || !$isIddsiLevelNumeric) {
            return ['status' => 'failed', 'error' => 'All fields are required'];
        }

        return array_merge($req, ['created_by' => $req['created_by'] ?? null]);
    }

    public function validateLoginParams(array $req): array {
        if (empty($req['username']) || empty($req['password'])) {
            return ['status' => 'Not Acceptable', 'msg' => 'All fields are required'];
        }

        return array_merge($req, ['created_by' => $req['created_by'] ?? null]);
    }

    public function validateRegisterParams(array $req): array {
        if (empty($req['username']) || empty($req['email']) || empty($req['password'])) {
            return ['status' => 'failed', 'msg' => 'All fields are required'];
        }

        return array_merge($req, ['created_by' => $req['created_by'] ?? null]);
    }

    public function validateCategoryParams(array $req): array {
        if (empty($req['name']) || empty($req['id'])) {
            return ['status' => 'failed', 'msg' => 'All fields are required'];
        }

        return array_merge($req, ['created_by' => $req['created_by'] ?? null]);
    }

    public function respond(bool $success, string $successMsg, string $failMsg): array {
        return $success ? ['status' => 'success', 'msg' => $successMsg] : ['status' => 'failed', 'msg' => $failMsg];
    }

    public function respondList(array $data, string $failMsg): array {
        return !empty($data) ? ['status' => 'success', 'data' => $data] : ['status' => 'failed', 'msg' => $failMsg];
    }

    public function formatErrorResponse(string $errorMsg): array {
        return ['status' => 'failed', 'error' => $errorMsg];
    }
}

<?php
// backend/api/controllers/TeamMembersController.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/TeamMember.php';
include_once '../utils/UploadHandler.php';

class TeamMembersController {
    private $teamMember;
    private $uploadHandler;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->teamMember = new TeamMember($db);
        $this->uploadHandler = new UploadHandler();
    }

    public function getAll() {
        try {
            $language = $_GET['language'] ?? 'en';
            $stmt = $this->teamMember->readWithLanguage($language);
            $num = $stmt->rowCount();

            if ($num > 0) {
                $team_arr = array();
                $team_arr["data"] = array();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $team_item = array(
                        "id" => $id,
                        "name" => $name,
                        "position" => $position,
                        "bio" => html_entity_decode($bio),
                        "name_fr" => $name_fr ?? '',
                        "position_fr" => $position_fr ?? '',
                        "bio_fr" => $bio_fr ?? '',
                        "image_path" => $image_path,
                        "email" => $email,
                        "phone" => $phone,
                        "linkedin_url" => $linkedin_url,
                        "twitter_url" => $twitter_url,
                        "status" => $status,
                        "sort_order" => $sort_order,
                        "created_at" => $created_at,
                        "updated_at" => $updated_at
                    );
                    array_push($team_arr["data"], $team_item);
                }

                http_response_code(200);
                echo json_encode(array(
                    "success" => true,
                    "data" => $team_arr["data"],
                    "count" => $num
                ));
            } else {
                http_response_code(200);
                echo json_encode(array(
                    "success" => true,
                    "data" => array(),
                    "count" => 0,
                    "message" => "No team members found"
                ));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array(
                "success" => false,
                "message" => "Error retrieving team members: " . $e->getMessage()
            ));
        }
    }

    public function getActive() {
        try {
            $language = $_GET['language'] ?? 'en';
            $stmt = $this->teamMember->readActiveWithLanguage($language);
            $num = $stmt->rowCount();

            if ($num > 0) {
                $team_arr = array();
                $team_arr["data"] = array();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $team_item = array(
                        "id" => $id,
                        "name" => $name,
                        "position" => $position,
                        "bio" => html_entity_decode($bio),
                        "name_fr" => $name_fr ?? '',
                        "position_fr" => $position_fr ?? '',
                        "bio_fr" => $bio_fr ?? '',
                        "image_path" => $image_path,
                        "email" => $email,
                        "phone" => $phone,
                        "linkedin_url" => $linkedin_url,
                        "twitter_url" => $twitter_url
                    );
                    array_push($team_arr["data"], $team_item);
                }

                http_response_code(200);
                echo json_encode(array(
                    "success" => true,
                    "data" => $team_arr["data"],
                    "count" => $num
                ));
            } else {
                http_response_code(200);
                echo json_encode(array(
                    "success" => true,
                    "data" => array(),
                    "count" => 0,
                    "message" => "No active team members found"
                ));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array(
                "success" => false,
                "message" => "Error retrieving team members: " . $e->getMessage()
            ));
        }
    }

    public function create() {
        try {
            $data = json_decode(file_get_contents("php://input"));

            if (!empty($data->name) && !empty($data->position)) {
                $this->teamMember->name = $data->name;
                $this->teamMember->name_fr = $data->name_fr ?? '';
                $this->teamMember->position = $data->position;
                $this->teamMember->position_fr = $data->position_fr ?? '';
                $this->teamMember->bio = $data->bio ?? '';
                $this->teamMember->bio_fr = $data->bio_fr ?? '';
                $this->teamMember->image_path = $data->image_path ?? '';
                $this->teamMember->email = $data->email ?? '';
                $this->teamMember->phone = $data->phone ?? '';
                $this->teamMember->linkedin_url = $data->linkedin_url ?? '';
                $this->teamMember->twitter_url = $data->twitter_url ?? '';
                $this->teamMember->status = $data->status ?? 'active';
                $this->teamMember->sort_order = $data->sort_order ?? 0;

                if ($this->teamMember->create()) {
                    http_response_code(201);
                    echo json_encode(array(
                        "success" => true,
                        "message" => "Team member created successfully",
                        "data" => array("id" => $this->teamMember->id)
                    ));
                } else {
                    http_response_code(500);
                    echo json_encode(array(
                        "success" => false,
                        "message" => "Unable to create team member"
                    ));
                }
            } else {
                http_response_code(400);
                echo json_encode(array(
                    "success" => false,
                    "message" => "Name and position are required"
                ));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array(
                "success" => false,
                "message" => "Error creating team member: " . $e->getMessage()
            ));
        }
    }

    public function uploadImage() {
        try {
            if (isset($_FILES['image'])) {
                $uploadPath = $this->uploadHandler->uploadFile($_FILES['image'], 'team/');
                
                if ($uploadPath !== false) {
                    http_response_code(200);
                    echo json_encode(array(
                        "success" => true,
                        "message" => "Image uploaded successfully",
                        "data" => array(
                            "image_path" => "uploads/" . $uploadPath,
                            "file_name" => basename($uploadPath)
                        )
                    ));
                } else {
                    $errors = $this->uploadHandler->getErrors();
                    http_response_code(400);
                    echo json_encode(array(
                        "success" => false,
                        "message" => "Upload failed: " . implode(", ", $errors)
                    ));
                }
            } else {
                http_response_code(400);
                echo json_encode(array(
                    "success" => false,
                    "message" => "No image file provided"
                ));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array(
                "success" => false,
                "message" => "Error uploading image: " . $e->getMessage()
            ));
        }
    }

    public function update($id) {
        try {
            $data = json_decode(file_get_contents("php://input"));

            $this->teamMember->id = $id;
            
            $stmt = $this->teamMember->readById();
            $num = $stmt->rowCount();

            if ($num > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                extract($row);

                $this->teamMember->name = $data->name ?? $name;
                $this->teamMember->name_fr = $data->name_fr ?? $name_fr;
                $this->teamMember->position = $data->position ?? $position;
                $this->teamMember->position_fr = $data->position_fr ?? $position_fr;
                $this->teamMember->bio = $data->bio ?? $bio;
                $this->teamMember->bio_fr = $data->bio_fr ?? $bio_fr;
                $this->teamMember->image_path = $data->image_path ?? $image_path;
                $this->teamMember->email = $data->email ?? $email;
                $this->teamMember->phone = $data->phone ?? $phone;
                $this->teamMember->linkedin_url = $data->linkedin_url ?? $linkedin_url;
                $this->teamMember->twitter_url = $data->twitter_url ?? $twitter_url;
                $this->teamMember->status = $data->status ?? $status;
                $this->teamMember->sort_order = $data->sort_order ?? $sort_order;

                if ($this->teamMember->update()) {
                    http_response_code(200);
                    echo json_encode(array(
                        "success" => true,
                        "message" => "Team member updated successfully"
                    ));
                } else {
                    http_response_code(500);
                    echo json_encode(array(
                        "success" => false,
                        "message" => "Unable to update team member"
                    ));
                }
            } else {
                http_response_code(404);
                echo json_encode(array(
                    "success" => false,
                    "message" => "Team member not found"
                ));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array(
                "success" => false,
                "message" => "Error updating team member: " . $e->getMessage()
            ));
        }
    }

    public function delete($id) {
        try {
            $this->teamMember->id = $id;
            
            if ($this->teamMember->delete()) {
                // Optionally delete the image file from server
                $stmt = $this->teamMember->readById();
                if ($stmt->rowCount() > 0) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!empty($row['image_path']) && file_exists("../../" . $row['image_path'])) {
                        unlink("../../" . $row['image_path']);
                    }
                }
                
                http_response_code(200);
                echo json_encode(array(
                    "success" => true,
                    "message" => "Team member deleted successfully"
                ));
            } else {
                http_response_code(500);
                echo json_encode(array(
                    "success" => false,
                    "message" => "Unable to delete team member"
                ));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array(
                "success" => false,
                "message" => "Error deleting team member: " . $e->getMessage()
            ));
        }
    }

    public function updateSortOrder() {
        try {
            $data = json_decode(file_get_contents("php://input"));
            
            if (!empty($data->items) && is_array($data->items)) {
                foreach ($data->items as $item) {
                    if (isset($item->id) && isset($item->sort_order)) {
                        $this->teamMember->id = $item->id;
                        $this->teamMember->sort_order = $item->sort_order;
                        $this->teamMember->updateSortOrder();
                    }
                }
                
                http_response_code(200);
                echo json_encode(array(
                    "success" => true,
                    "message" => "Sort order updated successfully"
                ));
            } else {
                http_response_code(400);
                echo json_encode(array(
                    "success" => false,
                    "message" => "Invalid sort order data"
                ));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array(
                "success" => false,
                "message" => "Error updating sort order: " . $e->getMessage()
            ));
        }
    }
}
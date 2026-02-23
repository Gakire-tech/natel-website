<?php
// backend/api/controllers/TestimonialsController.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/Testimonial.php';
include_once '../utils/UploadHandler.php';

class TestimonialsController {
    private $testimonial;
    private $uploadHandler;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->testimonial = new Testimonial($db);
        $this->uploadHandler = new UploadHandler();
    }

    public function getAll() {
        try {
            $stmt = $this->testimonial->read();
            $num = $stmt->rowCount();

            if ($num > 0) {
                $testimonials_arr = array();
                $testimonials_arr["data"] = array();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $testimonial_item = array(
                        "id" => $id,
                        "name" => $name,
                        "position" => $position,
                        "company" => $company,
                        "content" => html_entity_decode($content),
                        "rating" => $rating,
                        "image_path" => $image_path,
                        "status" => $status,
                        "created_at" => $created_at,
                        "updated_at" => $updated_at
                    );
                    array_push($testimonials_arr["data"], $testimonial_item);
                }

                http_response_code(200);
                echo json_encode(array(
                    "success" => true,
                    "data" => $testimonials_arr["data"],
                    "count" => $num
                ));
            } else {
                http_response_code(200);
                echo json_encode(array(
                    "success" => true,
                    "data" => array(),
                    "count" => 0,
                    "message" => "No testimonials found"
                ));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array(
                "success" => false,
                "message" => "Error retrieving testimonials: " . $e->getMessage()
            ));
        }
    }

    public function getActive() {
        try {
            $stmt = $this->testimonial->readActive();
            $num = $stmt->rowCount();

            if ($num > 0) {
                $testimonials_arr = array();
                $testimonials_arr["data"] = array();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $testimonial_item = array(
                        "id" => $id,
                        "name" => $name,
                        "position" => $position,
                        "company" => $company,
                        "content" => html_entity_decode($content),
                        "rating" => $rating,
                        "image_path" => $image_path
                    );
                    array_push($testimonials_arr["data"], $testimonial_item);
                }

                http_response_code(200);
                echo json_encode(array(
                    "success" => true,
                    "data" => $testimonials_arr["data"],
                    "count" => $num
                ));
            } else {
                http_response_code(200);
                echo json_encode(array(
                    "success" => true,
                    "data" => array(),
                    "count" => 0,
                    "message" => "No active testimonials found"
                ));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array(
                "success" => false,
                "message" => "Error retrieving testimonials: " . $e->getMessage()
            ));
        }
    }

    public function create() {
        try {
            $data = json_decode(file_get_contents("php://input"));

            if (!empty($data->name) && !empty($data->content)) {
                $this->testimonial->name = $data->name;
                $this->testimonial->name_fr = $data->name_fr ?? '';
                $this->testimonial->position = $data->position ?? '';
                $this->testimonial->position_fr = $data->position_fr ?? '';
                $this->testimonial->company = $data->company ?? '';
                $this->testimonial->content = $data->content;
                $this->testimonial->content_fr = $data->content_fr ?? '';
                $this->testimonial->rating = $data->rating ?? 5;
                $this->testimonial->image_path = $data->image_path ?? '';
                $this->testimonial->status = $data->status ?? 'active';

                if ($this->testimonial->create()) {
                    http_response_code(201);
                    echo json_encode(array(
                        "success" => true,
                        "message" => "Testimonial created successfully",
                        "data" => array("id" => $this->testimonial->id)
                    ));
                } else {
                    http_response_code(500);
                    echo json_encode(array(
                        "success" => false,
                        "message" => "Unable to create testimonial"
                    ));
                }
            } else {
                http_response_code(400);
                echo json_encode(array(
                    "success" => false,
                    "message" => "Name and content are required"
                ));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array(
                "success" => false,
                "message" => "Error creating testimonial: " . $e->getMessage()
            ));
        }
    }

    public function uploadImage() {
        try {
            if (isset($_FILES['image'])) {
                $uploadPath = $this->uploadHandler->uploadFile($_FILES['image'], 'testimonials/');
                
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

            $this->testimonial->id = $id;
            
            $stmt = $this->testimonial->readById();
            $num = $stmt->rowCount();

            if ($num > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                extract($row);

                $this->testimonial->name = $data->name ?? $name;
                $this->testimonial->name_fr = $data->name_fr ?? $name_fr;
                $this->testimonial->position = $data->position ?? $position;
                $this->testimonial->position_fr = $data->position_fr ?? $position_fr;
                $this->testimonial->company = $data->company ?? $company;
                $this->testimonial->content = $data->content ?? $content;
                $this->testimonial->content_fr = $data->content_fr ?? $content_fr;
                $this->testimonial->rating = $data->rating ?? $rating;
                $this->testimonial->image_path = $data->image_path ?? $image_path;
                $this->testimonial->status = $data->status ?? $status;

                if ($this->testimonial->update()) {
                    http_response_code(200);
                    echo json_encode(array(
                        "success" => true,
                        "message" => "Testimonial updated successfully"
                    ));
                } else {
                    http_response_code(500);
                    echo json_encode(array(
                        "success" => false,
                        "message" => "Unable to update testimonial"
                    ));
                }
            } else {
                http_response_code(404);
                echo json_encode(array(
                    "success" => false,
                    "message" => "Testimonial not found"
                ));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array(
                "success" => false,
                "message" => "Error updating testimonial: " . $e->getMessage()
            ));
        }
    }

    public function delete($id) {
        try {
            $this->testimonial->id = $id;
            
            if ($this->testimonial->delete()) {
                // Optionally delete the image file from server
                $stmt = $this->testimonial->readById();
                if ($stmt->rowCount() > 0) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!empty($row['image_path']) && file_exists("../../" . $row['image_path'])) {
                        unlink("../../" . $row['image_path']);
                    }
                }
                
                http_response_code(200);
                echo json_encode(array(
                    "success" => true,
                    "message" => "Testimonial deleted successfully"
                ));
            } else {
                http_response_code(500);
                echo json_encode(array(
                    "success" => false,
                    "message" => "Unable to delete testimonial"
                ));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array(
                "success" => false,
                "message" => "Error deleting testimonial: " . $e->getMessage()
            ));
        }
    }
}
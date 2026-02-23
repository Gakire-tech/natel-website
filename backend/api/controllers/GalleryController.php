<?php
// backend/api/controllers/GalleryController.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/Gallery.php';
include_once '../utils/UploadHandler.php';

class GalleryController {
    private $gallery;
    private $uploadHandler;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->gallery = new Gallery($db);
        $this->uploadHandler = new UploadHandler();
    }

    public function getAll() {
        try {
            $stmt = $this->gallery->read();
            $num = $stmt->rowCount();

            if ($num > 0) {
                $gallery_arr = array();
                $gallery_arr["data"] = array();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $gallery_item = array(
                        "id" => $id,
                        "title" => $title,
                        "description" => html_entity_decode($description),
                        "image_path" => $image_path,
                        "category" => $category,
                        "status" => $status,
                        "sort_order" => $sort_order,
                        "created_at" => $created_at,
                        "updated_at" => $updated_at
                    );
                    array_push($gallery_arr["data"], $gallery_item);
                }

                http_response_code(200);
                echo json_encode(array(
                    "success" => true,
                    "data" => $gallery_arr["data"],
                    "count" => $num
                ));
            } else {
                http_response_code(200);
                echo json_encode(array(
                    "success" => true,
                    "data" => array(),
                    "count" => 0,
                    "message" => "No gallery items found"
                ));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array(
                "success" => false,
                "message" => "Error retrieving gallery items: " . $e->getMessage()
            ));
        }
    }

    public function getByCategory($category) {
        try {
            $stmt = $this->gallery->readByCategory($category);
            $num = $stmt->rowCount();

            if ($num > 0) {
                $gallery_arr = array();
                $gallery_arr["data"] = array();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $gallery_item = array(
                        "id" => $id,
                        "title" => $title,
                        "description" => html_entity_decode($description),
                        "image_path" => $image_path,
                        "category" => $category,
                        "status" => $status,
                        "sort_order" => $sort_order,
                        "created_at" => $created_at,
                        "updated_at" => $updated_at
                    );
                    array_push($gallery_arr["data"], $gallery_item);
                }

                http_response_code(200);
                echo json_encode(array(
                    "success" => true,
                    "data" => $gallery_arr["data"],
                    "count" => $num
                ));
            } else {
                http_response_code(200);
                echo json_encode(array(
                    "success" => true,
                    "data" => array(),
                    "count" => 0,
                    "message" => "No gallery items found in this category"
                ));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array(
                "success" => false,
                "message" => "Error retrieving gallery items: " . $e->getMessage()
            ));
        }
    }

    public function create() {
        try {
            $data = json_decode(file_get_contents("php://input"));

            if (!empty($data->title) && !empty($data->image_path)) {
                $this->gallery->title = $data->title;
                $this->gallery->description = $data->description ?? '';
                $this->gallery->image_path = $data->image_path;
                $this->gallery->category = $data->category ?? 'general';
                $this->gallery->status = $data->status ?? 'active';
                $this->gallery->sort_order = $data->sort_order ?? 0;

                if ($this->gallery->create()) {
                    http_response_code(201);
                    echo json_encode(array(
                        "success" => true,
                        "message" => "Gallery item created successfully",
                        "data" => array("id" => $this->gallery->id)
                    ));
                } else {
                    http_response_code(500);
                    echo json_encode(array(
                        "success" => false,
                        "message" => "Unable to create gallery item"
                    ));
                }
            } else {
                http_response_code(400);
                echo json_encode(array(
                    "success" => false,
                    "message" => "Title and image path are required"
                ));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array(
                "success" => false,
                "message" => "Error creating gallery item: " . $e->getMessage()
            ));
        }
    }

    public function uploadImage() {
        try {
            if (isset($_FILES['image'])) {
                $uploadPath = $this->uploadHandler->uploadFile($_FILES['image'], 'gallery/');
                
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

            $this->gallery->id = $id;
            
            $stmt = $this->gallery->readById();
            $num = $stmt->rowCount();

            if ($num > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                extract($row);

                $this->gallery->title = $data->title ?? $title;
                $this->gallery->description = $data->description ?? $description;
                $this->gallery->image_path = $data->image_path ?? $image_path;
                $this->gallery->category = $data->category ?? $category;
                $this->gallery->status = $data->status ?? $status;
                $this->gallery->sort_order = $data->sort_order ?? $sort_order;

                if ($this->gallery->update()) {
                    http_response_code(200);
                    echo json_encode(array(
                        "success" => true,
                        "message" => "Gallery item updated successfully"
                    ));
                } else {
                    http_response_code(500);
                    echo json_encode(array(
                        "success" => false,
                        "message" => "Unable to update gallery item"
                    ));
                }
            } else {
                http_response_code(404);
                echo json_encode(array(
                    "success" => false,
                    "message" => "Gallery item not found"
                ));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array(
                "success" => false,
                "message" => "Error updating gallery item: " . $e->getMessage()
            ));
        }
    }

    public function delete($id) {
        try {
            $this->gallery->id = $id;
            
            if ($this->gallery->delete()) {
                // Optionally delete the image file from server
                $stmt = $this->gallery->readById();
                if ($stmt->rowCount() > 0) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!empty($row['image_path']) && file_exists("../../" . $row['image_path'])) {
                        unlink("../../" . $row['image_path']);
                    }
                }
                
                http_response_code(200);
                echo json_encode(array(
                    "success" => true,
                    "message" => "Gallery item deleted successfully"
                ));
            } else {
                http_response_code(500);
                echo json_encode(array(
                    "success" => false,
                    "message" => "Unable to delete gallery item"
                ));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array(
                "success" => false,
                "message" => "Error deleting gallery item: " . $e->getMessage()
            ));
        }
    }

    public function updateSortOrder() {
        try {
            $data = json_decode(file_get_contents("php://input"));
            
            if (!empty($data->items) && is_array($data->items)) {
                foreach ($data->items as $item) {
                    if (isset($item->id) && isset($item->sort_order)) {
                        $this->gallery->id = $item->id;
                        $this->gallery->sort_order = $item->sort_order;
                        $this->gallery->updateSortOrder();
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
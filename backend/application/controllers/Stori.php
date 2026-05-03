<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stori extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('stori_model');
        $this->load->library('form_validation');
        $this->load->library('upload');
        $this->load->helper('url');

        // CORS headers
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

        // Handle OPTIONS preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }
    }

    private function get_request_data() {
        // For multipart/form-data or application/x-www-form-urlencoded
        if (!empty($_POST)) {
            return $this->input->post();
        }
        // For JSON payloads
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    private function json_response($data, $status_code = 200) {
        $this->output
            ->set_content_type('application/json')
            ->set_status_header($status_code)
            ->set_output(json_encode($data));
    }

    public function index() {
        try {
            $stories = $this->stori_model->get_all();
            log_message('info', 'Fetching all stories');

            $this->json_response([
                'success' => true,
                'data' => $stories
            ], 200);
        } catch (Exception $e) {
            log_message('error', 'Error fetching stories: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'Failed to fetch stories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id) {
        try {
            $story = $this->stori_model->get($id);
            if (!$story) {
                log_message('error', 'Story not found with ID: ' . $id);
                $this->json_response([
                    'success' => false,
                    'message' => 'Story not found'
                ], 404);
                return;
            }

            log_message('info', 'Fetching story with ID: ' . $id);

            $this->json_response([
                'success' => true,
                'data' => $story
            ], 200);
        } catch (Exception $e) {
            log_message('error', 'Error fetching story: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'Failed to fetch story',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store() {
        try {
            $data = $this->get_request_data();
            log_message('info', 'Store Story Request: ' . json_encode($data));

            // Validation rules
            $this->form_validation->set_data($data);
            $this->form_validation->set_rules('title', 'Title', 'required|max_length[255]');
            $this->form_validation->set_rules('author', 'Author', 'required|max_length[255]');
            $this->form_validation->set_rules('description', 'Description', 'required');
            $this->form_validation->set_rules('story_detail', 'Story Detail', 'required');

            if ($this->form_validation->run() === FALSE) {
                log_message('error', 'Validation error: ' . json_encode($this->form_validation->error_array()));
                $this->json_response([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $this->form_validation->error_array()
                ], 422);
                return;
            }

            $imagePath = null;
            $bgImagePath = null;

            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $config['upload_path'] = FCPATH . 'storage/images/';
                $config['allowed_types'] = 'jpeg|jpg|png|gif';
                $config['max_size'] = 2048;
                $config['encrypt_name'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('image')) {
                    log_message('error', 'Image upload error: ' . $this->upload->display_errors());
                    $this->json_response([
                        'success' => false,
                        'message' => 'Failed to upload image',
                        'error' => $this->upload->display_errors()
                    ], 500);
                    return;
                } else {
                    $upload_data = $this->upload->data();
                    $imagePath = 'images/' . $upload_data['file_name'];
                }
            }

            // Handle background image upload
            if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] == 0) {
                $config['upload_path'] = FCPATH . 'storage/images/';
                $config['allowed_types'] = 'jpeg|jpg|png|gif';
                $config['max_size'] = 2048;
                $config['encrypt_name'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('background_image')) {
                    // Delete image if already uploaded
                    if ($imagePath && file_exists('./storage/' . $imagePath)) {
                        unlink('./storage/' . $imagePath);
                    }
                    log_message('error', 'Background image upload error: ' . $this->upload->display_errors());
                    $this->json_response([
                        'success' => false,
                        'message' => 'Failed to upload background image',
                        'error' => $this->upload->display_errors()
                    ], 500);
                    return;
                } else {
                    $upload_data = $this->upload->data();
                    $bgImagePath = 'images/' . $upload_data['file_name'];
                }
            }

            // Prepare data
            $insert_data = [
                'title' => $data['title'] ?? null,
                'author' => $data['author'] ?? null,
                'description' => $data['description'] ?? null,
                'story_detail' => $data['story_detail'] ?? null,
                'image' => $imagePath,
                'background_image' => $bgImagePath,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $story_id = $this->stori_model->insert($insert_data);
            $story = $this->stori_model->get($story_id);

            log_message('info', 'Story created successfully: ' . json_encode($story));

            $this->json_response([
                'success' => true,
                'message' => 'Story successfully created',
                'data' => $story
            ], 201);

        } catch (Exception $e) {
            // Cleanup uploaded files on error
            if (isset($imagePath) && file_exists('./storage/' . $imagePath)) {
                unlink('./storage/' . $imagePath);
            }
            if (isset($bgImagePath) && file_exists('./storage/' . $bgImagePath)) {
                unlink('./storage/' . $bgImagePath);
            }

            log_message('error', 'Store story error: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'Failed to create story',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update($id) {
        try {
            $data = $this->get_request_data();
            log_message('info', 'Update Story Request for ID ' . $id . ': ' . json_encode($data));

            // Check if story exists
            $existing = $this->stori_model->get($id);
            if (!$existing) {
                log_message('error', 'Story not found with ID: ' . $id);
                $this->json_response([
                    'success' => false,
                    'message' => 'Story not found'
                ], 404);
                return;
            }

            // Validation rules
            $this->form_validation->set_data($data);
            $this->form_validation->set_rules('title', 'Title', 'required|max_length[255]');
            $this->form_validation->set_rules('author', 'Author', 'required|max_length[255]');
            $this->form_validation->set_rules('description', 'Description', 'required');
            $this->form_validation->set_rules('story_detail', 'Story Detail', 'required');

            if ($this->form_validation->run() === FALSE) {
                log_message('error', 'Validation error: ' . json_encode($this->form_validation->error_array()));
                $this->json_response([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $this->form_validation->error_array()
                ], 422);
                return;
            }

            $imagePath = null;
            $bgImagePath = null;

            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $config['upload_path'] = './uploads/images/';
                $config['allowed_types'] = 'jpeg|jpg|png|gif';
                $config['max_size'] = 2048;
                $config['encrypt_name'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('image')) {
                    log_message('error', 'Image upload error: ' . $this->upload->display_errors());
                    $this->json_response([
                        'success' => false,
                        'message' => 'Failed to upload image',
                        'error' => $this->upload->display_errors()
                    ], 500);
                    return;
                } else {
                    $upload_data = $this->upload->data();
                    $imagePath = 'images/' . $upload_data['file_name'];
                     // Delete old image
                     if ($existing->image && file_exists('./storage/' . $existing->image)) {
                         unlink('./storage/' . $existing->image);
                     }
                }
            }

            // Handle background image upload
            if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] == 0) {
                $config['upload_path'] = FCPATH . 'storage/images/';
                $config['allowed_types'] = 'jpeg|jpg|png|gif';
                $config['max_size'] = 2048;
                $config['encrypt_name'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('background_image')) {
                    // Delete newly uploaded image if background fails
                    if ($imagePath && file_exists('./storage/' . $imagePath)) {
                        unlink('./storage/' . $imagePath);
                    }
                    log_message('error', 'Background image upload error: ' . $this->upload->display_errors());
                    $this->json_response([
                        'success' => false,
                        'message' => 'Failed to upload background image',
                        'error' => $this->upload->display_errors()
                    ], 500);
                    return;
                } else {
                    $upload_data = $this->upload->data();
                    $bgImagePath = 'images/' . $upload_data['file_name'];
                    // Delete old background image
                    if ($existing->background_image && file_exists('./storage/' . $existing->background_image)) {
                        unlink('./storage/' . $existing->background_image);
                    }
                }
            }

            // Prepare update data
            $update_data = [
                'title' => $data['title'] ?? $existing->title,
                'author' => $data['author'] ?? $existing->author,
                'description' => $data['description'] ?? $existing->description,
                'story_detail' => $data['story_detail'] ?? $existing->story_detail,
            ];

            if ($imagePath) {
                $update_data['image'] = $imagePath;
            }
            if ($bgImagePath) {
                $update_data['background_image'] = $bgImagePath;
            }

            $update_data['updated_at'] = date('Y-m-d H:i:s');

            $this->stori_model->update($id, $update_data);
            $story = $this->stori_model->get($id);

            log_message('info', 'Story updated successfully: ' . json_encode($story));

            $this->json_response([
                'success' => true,
                'message' => 'Story successfully updated',
                'data' => $story
            ], 200);

        } catch (Exception $e) {
            log_message('error', 'Update story error: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'Failed to update story',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id) {
        try {
            log_message('info', 'Attempting to delete story with ID: ' . $id);

            $story = $this->stori_model->get($id);
            if (!$story) {
                log_message('error', 'Story not found with ID: ' . $id);
                $this->json_response([
                    'success' => false,
                    'message' => 'Story not found'
                ], 404);
                return;
            }

            // Delete associated images
            if ($story->image && file_exists('./storage/' . $story->image)) {
                unlink('./storage/' . $story->image);
            }
            if ($story->background_image && file_exists('./storage/' . $story->background_image)) {
                unlink('./storage/' . $story->background_image);
            }

            $this->stori_model->delete($id);

            log_message('info', 'Story deleted successfully with ID: ' . $id);

            $this->json_response([
                'success' => true,
                'message' => 'Story successfully deleted'
            ], 200);

        } catch (Exception $e) {
            log_message('error', 'Delete story error: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'Failed to delete story',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

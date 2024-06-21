<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Admin extends MY_Controller {

        function __construct(){
            parent::__construct();
            if($this->session->userdata('user')['category'] < 2){
                redirect(base_url('logout'));
                return false;
            }
            $this->load->model('Activity_Model');
            $this->load->model('ActivityMedia_Model');
            $this->load->model('Media_Model');
            $this->loggedIn();
        }

        public function index() {
            $this->data['title'] = TITLE.' | Admin';
            $this->data['user'] = $this->session->userdata('user');


            $this->load->view('common/header', $this->data);
            $this->load->view('common/menu', $this->data);
            $this->load->view('admin_view', $this->data);
            $this->load->view('common/footer', $this->data);
        }

        public function new_activity() {
            $data = $this->input->post();
            
            $upload_dir = './media/activity-media/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
        
            if (empty($data)) {
                redirect(base_url('admin_base'));
                return false;
            }
            
            $insert_data = [
                'name' => $data['activityName'],
                'description' => $data['activityDescription']
            ];
        
            $activity_id = $this->Activity_Model->insert($insert_data);
        
            foreach ($_FILES['activityImages']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['activityImages']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_name = md5(basename($_FILES['activityImages']['name'][$key]));
                    $target_file = $upload_dir . $file_name;
            
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $resize_result = $this->resize_image($target_file, 200, 200);
                        if (!$resize_result['status']) {
                            echo "Erro ao redimensionar a imagem $file_name: " . $resize_result['message'];
                        } else {
                            // Atualiza o caminho da imagem redimensionada
                            $resized_file_path = $resize_result['file_path'];
                            $resized_file_path = base_url() . substr($resized_file_path, 1);
        
                            $media_data = [
                                'path' => $resized_file_path,
                                'alt' => md5($resized_file_path) // Exemplo de alt para imagem, ajuste conforme necessário
                            ];
                            $media_id = $this->Media_Model->insert($media_data);
        
                            $activity_media_data = [
                                'id_activity' => $activity_id,
                                'id_media' => $media_id
                            ];
                            $this->ActivityMedia_Model->insert($activity_media_data);
                        }
                    } else {
                        echo "Erro ao mover o arquivo $file_name para $upload_dir";
                    }
                } else {
                    echo "Erro no upload do arquivo $file_name: " . $_FILES['activityImages']['error'][$key];
                }
            }
        
            redirect(base_url('admin_base'));
            return true;
        }
        

        public function get_activities(){
            header('Content-Type: application/json');

            $activities = $this->Activity_Model->fetch_all(false, null, null, 'id DESC', null);

            foreach($activities as $activity => $key){
                $medias = $this->ActivityMedia_Model->fetch_all(false, null, null, null, ['id_activity' => $activities[$activity]['id']]);
                foreach($medias as $md => $value){
                    $activities[$activity]['media'][] = $this->Media_Model->fetch(['id' => $medias[$md]['id_media']]);
                }
            }

            echo json_encode($activities);
        }

        private function resize_image($file_path, $width, $height) {
            $config['image_library'] = 'gd2';
            $config['source_image'] = $file_path;
            $config['maintain_ratio'] = TRUE;
            $config['width'] = $width;
            $config['height'] = $height;
        
            $this->load->library('image_lib', $config);
        
            // Inicializa a biblioteca com a configuração para esta imagem
            $this->image_lib->initialize($config);
        
            // Verifica se ocorreu algum erro durante o redimensionamento
            if (!$this->image_lib->resize()) {
                return [
                    'status' => false,
                    'message' => $this->image_lib->display_errors(),
                    'file_path' => $file_path
                ];
            }
        
            // Limpa a instância da biblioteca para a próxima imagem
            $this->image_lib->clear();
        
            return [
                'status' => true,
                'message' => null,
                'file_path' => $config['source_image'] // Retorna o caminho da imagem redimensionada
            ];
        }
         
        public function delete_activity($id = null){
            header('Content-Type: application/json');
            if(empty($id)){
                echo json_encode(['success' => false]);
                return false;
            }

            $medias = $this->ActivityMedia_Model->fetch_all(false, null, null, null, ['id_activity' => $id]);

            if(!empty($medias)){
                foreach($medias as $media){
                    $this->ActivityMedia_Model->delete(['id_media' => $media['id_media']]);
                    $this->Media_model->delete(['id' => $media['id_media']]);
                }
            }

            $this->Activity_Model->delete(['id' => $id]);

            if(empty($this->Activity_Model->fetch(['id' => $id]))){
                echo json_encode(['success' => true]);
                return true;
            }

            echo json_encode(['success' => false]);
            return false;
        }

        public function edit_activity(){
            header('Content-Type: application/json');
            $return_data = [
                'success' => false,
                'message' => ''
            ];

            $data = $this->input->post();

            $activity = $this->Activity_Model->fetch(['id' => $data['id']]);

            if(($activity['name'] == $data['name']) && ($activity['description'] == $data['desc'])){
                $return_data['message'] = 'Faça alterações à atividade antes de submeter!';
                echo json_encode($return_data);
                return false;
            }

            $this->Activity_Model->update(
                ['name' => $data['name'], 'description' => $data['desc']],
                ['id' => $data['id']]
            );

            if($this->Activity_Model->error){
                $return_data['message'] = 'Ocorreu um erro! Tente novamente mais tarde.';
                echo json_encode($return_data);
                return false;
            }

            $return_data['success'] = true;
            $return_data['message'] = 'Atividade atualizada com sucesso!';
            echo json_encode($return_data);
            return true;
        }

    }   
?>
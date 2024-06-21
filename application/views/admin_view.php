<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
    //print_r($friends);
?>

<main class="admin container mt-5">
    <ul class="menu_admin list-unstyled">
        <li>
            <button class="manage_activities btn btn-primary">Gerir Atividades</button>
        </li>
    </ul>
    <div id="activity-list" class="mt-4 hidden">
        <button class="btn btn-success mt-3" data-toggle="modal" data-target="#addActivityModal">Adicionar Atividade</button>
        <div id="activities-container"></div>   
    </div>
    <div class="modal fade" id="addActivityModal" tabindex="-1" role="dialog" aria-labelledby="addActivityModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addActivityModalLabel">Adicionar Nova Atividade</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addActivityForm" method="post" action="<?=base_url('new_activity')?>" enctype="multipart/form-data">
                        <div id="imagePreviewContainer" class="form-group text-center"></div>
                        <div class="form-group">
                            <label for="activityName">Nome da Atividade</label>
                            <input type="text" name="activityName" class="form-control" id="activityName" required>
                        </div>
                        <div class="form-group">
                            <label for="activityDescription">Descrição</label>
                            <textarea class="form-control" name="activityDescription" id="activityDescription" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="activityImages">Imagens</label>
                            <input type="file" class="form-control-file" name="activityImages[]" id="activityImages" accept="image/*" multiple required>
                            <small class="form-text text-muted">Pode adicionar até 3 imagens.</small>
                        </div>
                        <button type="submit" class="btn btn-primary">Adicionar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">Tem a certeza?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Tem certeza que deseja apagar esta atividade?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Apagar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="dynamicModal" tabindex="-1" role="dialog" aria-labelledby="dynamicModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dynamicModalLabel">Default Title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Default content
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

</main>

<script>
    $(document).ready(function() {
        var deleteActivityId = null; // Variável para armazenar o ID da atividade a ser deletada

        document.getElementById('activityImages').addEventListener('change', function() {
            const files = this.files;
            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
            imagePreviewContainer.innerHTML = ''; // Clear existing previews

            if (files.length > 3) {
                alert('Pode selecionar no máximo 3 imagens.');
                this.value = ''; // Clear the file input
                return;
            }

            Array.from(files).forEach(file => {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.classList.add('img-thumbnail', 'mr-2');
                img.style.width = '200px';
                img.style.height = '200px';
                img.style.objectFit = 'cover';
                img.style.marginTop = '1rem';
                imagePreviewContainer.appendChild(img);
            });
        });

        const ajax = new AjaxHandler();

        $('#activity-list').on('click', '.edit-activity-btn', function() {
            const form = $(this).closest('.activity-card').find('.activity-form');
            form.toggleClass('hidden');
        });

        $(".manage_activities").click(function(){
            $("#activity-list").toggle();
            loadActivities();
        });

        $('#activity-list').on('click', '.delete-activity-btn', function() {
            deleteActivityId = $(this).closest('.activity-card').find('.act_id').text(); // Armazena o ID da atividade
            $('#deleteConfirmModal').modal('show'); // Mostra o modal de confirmação
        });

        // Evento do botão de confirmação de exclusão
        $('#confirmDeleteBtn').click(function() {
            $('#deleteConfirmModal').modal('hide'); // Esconde o modal de confirmação
            $(".loading").toggle();
            ajax.get('<?=base_url('delete_activity')?>/'+deleteActivityId, (data) => {
                console.log(data);
                if(data.success){
                    $(".loading").toggle();
                    setTimeout(()=>{
                        window.location.reload();
                    }, 200);
                }
            });
        });

        $('#activity-list').on('submit', '.activity-form', function(event) {
            event.preventDefault();
            $(".loading").toggle();
            var name = $(this).find('.actName').val();
            var desc = $(this).find('.actDesc').val();
            console.log(name, desc);
            var id = $(this).closest('.activity-card').find('.act_id').text(); // Armazena o ID da atividade
            ajax.post('<?=base_url('edit_activity')?>', {name: name, desc: desc, id: id}, (data) => {
                console.log(data);
                if(!data.success){
                    $(".loading").toggle();
                    $('#dynamicModalLabel').text('Erro!');
                    $('#dynamicModal .modal-body').html('<p>'+data.message+'</p>');
                    
                    // Show the modal
                    $('#dynamicModal').modal('show');
                }else{
                    $(".loading").toggle();
                    $('#dynamicModalLabel').text('Atualizado!');
                    $('#dynamicModal .modal-body').html('<p>'+data.message+'</p>');
                    
                    // Show the modal
                    $('#dynamicModal').modal('show');
                    setTimeout(()=>{
                        window.location.reload();
                    }, 200);
                }
            })
        });

        function loadActivities(){
            $(".loading").toggle();
            ajax.get('<?=base_url('get_activities')?>', function(data){
                console.log(data);
                const activitiesContainer = $('#activities-container');
                activitiesContainer.empty('');
                
                data.forEach(activity => {
                    const activityCard = `
                        <div class="activity-card">
                            <div>
                                <h5>${activity.name}</h5>
                                <p>${activity.description}</p>
                                <div>
                                    ${activity.media.map(media => `<img src="${media.path}" alt="${media.alt}" class="img-thumbnail">`).join('')}
                                </div>
                            </div>
                            <div class="activity-actions">
                                <button class="btn btn-warning edit-activity-btn">Editar</button>
                                <button class="btn btn-danger delete-activity-btn">Apagar</button>
                            </div>
                            <div class="hidden act_id">${activity.id}</div>
                            <form class="activity-form mt-3 hidden" data-id="${activity.id}">
                                <div class="form-group">
                                    <label for="activityName_${activity.id}">Nome da Atividade</label>
                                    <input type="text" class="actName form-control" id="activityName_${activity.id}" value="${activity.name}" required>
                                </div>
                                <div class="form-group">
                                    <label for="activityDescription_${activity.id}">Descrição</label>
                                    <textarea class="actDesc form-control" id="activityDescription_${activity.id}" rows="3" required>${activity.description}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </form>
                        </div>
                    `;
                    activitiesContainer.append(activityCard);
                });
                $(".loading").toggle();
            });
        }
    });
</script>

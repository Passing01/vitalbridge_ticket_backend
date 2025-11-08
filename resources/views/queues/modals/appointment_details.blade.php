<!-- Modal de détails du rendez-vous -->
<div class="modal fade" id="appointmentDetailsModal" tabindex="-1" aria-labelledby="appointmentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="appointmentDetailsModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i>Détails du rendez-vous
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body" id="appointmentDetailsContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
function showAppointmentDetails(appointmentId) {
    console.log('Fonction showAppointmentDetails appelée avec ID:', appointmentId);
    const modal = new bootstrap.Modal(document.getElementById('appointmentDetailsModal'));
    const content = document.getElementById('appointmentDetailsContent');
    
    // Afficher le spinner
    content.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
        </div>`;
    
    // Afficher la modale
    modal.show();
    
    // Simuler un chargement pour le test
    setTimeout(() => {
        content.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3">
                        <i class="fas fa-user-injured me-2"></i>
                        Informations patient
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><strong>Nom complet :</strong> Test Patient</li>
                        <li class="mb-2"><strong>Date de naissance :</strong> 01/01/1980</li>
                        <li class="mb-2"><strong>Genre :</strong> Masculin</li>
                        <li class="mb-2"><strong>Téléphone :</strong> <a href="tel:0123456789">01 23 45 67 89</a></li>
                        <li><strong>Email :</strong> <a href="mailto:test@example.com">test@example.com</a></li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5 class="mb-3">
                        <i class="fas fa-calendar-check me-2"></i>
                        Détails du rendez-vous
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><strong>Date et heure :</strong> ${new Date().toLocaleString('fr-FR')}</li>
                        <li class="mb-2"><strong>Statut :</strong> <span class="badge bg-primary">Planifié</span></li>
                    </ul>
                </div>
            </div>`;
    }, 1000);
}
</script>

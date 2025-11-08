<!-- Modal d'ajout d'indisponibilité -->
<div class="modal fade" id="addUnavailabilityModal" tabindex="-1" aria-labelledby="addUnavailabilityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('doctors.unavailable', $doctor) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addUnavailabilityModalLabel">
                        <i class="fas fa-ban me-2"></i>Déclarer une indisponibilité
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="unavailable_date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="unavailable_date" name="date" 
                               value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="start_time" class="form-label">De</label>
                            <input type="time" class="form-control" id="start_time" name="start_time" required>
                        </div>
                        <div class="col-md-6">
                            <label for="end_time" class="form-label">À</label>
                            <input type="time" class="form-control" id="end_time" name="end_time" required>
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label for="reason" class="form-label">Raison</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

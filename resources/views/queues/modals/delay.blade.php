<!-- Modal d'ajout de retard -->
<div class="modal fade" id="addDelayModal" tabindex="-1" aria-labelledby="addDelayModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('doctors.delay', $doctor) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addDelayModalLabel">
                        <i class="fas fa-clock me-2"></i>Déclarer un retard
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="delay_duration" class="form-label">Durée du retard (en minutes)</label>
                        <input type="number" class="form-control" id="delay_duration" name="duration" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="delay_reason" class="form-label">Raison</label>
                        <textarea class="form-control" id="delay_reason" name="reason" rows="3" required></textarea>
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

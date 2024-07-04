document.addEventListener('DOMContentLoaded', () => {
    const schedulingModal = document.getElementById('schedulingModal');
    const closeModalButton = schedulingModal.querySelector('.close');
    const savePlanningButton = document.getElementById('savePlanning');
    const addSlotButton = document.getElementById('addSlot');
    const timeSlots = document.getElementById('timeSlots');
    const doctorIdInput = document.getElementById('doctorId');
    const planningDateInput = document.getElementById('planningDate');
    const planningDateHiddenInput = document.getElementById('planningDateHidden');
    const slotsDataInput = document.getElementById('slotsData');

    // Boîte de dialogue pour les erreurs
    const errorDialog = document.getElementById('errorDialog');
    const errorTitle = document.getElementById('errorTitle');
    const errorMessage = document.getElementById('errorMessage');
    const errorOkButton = document.getElementById('errorOkButton');

    // Ouvrir la modale lorsqu'on clique sur le bouton "Planifier"
    document.querySelectorAll('.schedule-button').forEach(button => {
        button.addEventListener('click', () => {
            const doctorId = button.getAttribute('data-doctor-id');
            doctorIdInput.value = doctorId;
            schedulingModal.style.display = 'block';
        });
    });

    // Fermer la modale
    closeModalButton.addEventListener('click', () => {
        schedulingModal.style.display = 'none';
    });

    // Ajouter un créneau horaire
    addSlotButton.addEventListener('click', () => {
        const existingSlots = timeSlots.querySelectorAll('.time-slot').length;
        if (existingSlots < 5) {
            const newSlot = document.createElement('div');
            newSlot.classList.add('time-slot');
            newSlot.innerHTML = `
                <label>Début: <input type="time" name="starttime[]" class="start-time" required></label>
                <label>Fin: <input type="time" name="endtime[]" class="end-time" required></label>
                <button class="delete-slot">Supprimer</button>
            `;
            timeSlots.appendChild(newSlot);

            newSlot.querySelector('.delete-slot').addEventListener('click', () => {
                timeSlots.removeChild(newSlot);
            });
        } else {
            showError('Vous ne pouvez pas ajouter plus de 5 créneaux horaires.');
        }
    });

    // Gestion de la suppression des créneaux horaires
    timeSlots.addEventListener('click', (event) => {
        if (event.target.classList.contains('delete-slot')) {
            const slot = event.target.closest('.time-slot');
            timeSlots.removeChild(slot);
        }
    });

    // Afficher une boîte de dialogue d'erreur
    function showError(message) {
        errorTitle.textContent = 'Erreur';
        errorMessage.textContent = message;
        errorDialog.style.display = 'flex';
    }

    // Gérer le clic sur le bouton OK de la boîte de dialogue d'erreur
    errorOkButton.addEventListener('click', () => {
        errorDialog.style.display = 'none';
    });

    // Fermer la boîte de dialogue en cliquant sur la croix
    document.querySelector('.close-error-dialog').addEventListener('click', () => {
        errorDialog.style.display = 'none';
    });

    // Gestion de la soumission du formulaire
    savePlanningButton.addEventListener('click', (event) => {
        event.preventDefault();  // Empêcher la soumission par défaut du formulaire

        const date = planningDateInput.value;
        const slots = [];
        let valid = true;
        let errorMessages = [];

        // Vérifier qu'il y a au moins un créneau
        if (timeSlots.querySelectorAll('.time-slot').length === 0) {
            valid = false;
            errorMessages.push('Vous devez ajouter au moins un créneau horaire.');
        }

        // Vérifier les créneaux horaires
        document.querySelectorAll('.time-slot').forEach(slot => {
            const starttime = slot.querySelector('.start-time').value;
            const endtime = slot.querySelector('.end-time').value;

            if (!starttime || !endtime) {
                valid = false;
                errorMessages.push('Tous les créneaux doivent être complétés.');
            } else {
                slots.push({ starttime, endtime });
            }
        });

        // Vérifier la date
        if (!date) {
            valid = false;
            errorMessages.push('La date doit être sélectionnée.');
        }

        if (!valid) {
            showError(errorMessages.join('\n'));  // Affiche tous les messages d'erreur
        } else {
            // Ajoutez la date au début et à la fin des créneaux horaires
            slots.forEach(slot => {
                slot.starttime = `${date}T${slot.starttime}`;  // Format YYYY-MM-DDTHH:MM
                slot.endtime = `${date}T${slot.endtime}`;
            });

            slotsDataInput.value = JSON.stringify(slots);
            planningDateHiddenInput.value = date;

            // Assurez-vous que doctorId est défini
            const doctorId = doctorIdInput.value;
            if (!doctorId) {
                showError('Le médecin doit être sélectionné.');
                return;
            }

            // Ajoutez doctorId dans le formulaire
            const doctorIdHiddenInput = document.createElement('input');
            doctorIdHiddenInput.type = 'hidden';
            doctorIdHiddenInput.name = 'doctorId';
            doctorIdHiddenInput.value = doctorId;
            document.getElementById('planningForm').appendChild(doctorIdHiddenInput);

            // Soumission du formulaire
            document.getElementById('planningForm').submit();
        }
    });

});

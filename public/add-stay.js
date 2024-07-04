document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM entièrement chargé et analysé');

    // Vérifiez si l'élément avec l'ID #form_entrydate existe
    const entryDateElement = document.querySelector('#stays_entrydate');
    if (!entryDateElement) {
        console.error('Le sélecteur #stays_entrydate est introuvable dans le DOM.');
        return; // Sortir de la fonction si l'élément est introuvable
    }
    console.log('Element #stays_entrydate trouvé:', entryDateElement);

    // Vérifiez si l'élément avec l'ID #stays_slot existe
    const slotSelect = document.querySelector('#stays_slot');
    if (!slotSelect) {
        console.error('Le sélecteur #stays_slot est introuvable dans le DOM.');
        return; // Sortir de la fonction si l'élément est introuvable
    }
    console.log('Element #stays_slot trouvé:', slotSelect);

    // Gestion du changement de spécialité
    document.querySelector('.speciality-selector').addEventListener('change', function () {
        const specialityId = this.value;

        fetch(`/stay-search?speciality=${specialityId}`)
            .then(response => response.json())
            .then(data => {
                console.log('Données reçues pour la spécialité:', data);
                const doctorSelect = document.querySelector('.doctor-selector');
                doctorSelect.innerHTML = '<option value="">Choisissez un médecin</option>';
                data.doctors.forEach(doctor => {
                    doctorSelect.innerHTML += `<option value="${doctor.id}">${doctor.firstname} ${doctor.lastname}</option>`;
                });

                const reasonSelect = document.querySelector('.reason-selector');
                reasonSelect.innerHTML = '<option value="">Choisissez un motif</option>';
                data.reasons.forEach(reason => {
                    reasonSelect.innerHTML += `<option value="${reason.id}">${reason.name}</option>`;
                });
            })
            .catch(error => console.error('Erreur lors du fetch pour la spécialité:', error));
    });

    // Gestion du changement de médecin
    document.querySelector('.doctor-selector').addEventListener('change', function () {
        const doctorId = this.value;
        // Extraire uniquement la date en format YYYY-MM-DD
        const date = new Date(entryDateElement.value).toISOString().split('T')[0];

        console.log('Doctor ID changé:', doctorId);
        console.log('Date sélectionnée:', date);

        if (!doctorId || !date) {
            console.error('Doctor ID ou Date est manquant.');
            return;
        }

        fetch('/get-availability', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: new URLSearchParams({
                doctor_id: doctorId,
                date: date
            })
        })
            .then(response => response.json())
            .then(data => {
                console.log('Disponibilités reçues:', data);
                slotSelect.innerHTML = '<option value="">Choisissez un créneau</option>';
                if (data.slots && data.slots.length > 0) {
                    data.slots.forEach(slot => {
                        slotSelect.innerHTML += `<option value="${slot.id}">${slot.starttime} - ${slot.endtime}</option>`;
                    });
                } else {
                    slotSelect.innerHTML = '<option value="">Aucun créneau disponible</option>';
                }
            })
            .catch(error => console.error('Erreur lors du fetch pour les disponibilités:', error));
    });
});


document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM entièrement chargé et analysé');

    // Obtenir les éléments nécessaires
    const extendYes = document.querySelector('#extend-yes');
    const extendNo = document.querySelector('#extend-no');
    const leavingDateGroup = document.querySelector('#leaving-date-group');
    const entryDateElement = document.querySelector('#stays_entrydate');
    const leavingDateElement = document.querySelector('#stays_leavingdate');  // Assurez-vous que l'ID est correct

    // Vérifiez si l'élément avec l'ID #form_leavingdate existe
    if (!leavingDateElement) {
        console.error('Le sélecteur #form_leavingdate est introuvable dans le DOM.');
        return; // Sortir de la fonction si l'élément est introuvable
    }

    // Fonction pour mettre à jour le champ de date de départ
    function updateLeavingDate() {
        if (extendYes.checked) {
            leavingDateGroup.style.display = 'block';
        } else {
            leavingDateGroup.style.display = 'none';
            // Si "Non" est sélectionné, le leavingdate prend la valeur de entrydate
            const entryDate = entryDateElement.value;
            leavingDateElement.value = entryDate ? `${entryDate.split('T')[0]}T23:59` : '';  // Remplace la date par celle d'entrydate avec l'heure 23:59
        }
    }

    // Initialiser le champ de leavingdate avec l'heure par défaut à 23:59
    if (extendNo.checked) {
        const entryDate = entryDateElement.value;
        leavingDateElement.value = entryDate ? `${entryDate.split('T')[0]}T23:59` : '';
    }

    // Ajouter des écouteurs d'événements sur les boutons radio
    extendYes.addEventListener('change', updateLeavingDate);
    extendNo.addEventListener('change', updateLeavingDate);

    // Initialiser l'état du champ leavingdate en fonction de la sélection initiale
    updateLeavingDate();
});

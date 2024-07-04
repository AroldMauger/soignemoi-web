document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM entièrement chargé et analysé');

    // --- Déclaration des éléments DOM ---
    const entryDateElement = document.querySelector('#stays_entrydate');
    const slotSelect = document.querySelector('#stays_slot');
    const specialityElement = document.querySelector('#stays_speciality');
    const reasonElement = document.querySelector('#stays_reason');
    const doctorElement = document.querySelector('#stays_doctor');
    const searchButton = document.querySelector('#search_button');
    const extendYes = document.querySelector('#stays_extendStay_0');
    const extendNo = document.querySelector('#stays_extendStay_1');
    const leavingDateGroup = document.querySelector('#leaving-date-group');
    const leavingDateElement = document.querySelector('#stays_leavingdate');
    const specialityContainer = document.querySelector(".speciality-container");
    const reasonContainer = document.querySelector(".reason-container");
    const doctorContainer = document.querySelector(".doctor-container");
    const slotContainer = document.querySelector("#availability-container");
    const questionContainer = document.querySelector('.question-in-add-stay');

    // Vérifications initiales des éléments DOM
    if (!entryDateElement || !slotSelect || !searchButton || !specialityElement || !reasonElement || !doctorElement || !leavingDateElement) {
        console.error('Un ou plusieurs éléments nécessaires sont manquants dans le DOM.');
        return;
    }

    console.log('Elements trouvés:', { entryDateElement, slotSelect, specialityElement, reasonElement, doctorElement, searchButton, leavingDateElement, extendYes, extendNo, leavingDateGroup, specialityContainer, reasonContainer, doctorContainer, slotContainer, questionContainer });

    // --- Fonctions Utilitaires ---
    const getTodayDate = () => {
        const today = new Date();
        const day = String(today.getDate()).padStart(2, '0');
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const year = today.getFullYear();
        return `${year}-${month}-${day}`;
    };

    // Vérification des dates
    const today = getTodayDate();
    entryDateElement.setAttribute('min', today);
    leavingDateElement.setAttribute('min', today);

    const validateDate = (dateInput, minDate, errorMessage) => {
        if (new Date(dateInput.value) < new Date(minDate)) {
            alert(errorMessage);
            dateInput.value = minDate;
        }
    };

    // Fonction pour afficher le champ de spécialité si une date d'entrée est définie
    const showSpeciality = () => {
        specialityContainer.style.display = entryDateElement.value ? 'block' : 'none';
    };

    // Fonction pour mettre à jour la visibilité des conteneurs en fonction des champs de spécialité, médecin et créneau
    const updateVisibility = () => {
        reasonContainer.style.display = "none";
        doctorContainer.style.display = "none";
        slotContainer.style.display = "none";
        questionContainer.style.display = "none";
        specialityElement.value = "";
        slotSelect.value = "";
        reasonElement.value = "";
        doctorElement.value = "";
        showSpeciality();
    };

    // Fonction pour mettre à jour les créneaux en fonction du médecin et de la date
    const updateSlots = () => {
        const doctorId = doctorElement.value;
        const date = entryDateElement.value ? new Date(entryDateElement.value).toISOString().split('T')[0] : '';

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
    };

    // Fonction pour mettre à jour le champ de date de départ
    const updateLeavingDate = () => {
        if (extendYes.checked) {
            leavingDateGroup.style.display = 'block';
        } else {
            leavingDateGroup.style.display = 'none';
            // Si "Non" est sélectionné, le leavingdate prend la valeur de entrydate
            const entryDate = entryDateElement.value;
            leavingDateElement.value = entryDate ? `${entryDate.split('T')[0]}T23:59` : '';  // Remplace la date par celle d'entrydate avec l'heure 23:59
        }
    };

    // --- Écouteurs d'Événements ---
    entryDateElement.addEventListener('change', () => {
        updateVisibility();
        updateSlots();
        validateDate(entryDateElement, today, "La date d'entrée ne peut pas être antérieure à la date du jour.");
        leavingDateElement.setAttribute('min', entryDateElement.value);
    });

    leavingDateElement.addEventListener('change', () => {
        validateDate(leavingDateElement, today, "La date de sortie ne peut pas être antérieure à la date du jour.");
    });

    specialityElement.addEventListener('change', () => {
        // Affiche ou cache les conteneurs de raison et médecin en fonction de la spécialité
        reasonContainer.style.display = specialityElement.value ? 'block' : 'none';
        doctorContainer.style.display = specialityElement.value ? 'block' : 'none';
        // Réinitialise les sélections
        reasonElement.value = "";
        doctorElement.value = "";
        slotSelect.innerHTML = '<option value="">Choisissez un créneau</option>';
    });

    doctorElement.addEventListener('change', () => {
        // Affiche ou cache le conteneur des créneaux en fonction du médecin
        slotContainer.style.display = doctorElement.value ? 'block' : 'none';
        updateSlots();
    });

    slotSelect.addEventListener('change', () => {
        // Affiche ou cache le conteneur de la question en fonction du créneau
        questionContainer.style.display = slotSelect.value ? 'block' : 'none';
    });

    searchButton.addEventListener('click', showSpeciality);
    extendYes.addEventListener('change', updateLeavingDate);
    extendNo.addEventListener('change', updateLeavingDate);

    // Initialiser les états et les valeurs au chargement de la page
    showSpeciality();
    updateLeavingDate();

    // Fonction pour gérer le changement de spécialité
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

    // Ajout d'écouteurs d'événements pour les champs de spécialité, médecin et créneau
    specialityElement.addEventListener('change', () => {
        // Affiche ou cache les conteneurs de raison et médecin en fonction de la spécialité
        reasonContainer.style.display = specialityElement.value ? 'block' : 'none';
        doctorContainer.style.display = specialityElement.value ? 'block' : 'none';
    });

    doctorElement.addEventListener('change', () => {
        // Affiche ou cache le conteneur des créneaux en fonction du médecin
        slotContainer.style.display = doctorElement.value ? 'block' : 'none';
        updateSlots();
    });

    slotSelect.addEventListener('change', () => {
        // Affiche ou cache le conteneur de la question en fonction du créneau
        questionContainer.style.display = slotSelect.value ? 'block' : 'none';
    });

    // --- Appels Initiaux ---
    showSpeciality();
    updateLeavingDate();
});

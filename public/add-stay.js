document.addEventListener('DOMContentLoaded', () => {

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

    // Fonction pour vérifier que la date et l'heure choisies ne sont pas dans le passé
    const validateDate = (dateTimeInput) => {
        const now = new Date();
        const chosenDateTime = new Date(dateTimeInput.value);

        if (chosenDateTime < now) {
            alert("La date et l'heure ne peuvent pas être antérieures à la date et l'heure actuelles.");
            dateTimeInput.value = '';  // Réinitialiser le champ de date et d'heure
        }
    };
    const validateEntryTime = () => {
        const selectedSlot = slotSelect.selectedOptions[0];
        if (!selectedSlot) return;

        const startTime = selectedSlot.getAttribute('data-starttime');
        const entryTime = entryDateElement.value;

        if (!entryTime || !startTime) return;

        const entryDateTime = new Date(entryTime);
        const [startHour, startMinute] = startTime.split(':').map(Number);
        const slotStartDateTime = new Date(entryDateTime.getFullYear(), entryDateTime.getMonth(), entryDateTime.getDate(), startHour, startMinute);

        if (entryDateTime > slotStartDateTime) {
            alert("Heure d'entrée du séjour et heure du rendez-vous incompatibles, veuillez sélectionner une autre heure en début de formulaire.");
            entryDateElement.value = '';  // Réinitialiser le champ de date et d'heure
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
        const specialty = specialityElement.value;  // Assuming there is an element for specialty selection

        // Check if both date and specialty are selected
        if (!date || !specialty) {
            console.log('Veuillez sélectionner une date et une spécialité.');
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
                date: date,
                specialty: specialty  // Include specialty in the request body
            })
        })
            .then(response => response.json())
            .then(data => {
                slotSelect.innerHTML = '<option value="">Choisissez un créneau</option>';
                if (data.slots && data.slots.length > 0) {
                    data.slots.forEach(slot => {
                        slotSelect.innerHTML += `<option value="${slot.id}" data-starttime="${slot.starttime}">${slot.starttime} - ${slot.endtime}</option>`;
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
        validateEntryTime()
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

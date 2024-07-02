document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');  // Sélecteur du formulaire
    const specialitySelector = document.querySelector('.speciality-selector');
    const reasonSelector = document.querySelector('.reason-selector');
    const doctorSelector = document.querySelector('.doctor-selector');
    const slotSelector = document.querySelector('.form_slot');  // Sélecteur de créneaux horaires
    const searchButton = document.querySelector('#search-speciality');
    const viewAvailabilityButton = document.querySelector('#view-availability');
    const leavingDateGroup = document.querySelector('#leaving-date-group');
    const extendYes = document.querySelector('#extend-yes');
    const extendNo = document.querySelector('#extend-no');
    const availabilityContainer = document.querySelector('#availability-container');
    const entryDate = document.querySelector('.entrydate-input');  // Corrigez ici l'attribut 'entrydate-input'

    if (extendYes.checked) {
        leavingDateGroup.style.display = 'block';
    } else {
        leavingDateGroup.style.display = 'none';
    }

    document.querySelectorAll('input[name="extend"]').forEach(input => {
        input.addEventListener('change', () => {
            if (extendYes.checked) {
                leavingDateGroup.style.display = 'block';
            } else {
                leavingDateGroup.style.display = 'none';
            }
        });
    });

    searchButton.addEventListener('click', function () {
        const speciality = specialitySelector.value;
        if (speciality) {
            fetch(`/stay-search?speciality=${speciality}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Data from search:', data);  // Affiche les données reçues

                    reasonSelector.innerHTML = '';
                    Object.keys(data.reasons).forEach(key => {
                        const option = document.createElement('option');
                        option.value = data.reasons[key];
                        option.textContent = key;
                        reasonSelector.appendChild(option);
                    });

                    doctorSelector.innerHTML = '<option value="">Choisissez un médecin</option>';
                    data.doctors.forEach(doctor => {
                        const option = document.createElement('option');
                        option.value = doctor.id;
                        option.textContent = `${doctor.firstname} ${doctor.lastname}`;
                        doctorSelector.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Erreur lors de la recherche des médecins ou des raisons :', error);
                });
        } else {
            reasonSelector.innerHTML = '';
            doctorSelector.innerHTML = '<option value="">Choisissez un médecin</option>';
        }
    });

    viewAvailabilityButton.addEventListener('click', () => {
        const doctorId = doctorSelector.value;
        const entryDateValue = entryDate.value;  // Récupérez la valeur de l'entrée de date

        if (doctorId && entryDateValue) {
            // Retirer l'heure de la date si elle est présente
            const formattedDate = entryDateValue.split('T')[0];

            fetch('/get-availability', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    doctor_id: doctorId,
                    date: formattedDate  // Envoyer uniquement la date sans l'heure
                })
            })
                .then(response => {
                    console.log('Response Status:', response.status);  // Affiche le statut de la réponse
                    return response.json();
                })
                .then(data => {
                    console.log('Data from availability:', data);  // Affiche les données reçues

                    if (data.slots && data.slots.length > 0) {
                        slotSelector.innerHTML = '<option value="">Choisissez un créneau</option>';
                        data.slots.forEach(slot => {
                            const option = document.createElement('option');
                            option.value = slot.id;
                            option.textContent = `${slot.starttime} - ${slot.endtime}`;
                            slotSelector.appendChild(option);
                        });
                        availabilityContainer.style.display = 'block';
                    } else {
                        alert('Aucun créneau disponible pour cette date.');
                        slotSelector.innerHTML = '<option value="">Choisissez un créneau</option>';
                        availabilityContainer.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des disponibilités :', error);
                    alert('Erreur lors de la récupération des disponibilités.');
                });
        } else {
            alert('Veuillez sélectionner un médecin et une date.');
        }
    });

    // Afficher les données du formulaire dans la console lors de la soumission
    form.addEventListener('submit', function (event) {

        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => {
            if (data[key]) {
                if (!Array.isArray(data[key])) {
                    data[key] = [data[key]];
                }
                data[key].push(value);
            } else {
                data[key] = value;
            }
        });

        console.log('Form Data:', data);  // Affiche les données du formulaire dans la console

        // form.submit();  // Décommentez ceci pour envoyer le formulaire après le test
    });
});

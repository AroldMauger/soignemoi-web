SELECT
    users.email, users.firstname, users.lastname, users.address,
    stays.entrydate, stays.leavingdate, stays.status,
    specialities.name as doctorSpeciality,
    reasons.name as reasonName,
    CONCAT(doctors.firstname, ' ', doctors.lastname) as doctorName,
    slot.starttime as startStay, slot.endtime as endStay
FROM users
         INNER JOIN stays ON stays.user_id = users.id
         INNER JOIN specialities ON specialities.id = stays.speciality_id
         INNER JOIN reasons ON reasons.id = stays.reason_id
         INNER JOIN doctors ON doctors.id = stays.doctor_id
         INNER JOIN slot ON slot.id = stays.slot_id
WHERE users.id = 2;

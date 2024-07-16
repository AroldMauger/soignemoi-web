# Application web de l'hôpital SoigneMoi

Cette application permet aux patients de l'hôpital de créer un compte pour ensuite prendre rendez-vous avec un spécialiste médical. Un tableau de bord avec l'historique des séjours à l'hôpital donne la possibilité de mieux organiser son séjour. 

Une session administrateur permet d'ajouter des médecins et de gérer leurs emplois du temps en ajoutant des créneaux de consultation.

## Technologies utilisées

Le projet a été réalisé avec les technologies suivantes :

- Symfony (version 7.1.2)
- PHP (version 8.2)
- Services WAMP pour le serveur (en local)
- PHPMyAdmin pour la base de données MySQL
- CSS
- JavaScript

## Configuration et lancement du projet (en local)

### Prérequis

Avant de lancer le projet, assurez-vous d'avoir installé les éléments suivants sur votre machine :

- Composer
- Symfony (v.7)
- PHP (v.8.2)
- WAMP ou un autre service permettant l'utilisation d'un serveur local

### Installation et lancement

1. Clonez ce dépôt sur votre machine :
   git clone https://github.com/AroldMauger/soignemoi-web.git

2. Accédez au répertoire du projet :
   cd nom-du-projet

3. Installez les dépendances PHP à l'aide de Composer :
   composer install

4. Lancez le serveur Symfony en exécutant la commande suivante à la racine du projet :
   symfony serve

5. Une fois le serveur démarré, ouvrez un navigateur web et accédez à l'URL suivante :
   http://localhost:8000

6. Pour tester la scénario "administrateur" : connectez-vous à l'aide des identifiants suivants :
- email : admin@soignemoi.fr
- password : admin

Vous devrez ensuite ajouter un médecin, puis lui assigner un nouvel emploi du temps.

7. Pour tester la scénario utilisateur "Patient" : créez un compte à l'aide du bouton en haut à droite, puis connectez-vous grâce "Se connecter".
Une fois connecté sur votre espace personnel, cliquez sur le bouton "Séjour" afin d'ajouter un séjour. 

Recommandation : veillez au préalable à avoir correctement ajouté un médecin et un emploi du temps depuis la session administrateur, afin que le créneau soit disponible pour le patient. 


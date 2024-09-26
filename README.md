# Installation du projet

## Récupérer le code
* ```git clone git@github.com:CindyPerat/test_project.git```

## Installer les dépendances
* ```composer install```

## Connecter la base de données
_La base de données doit être préalablement créée._
1. Copier le fichier ```.env``` se trouvant à la racine du projet et le renommer en ```.env.local```
2. Dans le fichier ```.env.local```, remplacer les variables ```DATABASE_URL``` et ```MAILER_DSN``` avec les identifiants de la base de données et du serveur mail
3. Mettre à jour la base de données : ```bin/console doctrine:migrations:migrate```

## Lancer le serveur Symfony
* ```symfony server:start```

Le site est maintenant consultable via ce lien : [https://127.0.0.1:8000](https://127.0.0.1:8000)

# Routes

* Le formulaire de contact : /
* La liste des contacts via l'API : /api/contacts
* La documentation de l'API : /api
